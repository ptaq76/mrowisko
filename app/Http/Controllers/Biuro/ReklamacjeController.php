<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Reklamacja;
use App\Models\ReklamacjaBled;
use Illuminate\Http\Request;

class ReklamacjeController extends Controller
{
    public function index(Request $request)
    {
        $typ = $request->input('typ');

        $reklamacje = Reklamacja::with('lieferscheinModel')
            ->when($typ, fn($q) => $q->where('typ', $typ))
            ->orderByDesc('mail_date')
            ->paginate(30)
            ->withQueryString();

        $bledy = ReklamacjaBled::where('status', 'nowy')->count();

        return view('biuro.reklamacje.index', compact('reklamacje', 'bledy', 'typ'));
    }

    public function gewichtsmeldung(Request $request)
    {
        return redirect()->route('biuro.reklamacje.index', ['typ' => 'gewichtsmeldung']);
    }

    public function bledy(Request $request)
    {
        $bledy = ReklamacjaBled::orderByDesc('created_at')->paginate(30);
        return view('biuro.reklamacje.bledy', compact('bledy'));
    }

    public function bladUpdate(Request $request, ReklamacjaBled $reklamacjaBled)
    {
        $request->validate(['status' => ['required', 'in:nowy,zweryfikowany,pominiety']]);
        $reklamacjaBled->update(['status' => $request->status]);
        return response()->json(['success' => true, 'status' => $reklamacjaBled->status]);
    }

    public function showFile(string $path)
    {
        $fullPath = storage_path('app/' . $path);
        if (!file_exists($fullPath)) {
            abort(404, 'Plik nie istnieje.');
        }
        return response()->file($fullPath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
        ]);
    }

    public function fetchMail()
    {
        try {
            \Artisan::call('reklamacje:przetwarzaj');
            return response()->json(['success' => true, 'message' => 'Przetwarzanie zakończone.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
