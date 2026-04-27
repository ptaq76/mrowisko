<?php

namespace App\Http\Controllers\Plac;

use App\Http\Controllers\Controller;
use App\Models\FuelTransaction;
use App\Models\FuelVehicle;
use App\Models\FuelVehicleGroup;
use Illuminate\Http\Request;

class FuelController extends Controller
{
    public function index()
    {
        $level = FuelTransaction::currentLevel();
        $transactions = FuelTransaction::with('vehicle.group')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        // Grupy z pojazdami – do selecta grupowanego
        $groups = FuelVehicleGroup::with(['vehicles' => function ($q) {
            $q->where('active', true)->orderBy('nazwa');
        }])->where('active', true)->orderBy('id')->get();

        // Ostatni przebieg per pojazd (dla pojazdów z tracks_mileage)
        $lastMileage = FuelTransaction::whereNotNull('mileage')
            ->whereNotNull('fuel_vehicle_id')
            ->selectRaw('fuel_vehicle_id, MAX(mileage) as max_mileage')
            ->groupBy('fuel_vehicle_id')
            ->pluck('max_mileage', 'fuel_vehicle_id');

        return view('plac.fuel', compact('level', 'transactions', 'groups', 'lastMileage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:tankowanie,dostawa,inwentaryzacja'],
            'liters' => ['required', 'integer', 'min:1'],
            'fuel_vehicle_id' => ['nullable', 'exists:fuel_vehicles,id'],
            'mileage' => ['nullable', 'integer', 'min:1'],
            'full_tank' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'liters.required' => 'Podaj liczbę litrów.',
            'liters.min' => 'Minimum 1 litr.',
        ]);

        $current = FuelTransaction::currentLevel();
        $liters = (int) $request->liters;
        $type = $request->type;
        $mileage = null;
        $fullTank = null;

        if ($type === 'tankowanie' && $liters > $current) {
            return response()->json([
                'success' => false,
                'error' => "Niewystarczająca ilość paliwa. Stan: {$current} L, żądano: {$liters} L.",
            ], 422);
        }

        if ($type === 'tankowanie' && $request->fuel_vehicle_id) {
            $vehicle = FuelVehicle::find($request->fuel_vehicle_id);
            if ($vehicle && $vehicle->tracks_mileage) {
                if (! $request->filled('mileage')) {
                    return response()->json([
                        'success' => false,
                        'error' => "Pojazd {$vehicle->nazwa} wymaga podania przebiegu.",
                    ], 422);
                }
                $mileage = (int) $request->mileage;
                $fullTank = (bool) $request->boolean('full_tank');

                $lastMileage = FuelTransaction::where('fuel_vehicle_id', $vehicle->id)
                    ->whereNotNull('mileage')
                    ->max('mileage');
                if ($lastMileage !== null && $mileage < $lastMileage) {
                    return response()->json([
                        'success' => false,
                        'error' => "Podany przebieg ({$mileage} km) jest mniejszy od ostatnio zapisanego ({$lastMileage} km) dla pojazdu {$vehicle->nazwa}.",
                    ], 422);
                }
            }
        }

        $tankAfter = match ($type) {
            'dostawa' => $current + $liters,
            'tankowanie' => max(0, $current - $liters),
            'inwentaryzacja' => $liters,
        };

        FuelTransaction::create([
            'type' => $type,
            'liters' => $liters,
            'tank_after' => $tankAfter,
            'mileage' => $mileage,
            'full_tank' => $fullTank,
            'fuel_vehicle_id' => $type === 'tankowanie' ? $request->fuel_vehicle_id : null,
            'operator' => auth()->user()->name ?? auth()->id(),
            'notes' => $request->notes,
        ]);

        return response()->json(['success' => true, 'tank_after' => $tankAfter, 'type' => $type]);
    }

    public function destroy(FuelTransaction $transaction)
    {
        $last = FuelTransaction::latest()->first();
        if (! $last || $last->id !== $transaction->id) {
            return response()->json(['success' => false, 'error' => 'Można cofnąć tylko ostatnią transakcję.'], 422);
        }
        $transaction->delete();

        return response()->json(['success' => true, 'tank_after' => FuelTransaction::currentLevel()]);
    }
}
