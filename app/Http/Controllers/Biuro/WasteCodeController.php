<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\WasteCode;
use Illuminate\Http\Request;

class WasteCodeController extends Controller
{
    public function index()
    {
        $codes = WasteCode::orderBy('code')->get();
        return view('biuro.waste_codes.index', compact('codes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'        => ['required', 'string', 'max:20', 'unique:waste_codes,code'],
            'description' => ['required', 'string', 'max:255'],
        ], [
            'code.unique'        => 'Ten kod odpadu już istnieje.',
            'code.required'      => 'Podaj kod odpadu.',
            'description.required'=> 'Podaj opis.',
        ]);

        WasteCode::create($request->only('code', 'description'));

        return response()->json(['success' => true]);
    }

    public function update(Request $request, WasteCode $wasteCode)
    {
        $request->validate([
            'code'        => ['required', 'string', 'max:20', "unique:waste_codes,code,{$wasteCode->id}"],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $wasteCode->update($request->only('code', 'description', 'is_active'));

        return response()->json(['success' => true]);
    }

    public function destroy(WasteCode $wasteCode)
    {
        if ($wasteCode->lieferscheins()->count()) {
            return response()->json(['success' => false, 'error' => 'Kod jest używany w Lieferscheinach.'], 422);
        }
        $wasteCode->delete();
        return response()->json(['success' => true]);
    }

    public function toggle(WasteCode $wasteCode)
    {
        $wasteCode->update(['is_active' => !$wasteCode->is_active]);
        return response()->json(['success' => true, 'is_active' => $wasteCode->is_active]);
    }
}
