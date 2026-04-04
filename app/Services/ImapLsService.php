<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImapLsService
{
    private string $host;
    private int    $port;
    private string $encryption;
    private string $username;
    private string $password;
    private string $folder;

    public function __construct()
    {
        $this->host       = env('IMAP_HOST', 'h56.seohost.pl');
        $this->port       = (int) env('IMAP_PORT', 993);
        $this->encryption = env('IMAP_ENCRYPTION', 'ssl');
        $this->username   = env('IMAP_USERNAME', 'ls@iantra.pl');
        $this->password   = env('IMAP_PASSWORD', '');
        $this->folder     = env('IMAP_FOLDER', 'INBOX');
    }

    public function fetch(): array
    {
        $result = ['fetched' => 0, 'saved' => 0, 'errors' => 0];

        // Połącz z IMAP
        $mailbox = $this->connect();

        // Pobierz nieprzeczytane maile
        $mailIds = imap_search($mailbox, 'UNSEEN');

        if (!$mailIds) {
            imap_close($mailbox);
            return $result;
        }

        $result['fetched'] = count($mailIds);

        foreach ($mailIds as $mailId) {
            try {
                $this->processMail($mailbox, $mailId, $result);
                // Oznacz jako przeczytany
                imap_setflag_full($mailbox, (string)$mailId, '\\Seen');
            } catch (\Throwable $e) {
                Log::error('ImapLsService: błąd przetwarzania maila #' . $mailId, [
                    'error' => $e->getMessage(),
                ]);
                $result['errors']++;
            }
        }

        imap_close($mailbox);
        return $result;
    }

    private function connect()
    {
        $flags = '/' . $this->encryption . '/novalidate-cert';
        $dsn   = '{' . $this->host . ':' . $this->port . $flags . '}' . $this->folder;

        $mailbox = imap_open($dsn, $this->username, $this->password);

        if (!$mailbox) {
            throw new \RuntimeException('Nie można połączyć z IMAP: ' . imap_last_error());
        }

        return $mailbox;
    }

    private function processMail($mailbox, int $mailId, array &$result): void
    {
        $structure = imap_fetchstructure($mailbox, $mailId);

        if (empty($structure->parts)) {
            return; // Brak załączników
        }

        foreach ($structure->parts as $idx => $part) {
            $partNum = $idx + 1;

            // Szukamy PDF
            if (!$this->isPdf($part)) {
                continue;
            }

            $filename = $this->getFilename($part);
            if (!$filename) {
                $filename = 'ls_' . $mailId . '_' . $partNum . '.pdf';
            }

            // Pobierz zawartość
            $data = imap_fetchbody($mailbox, $mailId, (string)$partNum);

            // Dekoduj base64
            if ($part->encoding == 3) {
                $data = base64_decode($data);
            } elseif ($part->encoding == 4) {
                $data = quoted_printable_decode($data);
            }

            if (empty($data)) {
                continue;
            }

            // Zapisz do folderu attachments
            $targetPath = 'attachments/' . $filename;
            Storage::disk('public')->put($targetPath, $data);

            Log::info('ImapLsService: zapisano PDF', [
                'filename' => $filename,
                'path'     => $targetPath,
                'mail_id'  => $mailId,
            ]);

            $result['saved']++;
        }
    }

    private function isPdf($part): bool
    {
        // Sprawdź typ MIME
        if (isset($part->subtype) && strtolower($part->subtype) === 'pdf') {
            return true;
        }

        // Sprawdź nazwę pliku
        $filename = $this->getFilename($part);
        if ($filename && str_ends_with(strtolower($filename), '.pdf')) {
            return true;
        }

        return false;
    }

    private function getFilename($part): ?string
    {
        // Sprawdź parameters
        if (!empty($part->parameters)) {
            foreach ($part->parameters as $p) {
                if (strtolower($p->attribute) === 'name') {
                    return $this->decodeMimeStr($p->value);
                }
            }
        }

        // Sprawdź dparameters
        if (!empty($part->dparameters)) {
            foreach ($part->dparameters as $p) {
                if (strtolower($p->attribute) === 'filename') {
                    return $this->decodeMimeStr($p->value);
                }
            }
        }

        return null;
    }

    private function decodeMimeStr(string $str): string
    {
        $decoded = imap_mime_header_decode($str);
        $result  = '';
        foreach ($decoded as $part) {
            $charset  = $part->charset ?? 'utf-8';
            $text     = $part->text ?? '';
            if (strtolower($charset) !== 'utf-8' && strtolower($charset) !== 'default') {
                $text = mb_convert_encoding($text, 'utf-8', $charset);
            }
            $result .= $text;
        }
        return $result ?: $str;
    }
}
