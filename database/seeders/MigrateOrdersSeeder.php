<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateOrdersSeeder extends Seeder
{
    // Tryb testowy - tylko wyświetla podsumowanie bez wstawiania
    private bool $testMode = false;

    // Limit rekordów do przetworzenia (null = wszystkie)
    private ?int $limit = null;

    // Bieżące zlecenia (planowanie.data >= dziś - X dni) wstawiamy zawsze,
    // nawet jeśli brak plan_na_plac / wazenia / towarów (status = 'planned', NULLs)
    private int $keepRecentDays = 10;

    public function run(): void
    {
        $this->command->info('=== MIGRACJA DANYCH: mrowisko → mrowisko_local ===');
        $this->command->newLine();

        // Wyłączenie kluczy i czyszczenie tabel docelowych
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('warehouse_items')->truncate();
        DB::table('loading_items')->truncate();
        DB::table('orders')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldDb = DB::connection('mrowisko');

        // 1. Pobierz planowanie
        $query = $oldDb->table('planowanie')->orderBy('data', 'asc');
        if ($this->limit) {
            $query->limit($this->limit);
        }
        $planowanieRecords = $query->get();
        $totalToScan = $planowanieRecords->count();
        $this->command->info("Pobrano {$totalToScan} rekordów z tabeli planowanie");

        // 2. Prefetch wszystkich powiązań ze starej bazy (zamiast N+1 zapytań)
        $this->command->info('Prefetch powiązań...');
        $planIds = $planowanieRecords->pluck('id');

        $planNaPlacByPlan = $oldDb->table('plan_na_plac')
            ->whereIn('planowanie_id', $planIds)
            ->get()
            ->keyBy('planowanie_id');

        $wazeniaByPlan = $oldDb->table('wazenia')
            ->whereIn('planowanie_id', $planIds)
            ->get()
            ->keyBy('planowanie_id');

        $planNaPlacIds = $planNaPlacByPlan->pluck('id');

        $dostawyByPlanNaPlac = $oldDb->table('dostawy')
            ->whereIn('plan_na_plac_id', $planNaPlacIds)
            ->get()
            ->keyBy('plan_na_plac_id');

        $zaladunkiByPlanNaPlac = $oldDb->table('zaladunki')
            ->whereIn('plan_na_plac_id', $planNaPlacIds)
            ->get()
            ->keyBy('plan_na_plac_id');

        $dostawyTowary = $oldDb->table('dostawy_towary')
            ->whereIn('dostawy_id', $dostawyByPlanNaPlac->pluck('id'))
            ->get()
            ->groupBy('dostawy_id');

        $zaladunkiTowary = $oldDb->table('zaladunki_towary')
            ->whereIn('zaladunki_id', $zaladunkiByPlanNaPlac->pluck('id'))
            ->get()
            ->groupBy('zaladunki_id');

        $this->command->info('Analiza i budowa rekordów...');
        $this->command->newLine();

        // 3. Buduj rekordy do wstawienia (z pre-przypisanymi ID)
        $now = now();
        $recentThreshold = now()->subDays($this->keepRecentDays)->toDateString();

        $ordersToInsert = [];
        $loadingItemsToInsert = [];
        $warehouseItemsToInsert = [];
        $skipReasons = [
            'no_plan_na_plac' => 0,
            'no_towary' => 0,
        ];
        $keptDespiteMissing = 0;
        $reconstructedCount = 0;
        $skippedSamples = [];

        $nextOrderId = 1;
        $processed = 0;

        foreach ($planowanieRecords as $planowanie) {
            $processed++;
            if ($processed % 200 === 0) {
                $this->command->info("  [analiza] {$processed}/{$totalToScan}");
            }

            $isRecent = $planowanie->data && $planowanie->data >= $recentThreshold;

            $planNaPlac = $planNaPlacByPlan->get($planowanie->id);
            $wazenie = $wazeniaByPlan->get($planowanie->id);

            $type = $planowanie->rodzaj === 'O' ? 'pickup' : 'sale';
            $warehouseOrigin = $planowanie->rodzaj === 'O' ? 'delivery' : 'loading';

            // Pobierz towary (jeśli są wszystkie potrzebne ogniwa)
            $towary = collect();
            $operatorId = null;
            $createdAt = null;
            $updatedAt = null;
            $operationNotes = null;
            $dostawa = null;
            $zaladunek = null;
            if ($planNaPlac) {
                if ($planowanie->rodzaj === 'O') {
                    $dostawa = $dostawyByPlanNaPlac->get($planNaPlac->id);
                    if ($dostawa) {
                        $towary = $dostawyTowary->get($dostawa->id) ?? collect();
                        $operatorId = $dostawa->operator_id;
                        $createdAt = $dostawa->created_at;
                        $updatedAt = $dostawa->updated_at;
                        $operationNotes = $dostawa->uwagi;
                    }
                } else {
                    $zaladunek = $zaladunkiByPlanNaPlac->get($planNaPlac->id);
                    if ($zaladunek) {
                        $towary = $zaladunkiTowary->get($zaladunek->id) ?? collect();
                        $operatorId = $zaladunek->operator_id;
                        $createdAt = $zaladunek->created_at;
                        $updatedAt = $zaladunek->updated_at;
                        $operationNotes = $zaladunek->uwagi;
                    }
                }
            }

            // Decyzja:
            // - Zlecenie WYKONANE (są towary w dostawach/załadunkach) → zawsze zachowaj
            // - Zlecenie tylko zaplanowane (są dane w planowaniu, brak towarów) → zachowaj jeśli świeże
            // - Stare i bez wykonania → pomiń
            $wasExecuted = ! $towary->isEmpty();

            if (! $wasExecuted && ! $isRecent) {
                // Stare i niewykonane — pomiń
                if (! $planNaPlac) {
                    $skipReasons['no_plan_na_plac']++;
                    $reason = 'Brak plan_na_plac';
                } else {
                    $skipReasons['no_towary']++;
                    $reason = 'Brak towarów w '.($planowanie->rodzaj === 'O' ? 'dostawy_towary' : 'zaladunki_towary');
                }
                if (count($skippedSamples) < 20) {
                    $skippedSamples[] = [$planowanie->id, $planowanie->data, $reason];
                }
                continue;
            }

            // Tara z vehicle_sets (po tractor+trailer); fallback: suma vehicles.tare_kg
            $taraTons = null;
            if ($planowanie->ciagnik_id) {
                $setRow = DB::table('vehicle_sets')
                    ->where('tractor_id', $planowanie->ciagnik_id)
                    ->when(
                        $planowanie->naczepa_id,
                        fn ($q) => $q->where('trailer_id', $planowanie->naczepa_id),
                        fn ($q) => $q->whereNull('trailer_id')
                    )
                    ->where('is_active', true)
                    ->first();
                if ($setRow) {
                    $taraTons = (float) $setRow->tare_kg;
                } else {
                    $tractorTara = (float) (DB::table('vehicles')->where('id', $planowanie->ciagnik_id)->value('tare_kg') ?? 0);
                    $trailerTara = $planowanie->naczepa_id
                        ? (float) (DB::table('vehicles')->where('id', $planowanie->naczepa_id)->value('tare_kg') ?? 0)
                        : 0;
                    if ($tractorTara > 0 || $trailerTara > 0) {
                        $taraTons = $tractorTara + $trailerTara;
                    }
                }
            }

            // Każde wykonane zlecenie bez wazenia w starej bazie → odtwarzamy
            // (przynajmniej netto z towary; brutto tylko gdy znamy tarę)
            $canReconstructWeighing = ! $wazenie && $wasExecuted;

            // Klasyfikacja statusu — wszystkie wykonane → closed (mamy lub odtworzyliśmy ważenie)
            if ($wasExecuted) {
                $status = 'closed';
            } else {
                $status = 'planned';       // tylko zaplanowane (świeże)
                $keptDespiteMissing++;
            }

            // Wagi
            if ($wazenie && $wazenie->waga1 !== null && $wazenie->waga2 !== null) {
                // Pełne ważenie ze starej bazy
                $wagaBrutto = max($wazenie->waga1, $wazenie->waga2);
                $wagaNetto = abs($wazenie->waga1 - $wazenie->waga2);
            } elseif ($wazenie && $wazenie->waga1 !== null) {
                // Częściowe — tylko waga1
                $wagaBrutto = $wazenie->waga1;
                $wagaNetto = null;
            } elseif ($canReconstructWeighing) {
                // Odtwarzamy z towary; brutto tylko gdy znamy tarę
                $nettoKg = $towary->sum('waga');
                $wagaNetto = round($nettoKg / 1000, 3);
                $wagaBrutto = $taraTons !== null ? round($wagaNetto + $taraTons, 3) : null;
                $reconstructedCount++;
            } else {
                $wagaBrutto = null;
                $wagaNetto = null;
            }

            // Archiwum
            $isArchived = 0;
            if ($wazenie && (int) $wazenie->status === 2) {
                $isArchived = 1; // stara baza: wazenia.status=2
            } elseif ($type === 'pickup' && $dostawa && (int) $dostawa->status === 2) {
                $isArchived = 1; // stara baza: dostawy.status=2 (tylko pickup)
            } elseif ($type === 'sale' && $canReconstructWeighing) {
                $isArchived = 1; // sale: brak osobnego sygnału w starej bazie, archiwizujemy odtworzone
            }

            $orderId = $nextOrderId++;

            $ordersToInsert[] = [
                'id' => $orderId,
                'type' => $type,
                'client_id' => $planowanie->kontrahent_cel,
                'driver_id' => $planowanie->kierowca_id,
                'start_client_id' => $planowanie->start,
                'tractor_id' => $planowanie->ciagnik_id,
                'trailer_id' => $planowanie->naczepa_id,
                'lieferschein_id' => $planowanie->ls_id,
                'planned_date' => $planowanie->data,
                'planned_time' => $planowanie->godzina,
                'plac_date' => $planNaPlac->data ?? null,
                'fractions_note' => $planowanie->towary,
                'notes' => $planowanie->uwagi,
                'status' => $status,
                'is_archived' => $isArchived,
                'weight_brutto' => $wagaBrutto,
                'weight_netto' => $wagaNetto,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Items tylko jeśli są towary
            if ($towary->isEmpty()) {
                continue;
            }

            $isLoading = $planowanie->rodzaj !== 'O';
            $itemDate = $planNaPlac->data ?? $planowanie->data;
            foreach ($towary as $towar) {
                $loadingItemsToInsert[] = [
                    'order_id' => $orderId,
                    'fraction_id' => $towar->towar_id,
                    'bales' => $towar->belki,
                    'weight_kg' => $towar->waga,
                    'notes' => null,
                    'operator_id' => $operatorId,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];

                $warehouseItemsToInsert[] = [
                    'date' => $itemDate,
                    'fraction_id' => $towar->towar_id,
                    'bales' => $isLoading ? -$towar->belki : $towar->belki,
                    'weight_kg' => $isLoading ? -$towar->waga : $towar->waga,
                    'origin' => $warehouseOrigin,
                    'origin_order_id' => $orderId,
                    'operator_id' => $operatorId,
                    'notes' => $operationNotes,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];
            }
        }

        // 4. Podsumowanie
        $this->command->newLine();
        $this->command->info('=== PODSUMOWANIE ===');
        $this->command->info('Rekordów do orders:          '.count($ordersToInsert));
        $this->command->info('Rekordów do loading_items:   '.count($loadingItemsToInsert));
        $this->command->info('Rekordów do warehouse_items: '.count($warehouseItemsToInsert));
        $this->command->warn('Pominięte (stare i niewykonane): brak plan_na_plac: '.$skipReasons['no_plan_na_plac']);
        $this->command->warn('Pominięte (stare i niewykonane): brak towarów:     '.$skipReasons['no_towary']);
        $this->command->info("Zachowane bez wazenia (status='delivered') lub bez wykonania ≤{$this->keepRecentDays} dni (status='planned'): {$keptDespiteMissing}");
        $this->command->info("Odtworzone ważenia (towary+tara, od razu w archiwum): {$reconstructedCount}");

        if (! empty($skippedSamples)) {
            $this->command->newLine();
            $this->command->info('Próbka pominiętych (max 20):');
            $this->command->table(['planowanie_id', 'data', 'powód'], $skippedSamples);
        }

        if ($this->testMode) {
            $this->command->newLine();
            $this->command->warn('>>> TRYB TESTOWY - dane NIE zostały wstawione <<<');
            return;
        }

        // 5. Bulk insert w chunkach
        if (empty($ordersToInsert)) {
            $this->command->warn('Brak rekordów do wstawienia.');
            return;
        }

        $this->command->newLine();
        $this->command->info('Wstawianie danych...');

        DB::connection('mysql')->beginTransaction();
        try {
            $totalOrders = count($ordersToInsert);
            $insertedOrders = 0;
            foreach (array_chunk($ordersToInsert, 200) as $chunk) {
                DB::table('orders')->insert($chunk);
                $insertedOrders += count($chunk);
                $this->command->info("  [orders] {$insertedOrders}/{$totalOrders}");
            }

            $totalLoading = count($loadingItemsToInsert);
            $insertedLoading = 0;
            foreach (array_chunk($loadingItemsToInsert, 500) as $chunk) {
                DB::table('loading_items')->insert($chunk);
                $insertedLoading += count($chunk);
                $this->command->info("  [loading_items] {$insertedLoading}/{$totalLoading}");
            }

            $totalWarehouse = count($warehouseItemsToInsert);
            $insertedWarehouse = 0;
            foreach (array_chunk($warehouseItemsToInsert, 500) as $chunk) {
                DB::table('warehouse_items')->insert($chunk);
                $insertedWarehouse += count($chunk);
                $this->command->info("  [warehouse_items] {$insertedWarehouse}/{$totalWarehouse}");
            }

            DB::connection('mysql')->commit();
        } catch (\Throwable $e) {
            DB::connection('mysql')->rollBack();
            $this->command->error('Błąd: '.$e->getMessage());
            throw $e;
        }

        // Reset AUTO_INCREMENT — robimy POZA transakcją bo ALTER TABLE implicit-commitje
        $this->resetAutoIncrement('orders');
        $this->resetAutoIncrement('loading_items');
        $this->resetAutoIncrement('warehouse_items');

        $this->command->info("✅ Migracja zakończona: {$insertedOrders} orders, {$insertedLoading} loading_items, {$insertedWarehouse} warehouse_items.");
    }

    private function resetAutoIncrement(string $table): void
    {
        $maxId = DB::table($table)->max('id') ?? 0;
        DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = ".($maxId + 1));
    }
}
