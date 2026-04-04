<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index()
    {
        $tractors = Vehicle::tractors()->orderBy('plate')->get();
        $trailers = Vehicle::trailers()->orderBy('plate')->get();
        $solos    = Vehicle::solo()->orderBy('plate')->get();

        return view('biuro.vehicles.index', compact('tractors', 'trailers', 'solos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate'   => ['required', 'string', 'max:20', 'unique:vehicles,plate'],
            'type'    => ['required', Rule::in(['ciągnik', 'naczepa', 'solo'])],
            'subtype' => ['nullable', Rule::in(['hakowiec', 'firana', 'walking_floor'])],
            'brand'   => ['nullable', 'string', 'max:100'],
            'tare_kg' => ['required', 'numeric', 'min:0'],
        ], [
            'plate.required' => 'Podaj numer rejestracyjny.',
            'plate.unique'   => 'Ten numer rejestracyjny już istnieje.',
            'type.required'  => 'Wybierz typ pojazdu.',
            'tare_kg.required' => 'Podaj tarę pojazdu.',
        ]);

        Vehicle::create($request->all());

        return redirect()->route('biuro.vehicles.index')
            ->with('success', 'Pojazd został dodany.');
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'plate'   => ['required', 'string', 'max:20', Rule::unique('vehicles', 'plate')->ignore($vehicle->id)],
            'type'    => ['required', Rule::in(['ciągnik', 'naczepa', 'solo'])],
            'subtype' => ['nullable', Rule::in(['hakowiec', 'firana', 'walking_floor'])],
            'brand'   => ['nullable', 'string', 'max:100'],
            'tare_kg' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $vehicle->update(array_merge(
            $request->all(),
            ['is_active' => $request->boolean('is_active')]
        ));

        return redirect()->route('biuro.vehicles.index')
            ->with('success', 'Pojazd został zaktualizowany.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->update(['is_active' => false]);
        return redirect()->route('biuro.vehicles.index')
            ->with('success', 'Pojazd został dezaktywowany.');
    }
}
