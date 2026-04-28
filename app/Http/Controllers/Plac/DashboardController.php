<?php

namespace App\Http\Controllers\Plac;

use App\Http\Controllers\Controller;
use App\Models\LoadingItem;
use App\Models\Order;
use App\Models\WarehouseItem;
use App\Models\WasteFraction;
use App\Models\Zadanie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('plac.dashboard');
    }

    public function orders(Request $request)
{
    $date = $request->filled('data')
        ? Carbon::parse($request->input('data'))->startOfDay()
        : Carbon::today();

    $today = Carbon::today();

    $orders = Order::with(['client', 'driver', 'tractor', 'trailer', 'loadingItems.fraction', 'lieferschein.importer'])
        ->where(function ($q) use ($date, $today) {
            $q->whereDate('plac_date', $date)
                ->orWhere(function ($q2) use ($today) {
                    $q2->whereDate('plac_date', '<', $today)
                        ->whereNotIn('status', ['closed', 'delivered', 'loaded']);
                });
        })
        ->orderByRaw("CASE WHEN status = 'loaded' THEN 1 ELSE 0 END")
        ->orderByRaw('CASE WHEN DATE(plac_date) = ? THEN 0 ELSE 1 END', [$date->format('Y-m-d')])
        ->orderBy('planned_time')
        ->get();

    $zadania = Zadanie::forPlac()
        ->onDate($date)
        ->orderBy('status')
        ->orderBy('id')
        ->get();

    return view('plac.orders', compact('orders', 'date', 'zadania'));
}

    public function _old_index(Request $request)
    {
        $date = $request->filled('data')
            ? Carbon::parse($request->input('data'))->startOfDay()
            : Carbon::today();

        // Zlecenia dzisiaj + zaległe niezakończone
        $orders = Order::with(['client', 'driver', 'tractor', 'trailer', 'loadingItems.fraction'])
            ->where(function ($q) use ($date) {
                $q->whereDate('planned_date', $date)
                    ->orWhere(function ($q2) use ($date) {
                        $q2->whereDate('planned_date', '<', $date)
                            ->whereNotIn('status', ['closed', 'tool']);
                    });
            })
            ->orderByRaw('CASE WHEN DATE(planned_date) = ? THEN 0 ELSE 1 END', [$date->format('Y-m-d')])
            ->orderBy('planned_time')
            ->get();

        return view('plac.dashboard', compact('orders', 'date'));
    }

    // Formularz załadunku dla zlecenia
    public function loadingForm(Order $order)
    {
        $order->load(['client', 'driver', 'tractor', 'trailer', 'loadingItems.fraction', 'lieferschein.importer']);

        $date = $order->planned_date ?? Carbon::today();

        // Frakcje do szybkiego wyboru (show_in_loadings)
        $fractions = WasteFraction::where('show_in_loadings', true)
            ->where('is_active', true)
            ->where('allows_belka', true)
            ->orderBy('name')
            ->get();

        return view('plac.loading_form', compact('order', 'fractions', 'date'));
    }

    // Dodaj pozycję załadunku
    public function loadingStore(Request $request, Order $order)
    {
        $request->validate([
            'fraction_id' => ['required', 'exists:waste_fractions,id'],
            'bales' => ['required', 'integer', 'min:0'],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ], [
            'fraction_id.required' => 'Wybierz frakcję.',
            'bales.required' => 'Podaj ilość belek.',
            'bales.min' => 'Ilość belek nie może być ujemna.',
        ]);

        $fractionId = $request->fraction_id;
        $bales = (int) $request->bales;
        $avgWeight = WarehouseItem::avgBaleWeight($fractionId);

        // Użyj wagi z formularza lub oblicz ze średniej
        if ($request->filled('weight_kg') && (float) $request->weight_kg > 0) {
            $weightKg = round((float) $request->weight_kg, 2);
        } else {
            $weightKg = round($avgWeight * $bales, 2);
        }

        // Dodaj pozycję załadunku (magazyn aktualizuje się przy zamknięciu)
        LoadingItem::create([
            'order_id' => $order->id,
            'fraction_id' => $fractionId,
            'bales' => $bales,
            'weight_kg' => $weightKg,
            'notes' => $request->notes,
            'operator_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pozycja załadunku dodana.',
            'weight_kg' => $weightKg,
            'avg_weight' => $avgWeight,
        ]);
    }

    // Usuń pozycję załadunku
    public function loadingDestroy(Order $order, LoadingItem $item)
    {
        if ($item->order_id !== $order->id) {
            return response()->json(['success' => false], 403);
        }

        // Usuń pozycję (magazyn aktualizuje się przy zamknięciu)
        $item->delete();

        return response()->json(['success' => true, 'message' => 'Pozycja usunięta.']);
    }

    public function loadingAdd(Order $order)
    {
        $order->load(['client', 'driver']);

        $fractions = WasteFraction::forLoadings()->orderBy('name')->get();

        // Stan magazynu per frakcja
        $stockMap = WarehouseItem::computeStockMap();
        $stockData = [];
        foreach ($fractions as $f) {
            $s = $stockMap->get($f->id);
            $bales = (int) ($s->total_bales ?? 0);
            $weight = (float) ($s->total_weight ?? 0);
            $avg = $bales > 0 ? round($weight / $bales) : 0;
            $stockData[$f->id] = compact('bales', 'weight', 'avg');
        }

        $editItem = null;
        $date = $order->planned_date ?? Carbon::today();

        return view('plac.loading_add', compact('order', 'fractions', 'stockData', 'editItem', 'date'));
    }

    public function loadingEdit(Order $order, LoadingItem $item)
    {
        $order->load(['client', 'driver']);

        $fractions = WasteFraction::forLoadings()->orderBy('name')->get();

        $stockMap = WarehouseItem::computeStockMap();
        $stockData = [];
        foreach ($fractions as $f) {
            $s = $stockMap->get($f->id);
            $bales = (int) ($s->total_bales ?? 0);
            $weight = (float) ($s->total_weight ?? 0);
            $avg = $bales > 0 ? round($weight / $bales) : 0;
            $stockData[$f->id] = compact('bales', 'weight', 'avg');
        }

        $editItem = $item;
        $date = $order->planned_date ?? Carbon::today();

        return view('plac.loading_add', compact('order', 'fractions', 'stockData', 'editItem', 'date'));
    }

    public function closeLoading(Order $order)
    {
        $order->load('loadingItems');

        DB::transaction(function () use ($order) {
            // Aktualizuj magazyn dla każdej pozycji
            foreach ($order->loadingItems as $item) {
                WarehouseItem::create([
                    'date' => now()->toDateString(),
                    'fraction_id' => $item->fraction_id,
                    'weight_kg' => -$item->weight_kg,
                    'bales' => -$item->bales,
                    'origin' => 'loading',
                    'origin_order_id' => $order->id,
                    'operator_id' => null,
                ]);
            }
            $order->update(['status' => 'loaded']);
        });

        return response()->json(['success' => true]);
    }

    // Stan magazynu (AJAX - do podglądu)
    public function stock()
    {
        $stock = WarehouseItem::stockSummary();

        return response()->json($stock);
    }
}