<?php

namespace App\Http\Controllers\Plac;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoadingController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('data')
            ? Carbon::parse($request->input('data'))->startOfDay()
            : Carbon::today();

        $closedStatuses = ['loaded', 'weighed', 'delivered', 'closed'];

        $orders = Order::with(['client', 'driver', 'tractor', 'trailer', 'loadingItems.fraction', 'lieferschein.importer'])
            ->where('type', 'sale')
            ->where(function ($q) use ($date, $closedStatuses) {
                $q->whereDate('planned_date', $date)
                    ->orWhere(function ($q2) use ($date, $closedStatuses) {
                        $q2->whereDate('planned_date', '<', $date)
                            ->whereNotIn('status', $closedStatuses);
                    });
            })
            ->orderByRaw("CASE WHEN status IN ('loaded','weighed','delivered','closed') THEN 1 ELSE 0 END")
            ->orderBy('planned_time')
            ->get();

        $placStatus = function ($order) {
            // Wszystko od "loaded" wzwyż jest dla placu zamknięte (loaded/weighed/delivered/closed)
            if (in_array($order->status, ['loaded', 'weighed', 'delivered', 'closed'])) {
                return ['label' => 'Zamknięty', 'class' => 'sp-closed', 'done' => true];
            }
            if ($order->loadingItems->isNotEmpty()) {
                return ['label' => 'W trakcie', 'class' => 'sp-progress', 'done' => false];
            }

            return ['label' => 'Zaplanowany', 'class' => 'sp-planned', 'done' => false];
        };

        return view('plac.loading_list', compact('orders', 'date', 'placStatus'));
    }
}
