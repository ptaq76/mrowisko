<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Hauler;
use App\Models\Order;
use App\Models\Vehicle;
use App\Models\VehicleSet;
use App\Models\WarehouseItem;
use App\Models\WasteFraction;
use App\Models\Weighing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WeighingController extends Controller
{
    /** Statusy oznaczające „plac zamknął zlecenie" — zależnie od typu */
    private const PLAC_CLOSED_PICKUP = ['delivered', 'closed'];
    private const PLAC_CLOSED_SALE = ['loaded', 'weighed', 'delivered', 'closed'];

    public function index(Request $request)
    {
        // Zlecenia, dla których waga jest podana (pełna lub częściowa) LUB plac zamknął — niezarchiwizowane
        $orderRows = Order::with(['client', 'tractor', 'trailer', 'driver', 'loadingItems.fraction'])
            ->where('is_archived', false)
            ->where(function ($q) {
                $q->whereNotNull('weight_brutto')
                    ->orWhereNotNull('weight_netto')
                    ->orWhere(function ($q2) {
                        $q2->where('type', 'pickup')->whereIn('status', self::PLAC_CLOSED_PICKUP);
                    })
                    ->orWhere(function ($q2) {
                        $q2->where('type', 'sale')->whereIn('status', self::PLAC_CLOSED_SALE);
                    });
            })
            ->orderByDesc('updated_at')
            ->limit(200)
            ->get();

        // Ważenia luźne (bez zlecenia), niezarchiwizowane
        $standalone = Weighing::with(['client'])
            ->whereNull('order_id')
            ->where('is_archived', false)
            ->orderByDesc('weighed_at')
            ->get();

        // Scalone wiersze (sortowane razem desc po dacie aktywności)
        $rows = $this->mergeRows($orderRows, $standalone);

        // Zlecenia aktywne do wyboru w modalu — bez ŻADNEJ wagi (nawet częściowej), niezamknięte, niezarchiwizowane
        $activeOrders = Order::with(['client', 'tractor', 'trailer', 'loadingItems.fraction'])
            ->whereNull('weight_netto')
            ->whereNull('weight_brutto')
            ->whereNotIn('status', ['closed', 'delivered'])
            ->whereDate('planned_date', '>=', now()->subDays(3))
            ->where('is_archived', false)
            ->orderBy('planned_date')
            ->get();

        $clients = Client::orderBy('short_name')->get();
        $haulers = Hauler::with('client')->orderBy('sort_order')->get();
        $fractions = WasteFraction::where('is_active', true)->orderBy('name')->get();

        return view('biuro.weighings.index', compact(
            'rows', 'activeOrders', 'clients', 'haulers', 'fractions'
        ));
    }

    /**
     * Mapuje orders + weighings na ujednolicony format wiersza i sortuje desc po sort_at.
     */
    private function mergeRows($orderRows, $standalone)
    {
        $a = $orderRows->map(function (Order $o) {
            $brutto = $o->weight_brutto !== null ? (float) $o->weight_brutto : null;
            $netto = $o->weight_netto !== null ? (float) $o->weight_netto : null;
            $tara = ($brutto !== null && $netto !== null) ? round($brutto - $netto, 3) : null;
            $closedStatuses = $o->type === 'sale' ? self::PLAC_CLOSED_SALE : self::PLAC_CLOSED_PICKUP;

            return (object) [
                'source' => 'order',
                'id' => $o->id,
                'sort_at' => $o->updated_at,
                'date' => $o->planned_date,
                'time_at' => $o->updated_at,
                'client' => $o->client,
                'type' => $o->type,
                'plate1' => $o->tractor?->plate,
                'plate2' => $o->trailer?->plate,
                'brutto' => $brutto,
                'tara' => $tara,
                'netto' => $netto,
                'driver_name' => $o->driver?->name,
                'goods' => $o->loadingItems->pluck('fraction.name')->filter()->unique()->implode(', ') ?: null,
                'notes' => null,
                'has_weight' => $netto !== null,
                'has_partial' => $brutto !== null && $netto === null,
                'plac_closed' => in_array($o->status, $closedStatuses, true),
            ];
        });

        $b = $standalone->map(function (Weighing $w) {
            $w1 = $w->weight1 !== null ? (float) $w->weight1 : null;
            $w2 = $w->weight2 !== null ? (float) $w->weight2 : null;
            $brutto = ($w1 !== null && $w2 !== null) ? max($w1, $w2) : $w1;
            $tara = ($w1 !== null && $w2 !== null) ? min($w1, $w2) : null;
            $netto = $w->result !== null ? (float) $w->result : (($w1 !== null && $w2 !== null) ? round(abs($w1 - $w2), 3) : null);

            return (object) [
                'source' => 'weighing',
                'id' => $w->id,
                'sort_at' => $w->weighed_at,
                'date' => $w->weighed_at,
                'time_at' => $w->weighed_at,
                'client' => $w->client,
                'type' => null,
                'plate1' => $w->plate1,
                'plate2' => $w->plate2,
                'brutto' => $brutto,
                'tara' => $tara,
                'netto' => $netto,
                'driver_name' => null,
                'goods' => $w->goods,
                'notes' => $w->notes,
                'has_weight' => $w1 !== null,
                'has_partial' => false,
                'plac_closed' => null, // luźne — brak ikon statusu
            ];
        });

        return $a->concat($b)->sortByDesc('sort_at')->values();
    }

    public function store(Request $request)
    {
        $request->validate([
            'weighed_at' => ['required', 'date'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'plate1' => ['nullable', 'string', 'max:20'],
            'plate2' => ['nullable', 'string', 'max:20'],
            'weight1' => ['required', 'numeric', 'min:0'],
            'weight2' => ['nullable', 'numeric', 'min:0'],
            'goods' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $w1 = (float) $request->weight1;
        $w2 = $request->filled('weight2') ? (float) $request->weight2 : null;

        // Reguła: jeśli oba wagi i NIE wybrano zlecenia → wymagany towar
        if ($w2 !== null && ! $request->order_id && ! $request->filled('goods')) {
            return response()->json([
                'success' => false,
                'errors' => ['goods' => ['Podaj towar gdy obie wagi są wpisane.']],
            ], 422);
        }

        DB::transaction(function () use ($request, $w1, $w2) {
            if ($request->order_id) {
                // Ważenie powiązane ze zleceniem → zapis bezpośrednio na orders
                $order = Order::findOrFail($request->order_id);

                $update = [];
                if ($w2 !== null) {
                    // Pełne ważenie
                    $update['weight_brutto'] = max($w1, $w2);
                    $update['weight_netto'] = round(abs($w1 - $w2), 3);
                    // Status idzie na 'weighed' tylko jeśli plac jeszcze nie zamknął
                    $closedStatuses = $order->type === 'sale' ? self::PLAC_CLOSED_SALE : self::PLAC_CLOSED_PICKUP;
                    if (! in_array($order->status, $closedStatuses, true)) {
                        $update['status'] = 'weighed';
                    }
                } else {
                    // Częściowe ważenie — zapisujemy w1 jako weight_brutto, netto null (placeholder)
                    $update['weight_brutto'] = $w1;
                    $update['weight_netto'] = null;
                }
                if ($request->filled('notes')) {
                    $update['notes'] = $request->notes;
                }
                $order->update($update);
            } else {
                // Ważenie luźne (bez zlecenia) → zapis do weighings
                Weighing::create([
                    'weighed_at' => $request->weighed_at,
                    'client_id' => $request->client_id ?: null,
                    'order_id' => null,
                    'plate1' => $request->plate1,
                    'plate2' => $request->plate2,
                    'weight1' => $w1,
                    'weight2' => $w2,
                    'goods' => $request->goods,
                    'notes' => $request->notes,
                    'source' => 'manual',
                    'created_by_user' => null,
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function update(Request $request, Weighing $weighing)
    {
        $request->validate([
            'weighed_at' => ['required', 'date'],
            'weight1' => ['required', 'numeric', 'min:0'],
            'weight2' => ['nullable', 'numeric', 'min:0'],
            'goods' => ['nullable', 'string', 'max:255'],
        ]);

        $w1 = (float) $request->weight1;
        $w2 = $request->filled('weight2') ? (float) $request->weight2 : null;

        // Reguła: oba wagi → wymagany towar
        if ($w2 !== null && ! $request->filled('goods')) {
            return response()->json([
                'success' => false,
                'errors' => ['goods' => ['Podaj towar gdy obie wagi są wpisane.']],
            ], 422);
        }

        $weighing->update([
            'weighed_at' => $request->weighed_at,
            'client_id' => $request->client_id ?: null,
            'plate1' => $request->plate1,
            'plate2' => $request->plate2,
            'weight1' => $w1,
            'weight2' => $w2,
            'goods' => $request->goods,
            'notes' => $request->notes,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Aktualizacja wag dla zlecenia (zamiast tworzenia weighing).
     * Używana gdy operator edytuje wiersz pochodzący z orders.
     */
    public function updateOrderWeight(Request $request, Order $order)
    {
        $request->validate([
            'weight1' => ['required', 'numeric', 'min:0'],
            'weight2' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $w1 = (float) $request->weight1;
        $w2 = $request->filled('weight2') ? (float) $request->weight2 : null;

        $update = [];
        if ($w2 !== null) {
            // Pełne ważenie
            $update['weight_brutto'] = max($w1, $w2);
            $update['weight_netto'] = round(abs($w1 - $w2), 3);
            $closedStatuses = $order->type === 'sale' ? self::PLAC_CLOSED_SALE : self::PLAC_CLOSED_PICKUP;
            if (! in_array($order->status, $closedStatuses, true)) {
                $update['status'] = 'weighed';
            }
        } else {
            // Częściowe — w1 jako placeholder w weight_brutto, netto null
            $update['weight_brutto'] = $w1;
            $update['weight_netto'] = null;
        }
        if ($request->has('notes')) {
            $update['notes'] = $request->notes;
        }
        $order->update($update);

        return response()->json(['success' => true]);
    }

    public function archived()
    {
        $standalone = Weighing::with(['client'])
            ->whereNull('order_id')
            ->where('is_archived', true)
            ->orderByDesc('weighed_at')
            ->get();

        $orderRows = Order::with(['client', 'tractor', 'trailer', 'driver', 'loadingItems.fraction'])
            ->where('is_archived', true)
            ->where(function ($q) {
                $q->whereNotNull('weight_netto')
                    ->orWhere(function ($q2) {
                        $q2->where('type', 'pickup')->whereIn('status', self::PLAC_CLOSED_PICKUP);
                    })
                    ->orWhere(function ($q2) {
                        $q2->where('type', 'sale')->whereIn('status', self::PLAC_CLOSED_SALE);
                    });
            })
            ->orderByDesc('updated_at')
            ->limit(200)
            ->get();

        $rows = $this->mergeRows($orderRows, $standalone);

        return view('biuro.weighings.archived', compact('rows'));
    }

    public function archive(Weighing $weighing)
    {
        $weighing->update(['is_archived' => true]);

        return response()->json(['success' => true]);
    }

    public function unarchive(Weighing $weighing)
    {
        $weighing->update(['is_archived' => false]);

        return response()->json(['success' => true]);
    }

    public function archiveOrder(Order $order)
    {
        $order->update(['is_archived' => true]);

        return response()->json(['success' => true]);
    }

    public function unarchiveOrder(Order $order)
    {
        $order->update(['is_archived' => false]);

        return response()->json(['success' => true]);
    }

    public function edit(Weighing $weighing)
    {
        return response()->json([
            'source' => 'weighing',
            'id' => $weighing->id,
            'weighed_at_input' => $weighing->weighed_at->format('Y-m-d\TH:i'),
            'client_id' => $weighing->client_id,
            'order_id' => null,
            'order_label' => null,
            'order_type' => null,
            'plate1' => $weighing->plate1,
            'plate2' => $weighing->plate2,
            'weight1' => $weighing->weight1,
            'weight2' => $weighing->weight2,
            'goods' => $weighing->goods,
            'notes' => $weighing->notes,
        ]);
    }

    /**
     * Edycja wagi powiązanej ze zleceniem (z tabeli orders).
     */
    public function editOrder(Order $order)
    {
        $order->load(['client', 'tractor', 'trailer', 'loadingItems.fraction']);

        // Wyznacz w1/w2 zgodnie z typem zlecenia
        // pickup: w1 = brutto, w2 = tara
        // sale:   w1 = tara,   w2 = brutto
        $brutto = $order->weight_brutto !== null ? (float) $order->weight_brutto : null;
        $netto = $order->weight_netto !== null ? (float) $order->weight_netto : null;

        if ($brutto !== null && $netto === null) {
            // Stan częściowy — weight_brutto przechowuje surową w1 (pierwsze ważenie)
            $w1 = $brutto;
            $w2 = null;
        } elseif ($brutto !== null && $netto !== null) {
            // Pełne ważenie — semantyczna interpretacja brutto/tara
            $tara = round($brutto - $netto, 3);
            if ($order->type === 'sale') {
                $w1 = $tara;
                $w2 = $brutto;
            } else {
                $w1 = $brutto;
                $w2 = $tara;
            }
        } else {
            $w1 = null;
            $w2 = null;
        }

        $goods = $order->loadingItems->pluck('fraction.name')->filter()->unique()->implode(', ');

        return response()->json([
            'source' => 'order',
            'id' => $order->id,
            'weighed_at_input' => optional($order->updated_at)->format('Y-m-d\TH:i'),
            'client_id' => $order->client_id,
            'order_id' => $order->id,
            'order_label' => ($order->client?->short_name ?? '?').' · '.optional($order->planned_date)->format('d.m'),
            'order_type' => $order->type,
            'plate1' => $order->tractor?->plate,
            'plate2' => $order->trailer?->plate,
            'weight1' => $w1,
            'weight2' => $w2,
            'goods' => $goods ?: null,
            'notes' => $order->notes,
        ]);
    }

    public function allTares()
    {
        $sets = VehicleSet::with(['tractor:id,plate', 'trailer:id,plate'])
            ->where('is_active', true)
            ->orderBy('label')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'label' => $s->label,
                'tare_kg' => $s->tare_kg,
                'tractor_plate' => $s->tractor?->plate,
                'trailer_plate' => $s->trailer?->plate,
            ]);

        return response()->json(['sets' => $sets]);
    }

    public function tareForVehicles(Request $request)
    {
        $plate1 = $request->input('plate1');
        $plate2 = $request->input('plate2');

        $set = VehicleSet::findForVehicles(
            optional(Vehicle::where('plate', $plate1)->first())->id,
            optional(Vehicle::where('plate', $plate2)->first())->id
        );

        return response()->json([
            'found' => (bool) $set,
            'tare' => $set?->tare_kg,
            'label' => $set?->label,
        ]);
    }

    public function destroy(Weighing $weighing)
    {
        $weighing->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Usunięcie wagi ze zlecenia — wagi czyszczone, status revert do 'loaded' (sale) / 'planned' (pickup).
     * Używane gdy biuro chce żeby kierowca/biuro mógł wpisać wagę ponownie.
     */
    public function destroyOrderWeight(Order $order)
    {
        $hasLoading = WarehouseItem::where('origin_order_id', $order->id)
            ->where('origin', 'loading')
            ->exists();

        $order->update([
            'weight_brutto' => null,
            'weight_netto' => null,
            'status' => $hasLoading ? 'loaded' : 'planned',
        ]);

        return response()->json(['success' => true]);
    }
}
