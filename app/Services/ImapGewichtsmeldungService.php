<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;

class ImapGewichtsmeldungService
{
    private ClientManager $manager;
    private Client $client;

    public function __construct()
    {
        $this->manager = new ClientManager();
    }

    private function polacz(): void
    {
        $this->client = $this->manager->make([
            'host'          => config('reklamacje.gewichtsmeldung_imap.host'),
            'port'          => config('reklamacje.gewichtsmeldung_imap.port'),
            'encryption'    => config('reklamacje.gewichtsmeldung_imap.encryption'),
            'validate_cert' => config('reklamacje.gewichtsmeldung_imap.validate_cert', false),
            'username'      => config('reklamacje.gewichtsmeldung_imap.username'),
            'password'      => config('reklamacje.gewichtsmeldung_imap.password'),
            'protocol'      => 'imap',
        ]);

        $this->client->connect();
    }

    /**
     * Pobiera nieprzeczytane wiadomości ze skrzynki gewichtsmeldung.
     * Każdy mail ma jeden załącznik PDF.
     *
     * Zwraca tablicę:
     * [
     *   'mail_subject' => string,
     *   'mail_date'    => string,
     *   'blad'         => string|null,
     *   'zalaczniki'   => [['nazwa' => string, 'zawartosc' => string]],
     * ]
     */
    public function pobierzNowe(): array
    {
        $this->polacz();

        $folderName = config('reklamacje.gewichtsmeldung_imap.folder', 'INBOX');
        $folder     = $this->client->getFolder($folderName);
        $messages   = $folder->query()->unseen()->get();

        $wyniki = [];

        foreach ($messages as $message) {
            try {
                $wyniki[] = $this->przetworzWiadomosc($message);
            } catch (\Exception $e) {
                Log::error('ImapGewichtsmeldungService: błąd: ' . $e->getMessage());
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
            'mail_date'    => $date,
            'blad'         => null,
            'zalaczniki'   => [],
        ];

        $pdfy = [];
        foreach ($message->getAttachments() as $attachment) {
            $mime = strtolower($attachment->getMimeType() ?? '');
            $name = $attachment->getName() ?? '';
            if ($mime === 'application/pdf' || str_ends_with(strtolower($name), '.pdf')) {
                $pdfy[] = $attachment;
            }
        }

        $message->setFlag('Seen');

        if (count($pdfy) < 1) {
            return array_merge($podstawa, [
                'blad' => 'Brak załącznika PDF w wiadomości.',
            ]);
        }

        $pdf = $pdfy[0];
        return array_merge($podstawa, [
            'zalaczniki' => [[
                'nazwa'     => $this->sanityzujNazwe($pdf->getName() ?? ('gewicht_' . uniqid() . '.pdf')),
                'zawartosc' => $pdf->getContent(),
            ]],
        ]);
    }

    private function sanityzujNazwe(string $nazwa): string
    {
        $czysta = preg_replace('/[^\w.\-]/u', '_', $nazwa);
        return substr($czysta, 0, 200);
    }
}
