<?php

namespace App\Http\Controllers\Kierowca;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Order;
use App\Models\VehicleSet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function getDriver(): ?Driver
    {
        $authUser = auth()->user();
        return Driver::where('user_id', $authUser->id)->first();
    }

    public function index(Request $request)
    {
        $date = $request->filled('data')
            ? Carbon::parse($request->input('data'))->startOfDay()
            : Carbon::today();

        $driver = $this->getDriver();

        if (!$driver) {
            abort(403, 'Brak przypisanego kierowcy dla tego konta.');
        }

        $orders = Order::with([
            'client', 'startClient', 'tractor', 'trailer',
            'lieferschein.importer', 'lieferschein.goods',
            'loadingItems',
        ])
            ->where('driver_id', $driver->id)
            ->whereDate('planned_date', $date)
            ->orderByRaw("CASE WHEN status IN ('closed','weighed') AND type = 'pickup' THEN 1 WHEN status = 'closed' THEN 1 ELSE 0 END")
            ->orderBy('planned_time')
            ->get();

        return view('kierowca.dashboard', compact('driver', 'orders', 'date'));
    }

    // Formularz ważenia
    public function weighForm(Order $order)
    {
        $driver = $this->getDriver();

        if (!$driver || $order->driver_id !== $driver->id) {
            abort(403);
        }

        // Tara z zestawu przypisanego do zlecenia lub domyślnego zestawu kierowcy
        $tractorId  = $order->tractor_id ?? $driver->tractor_id;
        $trailerId  = $order->trailer_id ?? $driver->trailer_id;
        $vehicleSet = VehicleSet::findForVehicles($tractorId, $trailerId);

        return view('kierowca.weigh', compact('order', 'driver', 'vehicleSet'));
    }

    // Oblicz i zapisz wagę
    public function weighSave(Request $request, Order $order)
    {
        $driver = $this->getDriver();

        if (!$driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $request->validate([
            'weight_brutto'   => ['required', 'numeric', 'min:1'],
            'vehicle_set_id'  => ['required', 'exists:vehicle_sets,id'],
        ], [
            'weight_brutto.required' => 'Podaj wskazanie wagi.',
            'weight_brutto.min'      => 'Wskazanie wagi musi być większe od 0.',
            'vehicle_set_id.required'=> 'Wybierz zestaw pojazdów.',
        ]);

        $set   = VehicleSet::findOrFail($request->vehicle_set_id);
        $brutto = (float) $request->weight_brutto;
        $tare   = (float) $set->tare_kg;
        $netto  = round($brutto - $tare, 3);

        return response()->json([
            'success'    => true,
            'brutto'     => $brutto,
            'tare'       => $tare,
            'netto'      => $netto,
            'set_label'  => $set->label,
        ]);
    }

    // Potwierdź i zapisz do bazy
    public function weighConfirm(Request $request, Order $order)
    {
        $driver = $this->getDriver();

        if (!$driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $request->validate([
            'weight_brutto'  => ['required', 'numeric', 'min:0.001'],
            'weight_netto'   => ['required', 'numeric'],
            'vehicle_set_id' => ['required', 'exists:vehicle_sets,id'],
        ]);

        $order->update([
            'weight_brutto' => $request->weight_brutto,
            'weight_netto'  => $request->weight_netto,
            'status'        => 'weighed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Waga zapisana. Status zmieniony na: Zważone.',
            'netto'   => $request->weight_netto,
        ]);
    }

    // Waga odbiorcy
    public function saveReceiverWeight(Request $request, Order $order)
    {
        $driver = $this->getDriver();
        if (!$driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $request->validate([
            'weight_receiver' => ['required', 'numeric', 'min:0.001'],
        ], [
            'weight_receiver.required' => 'Podaj wagę odbiorcy.',
            'weight_receiver.min'      => 'Waga musi być większa od 0.',
        ]);

        $order->update([
            'weight_receiver' => $request->weight_receiver,
            'status'          => $order->type === 'sale' ? 'closed' : $order->status,
        ]);

        return response()->json(['success' => true, 'weight_receiver' => $order->weight_receiver]);
    }

    // Zmiana statusu
    public function setStatus(Request $request, Order $order)
    {
        $driver = $this->getDriver();

        if (!$driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $request->validate(['status' => ['required', 'string']]);
        $order->update(['status' => $request->status]);

        return response()->json(['success' => true, 'status' => $order->status]);
    }
}
