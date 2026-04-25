<?php

namespace App\Http\Controllers\Karchem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Karchem\KarchemKodyOdpadow;
use App\Models\Karchem\KarchemStanPoczatkowy;
use App\Models\Karchem\KarchemWysylki;
use App\Models\KarchemKlienci;
use App\Models\BdoKarty;

class KarchemMagazynController extends Controller
{
    public function index(Request $request)
    {
        // Domyślny rok to bieżący
        $rok = $request->get('rok', Carbon::now()->year);
        
        // Wybrany miesiąc (null = wszystkie)
        $selectedMiesiac = $request->get('miesiac');

        
        // 1. Pobierz stany początkowe dla wybranego roku
        $stanyPoczatkowe = KarchemStanPoczatkowy::where('rok', $rok)
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
            ->pluck('ilosc', 'kod');

        
        // 2. Pobierz dane z BDO (przyjęcia na magazyn)
        $nipy = KarchemKlienci::pluck('nip')->filter()->toArray();
        
        $bdoQuery = BdoKarty::select([
                'real_transport_date',
                'waste_code_and_description',
                'waste_mass'
            ])
            ->where('card_number', '!=', '')
            ->where('kpo_id', '!=', '')
            ->whereIn('card_status', ['Potwierdzenie transportu', 'Potwierdzenie przejęcia'])
            ->whereIn('sender_nip', $nipy)
            ->where('ewrant', 0)
            // Wykluczenia: jeśli carrier = EWRANT AND sender in (POCZTA..., Voigt...) -> wykluczamy
            ->whereNot(function ($q2) {
                $q2->where('carrier_name_or_first_name_and_last_name',
                           'EWRANT SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ SPÓŁKA KOMANDYTOWA')
                   ->whereIn('sender_name_or_first_name_and_last_name', [
                        'POCZTA POLSKA S.A.',
                        'Voigt Promotion sp. z o.o.'
                   ]);
            })
            ->whereYear('real_transport_date', $rok);
        
        // Filtruj po miesiącu jeśli wybrany
        if ($selectedMiesiac) {
            $bdoQuery->whereMonth('real_transport_date', $selectedMiesiac);
        }
        
        $bdoKarty = $bdoQuery->get();
        
        // Grupowanie BDO według kodu (pierwsze 8 znaków) i sumowanie wagi
        $bdoPoKodach = $bdoKarty->groupBy(function ($item) {
            return substr($item->waste_code_and_description, 0, 8);
        })->map(function ($items) {
            return $items->sum('waste_mass'); // waste_mass już w kg
        });

 
        // 3. Pobierz wysyłki
        $wysylkiQuery = KarchemWysylki::whereYear('data', $rok);
        
        if ($selectedMiesiac) {
            $wysylkiQuery->whereMonth('data', $selectedMiesiac);
        }

        $wysylki = $wysylkiQuery->get();

        // Grupowanie wysyłek według kodu (ilość jest już w kg)
        $wysylkiPoKodach = $wysylki->groupBy('kod')->map(function ($items) {
            return $items->sum('ilosc'); // pozostaw w kg
        });

  
        // 4. Pobierz wszystkie kody z sortowaniem
        $kody = KarchemKodyOdpadow::orderByRaw("
            CASE 
                WHEN kod = '15 01 01' THEN 0
                WHEN kod = '15 01 02' THEN 1
                WHEN kod = '15 01 03' THEN 2
                WHEN kod = '19 12 01' THEN 3
                WHEN kod = '03 03 08' THEN 4
                ELSE 5
            END, kod
        ")->get();

        // 5. Oblicz stan magazynu: stan początkowy + BDO - wysyłki
        $stanMagazynu = [];
        
        foreach ($kody as $kodObj) {
            $kod = $kodObj->kod;
            
            $stanPoczatkowy = $stanyPoczatkowe[$kod] ?? 0;
            $bdo = $bdoPoKodach[$kod] ?? 0;
            $wys = $wysylkiPoKodach[$kod] ?? 0;
            
            // Wszystko w kg
            $stanMagazynu[$kod] = $stanPoczatkowy + $bdo - $wys;
        }

        // Dostępne lata
        $lata = range(2024, 2027);

    // 6. Przygotuj szczegółowe dane dla każdego kodu
    $szczegolyDane = [];
    
    foreach ($kody as $kodObj) {
        $kod = $kodObj->kod;
        
        $stanPocz = $stanyPoczatkowe[$kod] ?? 0;
        $bdo = $bdoPoKodach[$kod] ?? 0;
        $wys = $wysylkiPoKodach[$kod] ?? 0;
        
        if ($selectedMiesiac) {
            // Dla wybranego miesiąca
            $szczegolyDane[$kod] = [
                'sumaBdo' => number_format($bdo, 3, ',', ' '),
                'sumaWysylek' => number_format($wys, 3, ',', ' '),
                'stanKoncowy' => number_format($bdo - $wys, 3, ',', ' ')
            ];
        } else {
            // Dla całego roku
            $szczegolyDane[$kod] = [
                'stanPoczatkowy' => number_format($stanPocz, 3, ',', ' '),
                'sumaBdo' => number_format($bdo, 3, ',', ' '),
                'sumaWysylek' => number_format($wys, 3, ',', ' '),
                'stanKoncowy' => number_format($stanPocz + $bdo - $wys, 3, ',', ' ')
            ];
        }
    }

    return view('karchem.magazyn', compact('stanMagazynu', 'rok', 'lata', 'selectedMiesiac', 'szczegolyDane'));

    }
}