<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Prasa;
use Illuminate\Http\Request;

class PrasaController extends Controller
{
    public function index()
    {
        $prasy = Prasa::with('client:id,short_name')
            ->orderBy('name')
            ->get();

        $clients = Client::orderBy('short_name')->get(['id', 'short_name']);

        return view('biuro.prasy.index', compact('prasy', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:prasy,name',
            'client_id' => 'nullable|exists:clients,id',
            'notes'     => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Nazwa jest wymagana.',
            'name.unique'   => 'Prasa o tej nazwie już istnieje.',
        ]);

        $p = Prasa::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Prasa „'.$p->name.'" została dodana.',
        ]);
    }

    public function update(Request $request, Prasa $prasa)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:prasy,name,'.$prasa->id,
            'client_id' => 'nullable|exists:clients,id',
            'is_active' => 'nullable|in:0,1',
            'notes'     => 'nullable|string|max:1000',
        ]);

        $validated['is_active'] = $request->input('is_active', 0);

        $prasa->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Prasa „'.$prasa->name.'" została zaktualizowana.',
        ]);
    }

    public function destroy(Prasa $prasa)
    {
        $name = $prasa->name;
        $prasa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Prasa „'.$name.'" została usunięta.',
        ]);
    }
}
