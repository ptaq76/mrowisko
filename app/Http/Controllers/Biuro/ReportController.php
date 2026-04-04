<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Order;
use App\Models\WasteFraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function loadings(Request $request)
    {
        $query = Order::with([
            'client', 'tractor', 'trailer', 'driver',
            'loadingItems.fraction',
        ])
            ->where('type', 'sale')
            ->whereIn('status', ['loaded', 'weighed', 'closed'])
            ->where('is_archived', false)
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                  ->from('warehouse_items')
                  ->whereColumn('warehouse_items.origin_order_id', 'orders.id')
                  ->where('warehouse_items.origin', 'loading');
            });

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('fraction_id')) {
            $query->whereHas('loadingItems', function ($q) use ($request) {
                $q->where('fraction_id', $request->fraction_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('planned_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('planned_date', '<=', $request->date_to);
        }

        $orders = $query->orderByDesc('planned_date')->get();

        $clients   = Client::orderBy('short_name')->get();
        $fractions = WasteFraction::where('allows_belka', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('biuro.reports.loadings', compact('orders', 'clients', 'fractions'));
    }

    public function loadingsArchived(Request $request)
    {
        $query = Order::with([
            'client', 'tractor', 'trailer', 'driver',
            'loadingItems.fraction',
        ])
            ->where('type', 'sale')
            ->where('is_archived', true);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $orders = $query->orderByDesc('planned_date')->get();
        $clients = Client::orderBy('short_name')->get();

        return view('biuro.reports.loadings_archived', compact('orders', 'clients'));
    }

    public function archive(Order $order)
    {
        $order->update(['is_archived' => true]);
        return response()->json(['success' => true]);
    }

    public function revert(Order $order)
    {
        \DB::transaction(function () use ($order) {
            // Usuń wpisy magazynowe z tego załadunku
            \App\Models\WarehouseItem::where('origin', 'loading')
                ->where('origin_order_id', $order->id)
                ->delete();

            // Ustaw właściwy status po cofnięciu załadunku
            if ($order->weight_netto) {
                // Waga już była podana – wróć do weighed
                $order->update(['status' => 'weighed']);
            } else {
                // Wagi nie było – wróć do planned
                $order->update(['status' => 'planned']);
            }
        });

        return response()->json(['success' => true]);
    }

    public function unarchive(Order $order)
    {
        $order->update(['is_archived' => false]);
        return response()->json(['success' => true]);
    }
    public function weighings(Request $request)
    {
        $query = Order::with(['client', 'tractor', 'trailer', 'driver'])
            ->whereNotNull('weight_netto')
            ->select('orders.*'); // weight_receiver jest już kolumną na orders

        $dateFrom = $request->filled('date_from') ? $request->date_from : now()->subDays(7)->format('Y-m-d');
        $dateTo   = $request->filled('date_to')   ? $request->date_to   : now()->format('Y-m-d');

        $query->whereDate('planned_date', '>=', $dateFrom)
              ->whereDate('planned_date', '<=', $dateTo);
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $orders  = $query->orderByDesc('planned_date')->orderByDesc('id')->get();
        $drivers = Driver::orderBy('name')->get();
        $clients = Client::whereIn('id',
            Order::whereNotNull('weight_netto')
                ->whereDate('planned_date', '>=', $dateFrom)
                ->whereDate('planned_date', '<=', $dateTo)
                ->pluck('client_id')->unique()
        )->orderBy('short_name')->get();

        return view('biuro.reports.weighings', compact('orders', 'drivers', 'clients'));
    }

    public function revertWeighing(Order $order)
    {
        // Cofnij status – zachowaj wagi w bazie
        // Jeśli waga była podana – wróć do weighed, inaczej do planned
        $hasLoading = \App\Models\WarehouseItem::where('origin_order_id', $order->id)
            ->where('origin', 'loading')->exists();

        if ($order->weight_netto) {
            // Waga była podana – status weighed niezależnie od załadunku
            $order->update(['status' => 'weighed']);
        } else {
            $order->update(['status' => $hasLoading ? 'loaded' : 'planned']);
        }

        return response()->json(['success' => true]);
    }

    public function deleteWeighing(Order $order)
    {
        // Usuń wagi i cofnij status
        $hasLoading = \App\Models\WarehouseItem::where('origin_order_id', $order->id)
            ->where('origin', 'loading')->exists();
        $order->update([
            'weight_brutto' => null,
            'weight_netto'  => null,
            'status'        => $hasLoading ? 'loaded' : 'planned',
        ]);

        return response()->json(['success' => true]);
    }
    public function deliveries(Request $request)
    {
        $dateFrom = $request->filled('date_from') ? $request->date_from : now()->subDays(30)->format('Y-m-d');
        $dateTo   = $request->filled('date_to')   ? $request->date_to   : now()->format('Y-m-d');

        $query = Order::with(['client', 'tractor', 'trailer', 'driver', 'loadingItems.fraction'])
            ->where('type', 'pickup')
            ->whereIn('status', ['delivered', 'closed'])
            ->where('is_archived', false)
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                  ->from('warehouse_items')
                  ->whereColumn('warehouse_items.origin_order_id', 'orders.id')
                  ->where('warehouse_items.origin', 'delivery');
            })
            ->whereDate('planned_date', '>=', $dateFrom)
            ->whereDate('planned_date', '<=', $dateTo);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('fraction_id')) {
            $query->whereHas('loadingItems', function ($q) use ($request) {
                $q->where('fraction_id', $request->fraction_id);
            });
        }

        $orders    = $query->orderByDesc('planned_date')->get();
        $clients   = Client::orderBy('short_name')->get();
        $fractions = WasteFraction::where('is_active', true)->orderBy('name')->get();

        return view('biuro.reports.deliveries', compact('orders', 'clients', 'fractions'));
    }

    public function archiveDelivery(Order $order)
    {
        $order->update(['is_archived' => true]);
        return response()->json(['success' => true]);
    }

    public function deliveriesArchived(Request $request)
    {
        $query = Order::with(['client', 'tractor', 'trailer', 'driver', 'loadingItems.fraction'])
            ->where('type', 'pickup')
            ->where('is_archived', true);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $orders  = $query->orderByDesc('planned_date')->get();
        $clients = Client::orderBy('short_name')->get();

        return view('biuro.reports.deliveries_archived', compact('orders', 'clients'));
    }

    public function unarchiveDelivery(Order $order)
    {
        $order->update(['is_archived' => false]);
        return response()->json(['success' => true]);
    }

    public function revertDelivery(Order $order)
    {
        \DB::transaction(function () use ($order) {
            // Usuń wpisy magazynowe z tej dostawy
            \App\Models\WarehouseItem::where('origin', 'delivery')
                ->where('origin_order_id', $order->id)
                ->delete();

            // Cofnij status do weighed (waga była podana)
            $order->update(['status' => $order->weight_netto ? 'weighed' : 'planned']);
        });

        return response()->json(['success' => true]);
    }
}
