<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeightReceiverSeeder extends Seeder
{
    /**
     * Czyta database/data/lieferschein_data.csv (separator ';', UTF-8) i wpisuje
     * sumy z kolumny "Waga" do orders.weight_receiver po dopasowaniu po lieferscheins.number.
     *
     * Uruchom: php artisan db:seed --class=WeightReceiverSeeder
     */
    public function run(): void
    {
        $path = database_path('data/lieferschein_data.csv');
        if (! file_exists($path)) {
            $this->command->error("Brak pliku: {$path}");

            return;
        }

        $handle = fopen($path, 'r');
        if (! $handle) {
            $this->command->error("Nie można otworzyć pliku: {$path}");

            return;
        }

        $headers = fgetcsv($handle, 0, ';');
        if (! $headers) {
            $this->command->error('Pusty plik CSV.');
            fclose($handle);

            return;
        }
        // BOM (jeśli plik zapisany jako UTF-8 z BOM)
        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);

        $idxLs = array_search('LS', $headers, true);
        $idxWaga = array_search('Waga', $headers, true);
        if ($idxLs === false || $idxWaga === false) {
            $this->command->error('Plik CSV nie ma wymaganych kolumn "LS" i "Waga". Znalezione: '.implode(', ', $headers));
            fclose($handle);

            return;
        }

        // Sumujemy wagi per LS (jeden LS może być rozbity na wiele wierszy)
        $weightByLs = [];
        $rowsRead = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $rowsRead++;
            $ls = trim($row[$idxLs] ?? '');
            if ($ls === '') {
                continue;
            }
            $wagaRaw = trim($row[$idxWaga] ?? '');
            // Polski format: "24,240" → 24.240; usuwamy też ewentualne spacje (separator tysięcy)
            $waga = (float) str_replace([' ', ','], ['', '.'], $wagaRaw);
            if ($waga <= 0) {
                continue;
            }
            $weightByLs[$ls] = ($weightByLs[$ls] ?? 0) + $waga;
        }
        fclose($handle);

        // Wpisujemy do orders przez lieferscheins.number
        $updatedOrders = 0;
        $lsNotFound = [];
        $lsNoOrder = [];

        foreach ($weightByLs as $lsNumber => $weight) {
            $lsId = DB::table('lieferscheins')->where('number', $lsNumber)->value('id');
            if (! $lsId) {
                $lsNotFound[] = $lsNumber;

                continue;
            }
            $matched = DB::table('orders')->where('lieferschein_id', $lsId)->count();
            if ($matched === 0) {
                $lsNoOrder[] = $lsNumber;

                continue;
            }
            DB::table('orders')
                ->where('lieferschein_id', $lsId)
                ->update(['weight_receiver' => round($weight, 3)]);
            $updatedOrders += $matched;
        }

        $this->command->info('Wczytano wierszy z CSV: '.$rowsRead);
        $this->command->info('Unikalnych LS: '.count($weightByLs));
        $this->command->info("Zaktualizowanych zleceń (orders.weight_receiver): {$updatedOrders}");
        if (! empty($lsNotFound)) {
            $this->command->warn('LS niezsynchronizowane (brak w lieferscheins): '.count($lsNotFound).
                ' — np. '.implode(', ', array_slice($lsNotFound, 0, 5)));
        }
        if (! empty($lsNoOrder)) {
            $this->command->warn('LS bez powiązanego zlecenia: '.count($lsNoOrder).
                ' — np. '.implode(', ', array_slice($lsNoOrder, 0, 5)));
        }
    }
}
