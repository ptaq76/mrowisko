<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\WarehouseItem;
use App\Models\WasteFraction;

class WarehouseController extends Controller
{
    public function index()
    {
        $fractions = WasteFraction::where('allows_belka', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $stockMap = WarehouseItem::selectRaw('fraction_id, SUM(bales) as total_bales, ROUND(SUM(weight_kg), 2) as total_weight')
            ->groupBy('fraction_id')
            ->get()
            ->keyBy('fraction_id');

        $stock = $fractions->map(function ($f) use ($stockMap) {
            $s = $stockMap->get($f->id);
            return (object) [
                'fraction_id'  => $f->id,
                'fraction'     => $f,
                'total_bales'  => $s ? (int) $s->total_bales : 0,
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
            'history'  => $history->map(fn($i) => [
                'id'       => $i->id,
                'date'     => $i->date->format('d.m.Y'),
                'weight'   => $i->weight_kg,
                'bales'    => $i->bales,
                'origin'   => WarehouseItem::ORIGINS[$i->origin] ?? $i->origin,
                'operator' => $i->operator?->name ?? $i->operator?->login ?? '–',
            ]),
        ]);
    }
}