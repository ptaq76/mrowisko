<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\ClientManager;

class ImapReklamacjeService
{
    private ClientManager $manager;

    private Client $client;

    public function __construct()
    {
        $this->manager = new ClientManager;
    }

    private function polacz(): void
    {
        $this->client = $this->manager->make([
            'host' => config('reklamacje.imap.host'),
            'port' => config('reklamacje.imap.port'),
            'encryption' => config('reklamacje.imap.encryption'),
            'validate_cert' => config('reklamacje.imap.validate_cert', true),
            'username' => config('reklamacje.imap.username'),
            'password' => config('reklamacje.imap.password'),
            'protocol' => 'imap',
        ]);

        $this->client->connect();
    }

    /**
     * Pobiera nieprzeczytane wiadomości ze skrzynki.
     *
     * Zwraca tablicę — dla każdego maila:
     * [
     *   'mail_subject' => string,
     *   'mail_date'    => string,
     *   'blad'         => string|null,   // wypełnione jeśli błąd na etapie IMAP
     *   'zalaczniki'   => [              // surowe dane załączników (pusta tablica przy błędzie)
     *     ['nazwa' => string, 'zawartosc' => string],
     *     ['nazwa' => string, 'zawartosc' => string],
     *   ],
     * ]
     *
     * Serwis NIE zapisuje plików na dysk — robi to Command,
     * bo dopiero po parsowaniu PDF znamy Lieferschein potrzebny do ścieżki.
     */
    public function pobierzNowe(): array
    {
        $this->polacz();

        $folderName = config('reklamacje.imap.folder', 'INBOX');
        $folder = $this->client->getFolder($folderName);
        $messages = $folder->query()->unseen()->get();

        $wyniki = [];

        foreach ($messages as $message) {
            try {
                $wyniki[] = $this->przetworzWiadomosc($message);
            } catch (\Exception $e) {
                Log::error('ImapReklamacjeService: błąd przetwarzania wiadomości: '.$e->getMessage());
            }
        }

        $this->client->disconnect();

        return $wyniki;
    }

    private function przetworzWiadomosc($message): array
    {
        $subject = (string) $message->getSubject();
        try {
            $dateAttr = $message->getDate();
            $date = $dateAttr?->first()?->toDateTimeString()
                ?? $dateAttr?->toDateTimeString()
                ?? now()->toDateTimeString();
        } catch (\Throwable $e) {
            $date = now()->toDateTimeString();
        }

        $podstawa = [
            'mail_subject' => $subject,
            'mail_date' => $date,
            'blad' => null,
            'zalaczniki' => [],
        ];

        // Filtrujemy tylko PDF-y
        $pdfy = [];
        foreach ($message->getAttachments() as $attachment) {
            $mime = strtolower($attachment->getMimeType() ?? '');
            $name = $attachment->getName() ?? '';

            if ($mime === 'application/pdf' || str_ends_with(strtolower($name), '.pdf')) {
                $pdfy[] = $attachment;
            }
        }

        // Oznacz jako przeczytany niezależnie od wyniku
        $message->setFlag('Seen');

        if (count($pdfy) !== 2) {
            return array_merge($podstawa, [
                'blad' => 'Wiadomość zawiera '.count($pdfy).' załączników PDF (oczekiwano 2).',
            ]);
        }

        $zalaczniki = [];
        foreach ($pdfy as $pdf) {
            $zalaczniki[] = [
                'nazwa' => $this->sanityzujNazwe($pdf->getName() ?? ('plik_'.uniqid().'.pdf')),
                'zawartosc' => $pdf->getContent(),
            ];
        }

        return array_merge($podstawa, ['zalaczniki' => $zalaczniki]);
    }

    private function sanityzujNazwe(string $nazwa): string
    {
        $czysta = preg_replace('/[^\w.\-]/u', '_', $nazwa);

        return substr($czysta, 0, 200);
    }
}
