<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KarchemSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('karchem_wysylki')->truncate();
        DB::table('karchem_stany_poczatkowe')->truncate();
        DB::table('karchem_kody_odpadow')->truncate();
        DB::table('karchem_klienci')->truncate();

        // 0. Klienci (NIPy do filtrów BDO)
        $oldKlienci = DB::table('mrowisko.karchem_klienci')->get();
        $this->command->info('Migracja '.$oldKlienci->count().' klientów Karchem...');

        foreach ($oldKlienci as $k) {
            DB::table('karchem_klienci')->insert([
                'id'         => $k->id,
                'nip'        => $k->nip,
                'nazwa'      => $k->nazwa ?? null,
                'created_at' => $k->created_at ?? now(),
                'updated_at' => $k->updated_at ?? now(),
            ]);
        }

        // 1. Kody odpadów
        $oldKody = DB::table('mrowisko.karchem_kody_odpadow')->get();
        $this->command->info('Migracja '.$oldKody->count().' kodów odpadów Karchem...');

        foreach ($oldKody as $k) {
            DB::table('karchem_kody_odpadow')->insert([
                'id'         => $k->id,
                'kod'        => $k->kod,
                'created_at' => $k->created_at ?? now(),
                'updated_at' => $k->updated_at ?? now(),
            ]);
        }

        // 2. Stany początkowe
        $oldStany = DB::table('mrowisko.karchem_stany_poczatkowe')->get();
        $this->command->info('Migracja '.$oldStany->count().' stanów początkowych Karchem...');

        foreach ($oldStany as $s) {
            DB::table('karchem_stany_poczatkowe')->insert([
                'id'         => $s->id,
                'rok'        => $s->rok,
                'kod'        => $s->kod,
                'ilosc'      => $s->ilosc,
                'created_at' => $s->created_at ?? now(),
                'updated_at' => $s->updated_at ?? now(),
            ]);
        }

        // 3. Wysyłki
        $oldWysylki = DB::table('mrowisko.karchem_wysylki')->get();
        $this->command->info('Migracja '.$oldWysylki->count().' wysyłek Karchem...');

        foreach ($oldWysylki as $w) {
            DB::table('karchem_wysylki')->insert([
                'id'         => $w->id,
                'data'       => $w->data,
                'kod'        => $w->kod,
                'ilosc'      => $w->ilosc,
                'klient'     => $w->klient,
                'created_at' => $w->created_at ?? now(),
                'updated_at' => $w->updated_at ?? now(),
            ]);
        }

        $this->resetAutoIncrement('karchem_klienci');
        $this->resetAutoIncrement('karchem_kody_odpadow');
        $this->resetAutoIncrement('karchem_stany_poczatkowe');
        $this->resetAutoIncrement('karchem_wysylki');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('✅ Migracja danych Karchem zakończona.');
    }

    private function resetAutoIncrement($table)
    {
        $maxId = DB::table($table)->max('id') ?? 0;
        $nextId = $maxId + 1;
        DB::statement("ALTER TABLE $table AUTO_INCREMENT = $nextId");
    }
}
