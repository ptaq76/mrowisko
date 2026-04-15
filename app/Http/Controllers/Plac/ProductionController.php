<?php

namespace App\Http\Controllers\Plac;

use App\Http\Controllers\Controller;
use App\Models\WarehouseItem;
use App\Models\WasteFraction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('data')
            ? Carbon::parse($request->input('data'))->startOfDay()
            : Carbon::today();

        $todayItems = WarehouseItem::with(['fraction', 'operator'])
            ->where('origin', 'production')
            ->whereDate('date', $date)
            ->orderByDesc('created_at')
            ->get();

        $monthItems = WarehouseItem::with(['fraction'])
            ->where('origin', 'production')
            ->whereYear('date',  $date->year)
            ->whereMonth('date', $date->month)
            ->get();

        $fractions = WasteFraction::forProduction()->orderBy('name')->get();

        return view('plac.production', compact('todayItems', 'monthItems', 'fractions', 'date'));
    }

    public function create()
    {
        $fractions = WasteFraction::forProduction()->orderBy('name')->get();
        return view('plac.production_create', compact('fractions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fraction_id' => ['required', 'exists:waste_fractions,id'],
            'bales'       => ['required', 'integer', 'min:0'],
            'weight_kg'   => ['required', 'numeric', 'min:0.01'],
        ], [
            'fraction_id.required' => 'Wybierz frakcję.',
            'bales.required'       => 'Podaj ilość belek.',
            'bales.min'            => 'Ilość belek nie może być ujemna.',
            'weight_kg.required'   => 'Podaj wagę.',
            'weight_kg.min'        => 'Waga musi być większa od 0.',
        ]);

        $item = WarehouseItem::create([
            'date'        => $request->filled('date') ? $request->date : now()->toDateString(),
            'fraction_id' => $request->fraction_id,
            'weight_kg'   => $request->weight_kg,
            'bales'       => $request->bales,
            'origin'      => 'production',
            'operator_id' => auth()->user()->id,
            'notes'       => $request->notes,
        ]);

        $item->load('fraction');

        return response()->json([
            'success' => true,
            'message' => 'Dodano do magazynu.',
            'item'    => [
                'id'        => $item->id,
                'fraction'  => $item->fraction->name,
                'bales'     => $item->bales,
                'weight_kg' => $item->weight_kg,
                'avg'       => $item->bales > 0 ? round($item->weight_kg / $item->bales) : 0,
            ],
        ]);
    }

    public function destroy(WarehouseItem $item)
    {
        if ($item->origin !== 'production') {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }
        $item->delete();
        return response()->json(['success' => true]);
    }
}