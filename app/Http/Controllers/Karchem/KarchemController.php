<?php

namespace App\Http\Controllers\Karchem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\WarehouseItem;
use App\Models\Karchem\KarchemKodyOdpadow;
use App\Models\KarchemKlienci;
use App\Models\BdoKarty;
use App\Services\Bdo\BdoAuthService;
use App\Services\Bdo\BdoApiService;
use Mpdf\Mpdf;




class KarchemController extends Controller

{
        public function __construct(BdoAuthService $bdoAuthService)
    {
        $this->bdoAuthService = $bdoAuthService;
    }
    
    public function index()
    {

        return view('karchem/index');
    }




    public function show()
    {
        // Przykładowe dane — w praktyce pobierz z DB
        $data = [];

        return view('karchem.dokument', $data);
    }
    public function klienci()
    {
        $bdoSubquery = DB::table('bdo_karty')
            ->selectRaw('sender_nip, MAX(sender_name_or_first_name_and_last_name) as sender_name')
            ->groupBy('sender_nip');

        $klienci = KarchemKlienci::leftJoinSub($bdoSubquery, 'bk', function ($join) {
                $join->on('karchem_klienci.nip', '=', 'bk.sender_nip');
            })
            ->select(
                'karchem_klienci.*',
                DB::raw("REPLACE(REPLACE(COALESCE(NULLIF(karchem_klienci.nazwa, ''), bk.sender_name, ''), '\"', ''), \"'\", '') as sender_name")
            )
            ->orderBy('sender_name')
            ->get();

        return view('karchem.klienci', compact('klienci'));
    }

public function destroy($id)
{
    try {
        $klient = KarchemKlienci::findOrFail($id);
        $klient->delete();

        return redirect()->route('karchem.klienci')
            ->with('success', 'Klient został pomyślnie usunięty.');
    } catch (\Exception $e) {
        // Możesz zapisać log błędu, jeśli chcesz
        // Log::error($e->getMessage());

        return redirect()->route('karchem.klienci')
            ->withErrors(['error' => 'Wystąpił błąd podczas usuwania klienta.']);
    }
}


// WSPÓLNA METODA DLA PLAC i DRUKUJ
private function getPlacData(Request $request): array
{
    $selectedTowar = $request->input('towar');
    $selectedRok = $request->input('rok', date('Y'));
    $selectedMiesiac = $request->input('miesiac');

    $kodyOdpadow = [
        'KARCHEM Paski mix BELKA' => '150102',
        'KARCHEM Folia Biała Gruba BELKA' => '150102',
        'KARCHEM Folia Kolor Gruba BELKA' => '150102',
        'KARCHEM Folia Kolor BELKA' => '150102',
        'KARCHEM F1 (98/02) BELKA' => '150102',
        'KARCHEM ŚMIECI BELKA' => '000000',
        'KARCHEM BigBag BELKA' => '150102',
        'KARCHEM HDPE BELKA' => '150102',
        'KARCHEM PET BELKA'  => '150102',
        'KARCHEM BIGBAG BRĄZ BELKA'  => '150102',
        'KARCHEM PP BELKA'  => '150102',
        'KARCHEM TEKPOL BELKA' => '150102',
    ];

    $select_towar = WarehouseItem::whereHas('fraction', fn ($q) =>
            $q->where('name', 'like', '%KARCHEM%')
        )
        ->with('fraction')
        ->select('fraction_id')
        ->distinct()
        ->get()
        ->pluck('fraction.name', 'fraction_id')
        ->map(fn ($nazwa) =>
            trim(str_replace(['KARCHEM ', ' BELKA'], '', $nazwa))
        )
        ->filter();

    $produkcja = WarehouseItem::where('origin', 'production')
        ->whereHas('fraction', fn ($q) =>
            $q->where('name', 'like', '%KARCHEM%')
        )
        ->when($selectedTowar, fn($q) => $q->where('fraction_id', $selectedTowar))
        ->when($selectedRok, fn($q) => $q->whereYear('date', $selectedRok))
        ->when($selectedMiesiac, fn($q) => $q->whereMonth('date', $selectedMiesiac))
        ->with(['fraction', 'operator'])
        ->orderByDesc('id')
        ->get();

    $podsumowanieTowar = $produkcja
        ->groupBy('fraction.name')
        ->map->sum('weight_kg');

    $podsumowanieKod = $produkcja
        ->groupBy(fn ($item) => $kodyOdpadow[$item->fraction->name] ?? 'Nieznany')
        ->map->sum('weight_kg');

    return [
        'produkcja' => $produkcja,
        'select_towar' => $select_towar,
        'selectedTowar' => $selectedTowar,
        'selectedRok' => $selectedRok,
        'selectedMiesiac' => $selectedMiesiac,
        'kodyOdpadow' => $kodyOdpadow,
        'podsumowanieTowar' => $podsumowanieTowar,
        'podsumowanieKod' => $podsumowanieKod,
        'sumaWagaProdukcji' => $produkcja->sum('weight_kg'),
    ];
}

public function plac(Request $request)
{
    return view('karchem.plac', $this->getPlacData($request));
}

public function placDrukuj(Request $request)
{
    return view('karchem.placDrukuj', $this->getPlacData($request));
}






public function addNip(Request $request)
{
    $request->validate([
        'nip' => ['required', 'digits:10', 'unique:karchem_klienci,nip'],
    ]);

    $nip = $request->nip;

    $existsInBdo = \DB::table('bdo_karty')->where('sender_nip', $nip)->exists();

    if (!$existsInBdo) {
        return back()->withErrors(['nip' => 'NIP nie istnieje w KPO'])->withInput();
    }

    KarchemKlienci::create(['nip' => $nip]);

    return redirect()->route('karchem.klienci')->with('success', 'Nowy klient został dodany.');
}


public function bdo(Request $request)
{
    $nipy = KarchemKlienci::pluck('nip')->filter()->toArray();

    // Budowanie query
    $query = BdoKarty::where('card_number', '!=', '')
        ->where('kpo_id', '!=', '')
        ->whereNotIn('card_status', ['Potwierdzenie transportu', 'Wycofana'])
        ->whereIn('sender_nip', $nipy)
        ->where('ewrant', 0);

    // Filtr: nazwa przekazującego
    if ($request->filled('przekazujacy')) {
        $query->where('sender_name_or_first_name_and_last_name', 'LIKE', '%' . $request->przekazujacy . '%');
    }

    // Filtr: kod odpadu
    if ($request->filled('kod_odpadu')) {
        $query->where('waste_code_and_description', 'LIKE', $request->kod_odpadu . '%');
    }

    // Filtr: status
    if ($request->filled('status')) {
        $query->where('card_status', $request->status);
    }

    $karty = $query->orderByDesc('real_transport_time')->get();

    // Pobieranie unikalnych wartości do selectów
    $przekazujacy = BdoKarty::where('card_number', '!=', '')
        ->where('kpo_id', '!=', '')
        ->whereNotIn('card_status', ['Potwierdzenie transportu', 'Wycofana'])
        ->whereIn('sender_nip', $nipy)
        ->where('ewrant', '!=', 1)
        ->select('sender_name_or_first_name_and_last_name')
        ->distinct()
        ->orderBy('sender_name_or_first_name_and_last_name')
        ->pluck('sender_name_or_first_name_and_last_name')
        ->filter();

    $kodyOdpadow = BdoKarty::where('card_number', '!=', '')
        ->where('kpo_id', '!=', '')
        ->whereNotIn('card_status', ['Potwierdzenie transportu', 'Wycofana'])
        ->whereIn('sender_nip', $nipy)
        ->where('ewrant', '!=', 1)
        ->selectRaw('SUBSTRING(waste_code_and_description, 1, 8) as kod')
        ->distinct()
        ->orderBy('kod')
        ->pluck('kod')
        ->filter();

    $statusy = BdoKarty::where('card_number', '!=', '')
        ->where('kpo_id', '!=', '')
        ->whereNotIn('card_status', ['Potwierdzenie transportu', 'Wycofana'])
        ->whereIn('sender_nip', $nipy)
        ->where('ewrant', '!=', 1)
        ->select('card_status')
        ->distinct()
        ->orderBy('card_status')
        ->pluck('card_status')
        ->filter();

    return view('karchem.bdo', compact('karty', 'przekazujacy', 'kodyOdpadow', 'statusy'));
}

public function archiwum(Request $request)
{
    $nipy = KarchemKlienci::pluck('nip')->filter()->toArray();

    // Parametry z request (GET)
    $rok = $request->query('rok', date('Y'));        // domyślnie bieżący rok
    $miesiac = $request->query('miesiac', 'all');    // 'all' lub '1'..'12'
    $senderFilter = $request->query('sender', '');
    $carrierFilter = $request->query('carrier', '');
    $kodFilter = $request->query('kod', '');

    // Budujemy zapytanie z podstawowymi warunkami i wykluczeniami
    $q = BdoKarty::select([
            'kpo_id',
            'card_number',
            'real_transport_date',
            'real_transport_time',
            'sender_name_or_first_name_and_last_name',
            'carrier_name_or_first_name_and_last_name',
            'vehicle_reg_number',
            'waste_code_and_description',
            'waste_mass',
            'card_status'
        ])
        ->where('card_number', '!=', '')
        ->where('kpo_id', '!=', '')
        ->whereIn('card_status', ['Potwierdzenie transportu', 'Potwierdzenie przejęcia'])
        ->whereIn('sender_nip', $nipy)
        ->where('ewrant', '!=', 1)
        // Wykluczenia: jeśli carrier = EWRANT AND sender in (POCZTA..., Voigt...) -> wykluczamy
        ->whereNot(function ($q2) {
            $q2->where('carrier_name_or_first_name_and_last_name',
                       'EWRANT SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ SPÓŁKA KOMANDYTOWA')
               ->whereIn('sender_name_or_first_name_and_last_name', [
                    'POCZTA POLSKA S.A.',
                    'Voigt Promotion sp. z o.o.'
               ]);
        });

    // Filtr roku (jeśli podany)
    if (!empty($rok)) {
        $q->whereYear('real_transport_date', (int) $rok);
    }

    // Filtr miesiąca
    if ($miesiac !== 'all') {
        $m = (int) $miesiac;
        if ($m >= 1 && $m <= 12) {
            $q->whereMonth('real_transport_date', $m);
        }
    }

    // Filtry kolumnowe (exact match dla sender/carrier; kod - prefix match)
    if ($senderFilter !== '') {
        $q->where('sender_name_or_first_name_and_last_name', $senderFilter);
    }
    if ($carrierFilter !== '') {
        $q->where('carrier_name_or_first_name_and_last_name', $carrierFilter);
    }
    if ($kodFilter !== '') {
        $q->where('waste_code_and_description', 'like', $kodFilter . '%');
    }

    // Pobranie wyników
    $karty = $q->orderByRaw("card_status = 'Potwierdzenie przejęcia' ASC")
               ->orderByDesc('real_transport_date')
               ->get();

    $kartyCount = $karty->count();
               

    // ► Selecty MUSZĄ powstać z pobranych danych $karty (po filtrowaniu)
    $senders = $karty->pluck('sender_name_or_first_name_and_last_name')
        ->filter()
        ->unique()
        ->sort()
        ->values();

    $carriers = $karty->pluck('carrier_name_or_first_name_and_last_name')
        ->filter()
        ->unique()
        ->sort()
        ->values();

    $kody = $karty->pluck('waste_code_and_description')
        ->map(fn($v) => $v !== null ? mb_substr($v, 0, 8, 'UTF-8') : '')
        ->filter()
        ->unique()
        ->sort()
        ->values();

    // ► NOWE: Podsumowanie mas po kodach odpadu
    $podsumowanieKodow = $karty->groupBy(function($karta) {
        return mb_substr($karta->waste_code_and_description ?? '', 0, 8, 'UTF-8');
    })
    ->map(function($group) {
        return [
            'kod' => mb_substr($group->first()->waste_code_and_description ?? '', 0, 8, 'UTF-8'),
            'suma' => $group->sum('waste_mass')
        ];
    })
    ->sortBy(function($item) {
        // Priorytet dla kodów 15 01 01, 15 01 02, 15 01 03
        $kod = $item['kod'];
        if ($kod === '15 01 01') return '1';
        if ($kod === '15 01 02') return '2';
        if ($kod === '15 01 03') return '3';
        return '9' . $kod; // reszta alfabetycznie po prefiksie '9'
    })
    ->values();

return view('karchem.archiwum', compact('karty', 'kartyCount', 'rok', 'miesiac', 'senders', 'carriers', 'kody', 'podsumowanieKodow'));

}

public function preview_card($kpoId)
{
    $token = $this->bdoAuthService->generateToken();
    $card = $this->bdoAuthService->fetchCardByKpoId($token, $kpoId);

    if (!$card) {
        return response()->json([
            'success' => false,
            'message' => 'Nie znaleziono karty lub wystąpił błąd BDO',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'card' => $card
    ]);
}


public function generatePdf()
{
   $html = view('karchem.dokument')->render();
    
    try {
        $mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L',  // L = Landscape (lub dodaj orientation poniżej)
    'orientation' => 'L',  // 'L' = Landscape, 'P' = Portrait
    'margin_top' => 1,
    'margin_bottom' => 1,
    'margin_left' => 2,
    'margin_right' => 2,
    'tempDir' => storage_path('app/temp'),
    'autoScriptToLang' => true,
    'autoLangToFont' => true,
]);
        // Zapisz style oddzielnie
      // Zmień z public_path() na resource_path()
    $mpdf->WriteHTML(file_get_contents(resource_path('css/kpoPdf.css')), \Mpdf\HTMLParserMode::HEADER_CSS);
        
        // Potem HTML
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        
        return $mpdf->Output('kpo.pdf', 'D');
        
    } catch (\Mpdf\MpdfException $e) {
        dd($e->getMessage());
    }
}

public function generujPdfZDanymi(Request $request)
{
    $kpoId = $request->input('kpoId');
    
    Log::info('START generowania PDF', ['kpoId' => $kpoId]);
    
    try {
        // 1. Pobranie tokenu
        $auth = new BdoAuthService();
        $token = $auth->generateToken();

        if (!$token) {
            Log::error("Brak tokenu BDO dla generowania PDF", ['kpoId' => $kpoId]);
            return back()->with('error', 'Nie udało się uzyskać autoryzacji BDO.');
        }

        // 2. Stwórz instancję API
        $bdoService = new BdoApiService($token);
        
        Log::info('Token BDO uzyskany, pobieranie danych karty');
        
        // 3. Pobierz dane karty
        $karta = $bdoService->fetchCardDetails($kpoId);
        
        Log::info('Dane karty pobrane', ['karta' => $karta ? 'OK' : 'NULL']);
        
        if (!$karta) {
            Log::warning("Nie udało się pobrać danych z BDO dla KpoId: " . $kpoId);
            return back()->with('error', 'Nie udało się pobrać danych karty z systemu BDO.');
        }
        
        // 4. Przygotuj dodatkowe dane
        $daneDodatkowe = [
            'data_generowania' => now()->format('d.m.Y H:i:s'),
            'wygenerowane_przez' => auth()->user()->name ?? 'System',
        ];
        
        Log::info('Generowanie widoku HTML');
        
        // 5. Wygeneruj widok
        $html = view('karchem.dokument', [
            'karta' => $karta,
            'daneDodatkowe' => $daneDodatkowe,
        ])->render();
        
        Log::info('Widok wygenerowany, długość: ' . strlen($html));
        
        // 6. Konfiguracja mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'orientation' => 'L',
            'margin_top' => 1,
            'margin_bottom' => 1,
            'margin_left' => 2,
            'margin_right' => 2,
            'tempDir' => storage_path('app/temp'),
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);
        
        Log::info('mPDF utworzony');
        
        // 7. Dodaj CSS
        $cssPath = resource_path('css/kpoPdf.css');
        if (file_exists($cssPath)) {
            $mpdf->WriteHTML(
                file_get_contents($cssPath), 
                \Mpdf\HTMLParserMode::HEADER_CSS
            );
            Log::info('CSS dodany');
        }
        
        // 8. Dodaj HTML
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        
        Log::info('HTML dodany do PDF');
        
        // 9. Wygeneruj nazwę pliku
        $numerKarty = $karta['numer_karty'] ?? 'karta';
        $filename = 'KPO_' . str_replace('/', '_', $numerKarty) . '_' . now()->format('Y-m-d_His') . '.pdf';
        
        Log::info('Generowanie PDF, nazwa: ' . $filename);
        
        // 10. Wygeneruj PDF
        $pdfContent = $mpdf->Output('', 'S');
        
        Log::info('PDF wygenerowany, rozmiar: ' . strlen($pdfContent) . ' bajtów');
        
        // 11. Zwróć PDF jako download
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
    } catch (\Mpdf\MpdfException $e) {
        Log::error('Błąd mPDF: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return back()->with('error', 'Błąd generowania PDF: ' . $e->getMessage());
    } catch (\Exception $e) {
        Log::error('Błąd generowania PDF: ' . $e->getMessage(), [
            'kpoId' => $kpoId,
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Błąd: ' . $e->getMessage());
    }
}


public function doEwrant(Request $request)
{
    try {
        $kpo_id = $request->input('kpoIdE');
        
        $updated = BdoKarty::where('kpo_id', $kpo_id)->update(['ewrant' => 1]);
        
        if ($updated) {
            return redirect()->back()->with('success', 'Karta została przeniesiona do Ewrant.');
        } else {
            return redirect()->back()->with('error', 'Nie znaleziono karty o podanym kpo_id.');
        }
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Błąd podczas aktualizacji karty: ' . $e->getMessage());
    }
}


}

   


