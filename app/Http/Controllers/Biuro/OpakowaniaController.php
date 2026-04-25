<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Opakowanie;
use Illuminate\Http\Request;

class OpakowaniaController extends Controller
{
    public function index()
    {
        $opakowania = Opakowanie::orderBy('name')->get();

        return view('biuro.opakowania.index', compact('opakowania'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:opakowania,name',
            'waga' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nazwa jest wymagana.',
            'name.unique'   => 'Opakowanie o tej nazwie juz istnieje.',
            'waga.required' => 'Waga jest wymagana.',
            'waga.numeric'  => 'Waga musi byc liczba.',
            'waga.min'      => 'Waga nie moze byc ujemna.',
        ]);

        $op = Opakowanie::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Opakowanie "' . $op->name . '" zostalo dodane.',
        ]);
    }

    public function update(Request $request, Opakowanie $opakowanie)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:opakowania,name,' . $opakowanie->id,
            'waga'      => 'required|numeric|min:0',
            'is_active' => 'nullable|in:0,1',
        ], [
            'name.required' => 'Nazwa jest wymagana.',
            'name.unique'   => 'Opakowanie o tej nazwie juz istnieje.',
            'waga.required' => 'Waga jest wymagana.',
            'waga.numeric'  => 'Waga musi byc liczba.',
            'waga.min'      => 'Waga nie moze byc ujemna.',
        ]);

        $validated['is_active'] = $request->input('is_active', 0);

        $opakowanie->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Opakowanie "' . $opakowanie->name . '" zostalo zaktualizowane.',
        ]);
    }

    public function destroy(Opakowanie $opakowanie)
    {
        $name = $opakowanie->name;
        $opakowanie->delete();

        return response()->json([
            'success' => true,
            'message' => 'Opakowanie "' . $name . '" zostalo usuniete.',
        ]);
    }
}