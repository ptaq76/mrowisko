<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContainerSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('order_containers')->truncate();
        DB::table('container_stock')->truncate();
        DB::table('containers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $containers = [
            ['name' => 'KONTENER CZARNY',        'tare_kg' => 2.40, 'type' => 'zwykly',        'qty' => 9],
            ['name' => 'KONTENER ZÓŁTY',         'tare_kg' => 2.92, 'type' => 'zwykly',        'qty' => 1],
            ['name' => 'KONTENER ZIELONY',       'tare_kg' => 4.10, 'type' => 'zwykly',        'qty' => 1],
            ['name' => 'PRASA NIEBIESKA',        'tare_kg' => 3.42, 'type' => 'prasokontener', 'qty' => 4],
            ['name' => 'KONTENER MAŁY',          'tare_kg' => 2.20, 'type' => 'zwykly',        'qty' => 1],
            ['name' => 'MONOBLOK',               'tare_kg' => 4.62, 'type' => 'zwykly',        'qty' => 1],
            ['name' => 'BESTSELLER',             'tare_kg' => 4.12, 'type' => 'zwykly',        'qty' => 1],
            ['name' => 'DIRKS ZIELONY',          'tare_kg' => 3.98, 'type' => 'zwykly',        'qty' => 4],
            ['name' => 'REMONDIS (SZARY)',       'tare_kg' => 4.02, 'type' => 'zwykly',        'qty' => 1],
            ['name' => 'ZENTEX CZARNY',          'tare_kg' => 4.30, 'type' => 'zwykly',        'qty' => 1],
            ['name' => 'PIOTR ZIELONY',          'tare_kg' => 2.54, 'type' => 'zwykly',        'qty' => 1],
            ['name' => 'KONTENER MAŁY ZIELONY',  'tare_kg' => 1.80, 'type' => 'zwykly',        'qty' => 1],
        ];

        $now = now();

        foreach ($containers as $c) {
            $id = DB::table('containers')->insertGetId([
                'name'       => $c['name'],
                'tare_kg'    => $c['tare_kg'],
                'type'       => $c['type'],
                'is_active'  => 1,
                'notes'      => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('container_stock')->insert([
                'container_id' => $id,
                'client_id'    => null,
                'quantity'     => $c['qty'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }
}
