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

            // towary

            WasteFractionSeeder::class,

            // Kontenery
            ContainerSeeder::class,

            // Pojazdy i kierowcy
            VehicleSeeder::class,
            DriverSeeder::class,
            VehicleSetSeeder::class,
            FuelVehicleSeeder::class,
            FuelTransactionSeeder::class,
            PojazdyTerminySeeder::class,
            TerminyAkcjeSeeder::class,

            // Importerzy i towary LS
            ImporterSeeder::class,
            LsGoodsSeeder::class,
            LsSeeder::class,

            MigrateOrdersSeeder::class,

            // Produkcja (wpisy magazynowe origin=production)
            ProductionSeeder::class,

            // Karchem
            KarchemSeeder::class,
            BdoSeeder::class,

            // Szybkie przyciski do zleceń
            OrderQuickButtonSeeder::class,

            // Zadania (z starej bazy: zadania_kierowcy + zadania_plac)
            ZadaniaSeeder::class,
        ]);

        $this->command->info('✅ Seedowanie zakończone pomyślnie!');
    }
}
