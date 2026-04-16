<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\FuelVehicle;
use App\Models\FuelVehicleGroup;
use Illuminate\Http\Request;

class FuelVehicleController extends Controller
{
    public function index()
    {
        $groups = FuelVehicleGroup::with(['vehicles' => function ($q) {
            $q->orderBy('nazwa');
        }])->orderBy('id')->get();

        return view('biuro.fuel_vehicles.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nazwa' => ['required', 'string', 'max:100'],
            'grupa_id' => ['required', 'exists:fuel_vehicle_groups,id'],
        ]);

        FuelVehicle::create($request->only('nazwa', 'grupa_id'));

        return response()->json(['success' => true]);
    }

    public function update(Request $request, FuelVehicle $fuelVehicle)
    {
        $request->validate([
            'nazwa' => ['required', 'string', 'max:100'],
            'grupa_id' => ['required', 'exists:fuel_vehicle_groups,id'],
        ]);

        $fuelVehicle->update($request->only('nazwa', 'grupa_id', 'active'));

        return response()->json(['success' => true]);
    }

    public function destroy(FuelVehicle $fuelVehicle)
    {
        if ($fuelVehicle->transactions()->count()) {
            return response()->json(['success' => false, 'error' => 'Pojazd ma historię tankowań.'], 422);
        }
        $fuelVehicle->delete();

        return response()->json(['success' => true]);
    }

    public function toggle(FuelVehicle $fuelVehicle)
    {
        $fuelVehicle->update(['active' => ! $fuelVehicle->active]);

        return response()->json(['success' => true, 'active' => $fuelVehicle->active]);
    }
}
