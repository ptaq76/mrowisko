<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Annex7Contractor;
use App\Models\Annex7RecoveryOperation;
use App\Models\Annex7Shipment;
use App\Models\Annex7WasteDescription;
use App\Models\WasteCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Annex7Controller extends Controller
{
    public function index()
    {
        $shipments = Annex7Shipment::with(['arranger', 'importer', 'carrier'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('biuro.annex7.index', compact('shipments'));
    }

    public function create()
    {
        $arrangers = Annex7Contractor::where('role', 'arranger')->orderBy('name')->get();
        $importers = Annex7Contractor::where('role', 'importer')->orderBy('name')->get();
        $carriers = Annex7Contractor::where('role', 'carrier')->orderBy('name')->get();
        $generators = Annex7Contractor::where('role', 'generator')->orderBy('name')->get();
        $recoveryOperations = Annex7RecoveryOperation::orderBy('code')->get();
        $wasteDescriptions = Annex7WasteDescription::orderBy('description')->get();
        $wasteCodes = WasteCode::orderBy('code')->get();

        return view('biuro.annex7.create', compact(
            'arrangers', 'importers', 'carriers', 'generators',
            'recoveryOperations', 'wasteDescriptions', 'wasteCodes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'arranger_id' => 'required|exists:annex7_contractors,id',
            'importer_id' => 'required|exists:annex7_contractors,id',
            'date_shipment' => 'required|date',
            'carrier_id' => 'required|exists:annex7_contractors,id',
            'carrier_date_transfer' => 'nullable|date',
            'generator_id' => 'required|exists:annex7_contractors,id',
            'recovery_operation_id' => 'required|exists:annex7_recovery_operations,id',
            'waste_description_id' => 'required|exists:annex7_waste_descriptions,id',
            'waste_code_id' => 'required|exists:waste_codes,id',
        ]);

        $shipment = Annex7Shipment::create($validated);

        return redirect()->route('biuro.annex7.show', $shipment)
            ->with('success', 'Dokument Annex 7 został zapisany.');
    }

    public function show(Annex7Shipment $annex7)
    {
        $annex7->load([
            'arranger', 'importer', 'carrier',
            'generator', 'recoveryOperation',
            'wasteDescription', 'wasteCode',
        ]);

        return view('biuro.annex7.show', compact('annex7'));
    }

    public function generatePdf(Annex7Shipment $annex7)
    {
        $annex7->load([
            'arranger', 'importer', 'carrier',
            'generator', 'recoveryOperation',
            'wasteDescription', 'wasteCode',
        ]);

        $pdf = Pdf::loadView('biuro.annex7.pdf', compact('annex7'))
            ->setPaper('a4', 'portrait');

        $filename = 'annex7_'.$annex7->id.'_'.now()->format('Ymd_His').'.pdf';
        $path = 'annex7/'.$filename;

        Storage::disk('public')->put($path, $pdf->output());

        $annex7->update([
            'status' => 'generated',
            'pdf_path' => $path,
        ]);

        return $pdf->download($filename);
    }

    public function contractorData(Annex7Contractor $contractor)
    {
        return response()->json($contractor);
    }
}
