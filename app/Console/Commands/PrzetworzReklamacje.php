<?php

namespace App\Console\Commands;

use App\Models\Lieferschein;
use App\Models\Reklamacja;
use App\Models\ReklamacjaBled;
use App\Services\ImapGewichtsmeldungService;
use App\Services\ImapReklamacjeService;
use App\Services\PdfParserService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PrzetworzReklamacje extends Command
{
    protected $signature = 'reklamacje:przetwarzaj';

    protected $description = 'Pobiera reklamacje i Gewichtsmeldungen z maili i zapisuje do bazy';

    public function __construct(
        private ImapReklamacjeService $imapService,
        private PdfParserService $pdfService,
        private ImapGewichtsmeldungService $gewichtService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        Log::info('reklamacje:przetwarzaj — start');

        // ── Reklamacje ────────────────────────────────────────────────────────
        $this->info('Sprawdzanie skrzynki reklamacji...');
        try {
            $wiadomosci = $this->imapService->pobierzNowe();
            if (empty($wiadomosci)) {
                $this->info('Brak nowych reklamacji.');
            } else {
                $this->info('Znaleziono reklamacji: '.count($wiadomosci));
                foreach ($wiadomosci as $w) {
                    $this->przetworzReklamacje($w);
                }
            }
        } catch (\Exception $e) {
            $this->error('Błąd IMAP reklamacji: '.$e->getMessage());
            Log::error('reklamacje:przetwarzaj — błąd IMAP: '.$e->getMessage());
        }

        // ── Gewichtsmeldungen ─────────────────────────────────────────────────
        $this->info('Sprawdzanie skrzynki Gewichtsmeldung...');
        try {
            $wiadomosci = $this->gewichtService->pobierzNowe();
            if (empty($wiadomosci)) {
                $this->info('Brak nowych Gewichtsmeldungen.');
            } else {
                $this->info('Znaleziono Gewichtsmeldungen: '.count($wiadomosci));
                foreach ($wiadomosci as $w) {
                    $this->przetworzGewichtsmeldung($w);
                }
            }
        } catch (\Exception $e) {
            $this->error('Błąd IMAP Gewichtsmeldung: '.$e->getMessage());
            Log::error('reklamacje:przetwarzaj — błąd IMAP Gewicht: '.$e->getMessage());
        }

        Log::info('reklamacje:przetwarzaj — koniec');

        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REKLAMACJE
    // ─────────────────────────────────────────────────────────────────────────

    private function przetworzReklamacje(array $wiadomosc): void
    {
        $subject = $wiadomosc['mail_subject'] ?? '(brak tematu)';
        $this->line("  Przetwarzam reklamację: {$subject}");

        if (! empty($wiadomosc['blad'])) {
            $this->zapiszBlad($wiadomosc, $wiadomosc['blad'], $wiadomosc['zalaczniki']);
            $this->warn("    → Błąd IMAP: {$wiadomosc['blad']}");

            return;
        }

        $zalaczniki = $wiadomosc['zalaczniki'];

        try {
            $dane = $this->pdfService->przetworzDwaBlobs(
                $zalaczniki[0]['zawartosc'], $zalaczniki[0]['nazwa'],
                $zalaczniki[1]['zawartosc'], $zalaczniki[1]['nazwa'],
            );
        } catch (\Exception $e) {
            $this->zapiszBlad($wiadomosc, 'Błąd parsowania PDF: '.$e->getMessage(), $zalaczniki);
            $this->warn('    → Błąd parsowania: '.$e->getMessage());

            return;
        }

        $lieferschein = $dane['lieferschein'];
        $masaNetto = $dane['masa_netto'];

        if ($lieferschein === null || $masaNetto === null) {
            $opis = $this->opisBledu($lieferschein, $masaNetto);
            $this->zapiszBlad($wiadomosc, $opis, $zalaczniki);
            $this->warn("    → Błąd: {$opis}");

            return;
        }

        $sciezkaPliku = $this->zapiszPlik(
            $dane['zawartosc_masy'],
            $dane['plik_masa'],
            $lieferschein,
            $wiadomosc['mail_date'],
        );

        $lieferscheinId = Lieferschein::where('number', $lieferschein)->value('id');

        Reklamacja::create([
            'typ' => 'reklamacja',
            'lieferschein' => $lieferschein,
            'lieferschein_id' => $lieferscheinId,
            'masa_netto' => $masaNetto,
            'mail_subject' => $wiadomosc['mail_subject'],
            'mail_date' => $wiadomosc['mail_date'],
            'plik_lieferschein' => $dane['plik_lieferschein'],
            'plik_masa' => $dane['plik_masa'],
            'sciezka_pliku_masy' => $sciezkaPliku,
        ]);

        $this->info("    → OK: Lieferschein={$lieferschein}, masa={$masaNetto} t");
        Log::info("reklamacje: zapisano Lieferschein={$lieferschein}, masa={$masaNetto}");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GEWICHTSMELDUNG
    // ─────────────────────────────────────────────────────────────────────────

    private function przetworzGewichtsmeldung(array $wiadomosc): void
    {
        $subject = $wiadomosc['mail_subject'] ?? '(brak tematu)';
        $this->line("  Przetwarzam Gewichtsmeldung: {$subject}");

        if (! empty($wiadomosc['blad'])) {
            $this->zapiszBlad($wiadomosc, $wiadomosc['blad'], []);
            $this->warn("    → Błąd IMAP: {$wiadomosc['blad']}");

            return;
        }

        $zal = $wiadomosc['zalaczniki'][0];

        try {
            $dane = $this->pdfService->przetworzGewichtsmeldung($zal['zawartosc'], $zal['nazwa']);
        } catch (\Exception $e) {
            $this->zapiszBlad($wiadomosc, 'Błąd parsowania PDF: '.$e->getMessage(), $wiadomosc['zalaczniki']);
            $this->warn('    → Błąd parsowania: '.$e->getMessage());

            return;
        }

        $lieferschein = $dane['lieferschein'];
        $masaNetto = $dane['masa_netto'];

        if ($lieferschein === null || $masaNetto === null) {
            $opis = $this->opisBledu($lieferschein, $masaNetto);
            $this->zapiszBlad($wiadomosc, $opis, $wiadomosc['zalaczniki']);
            $this->warn("    → Błąd: {$opis}");

            return;
        }

        $sciezkaPliku = $this->zapiszPlik(
            $dane['zawartosc'],
            $dane['plik'],
            $lieferschein,
            $wiadomosc['mail_date'],
        );

        $lieferscheinId = Lieferschein::where('number', $lieferschein)->value('id');

        Reklamacja::create([
            'typ' => 'gewichtsmeldung',
            'lieferschein' => $lieferschein,
            'lieferschein_id' => $lieferscheinId,
            'masa_netto' => $masaNetto,
            'mail_subject' => $wiadomosc['mail_subject'],
            'mail_date' => $wiadomosc['mail_date'],
            'plik_lieferschein' => null,
            'plik_masa' => $dane['plik'],
            'sciezka_pliku_masy' => $sciezkaPliku,
        ]);

        $this->info("    → OK: Lieferschein={$lieferschein}, Gewicht={$masaNetto} t");
        Log::info("gewichtsmeldung: zapisano Lieferschein={$lieferschein}, masa={$masaNetto}");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function zapiszPlik(string $zawartosc, string $nazwaPliku, string $lieferschein, ?string $mailDate): string
    {
        $data = $mailDate ? Carbon::parse($mailDate) : now();
        $folderLieferschein = preg_replace('/[^\w\-]/u', '_', $lieferschein);
        $folder = "reklamacje/{$data->format('Y')}/{$data->format('m')}/{$folderLieferschein}";
        $sciezka = "{$folder}/{$nazwaPliku}";

        Storage::makeDirectory($folder);
        Storage::put($sciezka, $zawartosc);

        return $sciezka;
    }

    private function zapiszBlad(array $wiadomosc, string $opis, array $zalaczniki): void
    {
        $folderBledy = null;
        $nazwy = [];

        if (! empty($zalaczniki)) {
            $uniqid = uniqid('blad_', true);
            $folderBledy = "reklamacje/_bledy/{$uniqid}";
            Storage::makeDirectory($folderBledy);

            foreach ($zalaczniki as $zal) {
                Storage::put("{$folderBledy}/{$zal['nazwa']}", $zal['zawartosc']);
                $nazwy[] = $zal['nazwa'];
            }
        }

        ReklamacjaBled::create([
            'mail_subject' => $wiadomosc['mail_subject'] ?? null,
            'mail_date' => $wiadomosc['mail_date'] ?? null,
            'blad' => $opis,
            'plik_1' => $nazwy[0] ?? null,
            'plik_2' => $nazwy[1] ?? null,
            'folder_temp' => $folderBledy,
            'status' => 'nowy',
        ]);

        Log::warning("reklamacje: błąd [{$opis}]");
    }

    private function opisBledu(?string $lieferschein, ?float $masaNetto): string
    {
        if ($lieferschein === null && $masaNetto === null) {
            return 'Nie znaleziono numeru Lieferschein ani masy.';
        }
        if ($lieferschein === null) {
            return 'Nie znaleziono numeru Lieferschein.';
        }

        return 'Nie znaleziono masy.';
    }
}
