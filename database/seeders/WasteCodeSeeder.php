<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WasteCodeSeeder extends Seeder
{
    /**
     * Stała lista kodów odpadów (wg katalogu odpadów).
     * Kody przechowywane bez spacji (np. "150101"), żeby pasowały bezpośrednio
     * do wartości w CSV. Wyświetlanie ze spacjami można zrobić w widoku.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('waste_codes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $codes = [
            '150101', '150102', '030308', '150103', '070213', '150203',
            '191207', '150106', '191201', '160214', '070214', '150105',
            '050102', '170201', '170203', '120105',
        ];

        $now = now();
        $rows = [];
        foreach ($codes as $code) {
            $rows[] = [
                'code' => $code,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('waste_codes')->insert($rows);

        $this->command->info('Wpisano '.count($rows).' kodów odpadów.');
    }
}
