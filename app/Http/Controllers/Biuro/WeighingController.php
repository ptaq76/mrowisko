<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Hauler;
use App\Models\Order;
use App\Models\Weighing;
use App\Models\WasteFraction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WeighingController extends Controller
{
    public function index(Request $request)
    {
        // Ważenia ręczne (nie zarchiwizowane)
        $manual = Weighing::with(['client', 'order'])
            ->where('source', 'manual')
            ->where('is_archived', false)
            ->orderByDesc('weighed_at')
            ->get();

        // Ważenia kierowców z tabeli orders
        $driver = Order::with(['client', 'tractor', 'trailer', 'driver'])
            ->whereNotNull('weight_netto')
            ->orderByDesc('updated_at')
            ->limit(100)
            ->get();

        // Zlecenia aktywne (niezakończone) do szybkiego przypisania
        $activeOrders = Order::with(['client'])
            ->whereNotIn('status', ['closed'])
            ->whereDate('planned_date', '>=', now()->subDays(3))
            ->orderBy('planned_date')
            ->get();

        $clients   = Client::orderBy('short_name')->get();
        $haulers   = Hauler::with('client')->orderBy('sort_order')->get();
        $fractions = WasteFraction::where('is_active', true)->orderBy('name')->get();

        return view('biuro.weighings.index', compact(
            'manual', 'driver', 'activeOrders', 'clients', 'haulers', 'fractions'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'weighed_at' => ['required', 'date'],
            'client_id'  => ['nullable', 'exists:clients,id'],
            'order_id'   => ['nullable', 'exists:orders,id'],
            'plate1'     => ['nullable', 'string', 'max:20'],
            'plate2'     => ['nullable', 'string', 'max:20'],
            'weight1'    => ['required', 'numeric', 'min:0'],
            'weight2'    => ['nullable', 'numeric', 'min:0'],
            'goods'      => ['nullable', 'string', 'max:255'],
            'notes'      => ['nullable', 'string'],
        ]);

        $weighing = DB::transaction(function () use ($request) {
            $w = Weighing::create([
                'weighed_at'      => $request->weighed_at,
                'client_id'       => $request->client_id ?: null,
                'order_id'        => $request->order_id ?: null,
                'plate1'          => $request->plate1,
                'plate2'          => $request->plate2,
                'weight1'         => $request->weight1,
                'weight2'         => $request->weight2 ?: null,
                'goods'           => $request->goods,
                'notes'           => $request->notes,
                'source'          => 'manual',
                'created_by_user' => null,
            ]);

            // Jeśli push_to_order i obie wagi podane - zapisz na zlecenie
            if ($request->push_to_order && $request->order_id && $request->weight1 && $request->weight2) {
                $order = Order::find($request->order_id);
                if ($order) {
                    $brutto = (float) $request->weight1;
                    $netto  = round(abs((float)$request->weight1 - (float)$request->weight2), 3);
                    $order->update([
                        'weight_brutto' => $brutto,
                        'weight_netto'  => $netto,
                        'status'        => 'weighed',
                    ]);
                }
            }

            // Archiwizuj ważenie jeśli przekazano na plac
            if ($request->archive_after) {
                $w->update(['is_archived' => true]);
            }

            return $w;
        });

        return response()->json([
            'success'  => true,
            'weighing' => [
                'id'         => $weighing->id,
                'weighed_at' => $weighing->weighed_at->format('d.m.Y H:i'),
                'client'     => $weighing->client?->short_name ?? '–',
                'plate1'     => $weighing->plate1,
                'plate2'     => $weighing->plate2,
                'weight1'    => $weighing->weight1,
                'weight2'    => $weighing->weight2,
                'result'     => $weighing->result,
                'goods'      => $weighing->goods,
                'notes'      => $weighing->notes,
            ],
        ]);
    }

    public function update(Request $request, Weighing $weighing)
    {
        $request->validate([
            'weighed_at' => ['required', 'date'],
            'weight1'    => ['required', 'numeric', 'min:0'],
            'weight2'    => ['nullable', 'numeric', 'min:0'],
        ]);

        $weighing->update([
            'weighed_at' => $request->weighed_at,
            'client_id'  => $request->client_id ?: null,
            'order_id'   => $request->order_id ?: null,
            'plate1'     => $request->plate1,
            'plate2'     => $request->plate2,
            'weight1'    => $request->weight1,
            'weight2'    => $request->weight2 ?: null,
            'goods'      => $request->goods,
            'notes'      => $request->notes,
        ]);

        if ($request->push_to_order && $weighing->order_id && $request->weight1 && $request->weight2) {
            $order = Order::find($weighing->order_id);
            if ($order) {
                $order->update([
                    'weight_brutto' => (float) $request->weight1,
                    'weight_netto'  => round(abs((float)$request->weight1 - (float)$request->weight2), 3),
                    'status'        => 'weighed',
                ]);
            }
        }

        if ($request->archive_after) {
            $weighing->update(['is_archived' => true]);
        }

        return response()->json(['success' => true, 'result' => $weighing->fresh()->result]);
    }

    public function archived()
    {
        $weighings = Weighing::with(['client'])
            ->where('is_archived', true)
            ->orderByDesc('weighed_at')
            ->get();

        return view('biuro.weighings.archived', compact('weighings'));
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

    public function edit(Weighing $weighing)
    {
        $order = $weighing->order_id ? \App\Models\Order::with('client')->find($weighing->order_id) : null;
        return response()->json([
            'id'               => $weighing->id,
            'weighed_at_input' => $weighing->weighed_at->format('Y-m-d\TH:i'),
            'client_id'        => $weighing->client_id,
            'order_id'         => $weighing->order_id,
            'order_label'      => $order ? ($order->client?->short_name . ' · ' . $order->planned_date->format('d.m')) : null,
            'order_type'       => $order?->type,
            'plate1'           => $weighing->plate1,
            'plate2'           => $weighing->plate2,
            'weight1'          => $weighing->weight1,
            'weight2'          => $weighing->weight2,
            'goods'            => $weighing->goods,
            'notes'            => $weighing->notes,
        ]);
    }

    public function destroy(Weighing $weighing)
    {
        $weighing->delete();
        return response()->json(['success' => true]);
    }
}
