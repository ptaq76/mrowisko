<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Mapowanie role_id → module
        $roleMap = [
            1 => 'admin',
            2 => 'biuro',
            3 => 'plac',
            4 => 'kierowca',
            5 => 'hakowiec',
            6 => 'handlowiec',
            7 => 'czarnypan',
            8 => 'karchem',
        ];

        // 1. Wyłączamy sprawdzanie kluczy obcych
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // 2. Czyścimy obecną tabelę users w mrowisko.local
        DB::table('users')->truncate();

        // 3. Pobieramy WSZYSTKIE dane z tabeli users w bazie 'mrowisko'
        $oldUsers = DB::table('mrowisko.users')->get();

        $this->command->info('Pobrano '.$oldUsers->count()." użytkowników z bazy 'mrowisko'.");

        // 4. Przegrywamy dane z mapowaniem
        $skipped = 0;
        foreach ($oldUsers as $user) {
            // Sprawdź czy role_id istnieje w mapowaniu
            if (! isset($roleMap[$user->role_id])) {
                $this->command->warn("Pominięto użytkownika {$user->username} (ID: {$user->id}) - nieznana role_id: {$user->role_id}");
                $skipped++;

                continue;
            }

            // Mapuj dane ze starej struktury na nową
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'login' => $user->username,  // username → login
                'password' => $user->password,
                'module' => $roleMap[$user->role_id],  // role_id → module
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];

            DB::table('users')->insert($userData);
        }

        // 5. Resetujemy licznik AUTO_INCREMENT
        $maxId = DB::table('users')->max('id') ?? 0;
        $nextId = $maxId + 1;
        DB::statement("ALTER TABLE users AUTO_INCREMENT = $nextId");

        // 6. Włączamy sprawdzanie kluczy obcych
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $imported = $oldUsers->count() - $skipped;
        $this->command->info("✅ Zaimportowano: {$imported} użytkowników");
        if ($skipped > 0) {
            $this->command->warn("⚠️  Pominięto: {$skipped} użytkowników (brak mapowania roli)");
        }
        $this->command->info("Tabela 'users' została pomyślnie zsynchronizowana.");
    }
}
