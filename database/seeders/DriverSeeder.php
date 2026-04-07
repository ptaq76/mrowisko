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

        // Mapowanie: stare pojazdy id → nowe vehicles id (te same ID)
        // user_id ze starej bazy: Sebastian=9→10, Łukasz=10→11, Vasyl=11→12,
        // Recykler=12→13, Tomek=13→14, Tadeusz=7→8 (po imporcie users)
        $drivers = [
            [
                'id'        => 1,
                'user_id'   => 9,  // Sebastian (login: Seba)
                'name'      => 'Sebastian',
                'full_name' => 'Sebastian Pawłowski',
                'color'     => '#987654',
                'phone'     => '726 427 271',
                'tractor_id'=> 1,
                'trailer_id'=> 11,
            ],
            [
                'id'        => 2,
                'user_id'   => 10,  // Łukasz
                'name'      => 'Łukasz',
                'full_name' => 'Łukasz Piątek',
                'color'     => '#5F9EA0',
                'phone'     => '531 783 316',
                'tractor_id'=> 3,
                'trailer_id'=> 9,
            ],
            [
                'id'        => 3,
                'user_id'   => 11,  // Vasyl
                'name'      => 'Vasyl',
                'full_name' => 'Vasyl Glushko',
                'color'     => '#0fc0fc',
                'phone'     => '579 145 400',
                'tractor_id'=> 2,
                'trailer_id'=> 8,
            ],
            [
                'id'        => 4,
                'user_id'   => 12,  // Karol (Recykler)
                'name'      => 'Recykler',
                'full_name' => 'Kierowca Recykler',
                'color'     => '#EC6A77',
                'phone'     => '721 843 598',
                'tractor_id'=> 14,
                'trailer_id'=> null,
            ],
            [
                'id'        => 5,
                'user_id'   => 13,  // Tomek (hakowiec)
                'name'      => 'Tomek',
                'full_name' => 'Tomasz Wytrwa',
                'color'     => '#E8F48C',
                'phone'     => '504 915 988',
                'tractor_id'=> 4,
                'trailer_id'=> 5,
            ],
            [
                'id'        => 6,
                'user_id'   => 7,   // Tadek (plac)
                'name'      => 'Tadeusz',
                'full_name' => 'Tadeusz Miaczkowski',
                'color'     => '#44e60a',
                'phone'     => '786 994 304',
                'tractor_id'=> 6,
                'trailer_id'=> null,
            ],
            [
                'id'        => 7,
                'user_id'   => null,
                'name'      => 'Zewnętrzny',
                'full_name' => 'Pan Zewnętrzny',
                'color'     => '#ffb347',
                'phone'     => null,
                'tractor_id'=> 12,
                'trailer_id'=> 13,
            ],
        ];

        foreach ($drivers as $d) {
            DB::table('drivers')->insert([
                'id'         => $d['id'],
                'user_id'    => $d['user_id'],
                'name'       => $d['name'],
                'full_name'  => $d['full_name'],
                'color'      => $d['color'],
                'phone'      => $d['phone'],
                'tractor_id' => $d['tractor_id'],
                'trailer_id' => $d['trailer_id'],
                'avatar'     => null,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::statement('ALTER TABLE drivers AUTO_INCREMENT = 8');
        $this->command->info('Kierowcy dodani. Wgraj awatary do public/storage/drivers/');
    }
}
