<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleSet;
use Illuminate\Http\Request;

class VehicleSetController extends Controller
{
    public function index()
    {
        $sets     = VehicleSet::with(['tractor', 'trailer'])
            ->orderBy('label')
            ->get();

        $tractors = Vehicle::where('type', 'ciągnik')
            ->where('is_active', true)
            ->where('plate', '!=', 'ZS438MG')
            ->where('plate', 'not like', '%ZEWN%')
            ->orderBy('plate')
            ->get();

        $trailers = Vehicle::where('type', 'naczepa')
            ->where('is_active', true)
            ->where('plate', 'not like', '%ZEWN%')
            ->orderBy('plate')
            ->get();

        return view('biuro.vehicle_sets.index', compact('sets', 'tractors', 'trailers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label'   => ['required', 'string', 'max:100'],
            'tare_kg' => ['required', 'numeric', 'min:0'],
        ], [
            'label.required'   => 'Podaj etykietę zestawu.',
            'tare_kg.required' => 'Podaj tarę.',
        ]);

        VehicleSet::create([
            'label'      => $request->label,
            'tractor_id' => $request->tractor_id ?: null,
            'trailer_id' => $request->trailer_id ?: null,
            'tare_kg'    => $request->tare_kg,
            'is_active'  => true,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, VehicleSet $vehicleSet)
    {
        $request->validate([
            'label'   => ['required', 'string', 'max:100'],
            'tare_kg' => ['required', 'numeric', 'min:0'],
        ]);

        $vehicleSet->update([
            'label'      => $request->label,
            'tractor_id' => $request->tractor_id ?: null,
            'trailer_id' => $request->trailer_id ?: null,
            'tare_kg'    => $request->tare_kg,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(VehicleSet $vehicleSet)
    {
        $vehicleSet->delete();
        return response()->json(['success' => true]);
    }
}