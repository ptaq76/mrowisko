<?php

namespace App\Http\Controllers\Karchem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Karchem\KarchemStanPoczatkowy;
use App\Models\Karchem\KarchemKodyOdpadow;

class StanyPoczatkoweController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function index(Request $request)
{
    // domyślny rok to bieżący
    $rok = $request->get('rok', Carbon::now()->year);

    // pobranie stanów dla wybranego roku
$stany = KarchemStanPoczatkowy::where('rok', $rok)
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
    ->get();



    // pobranie listy kodów odpadów (do formularza Dodaj)
    $kody = KarchemKodyOdpadow::orderBy('kod')->get();

    // dostępne lata
    $lata = range(2024, 2027);

    return view('karchem.stanyPoczatkowe', compact('stany', 'rok', 'kody', 'lata'));
}


   public function store(Request $request)
{
    $request->validate([
        'rok' => 'required|integer|min:2023|max:2030',
        'kody' => 'required|array',
        'wartosci' => 'required|array',
    ]);

    $rok = $request->rok;

    // Sprawdź czy już istnieją wpisy dla roku
    $istnieje = DB::table('karchem_stany_poczatkowe')
        ->where('rok', $rok)
        ->exists();

    if ($istnieje) {
        return back()->with('error', 'Dla tego roku istnieją już stany początkowe.');
    }

    $wstaw = [];

    foreach ($request->kody as $i => $kod) {
        $wstaw[] = [
            'rok' => $rok,
            'kod' => $kod,
            'ilosc' => $request->wartosci[$i] ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('karchem_stany_poczatkowe')->insert($wstaw);

    return back()->with('success', 'Stany początkowe zapisane.');
}


public function update(Request $request, $id)
{
    $request->validate([
        'ilosc' => 'required|numeric|min:0'
    ]);

    $stan = KarchemStanPoczatkowy::findOrFail($id);

    $stan->ilosc = $request->ilosc;
    $stan->save();

    return response()->json(['success' => true]);
}


}
