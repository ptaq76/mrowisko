<?php

namespace App\Http\Controllers\Kierowca;

use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\ContainerStock;
use App\Models\Driver;
use App\Models\Opakowanie;
use App\Models\Order;
use App\Models\OrderContainer;
use App\Models\OrderPackaging;
use App\Models\Vehicle;
use App\Models\VehicleSet;
use App\Models\Zadanie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'orderContainers.container',
            'packaging.opakowanie',
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

        // Kontenery z ilością na placu — dla modala "Pozostawione kontenery"
        $containers = Container::active()
            ->leftJoin('container_stock', function ($j) {
                $j->on('containers.id', '=', 'container_stock.container_id')
                    ->whereNull('container_stock.client_id');
            })
            ->orderBy('containers.name')
            ->get([
                'containers.id',
                'containers.name',
                DB::raw('COALESCE(container_stock.quantity, 0) as plac_qty'),
            ]);

        // Kontenery dostępne u klienta — dla modala "Zabrane kontenery"
        // (per client_id, używane gdy biuro zważyło i kierowca-hakowiec musi odhaczyć
        // co zabrał od klienta).
        $pickupContainersByClient = [];
        $hakowiecClientIds = $orders
            ->filter(fn ($o) => $o->tractor?->subtype === 'hakowiec' && $o->client_id)
            ->pluck('client_id')
            ->unique()
            ->values();

        if ($hakowiecClientIds->isNotEmpty()) {
            $rows = Container::active()
                ->join('container_stock', 'containers.id', '=', 'container_stock.container_id')
                ->whereIn('container_stock.client_id', $hakowiecClientIds)
                ->where('container_stock.quantity', '>', 0)
                ->orderBy('containers.name')
                ->get([
                    'containers.id',
                    'containers.name',
                    'container_stock.client_id',
                    DB::raw('container_stock.quantity as client_qty'),
                ]);

            foreach ($rows as $r) {
                $pickupContainersByClient[$r->client_id][] = [
                    'id' => $r->id,
                    'name' => $r->name,
                    'client_qty' => (int) $r->client_qty,
                ];
            }
        }

        return view('kierowca.dashboard', compact(
            'driver', 'orders', 'date', 'zadania', 'containers', 'pickupContainersByClient'
        ));
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

    // Formularz ważenia dla hakowca — pokazuje kontenery dostępne u tego klienta
    private function weighFormHakowiec(Order $order, Driver $driver, Vehicle $tractor, ?Vehicle $trailer)
    {
        $clientId = $order->client_id;

        // Kontenery aktualnie u klienta + (na wypadek re-weigh) te już wybrane jako pickup
        $pickupIds = $order->orderContainers()
            ->where('direction', 'pickup')
            ->pluck('container_id')->all();

        $containers = Container::active()
            ->leftJoin('container_stock', function ($j) use ($clientId) {
                $j->on('containers.id', '=', 'container_stock.container_id')
                    ->where('container_stock.client_id', $clientId);
            })
            ->where(function ($q) use ($pickupIds) {
                $q->where('container_stock.quantity', '>', 0);
                if ($pickupIds) {
                    $q->orWhereIn('containers.id', $pickupIds);
                }
            })
            ->orderBy('containers.name')
            ->get([
                'containers.id',
                'containers.name',
                'containers.tare_kg',
                DB::raw('COALESCE(container_stock.quantity, 0) as client_qty'),
            ]);

        return view('kierowca.weigh_hakowiec', compact(
            'order', 'driver', 'tractor', 'trailer', 'containers'
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
            'weight_netto' => ['required', 'numeric', 'min:0'],
            'vehicle_set_id' => ['required', 'exists:vehicle_sets,id'],
        ], [
            'weight_netto.min' => 'Waga netto nie może być ujemna.',
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

        $hasTrailer = (bool) $order->trailer_id;
        $requiredSlots = $hasTrailer ? 2 : 1;

        $reportedDrops = $order->orderContainers()->where('direction', 'drop')->count();
        if ($reportedDrops < $requiredSlots) {
            return response()->json([
                'success' => false,
                'message' => 'Najpierw zgłoś pozostawione kontenery.',
            ], 422);
        }

        $rules = [
            'tractor_container_id' => ['required', 'exists:containers,id'],
            'tractor_brutto'       => ['required', 'numeric', 'min:0.001'],
        ];
        if ($hasTrailer) {
            $rules['trailer_container_id'] = ['required', 'exists:containers,id'];
            $rules['trailer_brutto']       = ['required', 'numeric', 'min:0.001'];
        }
        $request->validate($rules);

        $order->loadMissing('tractor', 'trailer');

        // Samochód
        $tractorContainer = Container::findOrFail($request->tractor_container_id);
        $tractorBrutto = (float) $request->tractor_brutto;
        $tractorTare = (float) ($order->tractor?->tare_kg ?? 0) + (float) $tractorContainer->tare_kg;
        $tractorNetto = round($tractorBrutto - $tractorTare, 3);

        if ($tractorNetto < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Waga samochodu jest niższa niż tara — netto byłoby ujemne.',
            ], 422);
        }

        // Naczepa (opcjonalna)
        $trailerNetto = 0;
        $trailerBrutto = 0;
        $trailerContainer = null;

        if ($hasTrailer) {
            $trailerContainer = Container::findOrFail($request->trailer_container_id);
            $trailerBrutto = (float) $request->trailer_brutto;
            $trailerTare = (float) ($order->trailer?->tare_kg ?? 0) + (float) $trailerContainer->tare_kg;
            $trailerNetto = round($trailerBrutto - $trailerTare, 3);

            if ($trailerNetto < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waga naczepy jest niższa niż tara — netto byłoby ujemne.',
                ], 422);
            }
        }

        $totalBrutto = $tractorBrutto + $trailerBrutto;
        $totalNetto = $tractorNetto + $trailerNetto;

        try {
            DB::transaction(function () use ($order, $totalBrutto, $totalNetto, $tractorContainer, $trailerContainer, $hasTrailer) {
                $order->update([
                    'weight_brutto' => $totalBrutto,
                    'weight_netto'  => $totalNetto,
                    'status'        => 'weighed',
                ]);

                // Cofnij stare pickup-y (jeśli re-weigh): kontener wraca z placu do klienta
                $oldPickups = $order->orderContainers()->where('direction', 'pickup')->get();
                foreach ($oldPickups as $oc) {
                    ContainerStock::moveToClient($oc->container_id, $order->client_id, 1);
                }
                $order->orderContainers()->where('direction', 'pickup')->delete();

                $pickups = [
                    ['slot' => 'tractor', 'container' => $tractorContainer],
                ];
                if ($hasTrailer && $trailerContainer) {
                    $pickups[] = ['slot' => 'trailer', 'container' => $trailerContainer];
                }

                foreach ($pickups as $p) {
                    ContainerStock::moveToPlac($p['container']->id, $order->client_id, 1);

                    OrderContainer::create([
                        'order_id'     => $order->id,
                        'container_id' => $p['container']->id,
                        'slot'         => $p['slot'],
                        'direction'    => 'pickup',
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Waga zapisana. Status zmieniony na: Zważone.',
            'netto' => $totalNetto,
        ]);
    }

    // Zgłoś pozostawione kontenery u klienta (hakowiec)
    public function dropContainers(Request $request, Order $order)
    {
        $driver = $this->getDriver();

        if (! $driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $requiredSlots = $order->trailer_id ? ['tractor', 'trailer'] : ['tractor'];

        $rules = [
            'tractor_container_id' => ['required', 'exists:containers,id'],
        ];
        if (in_array('trailer', $requiredSlots)) {
            $rules['trailer_container_id'] = ['required', 'exists:containers,id'];
        }

        $request->validate($rules, [
            'tractor_container_id.required' => 'Wybierz kontener pozostawiony przez samochód.',
            'trailer_container_id.required' => 'Wybierz kontener pozostawiony przez naczepę.',
        ]);

        try {
            DB::transaction(function () use ($order, $request, $requiredSlots) {
                // Cofnij stare drop-y (jeśli re-edycja): kontener wraca z klienta na plac
                $oldDrops = $order->orderContainers()->where('direction', 'drop')->get();
                foreach ($oldDrops as $oc) {
                    ContainerStock::moveToPlac($oc->container_id, $order->client_id, 1);
                }
                $order->orderContainers()->where('direction', 'drop')->delete();

                $entries = [
                    ['slot' => 'tractor', 'container_id' => (int) $request->tractor_container_id],
                ];
                if (in_array('trailer', $requiredSlots)) {
                    $entries[] = ['slot' => 'trailer', 'container_id' => (int) $request->trailer_container_id];
                }

                foreach ($entries as $entry) {
                    ContainerStock::moveToClient($entry['container_id'], $order->client_id, 1);

                    OrderContainer::create([
                        'order_id'     => $order->id,
                        'container_id' => $entry['container_id'],
                        'slot'         => $entry['slot'],
                        'direction'    => 'drop',
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json(['success' => true, 'message' => 'Pozostawione kontenery zapisane.']);
    }

    // Zarejestruj zabrane od klienta kontenery (hakowiec)
    // Używane gdy biuro wpisało wagę zamiast kierowcy — kierowca dalej musi
    // odhaczyć co zabrał, bo normalnie te dane wpadają przy weighConfirmHakowiec.
    public function pickupContainers(Request $request, Order $order)
    {
        $driver = $this->getDriver();

        if (! $driver || $order->driver_id !== $driver->id) {
            return response()->json(['success' => false, 'message' => 'Brak dostępu.'], 403);
        }

        $requiredSlots = $order->trailer_id ? ['tractor', 'trailer'] : ['tractor'];

        $rules = [
            'tractor_container_id' => ['required', 'exists:containers,id'],
        ];
        if (in_array('trailer', $requiredSlots)) {
            $rules['trailer_container_id'] = ['required', 'exists:containers,id'];
        }

        $request->validate($rules, [
            'tractor_container_id.required' => 'Wybierz kontener zabrany przez samochód.',
            'trailer_container_id.required' => 'Wybierz kontener zabrany przez naczepę.',
        ]);

        try {
            DB::transaction(function () use ($order, $request, $requiredSlots) {
                // Cofnij stare pickup-y (jeśli re-edycja): kontener wraca z placu do klienta
                $oldPickups = $order->orderContainers()->where('direction', 'pickup')->get();
                foreach ($oldPickups as $oc) {
                    ContainerStock::moveToClient($oc->container_id, $order->client_id, 1);
                }
                $order->orderContainers()->where('direction', 'pickup')->delete();

                $entries = [
                    ['slot' => 'tractor', 'container_id' => (int) $request->tractor_container_id],
                ];
                if (in_array('trailer', $requiredSlots)) {
                    $entries[] = ['slot' => 'trailer', 'container_id' => (int) $request->trailer_container_id];
                }

                foreach ($entries as $entry) {
                    ContainerStock::moveToPlac($entry['container_id'], $order->client_id, 1);

                    OrderContainer::create([
                        'order_id'     => $order->id,
                        'container_id' => $entry['container_id'],
                        'slot'         => $entry['slot'],
                        'direction'    => 'pickup',
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json(['success' => true, 'message' => 'Zabrane kontenery zapisane.']);
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