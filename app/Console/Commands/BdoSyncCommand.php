<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Bdo\BdoSyncService;
use App\Services\Bdo\BdoLogger;

class BdoSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bdo:sync {--type=all : Typ synchronizacji: all, przejmujacy, przekazujacy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizacja kart BDO z API rządowym';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->option('type');
        
        $this->info('=== BDO Sync Start ===');
        $this->info('Typ: ' . $type);
        $this->info('Czas: ' . now()->format('Y-m-d H:i:s'));

        $bdoSync = new BdoSyncService();

        try {
            // Synchronizacja przejmujący
            if ($type === 'all' || $type === 'przejmujacy') {
                $this->info('');
                $this->info('--- Synchronizacja PRZEJMUJĄCY ---');
                
                $result = $bdoSync->fetchAndSync();
                
                $this->displayResult($result, 'Przejmujący');
            }

            // Synchronizacja przekazujący
            if ($type === 'all' || $type === 'przekazujacy') {
                $this->info('');
                $this->info('--- Synchronizacja PRZEKAZUJĄCY ---');
                
                $result = $bdoSync->fetchAndSyncPrzekazujacy();
                
                $this->displayResult($result, 'Przekazujący');
            }

            $this->info('');
            $this->info('=== BDO Sync End ===');
            
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('Błąd synchronizacji: ' . $e->getMessage());
            BdoLogger::error('BDO Sync Command failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }

    /**
     * Wyświetla wynik synchronizacji
     */
    private function displayResult(array $result, string $label): void
    {
        $status = $result['status'] ?? 'UNKNOWN';
        
        if ($status === 'SUCCESS') {
            $this->info("Status: {$status}");
        } else {
            $this->error("Status: {$status}");
        }

        $this->table(
            ['Metryka', 'Wartość'],
            [
                ['Pobrano', $result['total'] ?? 0],
                ['Utworzono', $result['created'] ?? 0],
                ['Zaktualizowano', $result['updated'] ?? 0],
                ['Pominięto', $result['skipped'] ?? 0],
                ['Błędy', $result['errors'] ?? 0],
            ]
        );

        if (isset($result['message'])) {
            $this->info("Komunikat: {$result['message']}");
        }

        if ($status === 'FAILED' && isset($result['stopped_at_card_number'])) {
            $this->warn("Zatrzymano na karcie: {$result['stopped_at_card_number']}");
        }
    }
}
