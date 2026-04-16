<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderQuickButtonSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('order_quick_buttons')->truncate();

        $goods = [
            'KARTON CZYSTY', 'KARTON BRUDNY', '3.05', '2.06',
            'TWORZYWA', 'FOLIA', 'GILZA', 'GAZETA',
        ];

        $notes = [
            'Zabrać palety jednorazowe',
            'Zabrać palety EURO',
            'Odjazd z bazy',
            'Drugi kurs',
        ];

        $sort = 1;
        foreach ($goods as $label) {
            DB::table('order_quick_buttons')->insert([
                'label' => $label,
                'type' => 'goods',
                'sort' => $sort++,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $sort = 1;
        foreach ($notes as $label) {
            DB::table('order_quick_buttons')->insert([
                'label' => $label,
                'type' => 'notes',
                'sort' => $sort++,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Przyciski szybkie dodane.');
    }
}
