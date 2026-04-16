<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PojazdyTerminySeeder extends Seeder
{
    public function run(): void
    {
        $pojazdy = [
            ['nr_rej' => 'WGM2624C',          'rodzaj' => 'Ciągnik',                'marka' => 'DAF',            'wlasciciel' => 'ING LEASING', 'vin' => 'XLRTEH4300G354253', 'rok_prod' => 2021, 'opis' => ''],
            ['nr_rej' => 'PNT81294',           'rodzaj' => 'Ciągnik',                'marka' => 'DAF',            'wlasciciel' => 'MLEASING',    'vin' => 'XLRTEH4300G333749', 'rok_prod' => 2020, 'opis' => ''],
            ['nr_rej' => 'WGM0958F',           'rodzaj' => 'Ciągnik',                'marka' => 'MAN',            'wlasciciel' => 'ING LEASING', 'vin' => 'WMA06KZZ2NM892315', 'rok_prod' => 2021, 'opis' => ''],
            ['nr_rej' => 'WGM3595C',           'rodzaj' => 'Ciągnik hakowiec',       'marka' => 'SCANIA',         'wlasciciel' => 'ING LEASING', 'vin' => 'YS2P6X20002182354', 'rok_prod' => 2021, 'opis' => ''],
            ['nr_rej' => 'ZS438MG',            'rodzaj' => 'Ciężarówka',             'marka' => 'Renault Midlum', 'wlasciciel' => 'EWRANT',      'vin' => 'VF640J566HB007628', 'rok_prod' => 2017, 'opis' => ''],
            ['nr_rej' => 'WGM2126P',           'rodzaj' => 'Naczepa',                'marka' => 'Schwarzmueller', 'wlasciciel' => 'ING LEASING', 'vin' => 'VAVJS1339MD468354', 'rok_prod' => 2021, 'opis' => ''],
            ['nr_rej' => 'PNTKY66',            'rodzaj' => 'Naczepa',                'marka' => 'Koegel',         'wlasciciel' => 'MLEASING',    'vin' => 'WK0S0002400258458', 'rok_prod' => 2020, 'opis' => ''],
            ['nr_rej' => 'WGM5564P',           'rodzaj' => 'Naczepa',                'marka' => 'Koegel',         'wlasciciel' => 'ING LEASING', 'vin' => 'WK0S0002400269951', 'rok_prod' => 2021, 'opis' => ''],
            ['nr_rej' => 'WGM8340P',           'rodzaj' => 'Naczepa ruchoma podłoga','marka' => 'Schwarzmueller', 'wlasciciel' => 'ING LEASING', 'vin' => 'VAVJS1339NH496432', 'rok_prod' => 2022, 'opis' => ''],
            ['nr_rej' => 'WGM4617P',           'rodzaj' => 'Naczepa ruchoma podłoga','marka' => 'Schwarzmueller', 'wlasciciel' => 'ING LEASING', 'vin' => 'VAVJS1339MH479982', 'rok_prod' => 2021, 'opis' => ''],
            ['nr_rej' => 'WGM2125P',           'rodzaj' => 'Przyczepa hakowa',       'marka' => 'Hueffermann',    'wlasciciel' => 'ING LEASING', 'vin' => 'W09HAR1870LH15A23', 'rok_prod' => 2020, 'opis' => ''],
            ['nr_rej' => 'ZS098MV',            'rodzaj' => 'VAN',                    'marka' => 'Toyota',         'wlasciciel' => 'JOLANT',      'vin' => 'YAREFYHZRGJ940107', 'rok_prod' => 2020, 'opis' => ''],
            ['nr_rej' => 'ZS161PU',            'rodzaj' => 'VAN',                    'marka' => 'Nissan',         'wlasciciel' => 'ANTRA',       'vin' => 'VSKHBAM20U0029151', 'rok_prod' => 2011, 'opis' => ''],
            ['nr_rej' => 'Licencja przewoźnika','rodzaj' => 'Licencja',               'marka' => 'Ewrant',         'wlasciciel' => 'Ewrant',      'vin' => null,                'rok_prod' => null, 'opis' => ''],
            ['nr_rej' => 'Z1ANT16',            'rodzaj' => 'osobowy',                'marka' => 'AUDI SQ7',       'wlasciciel' => 'Ewrant',      'vin' => 'WAUZZZ4M5MD008282', 'rok_prod' => 2020, 'opis' => 'Radek'],
            ['nr_rej' => 'Z5 ANTRA',           'rodzaj' => 'osobowy',                'marka' => 'AUDI SQ5',       'wlasciciel' => 'ANTRA',       'vin' => 'WAUZZZGUXS2019685', 'rok_prod' => 2025, 'opis' => 'Filip'],
            ['nr_rej' => 'PY5179F',            'rodzaj' => 'osobowy',                'marka' => 'AUDI R8',        'wlasciciel' => 'ANTRA',       'vin' => 'WUAZZZFX5P7902136', 'rok_prod' => 2024, 'opis' => 'Radek R8'],
            ['nr_rej' => 'ZS778TM',            'rodzaj' => 'osobowy',                'marka' => 'AUDI A5',        'wlasciciel' => 'ANTRA',       'vin' => 'WAUZZZF54RA127424', 'rok_prod' => 2024, 'opis' => 'Jola'],
            ['nr_rej' => 'PY0122F',            'rodzaj' => 'osobowy',                'marka' => 'AUDI SQ6',       'wlasciciel' => 'ANTRA',       'vin' => 'WAUZZZGF6SA017116', 'rok_prod' => 2024, 'opis' => 'Przemek'],
        ];

        foreach ($pojazdy as $p) {
            DB::table('pojazdy_terminy')->updateOrInsert(
                ['nr_rej' => $p['nr_rej']],
                array_merge($p, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
