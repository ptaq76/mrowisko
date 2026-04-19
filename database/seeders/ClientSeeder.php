<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    // Klienci z kraju DE (dopasowanie po nazwie - case insensitive)
    private array $germanClients = [
        'DETTELBACH',
        'EILENBURG',
        'EISEN',
        'GLÜCKSTADT',
        'GREIZ',
        'HOHENWESTEDT',
        'KIMBERLY-CLARK',
        'KROSTITZ',
        'LEHNICE',
        'LEIPA',
        'LILLA EDET',
        'SANDERSDORF',
        'SONAE ARAUCO',
        'SPREMBERG',
        'TREBSEN',
    ];

    public function run(): void
    {
        // 1. Wyłączenie kluczy i czyszczenie tabel
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('client_contacts')->truncate();
        DB::table('client_addresses')->truncate();
        DB::table('clients')->truncate();

        // --- MIGRACJA KONTRAHENTÓW ---
        $oldKontrahenci = DB::table('mrowisko.kontrahenci')->get();
        $this->command->info('Migracja '.$oldKontrahenci->count().' kontrahentów...');

        foreach ($oldKontrahenci as $k) {
            // Logika mapowania typu: dostawca/odbiorca -> pickup/sale/both
            $type = 'both';
            if ($k->dostawca == 1 && $k->odbiorca == 0) {
                $type = 'pickup';
            }
            if ($k->dostawca == 0 && $k->odbiorca == 1) {
                $type = 'sale';
            }

            // Sprawdź czy klient jest z Niemiec
            $country = $this->isGermanClient($k->nazwa) ? 'DE' : 'PL';

            DB::table('clients')->insert([
                'id' => $k->id,
                'name' => $k->nazwa,
                'short_name' => $k->skrot,
                'nip' => $k->nip,
                'bdo' => null, // brak w starej bazie
                'country' => $country,
                'type' => $type,
                'street' => $k->adres,
                'postal_code' => $k->kod,
                'city' => $k->miasto,
                'salesman_id' => $k->operator, // mapowanie na ID handlowca
                'is_active' => 1,
                'created_at' => $k->created_at,
                'updated_at' => $k->updated_at,
            ]);
        }

        // --- MIGRACJA ADRESÓW ---
        $oldAdresy = DB::table('mrowisko.kontrahenci_adresy')->get();
        $this->command->info('Migracja '.$oldAdresy->count().' dodatkowych adresów...');

        foreach ($oldAdresy as $a) {
            DB::table('client_addresses')->insert([
                'id' => $a->id,
                'client_id' => $a->id_kontrahenta,
                'city' => $a->miasto,
                'postal_code' => $a->kod,
                'street' => $a->adres,
                'hours' => $a->godziny,
                'notes' => $a->uwagi,
                'distance_km' => $a->dystans,
                'latitude' => $a->latitude,
                'longitude' => $a->longitude,
                'created_at' => $a->created_at,
                'updated_at' => $a->updated_at,
            ]);
        }

        // --- MIGRACJA KONTAKTÓW ---
        $oldKontakty = DB::table('mrowisko.kontrahenci_kontakty')->get();
        $this->command->info('Migracja '.$oldKontakty->count().' kontaktów...');

        foreach ($oldKontakty as $c) {
            // Mapowanie działów na ENUM: awizacje, faktury, handlowe
            // Zakładam mapowanie na podstawie nazw działów ze starej bazy
            $category = 'handlowe'; // default
            $dzial = mb_strtolower($c->dzial);

            if (str_contains($dzial, 'awiz') || str_contains($dzial, 'logist')) {
                $category = 'awizacje';
            }
            if (str_contains($dzial, 'fakt') || str_contains($dzial, 'księg')) {
                $category = 'faktury';
            }
            if (str_contains($dzial, 'handl') || str_contains($dzial, 'sprzed')) {
                $category = 'handlowe';
            }

            DB::table('client_contacts')->insert([
                'id' => $c->id,
                'client_id' => $c->id_kontrahenta,
                'category' => $category,
                'name' => $c->osoba,
                'email' => $c->mail,
                'phone' => $c->telefon,
                'created_at' => $c->timestamp,
                'updated_at' => $c->timestamp,
            ]);
        }

        // 2. Resetowanie liczników AUTO_INCREMENT
        $this->resetAutoIncrement('clients');
        $this->resetAutoIncrement('client_addresses');
        $this->resetAutoIncrement('client_contacts');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('✅ Migracja klientów zakończona pomyślnie.');
    }

    /**
     * Sprawdza czy nazwa klienta pasuje do listy niemieckich klientów
     */
    private function isGermanClient(string $name): bool
    {
        $nameUpper = mb_strtoupper(trim($name));
        
        foreach ($this->germanClients as $germanName) {
            if (str_contains($nameUpper, $germanName)) {
                return true;
            }
        }
        
        return false;
    }

    private function resetAutoIncrement($table)
    {
        $maxId = DB::table($table)->max('id') ?? 0;
        $nextId = $maxId + 1;
        DB::statement("ALTER TABLE $table AUTO_INCREMENT = $nextId");
    }
}