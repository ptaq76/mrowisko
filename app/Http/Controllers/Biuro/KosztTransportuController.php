<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\KosztTransportu;
use App\Models\Przewoznik;
use Illuminate\Http\Request;

class KosztTransportuController extends Controller
{
    public function index()
    {
        $koszty = KosztTransportu::with(['start', 'stop', 'przewoznik'])
            ->orderBy('start_id')->orderBy('stop_id')->get();
        $przewoznicy = Przewoznik::where('is_active', true)->orderBy('nazwa')->get();
        $klienci = Client::where('is_active', true)->orderBy('short_name')->get();

        return view('biuro.koszty_transportu.index', compact('koszty', 'przewoznicy', 'klienci'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_id' => ['required', 'exists:clients,id'],
            'stop_id' => ['required', 'exists:clients,id'],
            'przewoznik_id' => ['nullable', 'exists:przewoznicy,id'],
            'cena_eur' => ['required', 'numeric', 'min:0'],
        ]);

        KosztTransportu::create($request->only('start_id', 'stop_id', 'przewoznik_id', 'cena_eur'));

        return response()->json(['success' => true]);
    }

    public function update(Request $request, KosztTransportu $kosztTransportu)
    {
        $request->validate([
            'start_id' => ['required', 'exists:clients,id'],
            'stop_id' => ['required', 'exists:clients,id'],
            'przewoznik_id' => ['nullable', 'exists:przewoznicy,id'],
            'cena_eur' => ['required', 'numeric', 'min:0'],
        ]);

        $kosztTransportu->update($request->only('start_id', 'stop_id', 'przewoznik_id', 'cena_eur', 'is_active'));

        return response()->json(['success' => true]);
    }

    public function destroy(KosztTransportu $kosztTransportu)
    {
        $kosztTransportu->delete();

        return response()->json(['success' => true]);
    }

    // CRUD przewoźników
    public function storePrzewoznik(Request $request)
    {
        $request->validate(['nazwa' => ['required', 'string', 'max:100']]);
        $p = Przewoznik::create(['nazwa' => $request->nazwa]);

        return response()->json(['success' => true, 'id' => $p->id, 'nazwa' => $p->nazwa]);
    }

    public function updatePrzewoznik(Request $request, Przewoznik $przewoznik)
    {
        $request->validate(['nazwa' => ['required', 'string', 'max:100']]);
        $przewoznik->update($request->only('nazwa', 'is_active'));

        return response()->json(['success' => true]);
    }

    public function destroyPrzewoznik(Przewoznik $przewoznik)
    {
        if ($przewoznik->koszty()->count()) {
            return response()->json(['success' => false, 'error' => 'Przewoźnik ma przypisane koszty.'], 422);
        }
        $przewoznik->delete();

        return response()->json(['success' => true]);
    }
}
