<?php

namespace App\Http\Controllers\Kierowca;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Opakowanie;
use App\Models\Order;
use App\Models\OrderPackaging;
use App\Models\Vehicle;
use App\Models\VehicleSet;
use App\Models\Zadanie;
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

        if (! $driver) {
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

        $zadania = Zadanie::forDriver($driver->id)
            ->onDate($date)
            ->orderBy('status')
            ->orderBy('id')
            ->get();

        return view('kierowca.dashboard', compact('driver', 'orders', 'date', 'zadania'));
    }

    // Formularz ważenia - sprawdza czy hakowiec i przekierowuje
    public function weighForm(Order $order)
    {
        $driver = $this->getDriver();

        if (! $driver || $order->driver_id !== $driver->id) {
            abort(403);
        }

        // ZMIANA: Pobieramy tylko to, co jest przypisane bezpośrednio do zlecenia
        $tractor = $order->tractor; // Usunięto: ?? $driver->tractor
        $trailer = $order->trailer; // Usunięto: ?? $driver->trailer

        // Sprawdź czy mamy ciągnik (skoro nie ma domyślnego, musimy sprawdzić czy w ogóle jest)
        if (! $tractor) {
            // Opcjonalnie: możesz wyrzucić błąd, jeśli zlecenie nie ma przypisanego auta
            abort(404, 'Do tego zlecenia nie przypisano pojazdu (samochodu).');
        }

        // Sprawdź czy hakowiec
        $tractorIsHakowiec = $tractor && $tractor->subtype === 'hakowiec';

        // Reszta logiki pozostaje bez zmian...
        if ($tractorIsHakowiec) {
            return $this->weighFormHakowiec($order, $driver, $tractor, $trailer);
        }

        $tractorId = $tractor?->id;
        $trailerId = $trailer?->id;
        $vehicleSet = VehicleSet::findForVehicles($tractorId, $trailerId);

        $opakowania = Opakowanie::active()->orderBy('name')->get();

        return view('kierowca.weigh', compact('order', 'driver', 'vehicleSet', 'opakowania'));
    }

    // Formularz ważenia dla hakowca
    private function weighFormHakowiec(Order $order, Driver $driver, Vehicle $tractor, ?Vehicle $trailer)
    {
        // Pobierz zestawy dla samochodu (gdzie label zaczyna się od numeru rej.)
        $tractorPlate = $tractor->plate;
        $tractorSets = VehicleSet::where('is_active', true)
            ->where('label', 'LIKE', $tractorPlate.'%')
            ->orderBy('label')
            ->get();

        // Pobierz zestawy dla naczepy (jeśli jest)
        $trailerSets = collect();
        if ($trailer) {
            $trailerPlate = $trailer->plate;
            $trailerSets = VehicleSet::where('is_active', true)
                ->where('label', 'LIKE', $trailerPlate.'%')
                ->orderBy('label')
                ->get();
        }

        return view('kierowca.weigh_hakowiec', compact(
            'order', 'driver', 'tractor', 'trailer',
            'tractorSets', 'trailerSets'
        ));
    }

    // Oblicz i zapisz wagę (standardowe)
    public function weighSave(Request $request, Order $order)
    {
        $driver = $this->getDriver();

        if (! $driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $request->validate([
            'weight_brutto' => ['required', 'numeric', 'min:1'],
            'vehicle_set_id' => ['required', 'exists:vehicle_sets,id'],
        ], [
            'weight_brutto.required' => 'Podaj wskazanie wagi.',
            'weight_brutto.min' => 'Wskazanie wagi musi być większe od 0.',
            'vehicle_set_id.required' => 'Wybierz zestaw pojazdów.',
        ]);

        $set = VehicleSet::findOrFail($request->vehicle_set_id);
        $brutto = (float) $request->weight_brutto;
        $tare = (float) $set->tare_kg;
        $netto = round($brutto - $tare, 3);

        return response()->json([
            'success' => true,
            'brutto' => $brutto,
            'tare' => $tare,
            'netto' => $netto,
            'set_label' => $set->label,
        ]);
    }

    // Potwierdź i zapisz do bazy (standardowe)
    public function weighConfirm(Request $request, Order $order)
    {
        $driver = $this->getDriver();

        if (! $driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $request->validate([
            'weight_brutto' => ['required', 'numeric', 'min:0.001'],
            'weight_netto' => ['required', 'numeric'],
            'vehicle_set_id' => ['required', 'exists:vehicle_sets,id'],
        ]);

        $order->update([
            'weight_brutto' => $request->weight_brutto,
            'weight_netto' => $request->weight_netto,
            'status' => 'weighed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Waga zapisana. Status zmieniony na: Zważone.',
            'netto' => $request->weight_netto,
        ]);
    }

    // Potwierdź i zapisz do bazy (hakowiec)
    public function weighConfirmHakowiec(Request $request, Order $order)
    {
        $driver = $this->getDriver();

        if (! $driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $request->validate([
            'tractor_set_id' => ['required', 'exists:vehicle_sets,id'],
            'tractor_brutto' => ['required', 'numeric', 'min:0.001'],
            'trailer_set_id' => ['nullable', 'exists:vehicle_sets,id'],
            'trailer_brutto' => ['nullable', 'numeric', 'min:0.001'],
        ]);

        // Samochód
        $tractorSet = VehicleSet::findOrFail($request->tractor_set_id);
        $tractorBrutto = (float) $request->tractor_brutto;
        $tractorTare = (float) $tractorSet->tare_kg;
        $tractorNetto = round($tractorBrutto - $tractorTare, 3);

        // Naczepa (opcjonalna)
        $trailerNetto = 0;
        $trailerBrutto = 0;
        $trailerTare = 0;
        $trailerSet = null;

        if ($request->filled('trailer_set_id') && $request->filled('trailer_brutto')) {
            $trailerSet = VehicleSet::findOrFail($request->trailer_set_id);
            $trailerBrutto = (float) $request->trailer_brutto;
            $trailerTare = (float) $trailerSet->tare_kg;
            $trailerNetto = round($trailerBrutto - $trailerTare, 3);
        }

        // Suma
        $totalBrutto = $tractorBrutto + $trailerBrutto;
        $totalNetto = $tractorNetto + $trailerNetto;

        // Uwagi kierowcy
        $driverNotes = sprintf(
            '%.3f t - %.3f t = %.3f t (%s)',
            $tractorBrutto, $tractorTare, $tractorNetto, $tractorSet->label
        );

        if ($trailerSet) {
            $driverNotes .= "\n".sprintf(
                '%.3f t - %.3f t = %.3f t (%s)',
                $trailerBrutto, $trailerTare, $trailerNetto, $trailerSet->label
            );
        }

        $order->update([
            'weight_brutto' => $totalBrutto,
            'weight_netto' => $totalNetto,
            'driver_notes' => $driverNotes,
            'status' => 'weighed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Waga zapisana. Status zmieniony na: Zważone.',
            'netto' => $totalNetto,
            'driver_notes' => $driverNotes,
        ]);
    }

    // Zapisz opakowania dla zlecenia
    public function savePackaging(Request $request, Order $order)
    {
        $driver = $this->getDriver();

        if (! $driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $request->validate([
            'packaging' => ['required', 'array'],
            'packaging.*.opakowanie_id' => ['required', 'exists:opakowania,id'],
            'packaging.*.quantity' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($request->packaging as $item) {
            $qty = (int) $item['quantity'];

            if ($qty > 0) {
                OrderPackaging::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'opakowanie_id' => $item['opakowanie_id'],
                    ],
                    ['quantity' => $qty]
                );
            } else {
                // Jeśli 0 — usuń wpis jeśli istnieje
                OrderPackaging::where('order_id', $order->id)
                    ->where('opakowanie_id', $item['opakowanie_id'])
                    ->delete();
            }
        }

        return response()->json(['success' => true]);
    }

    // Waga odbiorcy
    public function saveReceiverWeight(Request $request, Order $order)
    {
        $driver = $this->getDriver();
        if (! $driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $request->validate([
            'weight_receiver' => ['required', 'numeric', 'min:0.001'],
        ], [
            'weight_receiver.required' => 'Podaj wagę odbiorcy.',
            'weight_receiver.min' => 'Waga musi być większa od 0.',
        ]);

        $order->update([
            'weight_receiver' => $request->weight_receiver,
            'status' => $order->type === 'sale' ? 'closed' : $order->status,
        ]);

        return response()->json(['success' => true, 'weight_receiver' => $order->weight_receiver]);
    }

    // Moje kursy
    public function kursy()
    {
        $driver = $this->getDriver();
        if (! $driver) {
            abort(403);
        }

        $orders = Order::with(['client'])
            ->where('driver_id', $driver->id)
            ->whereNotNull('planned_date')
            ->orderByDesc('planned_date')
            ->limit(300)
            ->get();

        $clients = $orders->pluck('client')->filter()->unique('id')->sortBy('short_name')->values();

        return view('kierowca.kursy', compact('orders', 'clients', 'driver'));
    }

    // Zmiana statusu
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