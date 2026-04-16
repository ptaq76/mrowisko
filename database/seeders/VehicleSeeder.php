<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('vehicles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $vehicles = [
            ['id' => 1,  'plate' => 'PNT81294',    'type' => 'ciągnik', 'subtype' => null,          'tare_kg' => 0, 'brand' => null],
            ['id' => 2,  'plate' => 'WGM0958F',    'type' => 'ciągnik', 'subtype' => null,          'tare_kg' => 0, 'brand' => null],
            ['id' => 3,  'plate' => 'WGM2624C',    'type' => 'ciągnik', 'subtype' => null,          'tare_kg' => 0, 'brand' => null],
            ['id' => 4,  'plate' => 'WGM3595C',    'type' => 'ciągnik', 'subtype' => 'hakowiec',    'tare_kg' => 0, 'brand' => null],
            ['id' => 5,  'plate' => 'WGM2125P',    'type' => 'naczepa', 'subtype' => 'hakowiec',    'tare_kg' => 0, 'brand' => null],
            ['id' => 6,  'plate' => 'ZS438MG',     'type' => 'ciągnik', 'subtype' => null,          'tare_kg' => 0, 'brand' => null],
            ['id' => 7,  'plate' => 'WGM4617P',    'type' => 'naczepa', 'subtype' => 'walking_floor', 'tare_kg' => 0, 'brand' => null],
            ['id' => 8,  'plate' => 'WGM5564P',    'type' => 'naczepa', 'subtype' => 'firana',      'tare_kg' => 0, 'brand' => null],
            ['id' => 9,  'plate' => 'WGM8340P',    'type' => 'naczepa', 'subtype' => 'walking_floor', 'tare_kg' => 0, 'brand' => null],
            ['id' => 10, 'plate' => 'WGM2126P',    'type' => 'naczepa', 'subtype' => 'firana',      'tare_kg' => 0, 'brand' => null],
            ['id' => 11, 'plate' => 'PNTKY66',     'type' => 'naczepa', 'subtype' => 'firana',      'tare_kg' => 0, 'brand' => null],
            ['id' => 12, 'plate' => 'ZEWN.CIAGNIK', 'type' => 'ciągnik', 'subtype' => null,          'tare_kg' => 0, 'brand' => 'Zewnętrzny'],
            ['id' => 13, 'plate' => 'ZEWN.NACZEPA', 'type' => 'naczepa', 'subtype' => null,          'tare_kg' => 0, 'brand' => 'Zewnętrzna'],
            ['id' => 14, 'plate' => 'ZS992RM',     'type' => 'ciągnik', 'subtype' => null,          'tare_kg' => 0, 'brand' => null],
            ['id' => 15, 'plate' => 'GCH5U46',     'type' => 'naczepa', 'subtype' => null,          'tare_kg' => 0, 'brand' => null],
        ];

        foreach ($vehicles as $v) {
            DB::table('vehicles')->insert([
                'id' => $v['id'],
                'plate' => $v['plate'],
                'type' => $v['type'],
                'subtype' => $v['subtype'],
                'tare_kg' => $v['tare_kg'],
                'brand' => $v['brand'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ustaw AUTO_INCREMENT
        DB::statement('ALTER TABLE vehicles AUTO_INCREMENT = 16');

        $this->command->info('Pojazdy zostały dodane. Uzupełnij tare_kg dla każdego pojazdu!');
    }
}
