<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            VehicleSeeder::class,
            ImporterSeeder::class,
            LsGoodsSeeder::class,
            DriverSeeder::class,
            VehicleSetSeeder::class,
            OrderQuickButtonSeeder::class,
        ]);
    }
}
