<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LsGoodsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('ls_goods')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Pobierz towary ze starej bazy
        $oldGoods = DB::connection('mrowisko')
            ->table('towary_wysylki')
            ->orderBy('id')
            ->get();

        $count = 0;
        $maxId = 0;

        foreach ($oldGoods as $g) {
            DB::table('ls_goods')->insert([
                'id' => $g->id,
                'name' => $g->nazwa,
                'is_active' => true,
                'created_at' => $g->created_at ?? now(),
                'updated_at' => $g->updated_at ?? now(),
            ]);
            $count++;
            $maxId = max($maxId, $g->id);
        }

        DB::statement('ALTER TABLE ls_goods AUTO_INCREMENT = ' . ($maxId + 1));
        $this->command->info("Towary wysyłkowe (ls_goods) dodane: {$count} rekordów.");
    }
}