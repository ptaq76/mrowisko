<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Zadanie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ZadanieController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'tresc' => ['required', 'string', 'max:1000'],
            'data' => ['required', 'date'],
            'target' => ['required', 'in:driver,plac,all_drivers'],
            'driver_id' => ['required_if:target,driver', 'nullable', 'exists:drivers,id'],
        ]);

        $userId = auth()->user()->id;

        if ($data['target'] === 'driver') {
            Zadanie::create([
                'tresc' => $data['tresc'],
                'data' => $data['data'],
                'target' => 'driver',
                'driver_id' => $data['driver_id'],
                'created_by_user_id' => $userId,
            ]);
        } elseif ($data['target'] === 'plac') {
            Zadanie::create([
                'tresc' => $data['tresc'],
                'data' => $data['data'],
                'target' => 'plac',
                'driver_id' => null,
                'created_by_user_id' => $userId,
            ]);
        } else {
            $batchId = (string) Str::uuid();
            $drivers = Driver::where('is_active', true)->get();

            foreach ($drivers as $d) {
                Zadanie::create([
                    'tresc' => $data['tresc'],
                    'data' => $data['data'],
                    'target' => 'all_drivers',
                    'driver_id' => $d->id,
                    'batch_id' => $batchId,
                    'created_by_user_id' => $userId,
                ]);
            }
        }

        return back()->with('success', 'Zadanie utworzone.');
    }

    public function update(Request $request, Zadanie $zadanie)
    {
        $data = $request->validate([
            'tresc' => ['required', 'string', 'max:1000'],
            'data' => ['required', 'date'],
        ]);

        if ($zadanie->batchHasDone()) {
            return back()->withErrors(['edit' => 'Nie można edytować — zadanie częściowo lub w pełni wykonane.']);
        }

        if ($zadanie->batch_id) {
            Zadanie::where('batch_id', $zadanie->batch_id)
                ->update(['tresc' => $data['tresc'], 'data' => $data['data']]);
        } else {
            $zadanie->update($data);
        }

        return back()->with('success', 'Zadanie zaktualizowane.');
    }

    public function destroy(Zadanie $zadanie)
    {
        if ($zadanie->batch_id) {
            Zadanie::where('batch_id', $zadanie->batch_id)
                ->where('status', 'pending')
                ->delete();
        } else {
            if ($zadanie->isDone()) {
                return back()->withErrors(['delete' => 'Nie można anulować wykonanego zadania.']);
            }
            $zadanie->delete();
        }

        return back()->with('success', 'Zadanie anulowane.');
    }

    public function complete(Zadanie $zadanie)
    {
        if ($zadanie->isDone()) {
            return back()->withErrors(['complete' => 'Zadanie już wykonane.']);
        }

        $routeName = Route::currentRouteName();
        $userDriverId = Driver::where('user_id', auth()->user()->id)->value('id');

        if ($routeName === 'kierowca.zadania.complete') {
            if ($zadanie->driver_id !== $userDriverId) {
                abort(403);
            }
        } elseif ($routeName === 'plac.zadania.complete') {
            if ($zadanie->driver_id !== null) {
                abort(403);
            }
        }

        $zadanie->update([
            'status' => 'done',
            'completed_at' => now(),
            'completed_by_user_id' => auth()->user()->id,
        ]);

        return back();
    }

    public function raporty(Request $request)
    {
        $query = Zadanie::with(['driver', 'creator', 'completer']);

        if ($request->filled('search')) {
            $query->where('tresc', 'like', '%'.$request->input('search').'%');
        }

        $scope = $request->input('scope', 'all');
        if ($scope === 'plac') {
            $query->whereNull('driver_id');
        } elseif ($scope === 'driver' && $request->filled('driver_id')) {
            $query->where('driver_id', $request->input('driver_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('data_from')) {
            $query->whereDate('data', '>=', $request->input('data_from'));
        }
        if ($request->filled('data_to')) {
            $query->whereDate('data', '<=', $request->input('data_to'));
        }

        $zadania = $query->orderByDesc('data')->orderBy('id')->paginate(50)->withQueryString();
        $drivers = Driver::orderBy('name')->get();

        return view('biuro.reports.zadania', compact('zadania', 'drivers'));
    }
}
