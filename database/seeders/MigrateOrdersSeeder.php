<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateOrdersSeeder extends Seeder
{
    // Tryb testowy - tylko wyświetla dane bez wstawiania
    private bool $testMode = false;
    
    // Limit rekordów do przetworzenia (null = wszystkie)
    private ?int $limit = 1477;

    public function run(): void
    {
        $this->command->info('=== MIGRACJA DANYCH: mrowisko → mrowisko_local ===');
        $this->command->newLine();

        // Pobierz rekordy z planowanie
        $query = DB::connection('mrowisko')
            ->table('planowanie')
            ->orderBy('data', 'asc');

        if ($this->limit) {
            $query->limit($this->limit);
        }

        $planowanieRecords = $query->get();

        $this->command->info("Pobrano {$planowanieRecords->count()} rekordów z tabeli planowanie");
        $this->command->newLine();

        $ordersToInsert = [];
        $loadingItemsToInsert = [];
        $warehouseItemsToInsert = [];
        $skippedRecords = [];

        foreach ($planowanieRecords as $planowanie) {
            // 1. Znajdź powiązany plan_na_plac
            $planNaPlac = DB::connection('mrowisko')
                ->table('plan_na_plac')
                ->where('planowanie_id', $planowanie->id)
                ->first();

            if (!$planNaPlac) {
                $skippedRecords[] = [
                    'planowanie_id' => $planowanie->id,
                    'data' => $planowanie->data,
                    'reason' => 'Brak powiązanego plan_na_plac',
                ];
                continue;
            }

            // 2. Znajdź powiązane wazenia
            $wazenie = DB::connection('mrowisko')
                ->table('wazenia')
                ->where('planowanie_id', $planowanie->id)
                ->first();

            if (!$wazenie) {
                $skippedRecords[] = [
                    'planowanie_id' => $planowanie->id,
                    'data' => $planowanie->data,
                    'reason' => 'Brak powiązanego wazenia',
                ];
                continue;
            }

            // 3. Określ typ i pobierz towary
            $type = $planowanie->rodzaj === 'O' ? 'pickup' : 'sale';
            $warehouseOrigin = $planowanie->rodzaj === 'O' ? 'delivery' : 'loading';
            $towary = [];
            $operatorId = null;
            $createdAt = null;
            $updatedAt = null;
            $operationNotes = null; // uwagi z dostawy lub załadunku

            if ($planowanie->rodzaj === 'O') {
                // Odbiór - tabela dostawy
                $dostawa = DB::connection('mrowisko')
                    ->table('dostawy')
                    ->where('plan_na_plac_id', $planNaPlac->id)
                    ->first();

                if ($dostawa) {
                    $towary = DB::connection('mrowisko')
                        ->table('dostawy_towary')
                        ->where('dostawy_id', $dostawa->id)
                        ->get();
                    $operatorId = $dostawa->operator_id;
                    $createdAt = $dostawa->created_at;
                    $updatedAt = $dostawa->updated_at;
                    $operationNotes = $dostawa->uwagi;
                }
            } else {
                // Wydanie - tabela zaladunki
                $zaladunek = DB::connection('mrowisko')
                    ->table('zaladunki')
                    ->where('plan_na_plac_id', $planNaPlac->id)
                    ->first();

                if ($zaladunek) {
                    $towary = DB::connection('mrowisko')
                        ->table('zaladunki_towary')
                        ->where('zaladunki_id', $zaladunek->id)
                        ->get();
                    $operatorId = $zaladunek->operator_id;
                    $createdAt = $zaladunek->created_at;
                    $updatedAt = $zaladunek->updated_at;
                    $operationNotes = $zaladunek->uwagi;
                }
            }

            if (empty($towary) || $towary->isEmpty()) {
                $skippedRecords[] = [
                    'planowanie_id' => $planowanie->id,
                    'data' => $planowanie->data,
                    'reason' => 'Brak towarów w ' . ($planowanie->rodzaj === 'O' ? 'dostawy_towary' : 'zaladunki_towary'),
                ];
                continue;
            }

            // 4. Oblicz wagę netto
            $wagaBrutto = $wazenie->waga1;
            $wagaNetto = null;
            if ($wazenie->waga1 !== null && $wazenie->waga2 !== null) {
                $wagaNetto = $wazenie->waga1 - $wazenie->waga2;
            }

            // 5. Przygotuj dane do orders
            $orderData = [
                'type' => $type,
                'client_id' => $planowanie->kontrahent_cel,
                'driver_id' => $planowanie->kierowca_id,
                'start_client_id' => $planowanie->start,
                'tractor_id' => $planowanie->ciagnik_id,
                'trailer_id' => $planowanie->naczepa_id,
                'lieferschein_id' => $planowanie->ls_id,
                'planned_date' => $planowanie->data,
                'planned_time' => $planowanie->godzina,
                'plac_date' => $planNaPlac->data,
                'fractions_note' => $planowanie->towary,
                'notes' => $planowanie->uwagi,
                'status' => 'closed',
                'is_archived' => 0,
                'weight_brutto' => $wagaBrutto,
                'weight_netto' => $wagaNetto,
                'created_at' => now(),
                'updated_at' => now(),
                // Dodatkowe info do wyświetlenia w teście
                '_planowanie_id' => $planowanie->id,
                '_warehouse_origin' => $warehouseOrigin,
                '_operation_notes' => $operationNotes,
            ];

            $ordersToInsert[] = $orderData;

            // 6. Przygotuj dane do loading_items i warehouse_items
            foreach ($towary as $towar) {
                $loadingItemsToInsert[] = [
                    '_planowanie_id' => $planowanie->id,
                    'order_id' => '[NEW]', // będzie uzupełnione po insercie
                    'fraction_id' => $towar->towar_id,
                    'bales' => $towar->belki,
                    'weight_kg' => $towar->waga,
                    'notes' => null,
                    'operator_id' => $operatorId,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];

                $warehouseItemsToInsert[] = [
                    '_planowanie_id' => $planowanie->id,
                    'date' => $planNaPlac->data,
                    'fraction_id' => $towar->towar_id,
                    'bales' => $towar->belki,
                    'weight_kg' => $towar->waga,
                    'origin' => $warehouseOrigin,
                    'origin_order_id' => '[NEW]', // będzie uzupełnione po insercie
                    'operator_id' => $operatorId,
                    'notes' => $operationNotes,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];
            }
        }

        // Wyświetl wyniki
        $this->displayResults($ordersToInsert, $loadingItemsToInsert, $warehouseItemsToInsert, $skippedRecords);

        // Jeśli nie tryb testowy - wykonaj insert
        if (!$this->testMode && !empty($ordersToInsert)) {
            $this->executeInserts($ordersToInsert, $loadingItemsToInsert, $warehouseItemsToInsert);
        }
    }

    private function displayResults(array $orders, array $loadingItems, array $warehouseItems, array $skipped): void
    {
        $this->command->info('=== DANE DO WSTAWIENIA DO TABELI orders ===');
        $this->command->newLine();

        if (empty($orders)) {
            $this->command->warn('Brak rekordów do wstawienia.');
        } else {
            $headers = [
                'plan_id',
                'type',
                'client_id',
                'driver_id',
                'start_client_id',
                'tractor_id',
                'trailer_id',
                'planned_date',
                'planned_time',
                'plac_date',
                'fractions_note',
                'notes',
                'status',
                'weight_brutto',
                'weight_netto',
            ];

            $rows = [];
            foreach ($orders as $order) {
                $rows[] = [
                    $order['_planowanie_id'],
                    $order['type'],
                    $order['client_id'],
                    $order['driver_id'],
                    $order['start_client_id'],
                    $order['tractor_id'],
                    $order['trailer_id'],
                    $order['planned_date'],
                    $order['planned_time'],
                    $order['plac_date'],
                    mb_substr($order['fractions_note'] ?? '', 0, 20) . '...',
                    mb_substr($order['notes'] ?? '', 0, 15) . '...',
                    $order['status'],
                    $order['weight_brutto'],
                    $order['weight_netto'],
                ];
            }

            $this->command->table($headers, $rows);
        }

        $this->command->newLine();
        $this->command->info('=== DANE DO WSTAWIENIA DO TABELI loading_items ===');
        $this->command->newLine();

        if (empty($loadingItems)) {
            $this->command->warn('Brak rekordów do wstawienia.');
        } else {
            $headers = [
                'plan_id',
                'order_id',
                'fraction_id',
                'bales',
                'weight_kg',
                'operator_id',
                'created_at',
            ];

            $rows = [];
            foreach ($loadingItems as $item) {
                $rows[] = [
                    $item['_planowanie_id'],
                    $item['order_id'],
                    $item['fraction_id'],
                    $item['bales'],
                    $item['weight_kg'],
                    $item['operator_id'],
                    $item['created_at'],
                ];
            }

            $this->command->table($headers, $rows);
        }

        $this->command->newLine();
        $this->command->info('=== DANE DO WSTAWIENIA DO TABELI warehouse_items ===');
        $this->command->newLine();

        if (empty($warehouseItems)) {
            $this->command->warn('Brak rekordów do wstawienia.');
        } else {
            $headers = [
                'plan_id',
                'date',
                'fraction_id',
                'bales',
                'weight_kg',
                'origin',
                'origin_order_id',
                'operator_id',
                'notes',
            ];

            $rows = [];
            foreach ($warehouseItems as $item) {
                $rows[] = [
                    $item['_planowanie_id'],
                    $item['date'],
                    $item['fraction_id'],
                    $item['bales'],
                    $item['weight_kg'],
                    $item['origin'],
                    $item['origin_order_id'],
                    $item['operator_id'],
                    mb_substr($item['notes'] ?? '', 0, 20) . '...',
                ];
            }

            $this->command->table($headers, $rows);
        }

        $this->command->newLine();
        $this->command->info('=== POMINIĘTE REKORDY ===');
        $this->command->newLine();

        if (empty($skipped)) {
            $this->command->info('Żaden rekord nie został pominięty.');
        } else {
            $this->command->table(
                ['planowanie_id', 'data', 'powód'],
                $skipped
            );
        }

        $this->command->newLine();
        $this->command->info('=== PODSUMOWANIE ===');
        $this->command->info('Rekordów do orders: ' . count($orders));
        $this->command->info('Rekordów do loading_items: ' . count($loadingItems));
        $this->command->info('Rekordów do warehouse_items: ' . count($warehouseItems));
        $this->command->warn('Pominiętych rekordów: ' . count($skipped));

        if ($this->testMode) {
            $this->command->newLine();
            $this->command->warn('>>> TRYB TESTOWY - dane NIE zostały wstawione <<<');
            $this->command->info('Aby wykonać migrację, ustaw $testMode = false w seederze.');
        }
    }

    private function executeInserts(array $orders, array $loadingItems, array $warehouseItems): void
    {
        $this->command->newLine();
        $this->command->info('Rozpoczynam wstawianie danych...');

        DB::connection('mysql')->beginTransaction();

        try {
            foreach ($orders as $orderData) {
                $planowanieId = $orderData['_planowanie_id'];
                unset($orderData['_planowanie_id']);
                unset($orderData['_warehouse_origin']);
                unset($orderData['_operation_notes']);

                // Wstaw order
                $orderId = DB::connection('mysql')
                    ->table('orders')
                    ->insertGetId($orderData);

                $this->command->info("Wstawiono order ID: {$orderId} (z planowanie ID: {$planowanieId})");

                // Wstaw loading_items dla tego orderu
                $itemsForOrder = array_filter($loadingItems, fn($item) => $item['_planowanie_id'] === $planowanieId);

                foreach ($itemsForOrder as $item) {
                    unset($item['_planowanie_id']);
                    $item['order_id'] = $orderId;

                    DB::connection('mysql')
                        ->table('loading_items')
                        ->insert($item);
                }

                $this->command->info("  → Wstawiono " . count($itemsForOrder) . " pozycji loading_items");

                // Wstaw warehouse_items dla tego orderu
                $warehouseForOrder = array_filter($warehouseItems, fn($item) => $item['_planowanie_id'] === $planowanieId);

                foreach ($warehouseForOrder as $item) {
                    unset($item['_planowanie_id']);
                    $item['origin_order_id'] = $orderId;

                    DB::connection('mysql')
                        ->table('warehouse_items')
                        ->insert($item);
                }

                $this->command->info("  → Wstawiono " . count($warehouseForOrder) . " pozycji warehouse_items");
            }

            DB::connection('mysql')->commit();
            $this->command->info('Migracja zakończona pomyślnie!');

        } catch (\Exception $e) {
            DB::connection('mysql')->rollBack();
            $this->command->error('Błąd podczas migracji: ' . $e->getMessage());
            throw $e;
        }
    }
}
