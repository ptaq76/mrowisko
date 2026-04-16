<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Importer;
use App\Models\Lieferschein;
use App\Models\LsGoods;
use App\Models\WasteCode;
use App\Services\ImapLsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LieferscheinController extends Controller
{
    public function fetchFromMail(): JsonResponse
    {
        try {
            $service = new ImapLsService;
            $result = $service->fetch();

            return response()->json([
                'success' => true,
                'message' => "Pobrano: {$result['fetched']} maili, zapisano: {$result['saved']} PDF",
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Dane wspólne dla formularzy
    private function formData(): array
    {
        $importers = Importer::where('is_active', true)->orderBy('name')->get();
        $goods = LsGoods::where('is_active', true)->orderBy('name')->get();
        $clients = Client::where('country', 'DE')
            ->where('is_active', true)
            ->whereIn('type', ['sale', 'both'])
            ->orderBy('short_name')->get();

        $topImporters = Lieferschein::with('importer')
            ->select('importer_id', DB::raw('count(*) as cnt'))
            ->groupBy('importer_id')->orderByDesc('cnt')->take(8)->get()
            ->filter(fn ($r) => $r->importer)
            ->map(fn ($r) => ['id' => $r->importer->id, 'name' => $r->importer->name])
            ->unique('id')->values();

        $topClients = Lieferschein::with('client')
            ->select('client_id', DB::raw('count(*) as cnt'))
            ->groupBy('client_id')->orderByDesc('cnt')->take(8)->get()
            ->filter(fn ($r) => $r->client)
            ->map(fn ($r) => ['id' => $r->client->id, 'name' => $r->client->short_name])
            ->unique('id')->values();

        $timeWindows = ['BEZ GODZIN', '6-8', '8-10', '10-12', '12-14', '14-16', '16-18', '18-20', '20-22'];

        $wasteCodes = WasteCode::where('is_active', true)->orderBy('code')->get();

        return compact('importers', 'goods', 'clients', 'topImporters', 'topClients', 'timeWindows', 'wasteCodes');
    }

    public function index(Request $request)
    {
        $startOfWeek = $request->has('data')
            ? Carbon::parse($request->input('data'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $ls = Lieferschein::with(['importer', 'client', 'goods'])
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->orderBy('date')->get();

        $lsByDate = $ls->groupBy(fn ($item) => Carbon::parse($item->date)->format('Y-m-d'));

        $weekDays = collect();
        for ($i = 0; $i < 5; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
            $weekDays->put($date, $lsByDate->get($date, collect()));
        }

        return view('biuro.ls.index', array_merge(
            compact('weekDays', 'startOfWeek'),
            $this->formData()
        ));
    }

    public function create()
    {
        return view('biuro.ls.create', $this->formData());
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => ['required', 'string', 'unique:lieferscheins,number'],
            'date' => ['required', 'date'],
            'importer_id' => ['required', 'exists:importers,id'],
            'goods_id' => ['required', 'exists:ls_goods,id'],
            'client_id' => ['required', 'exists:clients,id'],
            'time_window' => ['required', 'string'],
        ], [
            'number.required' => 'Podaj numer LS.',
            'number.unique' => 'Ten numer LS już istnieje.',
            'date.required' => 'Podaj datę.',
            'importer_id.required' => 'Wybierz importera.',
            'goods_id.required' => 'Wybierz towar.',
            'client_id.required' => 'Wybierz kierunek.',
            'time_window.required' => 'Podaj okienko.',
        ]);

        $ls = Lieferschein::create([
            'number' => $request->number,
            'date' => $request->date,
            'importer_id' => $request->importer_id,
            'goods_id' => $request->goods_id,
            'client_id' => $request->client_id,
            'time_window' => $request->time_window,
            'transp_zew' => false,
            'status' => false,
            'is_used' => false,
            'waste_code_id' => $request->waste_code_id ?: null,
        ]);

        // Upload pliku z komputera
        if ($request->hasFile('pdf_file')) {
            $this->uploadPdf($ls, $request->file('pdf_file'));
        } elseif ($request->filled('pdf_path')) {
            $this->movePdf($ls, $request->pdf_path);
        }

        return redirect()->route('biuro.ls.index')
            ->with('success', 'LS '.$ls->number.' został dodany.');
    }

    public function edit(Lieferschein $lieferschein)
    {
        return view('biuro.ls.edit', array_merge(
            compact('lieferschein'),
            $this->formData()
        ));
    }

    public function update(Request $request, Lieferschein $lieferschein)
    {
        $request->validate([
            'number' => ['required', 'string', Rule::unique('lieferscheins', 'number')->ignore($lieferschein->id)],
            'date' => ['required', 'date'],
            'importer_id' => ['required', 'exists:importers,id'],
            'goods_id' => ['required', 'exists:ls_goods,id'],
            'client_id' => ['required', 'exists:clients,id'],
            'time_window' => ['required', 'string'],
        ], [
            'number.required' => 'Podaj numer LS.',
            'number.unique' => 'Ten numer LS już istnieje.',
        ]);

        $oldDate = $lieferschein->date;
        $oldImporterId = $lieferschein->importer_id;

        $lieferschein->update([
            'number' => $request->number,
            'date' => $request->date,
            'importer_id' => $request->importer_id,
            'goods_id' => $request->goods_id,
            'client_id' => $request->client_id,
            'time_window' => $request->time_window,
            'waste_code_id' => $request->waste_code_id ?: null,
        ]);

        // Upload pliku z komputera ma priorytet
        \Log::info('LS update - files check', [
            'hasFile' => $request->hasFile('pdf_file'),
            'allFiles' => array_keys($request->allFiles()),
            'pdf_path' => $request->pdf_path,
        ]);
        if ($request->hasFile('pdf_file')) {
            $this->uploadPdf($lieferschein, $request->file('pdf_file'));
        } elseif ($request->filled('pdf_path') && $request->pdf_path !== $lieferschein->pdf_path) {
            $this->movePdf($lieferschein, $request->pdf_path);
        } elseif ($lieferschein->pdf_path) {
            // Przenieś jeśli zmieniono datę lub importera
            $dateChanged = Carbon::parse($request->date)->ne(Carbon::parse($oldDate));
            $importerChanged = $request->importer_id != $oldImporterId;
            if ($dateChanged || $importerChanged) {
                $this->movePdf($lieferschein, $lieferschein->pdf_path);
            }
        }

        return request()->wantsJson()
            ? response()->json(['success' => true, 'message' => 'LS '.$lieferschein->number.' został zaktualizowany.'])
            : redirect()->route('biuro.ls.index')->with('success', 'LS '.$lieferschein->number.' został zaktualizowany.');
    }

    public function markStatus(Lieferschein $lieferschein)
    {
        $lieferschein->update(['status' => ! $lieferschein->status]);

        return response()->json(['success' => true, 'status' => $lieferschein->status]);
    }

    public function destroy(Lieferschein $lieferschein)
    {
        if ($lieferschein->pdf_path && Storage::disk('public')->exists($lieferschein->pdf_path)) {
            Storage::disk('public')->delete($lieferschein->pdf_path);
        }
        $lieferschein->delete();

        return response()->json(['success' => true, 'message' => 'LS został usunięty.']);
    }

    public function viewPdf(Lieferschein $lieferschein)
    {
        if (! $lieferschein->pdf_path || ! Storage::disk('public')->exists($lieferschein->pdf_path)) {
            abort(404, 'Plik PDF nie istnieje.');
        }

        return response()->file(Storage::disk('public')->path($lieferschein->pdf_path));
    }

    public function getPdfFiles()
    {
        $files = collect(Storage::disk('public')->files('attachments'))
            ->filter(fn ($f) => str_ends_with($f, '.pdf'))
            ->map(fn ($f) => [
                'filename' => basename($f),
                'relative_path' => $f,
                'ctime' => filectime(Storage::disk('public')->path($f)),
            ])
            ->sortByDesc('ctime')
            ->values();

        return response()->json($files);
    }

    private function uploadPdf(Lieferschein $ls, $file): void
    {
        \Log::info('uploadPdf called', [
            'ls_id' => $ls->id,
            'filename' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'valid' => $file->isValid(),
        ]);

        $date = Carbon::parse($ls->date);
        $week = $date->format('W');
        $year = $date->year;
        $importer = Importer::find($ls->importer_id);
        $importerSlug = $importer ? Str::slug($importer->name) : 'importer';
        $newName = $importerSlug.'_'.$ls->number.'.pdf';
        $targetDir = "$year/$week";

        Storage::disk('public')->makeDirectory($targetDir);

        // Usuń stary plik
        if ($ls->pdf_path && Storage::disk('public')->exists($ls->pdf_path)) {
            Storage::disk('public')->delete($ls->pdf_path);
        }

        $newPath = $file->storeAs($targetDir, $newName, 'public');
        $ls->update(['pdf_path' => $newPath]);
    }

    private function movePdf(Lieferschein $ls, string $sourcePath): void
    {
        if (! Storage::disk('public')->exists($sourcePath)) {
            return;
        }

        $date = Carbon::parse($ls->date);
        $week = $date->format('W');
        $year = $date->year;
        $importer = Importer::find($ls->importer_id);
        $importerSlug = $importer ? Str::slug($importer->name) : 'importer';
        $newName = $importerSlug.'_'.$ls->number.'.pdf';
        $targetDir = "$year/$week";
        $newPath = "$targetDir/$newName";

        Storage::disk('public')->makeDirectory($targetDir);

        if ($ls->pdf_path && $ls->pdf_path !== $sourcePath && Storage::disk('public')->exists($ls->pdf_path)) {
            Storage::disk('public')->delete($ls->pdf_path);
        }

        Storage::disk('public')->move($sourcePath, $newPath);
        $ls->update(['pdf_path' => $newPath]);
    }
}
