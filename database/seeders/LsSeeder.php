<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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

        // 2. Pobranie danych ze starej bazy
        // Zakładamy, że połączenie ma dostęp do bazy 'mrowisko'
        $oldLS = DB::table('mrowisko.ls')->get();

        $this->command->info('Rozpoczynanie migracji '.$oldLS->count().' rekordów z tabeli ls...');

        $countCreated = 0;
        $countSkipped = 0;

        foreach ($oldLS as $l) {
            // 3. Sprawdzenie, czy numer już istnieje (pole number jest UNIQUE)
            $exists = DB::table('lieferscheins')->where('number', $l->numer)->exists();

            if (! $exists) {
                // 4. Dopasowanie klienta na podstawie kierunku
                $directionKey = trim($l->kierunek);
                $mappedClientId = $directionToClientId[$directionKey] ?? null;

                // 5. Wstawienie rekordu
                DB::table('lieferscheins')->insert([
                    'id' => $l->id,
                    'number' => $l->numer,
                    'importer_id' => $l->importer_id,
                    'client_id' => $mappedClientId,
                    'goods_id' => $l->towar_id,
                    'waste_code_id' => null, // brak odpowiednika w starej bazie
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

                $countCreated++;
            } else {
                $countSkipped++;
            }
        }

        $this->command->info('Migracja zakończona!');
        $this->command->info("Utworzono: $countCreated");
        if ($countSkipped > 0) {
            $this->command->warn("Pominięto duplikaty: $countSkipped");
        }
    }
}
