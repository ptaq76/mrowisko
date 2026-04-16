<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Pobieranie PDF Lieferscheinów ze skrzynki mailowej co 15 minut
        $schedule->command('ls:fetch-pdfs')->everyFifteenMinutes();

        // Przetwarzanie reklamacji co 5 minut
        $schedule->command('reklamacje:przetwarzaj')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/reklamacje-cron.log'));
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
