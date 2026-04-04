<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Importer;
use App\Models\LsGoods;
use App\Models\Order;
use App\Models\WasteCode;
use App\Models\WysylkaCena;
use App\Models\WysylkaTransport;
use App\Models\KosztTransportu;
use App\Models\Przewoznik;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RaportWysylekController extends Controller
{
    public function saveTransport(\Illuminate\Http\Request $request, \App\Models\Order $order)
    {
        $request->validate([
            'cena_eur'      => ['nullable', 'numeric', 'min:0'],
            'przewoznik_id' => ['nullable', 'exists:przewoznicy,id'],
        ]);

        WysylkaTransport::updateOrCreate(
            ['order_id' => $order->id],
            [
                'cena_eur'      => $request->cena_eur,
                'przewoznik_id' => $request->przewoznik_id,
                'recznie'       => true,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function saveCena(\Illuminate\Http\Request $request, \App\Models\Order $order)
    {
        $request->validate(['cena_eur' => ['nullable', 'numeric', 'min:0']]);

        WysylkaCena::updateOrCreate(
            ['order_id' => $order->id],
            ['cena_eur' => $request->cena_eur]
        );

        return response()->json(['success' => true]);
    }

    public function saveCenaBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'cena_eur'  => ['nullable', 'numeric', 'min:0'],
            'order_ids' => ['required', 'array'],
            'order_ids.*' => ['integer', 'exists:orders,id'],
        ]);

        foreach ($request->order_ids as $orderId) {
            WysylkaCena::updateOrCreate(
                ['order_id' => $orderId],
                ['cena_eur' => $request->cena_eur]
            );
        }

        return response()->json(['success' => true, 'updated' => count($request->order_ids)]);
    }

    public function index(Request $request)
    {
        // Miesiąc – domyślnie bieżący
        $miesiac = $request->input('miesiac', Carbon::now()->format('Y-m'));
        $tydzien = $request->input('tydzien');

        $miesiacCarbon = Carbon::parse($miesiac . '-01');

        // Tygodnie w wybranym miesiącu
        $tygodnieWMiesiacu = $this->tygodnieWMiesiacu($miesiacCarbon);

        // Filtr dat
        if ($request->filled('date_from')) {
            $query->whereDate('planned_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('planned_date', '<=', $request->date_to);
        }

        // Filtry słownikowe
        $filtImporter  = $request->input('importer_id');
        $filtGoods     = $request->input('goods_id');
        $filtWasteCode = $request->input('waste_code_id');

        // Zapytanie – tylko wysyłki zagraniczne (mają LS z importerem)
        $query = Order::with([
            'lieferschein.importer',
            'lieferschein.goods',
            'lieferschein.wasteCode',
            'client',
            'warehouseLoadingItems',
            'wysylkaCena',
            'wysylkaTransport.przewoznik',
        ])
        ->where('type', 'sale')
        ->whereNotNull('lieferschein_id')
        ->whereHas('lieferschein', fn($q) => $q->whereNotNull('importer_id'))
        ->whereIn('status', ['loaded', 'weighed', 'closed', 'delivered'])
        ->whereYear('planned_date', $miesiacCarbon->year)
        ->whereMonth('planned_date', $miesiacCarbon->month);

        // Filtr tygodnia
        if ($tydzien) {
            $query->whereRaw('WEEK(planned_date, 3) = ?', [$tydzien]);
        }

        // Filtry słownikowe
        if ($filtImporter) {
            $query->whereHas('lieferschein', fn($q) => $q->where('importer_id', $filtImporter));
        }
        if ($filtGoods) {
            $query->whereHas('lieferschein', fn($q) => $q->where('goods_id', $filtGoods));
        }
        if ($filtWasteCode) {
            $query->whereHas('lieferschein', fn($q) => $q->where('waste_code_id', $filtWasteCode));
        }

        $wysylki = $query->orderBy('planned_date')->get();

        // Pobierz numery LS dla powiązania z reklamacjami/gewichtsmeldung
        $lsIds = $wysylki->pluck('lieferschein_id')->filter()->unique();
        $dokumenty = \App\Models\Reklamacja::whereIn('lieferschein_id', $lsIds)
            ->orderBy('mail_date')
            ->get()
            ->groupBy('lieferschein_id');

        // Słowniki do filtrów
        $importerzy = Importer::where('is_active', true)->orderBy('name')->get();
        $przewoznicy = Przewoznik::where('is_active', true)->orderBy('nazwa')->get();
        $towary     = LsGoods::where('is_active', true)->orderBy('name')->get();
        $kodyOdpadow = WasteCode::where('is_active', true)->orderBy('code')->get();

        // Miesiące do nawigacji (ostatnie 11 + bieżący, bieżący ostatni)
        $miesiace = collect(range(-11, 0))->map(fn($i) => Carbon::now()->startOfMonth()->addMonths($i));

        // Automatyczne dopasowanie kosztu transportu
        $this->dopasujKosztyTransportu($wysylki);

        return view('biuro.reports.wysylki', compact(
            'wysylki', 'dokumenty', 'miesiac', 'miesiacCarbon',
            'tydzien', 'tygodnieWMiesiacu',
            'importerzy', 'towary', 'kodyOdpadow',
            'filtImporter', 'filtGoods', 'filtWasteCode',
            'miesiace', 'przewoznicy'
        ));
    }

    private function dopasujKosztyTransportu($wysylki): void
    {
        foreach ($wysylki as $w) {
            // Pomiń jeśli już ma ręcznie wpisany koszt
            if ($w->wysylkaTransport && $w->wysylkaTransport->recznie) {
                continue;
            }

            // Szukaj dopasowania: start = start_client z zlecenia, stop = klient LS
            $startId = $w->start_client_id;
            $stopId  = $w->client_id;

            if (!$startId || !$stopId) continue;

            $koszt = KosztTransportu::where('start_id', $startId)
                ->where('stop_id', $stopId)
                ->where('is_active', true)
                ->first();

            if (!$koszt) continue;

            // Zapisz automatycznie jeśli nie istnieje lub różni się
            $existing = $w->wysylkaTransport;
            if (!$existing) {
                WysylkaTransport::create([
                    'order_id'      => $w->id,
                    'przewoznik_id' => $koszt->przewoznik_id,
                    'cena_eur'      => $koszt->cena_eur,
                    'recznie'       => false,
                ]);
                $w->load('wysylkaTransport.przewoznik');
            } elseif (!$existing->recznie && $existing->cena_eur != $koszt->cena_eur) {
                $existing->update([
                    'przewoznik_id' => $koszt->przewoznik_id,
                    'cena_eur'      => $koszt->cena_eur,
                ]);
                $w->load('wysylkaTransport.przewoznik');
            }
        }
    }

    private function tygodnieWMiesiacu(Carbon $miesiac): array
    {
        $tygodnie = [];
        $dzien = $miesiac->copy()->startOfMonth();
        $koniec = $miesiac->copy()->endOfMonth();

        while ($dzien->lte($koniec)) {
            $nr = $dzien->isoWeek;
            if (!in_array($nr, $tygodnie)) {
                $tygodnie[] = $nr;
            }
            $dzien->addDay();
        }

        return $tygodnie;
    }
}
