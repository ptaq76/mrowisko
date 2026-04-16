<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Rozpoczynam seedowanie bazy danych...');

        $this->call([
            // Użytkownicy i dane podstawowe
            UserSeeder::class,
            // AdminSeeder::class,

            // klienci
            clientSeeder::class,

            // Pojazdy i kierowcy
            VehicleSeeder::class,
            DriverSeeder::class,
            VehicleSetSeeder::class,
            FuelVehicleSeeder::class,

            // Importerzy i towary LS
            WasteFractionSeeder::class,
            ImporterSeeder::class,
            LsGoodsSeeder::class,
            LsSeeder::class,

            // Szybkie przyciski do zleceń
            OrderQuickButtonSeeder::class,
        ]);

        $this->command->info('✅ Seedowanie zakończone pomyślnie!');
    }
}
