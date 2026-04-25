<?php

namespace App\Http\Controllers\Plac;

use App\Http\Controllers\Controller;
use App\Models\LoadingItem;
use App\Models\Opakowanie;
use App\Models\Order;
use App\Models\OrderPackaging;
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
        $order->load(['client', 'driver', 'loadingItems', 'packaging.opakowanie']);

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
        $allOpakowania = Opakowanie::active()->orderBy('name')->get();
        $hasPkg = $order->packaging->isNotEmpty();

        return view('plac.delivery_add', compact(
            'order', 'fractions', 'stockData', 'editItem', 'date', 'allOpakowania', 'hasPkg'
        ));
    }

    public function deliveryForm(Order $order)
    {
        $order->load([
            'client', 'driver', 'tractor', 'trailer',
            'loadingItems.fraction',
            'packaging.opakowanie',
        ]);
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

        // Wszystkie aktywne opakowania (do formularza placu gdy kierowca nic nie podał)
        $allOpakowania = Opakowanie::active()->orderBy('name')->get();

        return view('plac.delivery_form', compact(
            'order', 'fractions', 'stockData', 'date', 'allOpakowania'
        ));
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
            foreach ($order->loadingItems as $item) {
                WarehouseItem::create([
                    'date' => now()->toDateString(),
                    'fraction_id' => $item->fraction_id,
                    'weight_kg' => $item->weight_kg,
                    'bales' => $item->bales,
                    'origin' => 'delivery',
                    'origin_order_id' => $order->id,
                    'operator_id' => null,
                ]);
            }
            $order->update(['status' => 'delivered']);
        });

        return response()->json(['success' => true]);
    }

    /**
     * POST /plac/delivery/{order}/packaging/confirm
     * Plac zatwierdza ilości podane przez kierowcę (kopiuje quantity → qty_plac)
     */
    public function packagingConfirm(Order $order)
    {
        $order->load('packaging');

        if ($order->packaging->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Brak opakowań do potwierdzenia.'], 422);
        }

        $userId = auth()->user()->id;
        $now = now();

        foreach ($order->packaging as $pkg) {
            $pkg->update([
                'qty_plac' => $pkg->quantity,   // przepisuje wartość kierowcy
                'confirmed_by' => $userId,
                'confirmed_at' => $now,
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * POST /plac/delivery/{order}/packaging
     * Plac sam wpisuje ilości (gdy kierowca nic nie podał lub plac chce nadpisać)
     * Body: { packaging: [{opakowanie_id, qty_plac}, ...] }
     */
    public function packagingStore(Request $request, Order $order)
    {
        $request->validate([
            'packaging' => ['required', 'array'],
            'packaging.*.opakowanie_id' => ['required', 'exists:opakowania,id'],
            'packaging.*.qty_plac' => ['required', 'integer', 'min:0'],
        ]);

        $userId = auth()->user()->id;
        $now = now();

        foreach ($request->packaging as $item) {
            $qtyPlac = (int) $item['qty_plac'];

            if ($qtyPlac > 0) {
                OrderPackaging::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'opakowanie_id' => $item['opakowanie_id'],
                    ],
                    [
                        'qty_plac' => $qtyPlac,
                        'confirmed_by' => $userId,
                        'confirmed_at' => $now,
                        // quantity (kierowca) zostaje null jeśli nie istnieje
                    ]
                );
            } else {
                // qty_plac = 0 → wyczyść potwierdzenie placu (nie kasuj wpisu kierowcy)
                OrderPackaging::where('order_id', $order->id)
                    ->where('opakowanie_id', $item['opakowanie_id'])
                    ->whereNull('quantity') // tylko jeśli kierowca też nic nie podał
                    ->delete();

                // Jeśli kierowca podał — tylko zeruj qty_plac
                OrderPackaging::where('order_id', $order->id)
                    ->where('opakowanie_id', $item['opakowanie_id'])
                    ->whereNotNull('quantity')
                    ->update([
                        'qty_plac' => null,
                        'confirmed_by' => null,
                        'confirmed_at' => null,
                    ]);
            }
        }

        return response()->json(['success' => true]);
    }
}