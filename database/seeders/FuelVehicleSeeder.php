<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelVehicleSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('fuel_vehicles')->truncate();
        DB::table('fuel_vehicle_groups')->truncate();

        // Grupy
        DB::table('fuel_vehicle_groups')->insert([
            ['id' => 1, 'nazwa' => 'Plac',         'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nazwa' => 'PlacTransport', 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nazwa' => 'Audi',          'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nazwa' => 'Prywatne',      'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nazwa' => 'TIR',           'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nazwa' => 'System',        'active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Pojazdy
        DB::table('fuel_vehicles')->insert([
            ['id' => 1, 'nazwa' => 'DOSTAWA',             'grupa_id' => 6, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nazwa' => 'KOMATSU (Stara)',      'grupa_id' => 1, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nazwa' => 'KOMATSU 80 (Nowa)',    'grupa_id' => 1, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nazwa' => 'KOPARKA',              'grupa_id' => 1, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nazwa' => 'SZTAPLARKA TOYOTA',    'grupa_id' => 1, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nazwa' => 'HAKOWIEC',             'grupa_id' => 2, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'nazwa' => 'TADKOWÓZ',             'grupa_id' => 2, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'nazwa' => 'AUDI SQ7',             'grupa_id' => 3, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'nazwa' => 'TOYOTA',               'grupa_id' => 2, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'nazwa' => 'AUDI Z3ANTRA',         'grupa_id' => 3, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'nazwa' => 'MIACZKOWSKI TADEUSZ',  'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'nazwa' => 'NISSAN',               'grupa_id' => 2, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'nazwa' => 'MAUZER',               'grupa_id' => 1, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'nazwa' => 'MURAWSKI ADAM',        'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'nazwa' => 'KAZIK',                'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'nazwa' => 'PIATEK LUKASZ',        'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'nazwa' => 'ROSZCZYK MACIEJ',      'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'nazwa' => 'WYTRWA TOMASZ',        'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'nazwa' => 'DAF PNT81294',         'grupa_id' => 5, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'nazwa' => 'MAN WGM0958F',         'grupa_id' => 5, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'nazwa' => 'DAF WGM2624C',         'grupa_id' => 5, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'nazwa' => 'LEWANDOWSKI WALDEK',   'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'nazwa' => 'INWENTARYZACJA',       'grupa_id' => 6, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'nazwa' => 'KARDANSKI PIOTR',      'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'nazwa' => 'VASYL',                'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'nazwa' => 'BOCIAN',               'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'nazwa' => 'PERLOWSKI JAKUB',      'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'nazwa' => 'SENNEBOGEN',           'grupa_id' => 1, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'nazwa' => 'SYLWESTER',            'grupa_id' => 4, 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->resetAutoIncrement('fuel_vehicle_groups');
        $this->resetAutoIncrement('fuel_vehicles');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function resetAutoIncrement($table)
    {
        $maxId = DB::table($table)->max('id') ?? 0;
        $nextId = $maxId + 1;
        DB::statement("ALTER TABLE $table AUTO_INCREMENT = $nextId");
    }
}
