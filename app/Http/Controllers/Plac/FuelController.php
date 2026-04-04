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
        $level        = FuelTransaction::currentLevel();
        $transactions = FuelTransaction::with('vehicle.group')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        // Grupy z pojazdami – do selecta grupowanego
        $groups = FuelVehicleGroup::with(['vehicles' => function ($q) {
            $q->where('active', true)->orderBy('nazwa');
        }])->where('active', true)->orderBy('id')->get();

        return view('plac.fuel', compact('level', 'transactions', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'            => ['required', 'in:tankowanie,dostawa,inwentaryzacja'],
            'liters'          => ['required', 'integer', 'min:1'],
            'fuel_vehicle_id' => ['nullable', 'exists:fuel_vehicles,id'],
            'notes'           => ['nullable', 'string', 'max:255'],
        ], [
            'liters.required' => 'Podaj liczbę litrów.',
            'liters.min'      => 'Minimum 1 litr.',
        ]);

        $current = FuelTransaction::currentLevel();
        $liters  = (int) $request->liters;
        $type    = $request->type;

        if ($type === 'tankowanie' && $liters > $current) {
            return response()->json([
                'success' => false,
                'error'   => "Niewystarczająca ilość paliwa. Stan: {$current} L, żądano: {$liters} L.",
            ], 422);
        }

        $tankAfter = match($type) {
            'dostawa'        => $current + $liters,
            'tankowanie'     => max(0, $current - $liters),
            'inwentaryzacja' => $liters,
        };

        FuelTransaction::create([
            'type'            => $type,
            'liters'          => $liters,
            'tank_after'      => $tankAfter,
            'fuel_vehicle_id' => $type === 'tankowanie' ? $request->fuel_vehicle_id : null,
            'operator'        => auth()->user()->name ?? auth()->id(),
            'notes'           => $request->notes,
        ]);

        return response()->json(['success' => true, 'tank_after' => $tankAfter, 'type' => $type]);
    }

    public function destroy(FuelTransaction $transaction)
    {
        $last = FuelTransaction::latest()->first();
        if (!$last || $last->id !== $transaction->id) {
            return response()->json(['success' => false, 'error' => 'Można cofnąć tylko ostatnią transakcję.'], 422);
        }
        $transaction->delete();
        return response()->json(['success' => true, 'tank_after' => FuelTransaction::currentLevel()]);
    }
}
