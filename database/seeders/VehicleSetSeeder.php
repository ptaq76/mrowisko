<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSetSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('vehicle_sets')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Mapowanie nr_rej → id z tabeli vehicles
        $plates = DB::table('vehicles')->pluck('id', 'plate');

        $sets = [
            ['label' => 'PNT81294 / WGM5564P',           'tractor' => 'PNT81294',    'trailer' => 'WGM5564P',    'tare' => 14.000],
            ['label' => 'PNT81294 / PNTKY66',             'tractor' => 'PNT81294',    'trailer' => 'PNTKY66',     'tare' => 14.100],
            ['label' => 'PNT81294 / WGM2126P',            'tractor' => 'PNT81294',    'trailer' => 'WGM2126P',    'tare' => 14.260],
            ['label' => 'ZS438MG',                        'tractor' => 'ZS438MG',     'trailer' => null,          'tare' => 6.500],
            ['label' => 'WGM0958F / WGM5564P',            'tractor' => 'WGM0958F',    'trailer' => 'WGM5564P',    'tare' => 13.900],
            ['label' => 'WGM0958F / PNTKY66',             'tractor' => 'WGM0958F',    'trailer' => 'PNTKY66',     'tare' => 14.220],
            ['label' => 'WGM2624C / WGM5564P',            'tractor' => 'WGM2624C',    'trailer' => 'WGM5564P',    'tare' => 14.800],
            ['label' => 'WGM2624C / WGM2126P',            'tractor' => 'WGM2624C',    'trailer' => 'WGM2126P',    'tare' => 13.940],
            ['label' => 'WGM2624C / WGM4617P',            'tractor' => 'WGM2624C',    'trailer' => 'WGM4617P',    'tare' => 16.060],
            ['label' => 'WGM3595C / kontener czarny',     'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 14.100],
            ['label' => 'WGM3595C / KONTENER ŻÓŁTY',      'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 14.620],
            ['label' => 'WGM3595C / kontener zielony',    'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 15.800],
            ['label' => 'WGM3595C / prasa niebieska',     'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 15.120],
            ['label' => 'WGM3595C / KONTENER MAŁY',       'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 13.900],
            ['label' => 'WGM3595C / monoblok',            'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 16.320],
            ['label' => 'WGM3595C / bestseller',          'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 15.820],
            ['label' => 'WGM2125P / kontener czarny',     'tractor' => 'WGM2125P',    'trailer' => null,          'tare' => 6.100],
            ['label' => 'WGM2125P / kontener zielony',    'tractor' => 'WGM2125P',    'trailer' => null,          'tare' => 7.660],
            ['label' => 'WGM2125P / prasa niebieska',     'tractor' => 'WGM2125P',    'trailer' => null,          'tare' => 7.100],
            ['label' => 'WGM2125P / bestseller',          'tractor' => 'WGM2125P',    'trailer' => null,          'tare' => 7.800],
            ['label' => 'ZS992RM / WGM5564P',             'tractor' => 'ZS992RM',     'trailer' => 'WGM5564P',    'tare' => 14.140],
            ['label' => 'WGM0958F / WGM4617P',            'tractor' => 'WGM0958F',    'trailer' => 'WGM4617P',    'tare' => 15.620],
            ['label' => 'WGM3595C / DIRKS ZIELONY',       'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 15.680],
            ['label' => 'WGM2125P / DIRKS ZIELONY',       'tractor' => 'WGM2125P',    'trailer' => null,          'tare' => 7.600],
            ['label' => 'ZS992RM / WGM2126P',             'tractor' => 'ZS992RM',     'trailer' => 'WGM2126P',    'tare' => 13.800],
            ['label' => 'WGM3595C / REMONDIS(SZARY)',     'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 15.720],
            ['label' => 'WGM2125P / REMONDIS(SZARY)',     'tractor' => 'WGM2125P',    'trailer' => null,          'tare' => 7.120],
            ['label' => 'WGM0958F / WGM8340P',            'tractor' => 'WGM0958F',    'trailer' => 'WGM8340P',    'tare' => 15.180],
            ['label' => 'WGM2624C / PNTKY66',             'tractor' => 'WGM2624C',    'trailer' => 'PNTKY66',     'tare' => 14.720],
            ['label' => 'WGM2624C / WGM8340P',            'tractor' => 'WGM2624C',    'trailer' => 'WGM8340P',    'tare' => 15.800],
            ['label' => 'WGM0958F / WGM2126P',            'tractor' => 'WGM0958F',    'trailer' => 'WGM2126P',    'tare' => 13.500],
            ['label' => 'WGM3595C / ZENTEX CZARNY',       'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 16.000],
            ['label' => 'WGM3595C / PIOTR ZIELONY',       'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 14.240],
            ['label' => 'WGM2125P / PIOTR ZIELONY',       'tractor' => 'WGM2125P',    'trailer' => null,          'tare' => 6.240],
            ['label' => 'WGM3595C / KONTENER MAŁY ZIELONY', 'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 13.500],
            ['label' => 'WGM3595C',                       'tractor' => 'WGM3595C',    'trailer' => null,          'tare' => 11.700],
            ['label' => 'WGM2624C / GCH5U46',             'tractor' => 'WGM2624C',    'trailer' => 'GCH5U46',     'tare' => 16.520],
        ];

        foreach ($sets as $s) {
            DB::table('vehicle_sets')->insert([
                'label' => $s['label'],
                'tractor_id' => $plates[$s['tractor']] ?? null,
                'trailer_id' => $s['trailer'] ? ($plates[$s['trailer']] ?? null) : null,
                'tare_kg' => $s['tare'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Zestawy pojazdów z tarami dodane ('.count($sets).' zestawów).');
    }
}
