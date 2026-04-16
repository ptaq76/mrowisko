<?php

namespace App\Http\Controllers\Plac;

use App\Http\Controllers\Controller;
use App\Models\LoadingItem;
use App\Models\Order;
use App\Models\WarehouseItem;
use App\Models\WasteFraction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('data')
            ? Carbon::parse($request->input('data'))->startOfDay()
            : Carbon::today();

        $orders = Order::with(['client', 'driver', 'tractor', 'trailer', 'loadingItems.fraction'])
            ->where('type', 'pickup')
            ->where(function ($q) use ($date) {
                $q->whereDate('planned_date', $date)
                    ->orWhere(function ($q2) use ($date) {
                        $q2->whereDate('planned_date', '<', $date)
                            ->whereNotIn('status', ['closed', 'delivered']);
                    });
            })
            ->orderByRaw("CASE WHEN status = 'delivered' THEN 1 ELSE 0 END")
            ->orderBy('planned_time')
            ->get();

        $placStatus = function ($order) {
            if (in_array($order->status, ['delivered', 'closed'])) {
                return ['label' => 'Zamknięte', 'done' => true];
            }
            if ($order->loadingItems->isNotEmpty()) {
                return ['label' => 'W trakcie', 'done' => false];
            }

            return ['label' => 'Zaplanowane', 'done' => false];
        };

        return view('plac.delivery', compact('orders', 'date', 'placStatus'));
    }

    public function deliveryAdd(Order $order)
    {
        return $this->deliveryAddForm($order, null);
    }

    public function deliveryEdit(Order $order, LoadingItem $item)
    {
        return $this->deliveryAddForm($order, $item);
    }

    private function deliveryAddForm(Order $order, ?LoadingItem $editItem)
    {
        $order->load(['client', 'driver']);

        $fractions = WasteFraction::forDeliveries()->orderBy('name')->get();

        $stockData = [];
        foreach ($fractions as $f) {
            $s = WarehouseItem::selectRaw('SUM(bales) as b, SUM(weight_kg) as w')
                ->where('fraction_id', $f->id)->first();
            $bales = (int) ($s->b ?? 0);
            $weight = (float) ($s->w ?? 0);
            $avg = $bales > 0 ? round($weight / $bales) : 0;
            $stockData[$f->id] = compact('bales', 'weight', 'avg');
        }

        $date = $order->planned_date ?? Carbon::today();

        return view('plac.delivery_add', compact('order', 'fractions', 'stockData', 'editItem', 'date'));
    }

    public function deliveryForm(Order $order)
    {
        $order->load(['client', 'driver', 'tractor', 'trailer', 'loadingItems.fraction']);
        $date = $order->planned_date ?? Carbon::today();

        $fractions = WasteFraction::forDeliveries()->orderBy('name')->get();
        $stockData = [];
        foreach ($fractions as $f) {
            $s = WarehouseItem::selectRaw('SUM(bales) as b, SUM(weight_kg) as w')
                ->where('fraction_id', $f->id)->first();
            $bales = (int) ($s->b ?? 0);
            $weight = (float) ($s->w ?? 0);
            $avg = $bales > 0 ? round($weight / $bales) : 0;
            $stockData[$f->id] = compact('bales', 'weight', 'avg');
        }

        return view('plac.delivery_form', compact('order', 'fractions', 'stockData', 'date'));
    }

    public function store(Request $request, Order $order)
    {
        $request->validate([
            'fraction_id' => ['required', 'exists:waste_fractions,id'],
            'bales' => ['required', 'integer', 'min:0'],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
        ], [
            'fraction_id.required' => 'Wybierz frakcję.',
            'bales.required' => 'Podaj ilość belek.',
        ]);

        $fractionId = $request->fraction_id;
        $bales = (int) $request->bales;
        $avgWeight = WarehouseItem::avgBaleWeight($fractionId);

        $weightKg = ($request->filled('weight_kg') && (float) $request->weight_kg > 0)
            ? round((float) $request->weight_kg, 2)
            : round($avgWeight * $bales, 2);

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
            'weight_kg' => $weightKg,
            'avg_weight' => $avgWeight,
        ]);
    }

    public function destroy(Order $order, LoadingItem $item)
    {
        if ($item->order_id !== $order->id) {
            return response()->json(['success' => false], 403);
        }
        $item->delete();

        return response()->json(['success' => true]);
    }

    public function close(Order $order)
    {
        $order->load('loadingItems');

        DB::transaction(function () use ($order) {
            // Przy dostawie magazyn ROŚNIE (dodajemy belki)
            foreach ($order->loadingItems as $item) {
                WarehouseItem::create([
                    'date' => now()->toDateString(),
                    'fraction_id' => $item->fraction_id,
                    'weight_kg' => $item->weight_kg,   // dodatnia wartość
                    'bales' => $item->bales,        // dodatnia wartość
                    'origin' => 'delivery',
                    'origin_order_id' => $order->id,
                    'operator_id' => null,
                ]);
            }
            $order->update(['status' => 'delivered']);
        });

        return response()->json(['success' => true]);
    }
}
