<?php

namespace App\Http\Controllers\Hakowiec;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Zadanie;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function getDriver(): ?Driver
    {
        return Driver::where('user_id', auth()->user()->id)->first();
    }

    public function index(Request $request)
    {
        $date = $request->filled('data')
            ? Carbon::parse($request->input('data'))->startOfDay()
            : Carbon::today();

        $driver = $this->getDriver();

        $orders = collect();
        $zadania = collect();

        if ($driver) {
            $orders = Order::with([
                'client', 'startClient', 'tractor', 'trailer',
                'lieferschein.importer', 'loadingItems',
                'orderContainers.container',
            ])
                ->where('driver_id', $driver->id)
                ->whereDate('planned_date', $date)
                ->orderByRaw("CASE WHEN status IN ('closed','weighed') THEN 1 ELSE 0 END")
                ->orderBy('planned_time')
                ->get();

            $zadania = Zadanie::with('creator')
                ->forDriver($driver->id)
                ->onDate($date)
                ->orderBy('status')
                ->orderBy('id')
                ->get();
        }

        return view('hakowiec.dashboard', compact('driver', 'orders', 'zadania', 'date'));
    }

    public function setStatus(Request $request, Order $order)
    {
        $driver = $this->getDriver();

        if (! $driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $request->validate(['status' => ['required', 'string']]);

        $order->update(['status' => $request->status]);

        return response()->json(['success' => true, 'status' => $order->status]);
    }
}
