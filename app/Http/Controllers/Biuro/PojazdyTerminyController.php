<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\PojazdTermin;
use App\Models\PojazdTerminAkcja;
use Illuminate\Http\Request;

class PojazdyTerminyController extends Controller
{
    public function index(Request $request)
    {
        // Zbliżające się terminy (do 30 dni) + przeterminowane
        $upcoming = PojazdTerminAkcja::with('pojazd')
            ->whereNotNull('deadline_date')
            ->where('deadline_date', '<=', now()->addDays(30))
            ->orderBy('deadline_date')
            ->get();

        // Wszystkie akcje z filtrem
        $query = PojazdTerminAkcja::with('pojazd')->orderByDesc('deadline_date');

        if ($request->filled('pojazd_id')) {
            $query->where('pojazd_id', $request->pojazd_id);
        }
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        $all = $query->get();

        // Dane do formularza
        $pojazdy = PojazdTermin::orderBy('nr_rej')->get();
        $actionTypes = PojazdTerminAkcja::select('action_type')
            ->distinct()
            ->orderBy('action_type')
            ->pluck('action_type');

        return view('biuro.pojazdy_terminy.index', compact(
            'upcoming', 'all', 'pojazdy', 'actionTypes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pojazd_id' => ['required', 'exists:pojazdy_terminy,id'],
            'action_type' => ['required', 'string', 'max:100'],
            'deadline_date' => ['nullable', 'date'],
            'completed_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ], [
            'pojazd_id.required' => 'Wybierz pojazd.',
            'action_type.required' => 'Podaj typ akcji.',
        ]);

        PojazdTerminAkcja::create($request->only(
            'pojazd_id', 'action_type', 'deadline_date', 'completed_date', 'notes'
        ));

        return response()->json(['success' => true]);
    }

    public function update(Request $request, PojazdTerminAkcja $akcja)
    {
        $request->validate([
            'pojazd_id' => ['required', 'exists:pojazdy_terminy,id'],
            'action_type' => ['required', 'string', 'max:100'],
            'deadline_date' => ['nullable', 'date'],
            'completed_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $akcja->update($request->only(
            'pojazd_id', 'action_type', 'deadline_date', 'completed_date', 'notes'
        ));

        return response()->json(['success' => true]);
    }

    public function storePojazd(Request $request)
    {
        $request->validate([
            'nr_rej' => ['required', 'string', 'max:20', 'unique:pojazdy_terminy,nr_rej'],
            'rodzaj' => ['required', 'string', 'max:50'],
            'marka' => ['required', 'string', 'max:50'],
        ]);

        PojazdTermin::create($request->only('nr_rej', 'rodzaj', 'marka', 'wlasciciel', 'vin', 'rok_prod', 'opis'));

        return response()->json(['success' => true]);
    }

    public function updatePojazd(Request $request, PojazdTermin $pojazd)
    {
        $request->validate([
            'nr_rej' => ['required', 'string', 'max:20'],
            'rodzaj' => ['required', 'string', 'max:50'],
            'marka' => ['required', 'string', 'max:50'],
        ]);

        $pojazd->update($request->only('nr_rej', 'rodzaj', 'marka', 'wlasciciel', 'vin', 'rok_prod', 'opis'));

        return response()->json(['success' => true]);
    }

    public function destroy(PojazdTerminAkcja $akcja)
    {
        $akcja->delete();

        return response()->json(['success' => true]);
    }
}
