<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PojazdyTerminySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('pojazdy_terminy')->truncate();

        $pojazdy = DB::table('mrowisko.pojazdy_terminy')->get();
        $this->command->info("Migracja " . $pojazdy->count() . " pojazdów...");

        foreach ($pojazdy as $p) {
            DB::table('pojazdy_terminy')->insert([
                'id'          => $p->id,
                'nr_rej'      => $p->nr_rej,
                'rodzaj'      => $p->rodzaj,
                'marka'       => $p->marka,
                'wlasciciel'  => $p->wlasciciel,
                'vin'         => $p->vin ?: null,
                'rok_prod'    => $p->rok_prod ?: null,
                'opis'        => $p->opis ?: null,
                'created_at'  => $p->created_at,
                'updated_at'  => $p->updated_at,
            ]);
        }

        $this->resetAutoIncrement('pojazdy_terminy');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info("✅ Zaimportowano " . $pojazdy->count() . " pojazdów.");
    }

    private function resetAutoIncrement(string $table): void
    {
        $maxId = DB::table($table)->max('id') ?? 0;
        DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = " . ($maxId + 1));
    }
}
