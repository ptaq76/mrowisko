<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('drivers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $drivers = [
            ['id' => 1, 'user_id' => 9,    'name' => 'Sebastian', 'full_name' => 'Sebastian Pawłowski',  'firma' => 'Ewrant',     'color' => '#987654', 'phone' => '726 427 271', 'tractor_id' => 1,  'trailer_id' => 11,   'avatar' => 'Sebastian.png'],
            ['id' => 2, 'user_id' => 10,   'name' => 'Łukasz',    'full_name' => 'Łukasz Piątek',         'firma' => 'Ewrant',     'color' => '#5F9EA0', 'phone' => '531 783 316', 'tractor_id' => 3,  'trailer_id' => 9,    'avatar' => 'Łukasz.png'],
            ['id' => 3, 'user_id' => 11,   'name' => 'Vasyl',     'full_name' => 'Vasyl Glushko',         'firma' => 'Ewrant',     'color' => '#0fc0fc', 'phone' => '579 145 400', 'tractor_id' => 2,  'trailer_id' => 8,    'avatar' => 'Vasyl.png'],
            ['id' => 4, 'user_id' => 12,   'name' => 'Recykler',  'full_name' => 'Kierowca Recykler',     'firma' => 'Recykler',   'color' => '#EC6A77', 'phone' => '721 843 598', 'tractor_id' => 14, 'trailer_id' => null, 'avatar' => 'Recykler.png'],
            ['id' => 5, 'user_id' => 13,   'name' => 'Tomek',     'full_name' => 'Tomasz Wytrwa',         'firma' => 'Ewrant',     'color' => '#E8F48C', 'phone' => '504 915 988', 'tractor_id' => 4,  'trailer_id' => 5,    'avatar' => 'Tomek.png'],
            ['id' => 6, 'user_id' => 7,    'name' => 'Tadeusz',   'full_name' => 'Tadeusz Miaczkowski',   'firma' => 'Ewrant',     'color' => '#44e60a', 'phone' => '786 994 304', 'tractor_id' => 6,  'trailer_id' => null, 'avatar' => 'Tadeusz.png'],
            ['id' => 7, 'user_id' => null, 'name' => 'Zewnętrzny','full_name' => 'Pan Zewnętrzny',        'firma' => 'Zewnętrzny', 'color' => '#ffb347', 'phone' => null,          'tractor_id' => 12, 'trailer_id' => 13,   'avatar' => 'Zewnetrzny.png'],
        ];

        foreach ($drivers as $d) {
            DB::table('drivers')->insert([
                'id' => $d['id'],
                'user_id' => $d['user_id'],
                'name' => $d['name'],
                'full_name' => $d['full_name'],
                'firma' => $d['firma'],
                'color' => $d['color'],
                'phone' => $d['phone'],
                'tractor_id' => $d['tractor_id'],
                'trailer_id' => $d['trailer_id'],
                'avatar' => $d['avatar'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::statement('ALTER TABLE drivers AUTO_INCREMENT = 8');
        $this->command->info('Kierowcy dodani. Wgraj awatary do public/storage/drivers/');
    }
}
