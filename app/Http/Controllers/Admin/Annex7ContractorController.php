<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Annex7Contractor;
use Illuminate\Http\Request;

class Annex7ContractorController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->input('role');

        $contractors = Annex7Contractor::when($role, fn($q) => $q->where('role', $role))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $roles = [
            'arranger'  => 'Arranger',
            'importer'  => 'Importer',
            'carrier'   => 'Carrier',
            'generator' => 'Generator',
            'recovery'  => 'Recovery facility',
        ];

        return view('admin.annex7.contractors.index', compact('contractors', 'roles', 'role'));
    }

    public function create()
    {
        return view('admin.annex7.contractors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'role'       => 'required|in:arranger,importer,carrier,generator',
            'address'    => 'required|string|max:500',
            'contact'    => 'required|string|max:255',
            'tel'        => 'required|string|max:50',
            'mail'       => 'required|email|max:255',
        ]);

        Annex7Contractor::create($request->only(
            'name', 'short_name', 'role', 'address', 'contact', 'tel', 'mail'
        ));

        return redirect()->route('admin.annex7-contractors.index')
            ->with('success', 'Kontrahent został dodany.');
    }

    public function edit(Annex7Contractor $annex7Contractor)
    {
        return view('admin.annex7.contractors.edit', compact('annex7Contractor'));
    }

    public function update(Request $request, Annex7Contractor $annex7Contractor)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'role'       => 'required|in:arranger,importer,carrier,generator',
            'address'    => 'required|string|max:500',
            'contact'    => 'required|string|max:255',
            'tel'        => 'required|string|max:50',
            'mail'       => 'required|email|max:255',
        ]);

        $annex7Contractor->update($request->only(
            'name', 'short_name', 'role', 'address', 'contact', 'tel', 'mail'
        ));

        return redirect()->route('admin.annex7-contractors.index')
            ->with('success', 'Kontrahent został zaktualizowany.');
    }

    public function destroy(Annex7Contractor $annex7Contractor)
    {
        if ($annex7Contractor->shipmentsAsArranger()->exists() ||
            $annex7Contractor->shipmentsAsImporter()->exists() ||
            $annex7Contractor->shipmentsAsCarrier()->exists() ||
            $annex7Contractor->shipmentsAsGenerator()->exists()) {
            return back()->with('error', 'Nie można usunąć kontrahenta powiązanego z dokumentami Annex 7.');
        }

        $annex7Contractor->delete();

        return redirect()->route('admin.annex7-contractors.index')
            ->with('success', 'Kontrahent został usunięty.');
    }
}
