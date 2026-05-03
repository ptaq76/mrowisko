<?php

namespace App\Http\Controllers\Plac;

use App\Http\Controllers\Controller;
use App\Models\WarehouseItem;
use App\Models\WasteFraction;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $fractions = WasteFraction::where('is_warehouse_tracked', true)
            ->where('is_active', true)
            ->where('name', 'not like', '%KARCHEM%')
            ->orderBy('name')
            ->get();

        $stockMap = WarehouseItem::computeStockMap();

        $stock = $fractions->map(function ($f) use ($stockMap) {
            $s = $stockMap->get($f->id);

            return (object) [
                'fraction_id'  => $f->id,
                'fraction'     => $f,
                'total_bales'  => $s ? (int)   $s->total_bales  : 0,
                'total_weight' => $s ? (float) $s->total_weight : 0,
            ];
        });

        return view('plac.inventory', compact('stock'));
    }

    public function adjust(Request $request, int $fractionId)
    {
        $fraction = WasteFraction::findOrFail($fractionId);

        $request->validate([
            'bales' => ['required', 'integer', 'min:0'],
            'weight_kg' => ['required', 'numeric', 'min:0'],
        ], [
            'bales.required' => 'Podaj ilość belek.',
            'weight_kg.required' => 'Podaj wagę.',
        ]);

        // Aktualny stan (snapshot logic — od ostatniej inwentaryzacji)
        $current = WarehouseItem::stockForFraction($fractionId);
        $currentBales = $current['bales'];
        $currentWeight = $current['weight'];

        $newBales = (int) $request->bales;
        $newWeight = (float) $request->weight_kg;

        // Zapisujemy SNAPSHOT — odczyt sumuje od ostatniej inwentaryzacji
        WarehouseItem::create([
            'date' => now()->toDateString(),
            'fraction_id' => $fractionId,
            'weight_kg' => $newWeight,
            'bales' => $newBales,
            'origin' => 'inventory',
            'operator_id' => null,
            'notes' => "Inwentaryzacja: było {$currentBales} bel. / {$currentWeight} kg → jest {$newBales} bel. / {$newWeight} kg",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stan magazynu zaktualizowany.',
            'fraction' => $fraction->name,
            'new_bales' => $newBales,
            'new_weight' => $newWeight,
            'diff_bales' => $newBales - $currentBales,
            'diff_weight' => $newWeight - $currentWeight,
        ]);
    }
}
