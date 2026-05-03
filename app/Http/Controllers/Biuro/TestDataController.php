<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class TestDataController extends Controller
{
    private const LOCK_FILE = 'test_data.lock';
    private const STALE_LOCK_AFTER_SECONDS = 600;

    public function run(Request $request)
    {
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
                'error' => 'Nieprawidłowe hasło.',
            ], 403);
        }

        $disk = Storage::disk('local');
        if ($disk->exists(self::LOCK_FILE)) {
            $age = now()->diffInSeconds(\Carbon\Carbon::createFromTimestamp($disk->lastModified(self::LOCK_FILE)));
            if ($age < self::STALE_LOCK_AFTER_SECONDS) {
                return response()->json([
                    'success' => false,
                    'error' => "Seeder już trwa (lock założony {$age}s temu). Poczekaj lub usuń storage/app/test_data.lock.",
                ], 423);
            }
            $disk->delete(self::LOCK_FILE);
        }
        $disk->put(self::LOCK_FILE, now()->toIso8601String().' user='.optional(auth()->user())->login);

        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        try {
            $exitCode = Artisan::call('db:seed', [
                '--class' => 'TestDataSeeder',
                '--force' => true,
            ]);
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
