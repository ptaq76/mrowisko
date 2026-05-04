<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionSeeder extends Seeder
{
    // Marker w notes — żeby idempotentnie usunąć tylko zmigrowane "szybkie wagi"
    // i nie ruszać ręcznych wpisów z biuro/shortcuts/recykler
    private const SZYBKA_WAGA_MARKER = '[migracja] szybka waga (skrót Recykler)';

    private const RECYKLER_CLIENT_ID = 113;
    private const RECYKLER_FRACTION_ID = 20;

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Czyszczenie wcześniejszych wpisów tej migracji (zostawiamy załadunki/dostawy z MigrateOrdersSeeder)
        $deletedProd = DB::table('warehouse_items')->where('origin', 'production')->delete();
        $deletedInv  = DB::table('warehouse_items')->where('origin', 'inventory')->delete();
        // szybka waga: najpierw warehouse_items po markerze, potem orders (CASCADE usuwa loading_items)
        DB::table('warehouse_items')->where('notes', self::SZYBKA_WAGA_MARKER)->delete();
        $deletedSw = DB::table('orders')->where('notes', self::SZYBKA_WAGA_MARKER)->delete();

        if ($deletedProd > 0) {
            $this->command->info("Usunięto {$deletedProd} istniejących wpisów produkcji.");
        }
        if ($deletedInv > 0) {
            $this->command->info("Usunięto {$deletedInv} istniejących wpisów inwentaryzacji.");
        }
        if ($deletedSw > 0) {
            $this->command->info("Usunięto {$deletedSw} istniejących orderów szybka waga.");
        }

        $this->migrateByOrigin('produkcja', 'production');
        $this->migrateByOrigin('inwentaryzacja', 'inventory');
        $this->migrateSzybkaWaga();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('✅ Migracja magazynu zakończona.');
    }

    /**
     * Migracja prostych wpisów magazynowych (produkcja, inwentaryzacja) → warehouse_items.
     */
    private function migrateByOrigin(string $oldPochodzenie, string $newOrigin): void
    {
        $rows = DB::connection('mrowisko')->table('magazyn')
            ->where('pochodzenie', $oldPochodzenie)
            ->orderBy('id')
            ->get();

        $this->command->info("Migracja {$rows->count()} wpisów [{$oldPochodzenie}]...");

        $mapped = $rows->map(fn ($r) => [
            'date'            => $r->data,
            'fraction_id'     => $r->towary_id,
            'bales'           => $r->belki,
            'weight_kg'       => $r->waga,
            'origin'          => $newOrigin,
            'origin_order_id' => null,
            'operator_id'     => $r->operator_id,
            'notes'           => null,
            'created_at'      => $r->created_at ?? now(),
            'updated_at'      => $r->updated_at ?? now(),
        ])->toArray();

        foreach (array_chunk($mapped, 500) as $chunk) {
            DB::table('warehouse_items')->insert($chunk);
        }
    }

    /**
     * Migracja "szybka waga" → analogicznie do ShortcutController::recykler:
     * tworzy order (pickup, klient Recykler, status closed) + loading_item + warehouse_item (delivery).
     * Daty i operator pochodzą ze starego rekordu (nie now()/auth()).
     */
    private function migrateSzybkaWaga(): void
    {
        $rows = DB::connection('mrowisko')->table('magazyn')
            ->where('pochodzenie', 'szybkaWaga')
            ->orderBy('id')
            ->get();

        $this->command->info("Migracja {$rows->count()} wpisów [szybkaWaga] jako orderów-skrótów Recykler...");

        if ($rows->isEmpty()) {
            return;
        }

        // Pre-przypisz ID orderów po aktualnym MAX (ProductionSeeder leci po MigrateOrdersSeeder)
        $nextOrderId = (int) (DB::table('orders')->max('id') ?? 0) + 1;
        $now = now();

        $orders = [];
        $loadingItems = [];
        $warehouseItems = [];

        foreach ($rows as $r) {
            $orderId = $nextOrderId++;
            // ShortcutController zapisuje weight_kg w KG, weight_netto w tonach.
            // W starej bazie magazyn.waga dla szybkiejWagi też jest w KG (typowe wartości 1500-3000 = ~1.5-3t).
            $weightKg = (float) $r->waga;

            $orders[] = [
                'id'           => $orderId,
                'type'         => 'pickup',
                'client_id'    => self::RECYKLER_CLIENT_ID,
                'planned_date' => $r->data,
                'status'       => 'closed',
                'is_archived'  => 1,   // dane historyczne — od razu do archiwum, żeby nie zaśmiecały biuro/weighings
                'weight_netto' => $weightKg / 1000,
                'notes'        => self::SZYBKA_WAGA_MARKER,
                'created_at'   => $r->created_at ?? $now,
                'updated_at'   => $r->updated_at ?? $now,
            ];

            $loadingItems[] = [
                'order_id'    => $orderId,
                'fraction_id' => self::RECYKLER_FRACTION_ID,
                'bales'       => 0,
                'weight_kg'   => $weightKg,
                'notes'       => 'Biuro',
                'operator_id' => $r->operator_id,
                'created_at'  => $r->created_at ?? $now,
                'updated_at'  => $r->updated_at ?? $now,
            ];

            $warehouseItems[] = [
                'date'            => $r->data,
                'fraction_id'     => self::RECYKLER_FRACTION_ID,
                'bales'           => 0,
                'weight_kg'       => $weightKg,
                'origin'          => 'delivery',
                'origin_order_id' => $orderId,
                'operator_id'     => $r->operator_id,
                'notes'           => self::SZYBKA_WAGA_MARKER,
                'created_at'      => $r->created_at ?? $now,
                'updated_at'      => $r->updated_at ?? $now,
            ];
        }

        foreach (array_chunk($orders, 200) as $chunk) {
            DB::table('orders')->insert($chunk);
        }
        foreach (array_chunk($loadingItems, 500) as $chunk) {
            DB::table('loading_items')->insert($chunk);
        }
        foreach (array_chunk($warehouseItems, 500) as $chunk) {
            DB::table('warehouse_items')->insert($chunk);
        }

        // Reset AUTO_INCREMENT po wstawianiu z jawnymi ID
        $maxId = DB::table('orders')->max('id') ?? 0;
        DB::statement('ALTER TABLE orders AUTO_INCREMENT = '.($maxId + 1));
    }
}
