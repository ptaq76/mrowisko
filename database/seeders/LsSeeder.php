<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Definicja mapowania: "kierunek" (stara baza) => client_id (nowa baza)
        $directionToClientId = [
            'Eisen' => 252,
            'Sandersdorf' => 323,
            'Schwedt' => 130,
            'SPREMBERG' => 203,
            'LEIPA' => 130,
            'Lilla Edet' => 331,
            'Hohenwestedt' => 332,
            'LEHNICE' => 324,
            'GREIZ' => 361,
            'KROSTITZ' => 365,
            'TREBSEN' => 264,
            'Glückstadt' => 195,
        ];

        // 2. Wyłączenie kluczy i czyszczenie tabeli
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('lieferscheins')->truncate();

        // 3. Pobranie danych ze starej bazy
        $oldLS = DB::connection('mrowisko')->table('ls')->get();
        $this->command->info('Migracja '.$oldLS->count().' rekordów z tabeli ls...');

        foreach ($oldLS as $l) {
            $directionKey = trim($l->kierunek);
            $mappedClientId = $directionToClientId[$directionKey] ?? null;

            DB::table('lieferscheins')->insert([
                'id' => $l->id,
                'number' => $l->numer,
                'importer_id' => $l->importer_id,
                'client_id' => $mappedClientId,
                'goods_id' => $l->towar_id,
                'waste_code_id' => null,
                'date' => $l->data,
                'time_window' => $l->okienko,
                'goods_description' => 'Stary kierunek: '.$l->kierunek,
                'is_used' => 0,
                'transp_zew' => $l->transp_zew,
                'status' => $l->status,
                'pdf_path' => $l->pdf_path,
                'created_at' => $l->created_at,
                'updated_at' => $l->updated_at,
            ]);
        }

        $this->resetAutoIncrement('lieferscheins');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('✅ Migracja lieferscheins zakończona.');
    }

    private function resetAutoIncrement($table)
    {
        $maxId = DB::table($table)->max('id') ?? 0;
        $nextId = $maxId + 1;
        DB::statement("ALTER TABLE $table AUTO_INCREMENT = $nextId");
    }
}
