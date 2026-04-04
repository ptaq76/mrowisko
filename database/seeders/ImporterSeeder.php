<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImporterSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('importers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $importers = [
            ['id' => 1,  'name' => 'Recyclog',     'country' => 'DE'],
            ['id' => 2,  'name' => 'DS Smith',      'country' => 'DE'],
            ['id' => 3,  'name' => 'EkoRecykling',  'country' => 'PL'],
            ['id' => 4,  'name' => 'Recon-T',       'country' => 'DE'],
            ['id' => 5,  'name' => 'Melosch',       'country' => 'DE'],
            ['id' => 6,  'name' => 'DEA',           'country' => 'PL'],
            ['id' => 7,  'name' => 'TM Recykling',  'country' => 'DE'],
            ['id' => 8,  'name' => 'EkoTrade',      'country' => 'PL'],
            ['id' => 9,  'name' => 'RLG',           'country' => 'DE'],
            ['id' => 10, 'name' => 'Sonae Arauco',  'country' => 'DE'],
        ];

        foreach ($importers as $i) {
            DB::table('importers')->insert([
                'id'         => $i['id'],
                'name'       => $i['name'],
                'country'    => $i['country'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::statement('ALTER TABLE importers AUTO_INCREMENT = 11');

        $this->command->info('Importerzy zostali dodani.');
    }
}
