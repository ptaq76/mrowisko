<?php

namespace App\Http\Controllers\Karchem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Karchem\KarchemWysylki;
use App\Models\Karchem\KarchemKodyOdpadow;

class KarchemWysylkiController extends Controller
{
    public function index(Request $request)
{
    // Domyślny rok to bieżący
    $rok = $request->get('rok', Carbon::now()->year);
    
    // Wybrany miesiąc (null = wszystkie)
    $selectedMiesiac = $request->get('miesiac');
    
    // Wybrani: klient i kod
    $selectedKlient = $request->get('klient');
    $selectedKod = $request->get('kod_filter');

    // Query builder
    $query = KarchemWysylki::whereYear('data', $rok);

    // Filtrowanie po miesiącu, jeśli wybrano
    if ($selectedMiesiac) {
        $query->whereMonth('data', $selectedMiesiac);
    }

    // Filtrowanie po kliencie
    if ($selectedKlient) {
        $query->where('klient', $selectedKlient);
    }

    // Filtrowanie po kodzie odpadu
    if ($selectedKod) {
        $query->where('kod', $selectedKod);
    }

    $wysylki = $query->orderBy('data', 'desc')->get();

    // Podsumowanie wg kodów odpadu z sortowaniem (z uwzględnieniem filtrów)
    $podsumowanieKod = KarchemWysylki::whereYear('data', $rok)
        ->when($selectedMiesiac, function($q) use ($selectedMiesiac) {
            return $q->whereMonth('data', $selectedMiesiac);
        })
        ->when($selectedKlient, function($q) use ($selectedKlient) {
            return $q->where('klient', $selectedKlient);
        })
        ->when($selectedKod, function($q) use ($selectedKod) {
            return $q->where('kod', $selectedKod);
        })
        ->selectRaw('kod, SUM(ilosc) as suma')
        ->groupBy('kod')
        ->orderByRaw("
            CASE 
                WHEN kod = '15 01 01' THEN 0
                WHEN kod = '15 01 02' THEN 1
                WHEN kod = '15 01 03' THEN 2
                WHEN kod = '19 12 01' THEN 3
                WHEN kod = '03 03 08' THEN 4
                ELSE 5
            END, kod
        ")
        ->get()
        ->pluck('suma', 'kod');

    // Pobranie listy kodów odpadów
    $kody = KarchemKodyOdpadow::orderBy('kod')->get();

    // Lista unikalnych klientów (dla selecta)
    $klienci = KarchemWysylki::whereYear('data', $rok)
        ->distinct()
        ->orderBy('klient')
        ->pluck('klient');

    // Dostępne lata
    $lata = range(2024, 2027);

    return view('karchem.wysylki', compact(
        'wysylki', 
        'rok', 
        'kody', 
        'lata', 
        'selectedMiesiac', 
        'podsumowanieKod',
        'klienci',
        'selectedKlient',
        'selectedKod'
    ));
}

public function store(Request $request)
{
    // Walidacja danych
$validated = $request->validate([
    'data' => 'required|date|after_or_equal:2024-01-01|before_or_equal:2028-12-31',
    'kod' => 'required|string|exists:karchem_kody_odpadow,kod',
    'ilosc' => 'required|numeric|min:0.001', // ilość większa od 0
    'klient' => 'required|string|max:255',
]);


// Tworzenie rekordu
KarchemWysylki::create([
    'data'   => $validated['data'], // dokładna data z formularza
    'kod'    => $validated['kod'],
    'ilosc'  => $validated['ilosc'],
    'klient' => $validated['klient'],
]);



    return redirect()->back()->with('success', 'Wysyłka dodana pomyślnie!');
}

public function update(Request $request, $id)
{
    // Walidacja danych
    $validated = $request->validate([
        'data' => 'required|date',
        'kod' => 'required|string|max:10',
        'ilosc' => 'required|numeric|min:0.01',
        'klient' => 'required|string|max:255',
    ]);

    // Pobranie wysyłki po ID
    $wysylka = KarchemWysylki::findOrFail($id);

    // Aktualizacja danych
    $wysylka->update([
        'data' => $validated['data'],
        'kod' => $validated['kod'],
        'ilosc' => $validated['ilosc'],
        'klient' => $validated['klient'],
    ]);

    // Odpowiedź AJAX
    return response()->json([
        'success' => true,
        'message' => 'Wysyłka zaktualizowana poprawnie'
    ]);
}

}
