<?php

namespace App\Console\Commands;

use App\Services\ImapLsService;
use Illuminate\Console\Command;

class FetchLsPdfs extends Command
{
    protected $signature = 'ls:fetch-pdfs';

    protected $description = 'Pobiera PDF z Lieferscheinami ze skrzynki ls@iantra.pl';

    public function __construct(private ImapLsService $service)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Łączenie ze skrzynką IMAP...');

        try {
            $result = $this->service->fetch();
            $this->info("Pobrano: {$result['fetched']} maili");
            $this->info("Zapisano PDF: {$result['saved']}");
            if ($result['errors']) {
                $this->warn("Błędy: {$result['errors']}");
            }
        } catch (\Throwable $e) {
            $this->error('Błąd IMAP: '.$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
