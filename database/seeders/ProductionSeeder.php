<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Usuń tylko wpisy produkcyjne — zostawiamy załadunki/dostawy z MigrateOrdersSeeder
        $deleted = DB::table('warehouse_items')->where('origin', 'production')->delete();
        if ($deleted > 0) {
            $this->command->info("Usunięto $deleted istniejących wpisów produkcji.");
        }

        $oldRows = DB::table('mrowisko.magazyn')
            ->where('pochodzenie', 'produkcja')
            ->orderBy('id')
            ->get();

        $this->command->info('Migracja '.$oldRows->count().' wpisów produkcji...');

        $rows = $oldRows->map(fn ($r) => [
            'date'            => $r->data,
            'fraction_id'     => $r->towary_id,
            'bales'           => $r->belki,
            'weight_kg'       => $r->waga / 1000,
            'origin'          => 'production',
            'origin_order_id' => null,
            'operator_id'     => $r->operator_id,
            'notes'           => null,
            'created_at'      => $r->created_at ?? now(),
            'updated_at'      => $r->updated_at ?? now(),
        ])->toArray();

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('warehouse_items')->insert($chunk);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('✅ Migracja produkcji zakończona.');
    }
}
