<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\WarehouseItem;
use App\Models\WasteFraction;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $fractions = WasteFraction::where('is_warehouse_tracked', true)
            ->where('is_active', true)
            ->where('name', 'not like', '%KARCHEM%')
            ->orderByDesc('fav_biuro')
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

        return view('biuro.reports.warehouse', compact('stock'));
    }

    public function history(int $fractionId)
    {
        $fraction = WasteFraction::findOrFail($fractionId);

        $history = WarehouseItem::with('operator')
            ->where('fraction_id', $fractionId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'fraction' => $fraction->name,
            'history' => $history->map(fn ($i) => [
                'id' => $i->id,
                'date' => $i->date->format('d.m.Y'),
                'weight' => $i->weight_kg,
                'bales' => $i->bales,
                'origin' => WarehouseItem::ORIGINS[$i->origin] ?? $i->origin,
                'origin_short' => WarehouseItem::ORIGIN_SHORT[$i->origin] ?? mb_strtoupper(mb_substr($i->origin, 0, 3)),
                'origin_code' => $i->origin,
                'operator' => $i->operator?->name ?? $i->operator?->login ?? '–',
            ]),
        ]);
    }

    public function toggleFav(Request $request, WasteFraction $fraction)
    {
        $module = $request->input('module', 'biuro');
        $column = match ($module) {
            'plac'     => 'fav_plac',
            'kierowca' => 'fav_kierowca',
            default    => 'fav_biuro',
        };

        $newVal = ! $fraction->$column;
        $fraction->update([$column => $newVal]);

        return response()->json(['success' => true, 'fav' => $newVal]);
    }
}