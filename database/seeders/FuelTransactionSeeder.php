<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelTransactionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('fuel_transactions')->truncate();

        $userNames = DB::table('mrowisko.users')->pluck('name', 'id');
        $vehicleIds = DB::table('fuel_vehicles')->pluck('id')->flip();

        $rows = DB::table('mrowisko.tankowania')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $this->command->info('Pobrano '.$rows->count().' rekordów z mrowisko.tankowania.');

        $tankAfter = 0;
        $batch = [];
        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $ilosc = (int) $row->ilosc;

            if ($row->maszyna_id == 23) {
                $type = 'inwentaryzacja';
                $liters = abs($ilosc);
                $tankAfter = $liters;
                $vehicleId = null;
                $mileage = null;
                $fullTank = null;
            } elseif ($ilosc < 0) {
                $type = 'tankowanie';
                $liters = abs($ilosc);
                $tankAfter = max(0, $tankAfter - $liters);
                $vehicleId = $vehicleIds->has($row->maszyna_id) ? $row->maszyna_id : null;
                $mileage = $row->km !== null ? (int) $row->km : null;
                $fullTank = $mileage !== null ? (bool) $row->pelny_bak : null;
            } else {
                $type = 'dostawa';
                $liters = $ilosc;
                $tankAfter = $tankAfter + $liters;
                $vehicleId = null;
                $mileage = null;
                $fullTank = null;
            }

            if ($liters < 1) {
                $skipped++;

                continue;
            }

            $batch[] = [
                'id' => $row->id,
                'type' => $type,
                'liters' => $liters,
                'tank_after' => $tankAfter,
                'mileage' => $mileage,
                'full_tank' => $fullTank,
                'fuel_vehicle_id' => $vehicleId,
                'operator' => $userNames[$row->operator_id] ?? null,
                'notes' => null,
                'created_at' => $row->created_at ?? $row->data,
                'updated_at' => $row->updated_at ?? $row->data,
            ];
            $imported++;

            if (count($batch) >= 500) {
                DB::table('fuel_transactions')->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('fuel_transactions')->insert($batch);
        }

        $maxId = DB::table('fuel_transactions')->max('id') ?? 0;
        DB::statement('ALTER TABLE fuel_transactions AUTO_INCREMENT = '.($maxId + 1));

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info("Zaimportowano: {$imported} transakcji paliwowych.");
        if ($skipped > 0) {
            $this->command->warn("Pominięto: {$skipped} (zerowa ilość).");
        }
    }
}
