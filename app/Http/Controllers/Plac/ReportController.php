<?php

namespace App\Http\Controllers\Plac;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function deliveries(Request $request)
    {
        $query = Order::with([
            'client', 'tractor', 'trailer', 'driver',
            'loadingItems.fraction',
            'packaging.opakowanie',
        ])
            ->where('type', 'pickup')
            ->whereIn('status', ['delivered', 'closed'])
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('warehouse_items')
                    ->whereColumn('warehouse_items.origin_order_id', 'orders.id')
                    ->where('warehouse_items.origin', 'delivery');
            });

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $orders = $query->orderByDesc('planned_date')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $clients = Client::whereHas('orders', function ($q) {
            $q->where('type', 'pickup')
                ->whereExists(function ($q2) {
                    $q2->select(\DB::raw(1))
                        ->from('warehouse_items')
                        ->whereColumn('warehouse_items.origin_order_id', 'orders.id')
                        ->where('warehouse_items.origin', 'delivery');
                });
        })
            ->orderBy('short_name')
            ->get();

        return view('plac.reports.deliveries', compact('orders', 'clients'));
    }

    public function loadings(Request $request)
    {
        $query = Order::with([
            'client', 'tractor', 'trailer', 'driver',
            'loadingItems.fraction',
            'packaging.opakowanie',
        ])
            ->where('type', 'sale')
            ->whereIn('status', ['loaded', 'weighed', 'closed'])
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('warehouse_items')
                    ->whereColumn('warehouse_items.origin_order_id', 'orders.id')
                    ->where('warehouse_items.origin', 'loading');
            });

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $orders = $query->orderByDesc('planned_date')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $clients = Client::whereHas('orders', function ($q) {
            $q->where('type', 'sale')
                ->whereExists(function ($q2) {
                    $q2->select(\DB::raw(1))
                        ->from('warehouse_items')
                        ->whereColumn('warehouse_items.origin_order_id', 'orders.id')
                        ->where('warehouse_items.origin', 'loading');
                });
        })
            ->orderBy('short_name')
            ->get();

        return view('plac.reports.loadings', compact('orders', 'clients'));
    }
}
