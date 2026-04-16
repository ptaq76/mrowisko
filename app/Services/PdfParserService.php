<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class PdfParserService
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser;
    }

    /**
     * Wyciąga tekst z surowej zawartości binarnej PDF.
     */
    private function extractTextFromBlob(string $zawartosc): string
    {
        try {
            $pdf = $this->parser->parseContent($zawartosc);

            return $pdf->getText();
        } catch (\Exception $e) {
            Log::error('PdfParserService: błąd parsowania PDF: '.$e->getMessage());

            return '';
        }
    }

    /**
     * Sprawdza czy tekst zawiera słowo "Lieferschein".
     */
    private function zawieraLieferschein(string $tekst): bool
    {
        return str_contains($tekst, 'Lieferschein');
    }

    /**
     * Wyciąga numer Lieferschein z tekstu PDF.
     *
     * Obsługuje dwa formaty:
     *   Format A: "Lieferschein\n1204005"  (numer w następnej linii)
     *   Format B: "Lieferschein 1204005"   (numer po spacji w tej samej linii)
     */
    private function odczytajLieferschein(string $tekst): ?string
    {
        // Format A: "Lieferschein 1204005" (numer po spacji w tej samej linii)
        // Format B: "Lieferschein / Positionsnummer\n600276036 / 00010" (numer w następnej linii)

        // Format B - numer w kolejnej linii po "Lieferschein / Positionsnummer"
        if (preg_match('/Lieferschein\s*\/\s*Positionsnummer\s*\n\s*(\d+)/u', $tekst, $matches)) {
            $numer = trim($matches[1]);
            if (! empty($numer)) {
                return $numer;
            }
        }

        // Format A - numer w tej samej linii (pomijamy jeśli następny token to "/")
        if (preg_match('/Lieferschein\s+(\d+)/u', $tekst, $matches)) {
            $numer = trim($matches[1]);
            if (! empty($numer)) {
                return $numer;
            }
        }

        Log::warning('PdfParserService: nie znaleziono numeru Lieferschein w tekście.');

        return null;
    }

    /**
     * Wyciąga masę netto z tekstu PDF.
     *
     * Obsługuje dwa formaty:
     *   Format A: "Loading quantity (net)  21,094 t"  (wartość w tej samej linii)
     *   Format B: "Loading quantity (net)\n\n19,791 t" (wartość w kolejnej linii)
     */
    private function odczytajMaseNetto(string $tekst): ?float
    {
        // Format PDF RecycLog:
        // "Loading quantity (net)\nDispo no.:\nNo :XXXXX\n22,800 t\n3,009 t\n19,791 t"
        // Szukamy trzeciej liczby po "Loading quantity (net)"
        // czyli: remuneration of load qty, complained quantity, loading quantity (net) - w tej kolejności
        if (preg_match(
            '/Loading quantity \(net\).*?[\d,\.]+\s*t\s*[\d,\.]+\s*t\s*([\d,\.]+)\s*t/su',
            $tekst, $matches
        )) {
            $wartosc = str_replace(',', '.', trim($matches[1]));
            $masa = floatval($wartosc);
            if ($masa > 0) {
                return $masa;
            }
        }

        // Fallback: pierwsza liczba bezpośrednio po "Loading quantity (net)"
        if (preg_match('/Loading quantity \(net\)[^\d]*([\d]+[,\.][\d]+)/u', $tekst, $matches)) {
            $wartosc = str_replace(',', '.', trim($matches[1]));
            $masa = floatval($wartosc);
            if ($masa > 0) {
                return $masa;
            }
        }

        Log::warning('PdfParserService: nie znaleziono masy netto w tekście.');

        return null;
    }

    /**
     * Główna metoda — przyjmuje surową zawartość dwóch PDF-ów (kolejność dowolna).
     * Automatycznie określa który zawiera Lieferschein, który masę.
     *
     * Zwraca:
     * [
     *   'lieferschein'      => string|null,
     *   'masa_netto'        => float|null,
     *   'plik_lieferschein' => string,   // nazwa pliku z Lieferschein
     *   'plik_masa'         => string,   // nazwa pliku z masą
     *   'zawartosc_masy'    => string,   // surowa zawartość pliku z masą (do zapisu na dysk)
     * ]
     */
    public function przetworzDwaBlobs(
        string $zawartosc1, string $nazwa1,
        string $zawartosc2, string $nazwa2,
    ): array {
        $tekst1 = $this->extractTextFromBlob($zawartosc1);
        $tekst2 = $this->extractTextFromBlob($zawartosc2);

        // Ustal który plik zawiera Lieferschein
        if ($this->zawieraLieferschein($tekst1)) {
            $tekstLieferschein = $tekst1;
            $tekstMasa = $tekst2;
            $plikLieferschein = $nazwa1;
            $plikMasa = $nazwa2;
            $zawartoscMasy = $zawartosc2;
        } elseif ($this->zawieraLieferschein($tekst2)) {
            $tekstLieferschein = $tekst2;
            $tekstMasa = $tekst1;
            $plikLieferschein = $nazwa2;
            $plikMasa = $nazwa1;
            $zawartoscMasy = $zawartosc1;
        } else {
            Log::error('PdfParserService: żaden z plików nie zawiera słowa "Lieferschein".');

            return [
                'lieferschein' => null,
                'masa_netto' => null,
                'plik_lieferschein' => $nazwa1,
                'plik_masa' => $nazwa2,
                'zawartosc_masy' => $zawartosc2,
            ];
        }

        return [
            'lieferschein' => $this->odczytajLieferschein($tekstLieferschein),
            'masa_netto' => $this->odczytajMaseNetto($tekstMasa),
            'plik_lieferschein' => $plikLieferschein,
            'plik_masa' => $plikMasa,
            'zawartosc_masy' => $zawartoscMasy,
        ];
    }

    /**
     * Parsuje Gewichtsmeldung – jeden plik PDF.
     * Wyciąga Lieferschein i Gewicht.
     *
     * Format:
     *   "Lieferschein: 600283211"
     *   "Gewicht: 19,340 t"
     *
     * Zwraca:
     * [
     *   'lieferschein' => string|null,
     *   'masa_netto'   => float|null,
     *   'plik'         => string,
     *   'zawartosc'    => string,
     * ]
     */
    public function przetworzGewichtsmeldung(string $zawartosc, string $nazwa): array
    {
        $tekst = $this->extractTextFromBlob($zawartosc);

        // Lieferschein: 600283211
        $lieferschein = null;
        if (preg_match('/Lieferschein:\s*(\d+)/u', $tekst, $matches)) {
            $lieferschein = trim($matches[1]);
        }

        // Gewicht: 19,340 t
        $masaNetto = null;
        if (preg_match('/Gewicht:\s*([\d,\.]+)\s*t/u', $tekst, $matches)) {
            $wartosc = str_replace(',', '.', trim($matches[1]));
            $masaNetto = floatval($wartosc) ?: null;
        }

        if (! $lieferschein) {
            Log::warning('PdfParserService Gewichtsmeldung: nie znaleziono numeru Lieferschein.');
        }
        if (! $masaNetto) {
            Log::warning('PdfParserService Gewichtsmeldung: nie znaleziono Gewicht.');
        }

        return [
            'lieferschein' => $lieferschein,
            'masa_netto' => $masaNetto,
            'plik' => $nazwa,
            'zawartosc' => $zawartosc,
        ];
    }
}
