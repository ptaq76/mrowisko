<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZadaniaSeeder extends Seeder
{
    /**
     * Import zadań ze starej bazy:
     * - zadania_kierowcy → target='driver', driver_id=kierowca_id
     * - zadania_plac     → target='plac',   driver_id=null
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('zadania')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $old = DB::connection('mrowisko');

        // Walidne user_id i driver_id w nowej bazie (FK)
        $validUserIds = DB::table('users')->pluck('id')->flip();
        $validDriverIds = DB::table('drivers')->pluck('id')->flip();
        $fallbackUserId = (int) (DB::table('users')->orderBy('id')->value('id') ?? 1);

        $rows = [];
        $skippedNoDriver = 0;

        // Zadania kierowców
        foreach ($old->table('zadania_kierowcy')->orderBy('id')->get() as $z) {
            if (! isset($validDriverIds[$z->kierowca_id])) {
                $skippedNoDriver++;

                continue;
            }
            $opId = isset($validUserIds[$z->operator_id]) ? $z->operator_id : $fallbackUserId;
            $isDone = (int) $z->status === 1;
            $rows[] = [
                'tresc' => $z->zadanie,
                'data' => $z->data,
                'target' => 'driver',
                'driver_id' => $z->kierowca_id,
                'batch_id' => null,
                'status' => $isDone ? 'done' : 'pending',
                'completed_at' => $isDone ? $z->updated_at : null,
                'completed_by_user_id' => $isDone && isset($validUserIds[$z->operator_id]) ? $z->operator_id : null,
                'created_by_user_id' => $opId,
                'created_at' => $z->created_at ?? now(),
                'updated_at' => $z->updated_at ?? now(),
            ];
        }

        // Zadania placu
        foreach ($old->table('zadania_plac')->orderBy('id')->get() as $z) {
            $opId = isset($validUserIds[$z->operator_id]) ? $z->operator_id : $fallbackUserId;
            $isDone = (int) $z->status === 1;
            $rows[] = [
                'tresc' => $z->zadanie,
                'data' => $z->data,
                'target' => 'plac',
                'driver_id' => null,
                'batch_id' => null,
                'status' => $isDone ? 'done' : 'pending',
                'completed_at' => $isDone ? $z->updated_at : null,
                'completed_by_user_id' => $isDone && isset($validUserIds[$z->operator_id]) ? $z->operator_id : null,
                'created_by_user_id' => $opId,
                'created_at' => $z->created_at ?? now(),
                'updated_at' => $z->updated_at ?? now(),
            ];
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('zadania')->insert($chunk);
        }

        $this->command->info('Zadania zaimportowane: '.count($rows).
            ' (kierowcy + plac).'.($skippedNoDriver > 0 ? " Pominięte (brak kierowcy w nowej bazie): {$skippedNoDriver}" : ''));
    }
}
