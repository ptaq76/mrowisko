<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LieferscheinWasteCodeSeeder extends Seeder
{
    /**
     * Czyta database/data/lieferschein_data.csv i wpisuje lieferscheins.waste_code_id
     * dopasowując po lieferscheins.number i waste_codes.code (oba bez spacji).
     *
     * Uruchom: php artisan db:seed --class=LieferscheinWasteCodeSeeder
     */
    public function run(): void
    {
        $path = database_path('data/lieferschein_data.csv');
        if (! file_exists($path)) {
            $this->command->error("Brak pliku: {$path}");

            return;
        }

        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle, 0, ';');
        if (! $headers) {
            $this->command->error('Pusty plik CSV.');
            fclose($handle);

            return;
        }
        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);

        $idxLs = array_search('LS', $headers, true);
        $idxKod = array_search('Kod odpadu', $headers, true);
        if ($idxLs === false || $idxKod === false) {
            $this->command->error('Plik CSV nie ma kolumn "LS" i "Kod odpadu".');
            fclose($handle);

            return;
        }

        // Cache: code → waste_code_id
        $codesMap = DB::table('waste_codes')->pluck('id', 'code')->toArray();

        // Pierwszy niepusty Kod odpadu per LS wygrywa (jeden LS = jeden kod)
        $codeByLs = [];
        $rowsRead = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $rowsRead++;
            $ls = trim($row[$idxLs] ?? '');
            $kod = preg_replace('/\s+/', '', $row[$idxKod] ?? ''); // usuwamy spacje
            if ($ls === '' || $kod === '') {
                continue;
            }
            // Normalizacja: kod odpadu zawsze 6 cyfr (Excel czasem ucina wiodące zera, np. 30308 → 030308)
            if (ctype_digit($kod) && strlen($kod) < 6) {
                $kod = str_pad($kod, 6, '0', STR_PAD_LEFT);
            }
            if (! isset($codeByLs[$ls])) {
                $codeByLs[$ls] = $kod;
            }
        }
        fclose($handle);

        $updatedCount = 0;
        $lsNotFound = [];
        $codeNotFound = [];

        foreach ($codeByLs as $lsNumber => $code) {
            $lsId = DB::table('lieferscheins')->where('number', $lsNumber)->value('id');
            if (! $lsId) {
                $lsNotFound[] = $lsNumber;

                continue;
            }
            if (! isset($codesMap[$code])) {
                $codeNotFound[$code] = ($codeNotFound[$code] ?? 0) + 1;

                continue;
            }
            DB::table('lieferscheins')
                ->where('id', $lsId)
                ->update(['waste_code_id' => $codesMap[$code]]);
            $updatedCount++;
        }

        $this->command->info('Wczytano wierszy z CSV: '.$rowsRead);
        $this->command->info('Unikalnych LS z kodem: '.count($codeByLs));
        $this->command->info("Zaktualizowanych LS-ów (waste_code_id): {$updatedCount}");
        if (! empty($lsNotFound)) {
            $this->command->warn('LS nieznalezione: '.count($lsNotFound).
                ' — np. '.implode(', ', array_slice($lsNotFound, 0, 5)));
        }
        if (! empty($codeNotFound)) {
            $this->command->warn('Kody odpadów nie istnieją w waste_codes: '.implode(', ', array_keys($codeNotFound)));
        }
    }
}
