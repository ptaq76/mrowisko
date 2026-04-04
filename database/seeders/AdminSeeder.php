<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('users')->where('module', 'admin')->exists()) {
            $this->command->info('Admin już istnieje – pomijam.');
            return;
        }

        DB::table('users')->insert([
            'name'       => 'Administrator',
            'login'      => 'admin',
            'password'   => Hash::make('Admin1234!'),
            'module'     => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Admin utworzony: login: admin / hasło: Admin1234!');
        $this->command->warn('Zmień hasło admina po pierwszym logowaniu!');
    }
}