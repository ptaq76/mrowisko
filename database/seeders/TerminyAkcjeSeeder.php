<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TerminyAkcjeSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('pojazdy_terminy_akcje')->truncate();

        $terminy = DB::connection('mrowisko')->table('terminy')->where('status', 0)->get();
        $this->command->info("Migracja " . $terminy->count() . " terminów...");

        $imported = 0;
        foreach ($terminy as $t) {
            $pojazd = DB::table('pojazdy_terminy')->where('id', $t->pojazdy_terminy_id)->first();
            if (!$pojazd) {
                $this->command->warn("Pominięto termin id={$t->id} – brak pojazdu id={$t->pojazdy_terminy_id}");
                continue;
            }

            DB::table('pojazdy_terminy_akcje')->insert([
                'pojazd_id'      => $t->pojazdy_terminy_id,
                'action_type'    => $t->nazwa,
                'completed_date' => $t->status == 1 ? $t->updated_at : null,
                'deadline_date'  => $t->data,
                'notes'          => null,
                'created_at'     => $t->created_at,
                'updated_at'     => $t->updated_at,
            ]);
            $imported++;
        }

        $this->resetAutoIncrement('pojazdy_terminy_akcje');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info("✅ Zaimportowano {$imported} terminów.");
    }

    private function resetAutoIncrement(string $table): void
    {
        $maxId = DB::table($table)->max('id') ?? 0;
        DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = " . ($maxId + 1));
    }
}