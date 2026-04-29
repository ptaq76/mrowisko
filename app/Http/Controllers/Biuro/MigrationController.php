<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class MigrationController extends Controller
{
    private const LOCK_FILE = 'migration.lock';
    private const STALE_LOCK_AFTER_SECONDS = 3600; // 1h — po tym czasie lock uznajemy za zawieszony

    /**
     * POST /biuro/migration/run
     * Uruchamia php artisan db:seed po podaniu hasła z .env (MIGRATION_PASSWORD).
     * Zwraca JSON {success, output, error?}.
     */
    public function run(Request $request)
    {
        // 1. Hasło
        $expected = (string) env('MIGRATION_PASSWORD', '');
        if ($expected === '') {
            return response()->json([
                'success' => false,
                'error' => 'Brak skonfigurowanego MIGRATION_PASSWORD w .env. Skontaktuj się z administratorem.',
            ], 500);
        }

        $given = (string) $request->input('password', '');
        if (! hash_equals($expected, $given)) {
            return response()->json([
                'success' => false,
                'error' => 'Nieprawidłowe hasło migracji.',
            ], 403);
        }

        // 2. Lock — zapobiega podwójnemu uruchomieniu
        $disk = Storage::disk('local');
        if ($disk->exists(self::LOCK_FILE)) {
            $age = now()->diffInSeconds(\Carbon\Carbon::createFromTimestamp($disk->lastModified(self::LOCK_FILE)));
            if ($age < self::STALE_LOCK_AFTER_SECONDS) {
                return response()->json([
                    'success' => false,
                    'error' => "Migracja już trwa (lock założony {$age}s temu). Poczekaj lub usuń storage/app/migration.lock.",
                ], 423);
            }
            // Stale lock — nadpisujemy
            $disk->delete(self::LOCK_FILE);
        }
        $disk->put(self::LOCK_FILE, now()->toIso8601String().' user='.optional(auth()->user())->login);

        // 3. Uruchomienie seederów (długie — bez timeoutu, większy memory limit)
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        try {
            $exitCode = Artisan::call('db:seed', ['--force' => true]);
            $output = Artisan::output();
        } catch (\Throwable $e) {
            $disk->delete(self::LOCK_FILE);

            return response()->json([
                'success' => false,
                'error' => 'Wyjątek podczas seedowania: '.$e->getMessage(),
                'output' => Artisan::output() ?: null,
            ], 500);
        }

        $disk->delete(self::LOCK_FILE);

        return response()->json([
            'success' => $exitCode === 0,
            'exit_code' => $exitCode,
            'output' => $output,
        ]);
    }
}
