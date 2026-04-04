<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Annex7WasteDescription;
use Illuminate\Http\Request;

class Annex7WasteDescriptionController extends Controller
{
    public function index()
    {
        $descriptions = Annex7WasteDescription::orderBy('description')->paginate(20);
        return view('admin.annex7.waste_descriptions.index', compact('descriptions'));
    }

    public function create()
    {
        return view('admin.annex7.waste_descriptions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:500|unique:annex7_waste_descriptions,description',
        ]);

        Annex7WasteDescription::create($request->only('description'));

        return redirect()->route('admin.annex7-waste-descriptions.index')
            ->with('success', 'Opis odpadu został dodany.');
    }

    public function edit(Annex7WasteDescription $annex7WasteDescription)
    {
        return view('admin.annex7.waste_descriptions.edit', compact('annex7WasteDescription'));
    }

    public function update(Request $request, Annex7WasteDescription $annex7WasteDescription)
    {
        $request->validate([
            'description' => 'required|string|max:500|unique:annex7_waste_descriptions,description,' . $annex7WasteDescription->id,
        ]);

        $annex7WasteDescription->update($request->only('description'));

        return redirect()->route('admin.annex7-waste-descriptions.index')
            ->with('success', 'Opis odpadu został zaktualizowany.');
    }

    public function destroy(Annex7WasteDescription $annex7WasteDescription)
    {
        if ($annex7WasteDescription->shipments()->exists()) {
            return back()->with('error', 'Nie można usunąć opisu powiązanego z dokumentami Annex 7.');
        }

        $annex7WasteDescription->delete();

        return redirect()->route('admin.annex7-waste-descriptions.index')
            ->with('success', 'Opis odpadu został usunięty.');
    }
}
