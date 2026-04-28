<?php

namespace App\Http\Controllers\Plac;

use App\Http\Controllers\Controller;
use App\Models\WarehouseItem;
use App\Models\WasteFraction;

class WarehouseController extends Controller
{
    public function index()
    {
        // Wszystkie aktywne frakcje śledzone w magazynie
        $fractions = WasteFraction::where('is_warehouse_tracked', true)
            ->where('is_active', true)
            ->where('name', 'not like', '%KARCHEM%')
            ->orderBy('name')
            ->get();

        // Stan magazynu per frakcja (snapshot inwentaryzacji uwzględniony)
        $stockMap = WarehouseItem::computeStockMap();

        // Połącz frakcje ze stanami (nawet jeśli stan = 0)
        $stock = $fractions->map(function ($f) use ($stockMap) {
            $s = $stockMap->get($f->id);

            return (object) [
                'fraction_id' => $f->id,
                'fraction' => $f,
                'total_bales' => $s ? (int) $s->total_bales : 0,
                'total_weight' => $s ? (float) $s->total_weight : 0,
            ];
        });

        return view('plac.warehouse', compact('stock'));
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
}