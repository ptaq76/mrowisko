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
        $oldLS = DB::table('mrowisko.ls')->get();

        $this->command->info('Rozpoczynanie migracji '.$oldLS->count().' rekordów z tabeli ls...');

        $countCreated = 0;
        $countUpdated = 0;

        foreach ($oldLS as $l) {
            // 3. Dopasowanie klienta na podstawie kierunku
            $directionKey = trim($l->kierunek);
            $mappedClientId = $directionToClientId[$directionKey] ?? null;

            // 4. Dane do wstawienia/aktualizacji
            $data = [
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
            ];

            // 5. Sprawdź czy rekord istnieje (po id)
            $exists = DB::table('lieferscheins')->where('id', $l->id)->exists();

            if ($exists) {
                // Aktualizuj istniejący rekord
                DB::table('lieferscheins')->where('id', $l->id)->update($data);
                $countUpdated++;
            } else {
                // Wstaw nowy rekord z zachowaniem id
                DB::table('lieferscheins')->insert(array_merge(['id' => $l->id], $data));
                $countCreated++;
            }
        }

        $this->command->info('Migracja zakończona!');
        $this->command->info("Utworzono: $countCreated");
        if ($countUpdated > 0) {
            $this->command->info("Zaktualizowano: $countUpdated");
        }
    }
}