<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Importer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ImporterController extends Controller
{
    public function index()
    {
        $importers = Importer::orderBy('name')->get();
        return view('biuro.importers.index', compact('importers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:255', 'unique:importers,name'],
            'country' => ['required', Rule::in(['PL', 'DE'])],
        ], [
            'name.required' => 'Podaj nazwę importera.',
            'name.unique'   => 'Importer o tej nazwie już istnieje.',
            'country.required' => 'Wybierz kraj.',
        ]);

        Importer::create([
            'name'      => $request->name,
            'country'   => $request->country,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Importer został dodany.']);
    }

    public function update(Request $request, Importer $importer)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:255', Rule::unique('importers', 'name')->ignore($importer->id)],
            'country' => ['required', Rule::in(['PL', 'DE'])],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'Podaj nazwę importera.',
            'name.unique'   => 'Importer o tej nazwie już istnieje.',
        ]);

        $importer->update([
            'name'      => $request->name,
            'country'   => $request->country,
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json(['success' => true, 'message' => 'Importer został zaktualizowany.']);
    }
}
