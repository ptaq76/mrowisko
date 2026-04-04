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

        $goods = [
            ['id' => 1,  'name' => '2.06'],
            ['id' => 2,  'name' => '1.04'],
            ['id' => 3,  'name' => 'Gilza'],
            ['id' => 4,  'name' => '1.06'],
            ['id' => 5,  'name' => '3.02'],
            ['id' => 6,  'name' => 'Tworzywa'],
            ['id' => 7,  'name' => '3.18'],
            ['id' => 8,  'name' => '1.11'],
            ['id' => 9,  'name' => 'Bibuła'],
            ['id' => 10, 'name' => '3.12'],
            ['id' => 11, 'name' => '2.03'],
            ['id' => 12, 'name' => '2.06 BELKA'],
            ['id' => 13, 'name' => '3.05'],
            ['id' => 14, 'name' => 'Papier silikonowy'],
            ['id' => 15, 'name' => 'Trociny A1'],
        ];

        foreach ($goods as $g) {
            DB::table('ls_goods')->insert([
                'id'         => $g['id'],
                'name'       => $g['name'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::statement('ALTER TABLE ls_goods AUTO_INCREMENT = 16');
        $this->command->info('Towary wysyłkowe (ls_goods) dodane.');
    }
}
