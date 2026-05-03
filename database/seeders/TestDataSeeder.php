<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\WarehouseItem;
use App\Models\WasteFraction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        // =====================================================
        // 1. INWENTARYZACJA – zerowy stan wszystkich frakcji
        // =====================================================
        $this->command->info('Przeprowadzanie inwentaryzacji (zerowy stan)...');

        $fractions = WasteFraction::where('is_warehouse_tracked', true)
            ->where('is_active', true)
            ->where('name', 'not like', '%KARCHEM%')
            ->get();

        foreach ($fractions as $fraction) {
            // Oblicz aktualny stan magazynu dla tej frakcji
            $stockMap = WarehouseItem::computeStockMap();
            $current = $stockMap->get($fraction->id);

            $currentWeight = $current ? (float) $current->total_weight : 0;
            $currentBales  = $current ? (int)   $current->total_bales  : 0;

            // Dodaj korektę inwentaryzacyjną zerującą stan
            // (ujemna wartość wyrównująca aktualny stan do zera)
            WarehouseItem::create([
                'fraction_id' => $fraction->id,
                'date'        => $today,
                'weight_kg'   => -$currentWeight,
                'bales'       => -$currentBales,
                'origin'      => 'inventory',   // dostosuj do stałej w WarehouseItem::ORIGINS
                'operator_id' => 1,
            ]);

            $this->command->line("  ✓ Frakcja [{$fraction->name}] → stan wyzerowany (było: {$currentWeight} kg, {$currentBales} bal)");
        }

        $this->command->info("Inwentaryzacja zakończona. Przetworzono {$fractions->count()} frakcji.");

        // =====================================================
        // 2. ZLECENIA DO PLANOWANIA
        // =====================================================
        $this->command->info('Dodawanie zleceń do planowania...');

        $orders = [
            [
                'type'            => 'pickup',
                'driver_id'       => 2,
                'start_client_id' => 157,
                'client_id'       => 144,
                'tractor_id'      => 3,
                'trailer_id'      => 10,
                'lieferschein_id' => null,
                'planned_date'    => $today,
                'plac_date'       => $today,
                'planned_time'    => '06:00',
                'fractions_note'  => 'Karton czysty',
                'notes'           => 'Uwagi do Eko 24',
            ],
            [
                'type'            => 'sale',
                'driver_id'       => 2,
                'start_client_id' => 157,
                'client_id'       => 130,
                'tractor_id'      => 3,
                'trailer_id'      => 10,
                'lieferschein_id' => 464,
                'planned_date'    => $today,
                'plac_date'       => $today,
                'planned_time'    => '12:00',
                'fractions_note'  => 'Karton czysty',
                'notes'           => 'Uwagi do Leipa',
            ],
            [
                'type'            => 'pickup',
                'driver_id'       => 5,
                'start_client_id' => 157,
                'client_id'       => 251,
                'tractor_id'      => 4,
                'trailer_id'      => 5,
                'lieferschein_id' => null,
                'planned_date'    => $today,
                'plac_date'       => $today,
                'planned_time'    => '08:00',
                'fractions_note'  => 'Karton czysty',
                'notes'           => 'Uwagi do Malinowski',
            ],
            [
                'type'            => 'pickup',
                'driver_id'       => 7,
                'start_client_id' => 157,
                'client_id'       => 333,
                'tractor_id'      => 12,
                'trailer_id'      => 13,
                'lieferschein_id' => null,
                'planned_date'    => $today,
                'plac_date'       => $today,
                'planned_time'    => '18:00',
                'fractions_note'  => 'Karton czysty',
                'notes'           => 'Uwagi do Remondis',
            ],
            [
                'type'            => 'sale',
                'driver_id'       => 7,
                'start_client_id' => 157,
                'client_id'       => 332,
                'tractor_id'      => 12,
                'trailer_id'      => 13,
                'lieferschein_id' => 454,
                'planned_date'    => $today,
                'plac_date'       => $today,
                'planned_time'    => '18:00',
                'fractions_note'  => 'Tworzywa',
                'notes'           => 'Uwagi do Hohen',
            ],
        ];

        foreach ($orders as $index => $data) {
            $order = Order::create($data);
            $num = $index + 1;
            $this->command->line("  ✓ Zlecenie #{$num} [{$data['type']}] id={$order->id} → kierowca {$data['driver_id']}, klient {$data['client_id']}, {$data['planned_time']}");
        }

        $this->command->info('Wszystkie zlecenia dodane.');
    }
}