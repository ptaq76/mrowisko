<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WasteFractionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Wyłączenie sprawdzania kluczy obcych i czyszczenie tabel
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('waste_fractions')->truncate();
        DB::table('waste_fraction_groups')->truncate();

        // 2. Import GRUP FRAKCJI (towary_grupy -> waste_fraction_groups)
        $oldGroups = DB::table('mrowisko.towary_grupy')->get();
        $this->command->info('Migracja '.$oldGroups->count().' grup frakcji...');

        foreach ($oldGroups as $group) {
            DB::table('waste_fraction_groups')->insert([
                'id' => $group->id,
                'name' => $group->nazwa,
                'created_at' => $group->created_at,
                'updated_at' => $group->updated_at,
            ]);
        }

        // 3. Przygotowanie danych do FRAKCJI
        // Próbujemy znaleźć ID klienta 'Karchem' w nowej tabeli clients
        $karchemId = DB::table('clients')
            ->where('short_name', 'LIKE', '%Karchem%')
            ->orWhere('name', 'LIKE', '%Karchem%')
            ->value('id');

        $oldTowary = DB::table('mrowisko.towary')->get();
        $this->command->info('Migracja '.$oldTowary->count().' frakcji odpadów...');

        foreach ($oldTowary as $t) {
            $name = $t->nazwa;

            // Logika allows_luz: 1 jeśli nazwa zawiera 'LUZ' lub nie zawiera ani LUZ ani BELKA
            $allowsLuz = (str_contains($name, 'LUZ') || (! str_contains($name, 'LUZ') && ! str_contains($name, 'BELKA'))) ? 1 : 0;

            // Logika allows_belka: 1 jeśli nazwa zawiera 'BELKA'
            $allowsBelka = str_contains($name, 'BELKA') ? 1 : 0;

            // Logika client_id dla Karchem
            $currentClientId = str_starts_with(strtoupper($name), 'KARCHEM') ? $karchemId : null;

            // Logika sells_as_luz (domyślnie 1 dla Gazeta LUZ, Gilza LUZ - wg Twojego opisu w SQL)
            $sellsAsLuz = 0;
            $luzManualList = ['Gazeta LUZ', 'Gilza LUZ', 'Karton LUZ', 'Offset LUZ']; // Przykładowa lista do korekty
            if (in_array($name, $luzManualList)) {
                $sellsAsLuz = 1;
            }

            DB::table('waste_fractions')->insert([
                'id' => $t->id,
                'name' => $name,
                'group_id' => $t->grupa,
                'allows_luz' => $allowsLuz,
                'allows_belka' => $allowsBelka,
                'sells_as_luz' => $sellsAsLuz,
                'show_in_deliveries' => $t->dostawy,
                'show_in_loadings' => $t->zaladunki,
                'show_in_production' => $t->produkcja,
                'client_id' => $currentClientId,
                'is_active' => $t->activ,
                'created_at' => $t->created_at,
                'updated_at' => $t->updated_at,
            ]);
        }

        // 4. Resetowanie liczników AUTO_INCREMENT
        $this->resetAutoIncrement('waste_fraction_groups');
        $this->resetAutoIncrement('waste_fractions');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('✅ Migracja frakcji zakończona.');
        if (! $karchemId) {
            $this->command->warn("⚠️  Nie znaleziono klienta 'Karchem' w tabeli clients. Pole client_id pozostało puste.");
        }

        // 5. Opakowania
        $this->command->info('Seedowanie opakowań...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('order_packaging')->truncate();
        DB::table('opakowania')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        DB::table('opakowania')->insert([
            ['name' => 'Paleta',  'waga' => 30.00, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BigBox',  'waga' => 50.00, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
        $this->command->info('✅ Opakowania dodane.');
    }

    private function resetAutoIncrement($table)
    {
        $maxId = DB::table($table)->max('id') ?? 0;
        $nextId = $maxId + 1;
        DB::statement("ALTER TABLE $table AUTO_INCREMENT = $nextId");
    }
}