<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BdoSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('bdo_karty')->truncate();
        DB::table('bdo_karty_przekazujacy')->truncate();

        $this->migrateTable('bdo_karty', hasEwrant: true);
        $this->migrateTable('bdo_karty_przekazujacy', hasEwrant: false);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('✅ Migracja BDO zakończona.');
    }

    private function migrateTable(string $table, bool $hasEwrant): void
    {
        $oldRows = DB::connection('mrowisko')->table($table)->orderBy('id')->get();
        $this->command->info("Migracja {$oldRows->count()} rekordów {$table}...");

        $rows = $oldRows->map(function ($r) use ($hasEwrant) {
            $row = [
                'kpo_id'                                   => $r->kpo_id,
                'card_number'                              => $r->card_number,
                'card_status'                              => $r->card_status,
                'card_status_code_name'                    => $r->card_status_code_name,
                'waste_code'                               => $r->waste_code,
                'waste_code_description'                   => $r->waste_code_description,
                'waste_code_and_description'               => $r->waste_code_and_description,
                'calendar_year'                            => $r->calendar_year,
                'waste_mass'                               => $this->parseDecimal($r->waste_mass),
                'corrected_waste_mass'                     => $this->parseDecimal($r->corrected_waste_mass),
                'planned_transport_date'                   => $this->parseDate($r->planned_transport_date),
                'planned_transport_time'                   => $this->parseDatetime($r->planned_transport_time),
                'real_transport_date'                      => $this->parseDate($r->real_transport_date),
                'real_transport_time'                      => $this->parseDatetime($r->real_transport_time),
                'receive_confirmation_date'                => $this->parseDate($r->receive_confirmation_date),
                'receive_confirmation_time'                => $this->parseDatetime($r->receive_confirmation_time),
                'receive_confirmed_by_user'                => $r->receive_confirmed_by_user,
                'approval_date'                            => $this->parseDate($r->approval_date),
                'approval_time'                            => $this->parseDatetime($r->approval_time),
                'approved_by_user'                         => $r->approved_by_user,
                'transport_confirmation_date'              => $this->parseDate($r->transport_confirmation_date),
                'transport_confirmation_time'              => $this->parseDatetime($r->transport_confirmation_time),
                'transport_confirmed_by_user'              => $r->transport_confirmed_by_user,
                'card_rejection_time'                      => $r->card_rejection_time,
                'rejected_by_user_first_name_and_last_name'=> $r->rejected_by_user_first_name_and_last_name,
                'sender_name_or_first_name_and_last_name'  => $r->sender_name_or_first_name_and_last_name,
                'sender_address'                           => $r->sender_address,
                'sender_eup_number'                        => $r->sender_eup_number,
                'sender_eup_name'                          => $r->sender_eup_name,
                'sender_eup_address'                       => $r->sender_eup_address,
                'sender_identification_number'             => $r->sender_identification_number,
                'sender_nip'                               => $r->sender_nip,
                'carrier_name_or_first_name_and_last_name' => $r->carrier_name_or_first_name_and_last_name,
                'carrier_address'                          => $r->carrier_address,
                'carrier_identification_number'            => $r->carrier_identification_number,
                'carrier_nip'                              => $r->carrier_nip,
                'receiver_name_or_first_name_and_last_name'=> $r->receiver_name_or_first_name_and_last_name,
                'receiver_address'                         => $r->receiver_address,
                'receiver_identification_number'           => $r->receiver_identification_number,
                'receiver_nip'                             => $r->receiver_nip,
                'vehicle_reg_number'                       => $r->vehicle_reg_number,
                'remarks'                                  => $r->remarks,
                'additional_info'                          => $r->additional_info,
                'kpo_last_modified_at'                     => $r->kpo_last_modified_at,
                'created_at'                               => $r->created_at ?? now(),
                'updated_at'                               => $r->updated_at ?? now(),
            ];

            if ($hasEwrant) {
                $row['ewrant'] = $r->ewrant;
            }

            return $row;
        })->toArray();

        $inserted = 0;
        foreach (array_chunk($rows, 200) as $chunk) {
            $inserted += DB::table($table)->insertOrIgnore($chunk);
        }

        $skipped = $oldRows->count() - $inserted;
        $this->command->info("  → wstawiono {$inserted}".($skipped > 0 ? ", pominięto {$skipped} (duplikaty kpo_id)" : ''));
    }

    private function parseDate($value): ?string
    {
        if (empty($value) || trim((string) $value) === '') {
            return null;
        }
        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parseDatetime($value): ?string
    {
        if (empty($value) || trim((string) $value) === '') {
            return null;
        }
        try {
            return Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parseDecimal($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $clean = str_replace(',', '.', (string) $value);

        return is_numeric($clean) ? $clean : null;
    }
}
