<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Lieferschein;
use App\Models\Order;
use App\Models\OrderQuickButton;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date'           => ['required', 'date'],
            'driver_id'      => ['required', 'exists:drivers,id'],
            'client_id'      => ['required', 'exists:clients,id'],
            'tractor_id'     => ['required', 'exists:vehicles,id'],
            'trailer_id'     => ['nullable', 'exists:vehicles,id'],
            'planned_time'   => ['nullable', 'string'],
            'fractions_note' => ['required', 'string'],
            'notes'          => ['nullable', 'string'],
            'lieferschein_id'=> ['nullable', 'exists:lieferscheins,id'],
            'type'           => ['required', Rule::in(['pickup', 'sale'])],
            'start_client_id'=> in_array((int)$request->driver_id, [4,7]) ? ['nullable', 'exists:clients,id'] : ['required', 'exists:clients,id'],
        ], [
            'date.required'           => 'Podaj datę.',
            'driver_id.required'      => 'Wybierz kierowcę.',
            'client_id.required'      => 'Wybierz kontrahenta.',
            'tractor_id.required'     => 'Wybierz ciągnik.',
            'fractions_note.required' => 'Podaj towary.',
        ]);

        // Sprawdź czy kolumna start_client_id istnieje w tabeli
        $hasStartClient = \Illuminate\Support\Facades\Schema::hasColumn('orders', 'start_client_id');

        $data = [
            'type'            => $request->type,
            'planned_date'    => $request->date,
            'planned_time'    => $request->planned_time ?: null,
            'driver_id'       => $request->driver_id,
            'client_id'       => $request->client_id,
            'tractor_id'      => $request->tractor_id,
            'trailer_id'      => $request->trailer_id ?: null,
            'fractions_note'  => $request->fractions_note,
            'notes'           => $request->notes ?: null,
            'lieferschein_id' => $request->lieferschein_id ?: null,
            'status'          => 'planned',
        ];

        if ($hasStartClient) {
            $data['start_client_id'] = $request->start_client_id ?: null;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'created_by')) {
            $data['created_by'] = null;
        }

        $order = Order::create($data);

        // Oznacz LS jako użyty
        if ($order->lieferschein_id) {
            Lieferschein::where('id', $order->lieferschein_id)->update(['is_used' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Zlecenie zostało dodane.', 'id' => $order->id, 'planned_date' => $order->planned_date]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'date'           => ['required', 'date'],
            'driver_id'      => ['required', 'exists:drivers,id'],
            'client_id'      => ['required', 'exists:clients,id'],
            'start_client_id'=> in_array((int)$request->driver_id, [4,7]) ? ['nullable', 'exists:clients,id'] : ['required', 'exists:clients,id'],
            'tractor_id'     => ['required', 'exists:vehicles,id'],
            'trailer_id'     => ['nullable', 'exists:vehicles,id'],
            'planned_time'   => ['nullable', 'string'],
            'fractions_note' => ['required', 'string'],
            'notes'          => ['nullable', 'string'],
            'lieferschein_id'=> ['nullable', 'exists:lieferscheins,id'],
            'type'           => ['required', Rule::in(['pickup', 'sale'])],
        ]);

        // Jeśli zmieniono LS - zwolnij stary, zajmij nowy
        if ($order->lieferschein_id && $order->lieferschein_id != $request->lieferschein_id) {
            Lieferschein::where('id', $order->lieferschein_id)->update(['is_used' => false]);
        }
        if ($request->lieferschein_id && $order->lieferschein_id != $request->lieferschein_id) {
            Lieferschein::where('id', $request->lieferschein_id)->update(['is_used' => true]);
        }

        $hasStartClient = \Illuminate\Support\Facades\Schema::hasColumn('orders', 'start_client_id');

        $oldDate    = $order->planned_date ? \Carbon\Carbon::parse($order->planned_date) : null;
        $newDate    = \Carbon\Carbon::parse($request->date);
        $dateChanged = $oldDate && !$oldDate->eq($newDate);

        // Proporcjonalne przesunięcie plac_date
        $newPlacDate = $order->plac_date;
        if ($dateChanged && $order->plac_date) {
            $oldPlac   = \Carbon\Carbon::parse($order->plac_date);
            $diffDays  = $oldPlac->diffInDays($oldDate, false); // ile dni przed zleceniem był plac
            $newPlacDate = $newDate->copy()->subDays(abs($diffDays));
            // Cofnij do dnia roboczego jeśli wypadł w weekend
            while ($newPlacDate->isWeekend()) {
                $newPlacDate->subDay();
            }
        }

        // Jeśli przesłano plac_date z formularza – nadpisz
        if ($request->filled('plac_date')) {
            $newPlacDate = $request->plac_date;
        }

        $updateData = [
            'type'            => $request->type,
            'planned_date'    => $request->date,
            'planned_time'    => $request->planned_time ?: null,
            'driver_id'       => $request->driver_id,
            'client_id'       => $request->client_id,
            'tractor_id'      => $request->tractor_id,
            'trailer_id'      => $request->trailer_id ?: null,
            'fractions_note'  => $request->fractions_note,
            'notes'           => $request->notes ?: null,
            'lieferschein_id' => $request->lieferschein_id ?: null,
            'plac_date'       => $newPlacDate,
        ];
        if ($hasStartClient) {
            $updateData['start_client_id'] = $request->start_client_id ?: null;
        }
        $order->update($updateData);

        $msg = 'Zlecenie zostało zaktualizowane.';
        if ($dateChanged && $order->plac_date) {
            $msg .= ' Data zlecenia na placu uległa zmianie proporcjonalnie.';
        }

        return response()->json(['success' => true, 'message' => $msg, 'date_changed' => $dateChanged]);
    }

    public function setPlacDate(Request $request, Order $order)
    {
        $request->validate(['plac_date' => ['nullable', 'date']]);
        $order->update(['plac_date' => $request->plac_date]);
        return response()->json(['success' => true]);
    }

    public function destroy(Order $order)
    {
        // Zwolnij LS
        if ($order->lieferschein_id) {
            Lieferschein::where('id', $order->lieferschein_id)->update(['is_used' => false]);
        }
        $order->delete();
        return response()->json(['success' => true, 'message' => 'Zlecenie zostało usunięte.']);
    }

    public function show(Order $order)
    {
        $order->load(['client', 'startClient', 'driver', 'tractor', 'trailer', 'lieferschein']);

        $data = $order->toArray();
        $data['planned_date'] = $order->planned_date?->format('Y-m-d');
        $data['plac_date']    = $order->plac_date?->format('Y-m-d');

        return response()->json($data);
    }

    public function setStatus(Request $request, Order $order)
    {
        $request->validate(['status' => ['required', 'string']]);
        $order->update(['status' => $request->status]);
        return response()->json(['success' => true, 'status' => $order->status]);
    }

    // Dane dla modala (AJAX)
    public function modalData(Request $request)
    {
        $drivers  = Driver::where('is_active', true)->with(['tractor', 'trailer'])->orderBy('name')->get();
        $clients  = Client::where('is_active', true)->orderBy('short_name')
                        ->get(['id', 'short_name', 'country', 'type']);
        $tractors = Vehicle::where('type', 'ciągnik')->where('is_active', true)->orderBy('plate')->get(['id', 'plate', 'subtype']);
        $trailers = Vehicle::where('type', 'naczepa')->where('is_active', true)->orderBy('plate')->get(['id', 'plate', 'subtype']);

        // Wolne LS - wszystkie nieużyte, niezależnie od daty
        $freeLs = Lieferschein::where('is_used', false)
            ->with(['client', 'importer', 'goods'])
            ->orderBy('date')
            ->get();

        // ID Leipa i Ewrant
        $leipa  = Client::where('short_name', 'LEIPA')->value('id');
        $ewrant = Client::where('short_name', 'EWRANT')->orWhere('short_name', 'Ewrant')->value('id');

        $quickGoods = OrderQuickButton::goods()->get(['id', 'label']);
        $quickNotes = OrderQuickButton::notes()->get(['id', 'label']);

        return response()->json(compact(
            'drivers', 'clients', 'tractors', 'trailers',
            'freeLs', 'leipa', 'ewrant', 'quickGoods', 'quickNotes'
        ));
    }

    // Konfiguracja przycisków
    public function quickButtons()
    {
        $buttons = OrderQuickButton::orderBy('type')->orderBy('sort')->get();
        return view('biuro.planning.quick_buttons', compact('buttons'));
    }

    public function quickButtonStore(Request $request)
    {
        $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'type'  => ['required', Rule::in(['goods', 'notes'])],
        ]);
        $max = OrderQuickButton::where('type', $request->type)->max('sort') ?? 0;
        OrderQuickButton::create([
            'label'     => $request->label,
            'type'      => $request->type,
            'sort'      => $max + 1,
            'is_active' => true,
        ]);
        return response()->json(['success' => true]);
    }

    public function quickButtonUpdate(Request $request, OrderQuickButton $button)
    {
        $request->validate([
            'label'     => ['required', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);
        $button->update($request->only('label', 'is_active'));
        return response()->json(['success' => true]);
    }

    public function quickButtonDestroy(OrderQuickButton $button)
    {
        $button->delete();
        return response()->json(['success' => true]);
    }
}
