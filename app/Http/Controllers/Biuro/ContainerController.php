<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Container;
use App\Models\ContainerStock;
use App\Models\OrderContainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContainerController extends Controller
{
    public function index(Request $request)
    {
        $query = Container::query();

        if ($request->filled('type') && in_array($request->type, ['zwykly', 'prasokontener'])) {
            $query->where('type', $request->type);
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }

        $containers = $query->orderBy('name')->get();

        // Doliczamy stock per typ
        $stockByContainer = ContainerStock::query()
            ->selectRaw('container_id, SUM(CASE WHEN client_id IS NULL THEN quantity ELSE 0 END) as plac_qty,
                                       SUM(CASE WHEN client_id IS NOT NULL THEN quantity ELSE 0 END) as client_qty')
            ->groupBy('container_id')
            ->get()
            ->keyBy('container_id');

        $containers->each(function ($c) use ($stockByContainer) {
            $row = $stockByContainer->get($c->id);
            $c->plac_qty = (int) ($row->plac_qty ?? 0);
            $c->client_qty = (int) ($row->client_qty ?? 0);
            $c->total_qty = $c->plac_qty + $c->client_qty;
        });

        // Filtr lokalizacji — robimy go po doliczeniu
        if ($request->filled('location')) {
            if ($request->location === 'plac') {
                $containers = $containers->filter(fn ($c) => $c->plac_qty > 0)->values();
            } elseif ($request->location === 'client') {
                $containers = $containers->filter(fn ($c) => $c->client_qty > 0)->values();
            }
        }

        $stats = [
            'plac'   => (int) ContainerStock::whereNull('client_id')->sum('quantity'),
            'client' => (int) ContainerStock::whereNotNull('client_id')->sum('quantity'),
            'total'  => (int) ContainerStock::sum('quantity'),
        ];

        $clients = Client::orderBy('short_name')->get(['id', 'short_name']);
        $filters = $request->only(['location', 'type', 'q']);

        return view('biuro.containers.index', compact('containers', 'clients', 'stats', 'filters'));
    }

    public function byClient()
    {
        // Plac
        $placStocks = ContainerStock::with('container:id,name,type')
            ->whereNull('client_id')
            ->where('quantity', '>', 0)
            ->get()
            ->sortBy(fn ($s) => $s->container->name)
            ->values();

        // Per klient
        $clientStocks = ContainerStock::with(['container:id,name,type', 'client:id,short_name'])
            ->whereNotNull('client_id')
            ->where('quantity', '>', 0)
            ->get()
            ->groupBy('client_id');

        $clients = Client::whereIn('id', $clientStocks->keys())
            ->orderBy('short_name')
            ->get(['id', 'short_name']);

        $stats = [
            'plac'   => (int) ContainerStock::whereNull('client_id')->sum('quantity'),
            'client' => (int) ContainerStock::whereNotNull('client_id')->sum('quantity'),
            'total'  => (int) ContainerStock::sum('quantity'),
        ];

        return view('biuro.containers.by_client', compact('placStocks', 'clientStocks', 'clients', 'stats'));
    }

    public function history(Container $container)
    {
        $entries = OrderContainer::with(['order:id,client_id,planned_date', 'order.client:id,short_name'])
            ->where('container_id', $container->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn ($oc) => [
                'created_at'    => $oc->created_at->format('Y-m-d H:i'),
                'order_id'      => $oc->order_id,
                'planned_date'  => $oc->order?->planned_date?->format('Y-m-d'),
                'client'        => $oc->order?->client?->short_name,
                'slot'          => $oc->slot,
                'direction'     => $oc->direction,
            ]);

        return response()->json([
            'success'   => true,
            'container' => ['id' => $container->id, 'name' => $container->name],
            'entries'   => $entries,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:50|unique:containers,name',
            'tare_kg'     => 'required|numeric|min:0',
            'type'        => 'required|in:zwykly,prasokontener',
            'plac_qty'    => 'nullable|integer|min:0',
            'notes'       => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Nazwa jest wymagana.',
            'name.unique'   => 'Kontener o tej nazwie już istnieje.',
            'tare_kg.required' => 'Tara jest wymagana.',
        ]);

        DB::transaction(function () use ($validated) {
            $c = Container::create([
                'name'      => $validated['name'],
                'tare_kg'   => $validated['tare_kg'],
                'type'      => $validated['type'],
                'is_active' => 1,
                'notes'     => $validated['notes'] ?? null,
            ]);

            if (! empty($validated['plac_qty']) && $validated['plac_qty'] > 0) {
                ContainerStock::create([
                    'container_id' => $c->id,
                    'client_id'    => null,
                    'quantity'     => (int) $validated['plac_qty'],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Kontener „'.$validated['name'].'" został dodany.',
        ]);
    }

    public function update(Request $request, Container $container)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:50|unique:containers,name,'.$container->id,
            'tare_kg'   => 'required|numeric|min:0',
            'type'      => 'required|in:zwykly,prasokontener',
            'is_active' => 'nullable|in:0,1',
            'notes'     => 'nullable|string|max:1000',
        ]);

        $validated['is_active'] = $request->input('is_active', 0);
        $container->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kontener „'.$container->name.'" został zaktualizowany.',
        ]);
    }

    public function destroy(Container $container)
    {
        $name = $container->name;

        $hasStock = ContainerStock::where('container_id', $container->id)->where('quantity', '>', 0)->exists();
        if ($hasStock) {
            return response()->json([
                'success' => false,
                'message' => 'Nie można usunąć — kontener ma stan na placu lub u klienta.',
            ], 422);
        }

        $container->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kontener „'.$name.'" został usunięty.',
        ]);
    }

    /**
     * Korekta stanu (przyrost/ubytek) na placu lub u klienta.
     */
    public function adjustStock(Request $request, Container $container)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'delta'     => 'required|integer|not_in:0',
        ], [
            'delta.not_in' => 'Korekta musi być różna od zera.',
        ]);

        try {
            ContainerStock::adjust(
                $container->id,
                $validated['client_id'] ?? null,
                (int) $validated['delta']
            );
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $location = empty($validated['client_id']) ? 'placu' : 'klienta';
        $sign = $validated['delta'] > 0 ? '+' : '';

        return response()->json([
            'success' => true,
            'message' => 'Stan '.$location.' zmieniony o '.$sign.$validated['delta'].' szt.',
        ]);
    }
}
