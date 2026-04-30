<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Lieferschein;
use App\Models\Order;
use App\Models\PickupRequest;
use App\Models\Zadanie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanningController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('data')
            ? Carbon::parse($request->input('data'))->startOfDay()
            : Carbon::today();

        $driverId = $request->input('kierowca');

        // Wszyscy aktywni kierowcy
        $drivers = Driver::where('is_active', true)
            ->with(['tractor', 'trailer'])
            ->orderBy('name')
            ->get();

        // Aktywny kierowca
        $driver = $driverId
            ? $drivers->firstWhere('id', $driverId)
            : $drivers->first();

        // Zlecenia aktywnego kierowcy na wybrany dzień
        $orders = Order::with([
            'client', 'startClient', 'tractor', 'trailer',
            'lieferschein.importer', 'loadingItems', 'warehouseLoadingItems', 'warehouseDeliveryItems',
        ])
            ->where('driver_id', $driver?->id)
            ->whereDate('planned_date', $date)
            ->orderBy('planned_time')
            ->get();

        // Tydzień do lewej kolumny (bieżący tydzień)
        $startOfWeek = $date->copy()->startOfWeek();
        $weekDays = collect();
        for ($i = 0; $i < 7; $i++) {
            $d = $startOfWeek->copy()->addDays($i);
            $weekOrders = Order::with(['client', 'driver', 'tractor', 'trailer', 'loadingItems'])
                ->whereDate('planned_date', $d)
                ->orderBy('planned_time')
                ->get();
            $weekDays->put($d->format('Y-m-d'), [
                'date' => $d,
                'orders' => $weekOrders,
            ]);
        }

        // Top 10 klientów do szybkich przycisków
        $topPickup = Order::where('type', 'pickup')
            ->select('client_id', DB::raw('count(*) as cnt'))
            ->groupBy('client_id')->orderByDesc('cnt')->take(10)->get()
            ->map(fn ($r) => Client::find($r->client_id))
            ->filter()->values();

        $topSale = Order::where('type', 'sale')
            ->select('client_id', DB::raw('count(*) as cnt'))
            ->groupBy('client_id')->orderByDesc('cnt')->take(10)->get()
            ->map(fn ($r) => Client::find($r->client_id))
            ->filter()->values();

        // Wolne LS (nieprzypisane do zlecenia) na wybrany dzień
        $freeLs = Lieferschein::whereDate('date', $date)
            ->where('is_used', false)
            ->with(['client', 'importer'])
            ->get();

        // Zlecenia handlowców – nowe i przyjęte
        $pickupRequests = PickupRequest::whereIn('status', ['nowe'])
            ->with(['client:id,short_name', 'salesman:id,name', 'items'])
            ->orderBy('requested_date')
            ->get();

        // Zadania na wybrany dzień dla aktywnego kierowcy
        $zadania = $driver
            ? Zadanie::with('creator')
                ->forDriver($driver->id)
                ->onDate($date)
                ->orderBy('status')
                ->orderBy('id')
                ->get()
            : collect();

        return view('biuro.planning.index', compact(
            'date', 'drivers', 'driver', 'orders',
            'weekDays', 'topPickup', 'topSale', 'freeLs',
            'pickupRequests', 'zadania'
        ));
    }

    public function planNaPlac(Request $request)
    {
        $date = $request->has('data')
            ? Carbon::parse($request->input('data'))->startOfDay()
            : Carbon::today();

        // Zlecenia widoczne na placu tego dnia
        $orders = Order::with([
            'client', 'startClient', 'driver', 'tractor', 'trailer',
            'lieferschein.importer', 'loadingItems.fraction', 'warehouseLoadingItems',
        ])
            ->whereNotNull('plac_date')
            ->whereDate('plac_date', $date)
            ->orderBy('planned_time')
            ->get();

        // Grupuj po kierowcach jak w module plac
        $drivers = $orders->groupBy('driver_id');

        $zadania = Zadanie::forPlac()
            ->onDate($date)
            ->orderBy('status')
            ->orderBy('id')
            ->get();

        return view('biuro.reports.plan_na_plac', compact('orders', 'drivers', 'date', 'zadania'));
    }
}
