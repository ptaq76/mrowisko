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
        'code' => ['required', 'string', 'max:20', 'unique:waste_codes,code'],
    ], [
        'code.unique'   => 'Ten kod odpadu już istnieje.',
        'code.required' => 'Podaj kod odpadu.',
    ]);

    WasteCode::create($request->only('code'));
    return response()->json(['success' => true]);
}


    public function update(Request $request, WasteCode $wasteCode)
{
    $request->validate([
        'code' => ['required', 'string', 'max:20', "unique:waste_codes,code,{$wasteCode->id}"],
    ]);

    $wasteCode->update($request->only('code'));
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
