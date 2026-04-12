<?php

namespace App\Http\Controllers\Handlowiec;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\PickupRequest;
use App\Models\PickupRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('handlowiec.dashboard');
    }

    public function klienci()
    {
        $klienci = Client::where('salesman_id', auth()->user()->id)
            ->where('is_active', true)
            ->orderBy('short_name')
            ->get();

        return view('handlowiec.klienci', compact('klienci'));
    }

    public function klientEdit(Client $client)
    {
        $this->authorizujKlienta($client);
        return view('handlowiec.klient_edit', compact('client'));
    }

    public function klientUpdate(Request $request, Client $client)
    {
        $this->authorizujKlienta($client);

        $request->validate([
            'name'        => ['required', 'string', 'max:200'],
            'short_name'  => ['nullable', 'string', 'max:50'],
            'nip'         => ['nullable', 'string', 'max:20'],
            'bdo'         => ['nullable', 'string', 'max:50'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'email'       => ['nullable', 'email', 'max:100'],
            'street'      => ['nullable', 'string', 'max:200'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city'        => ['nullable', 'string', 'max:100'],
            'notes'       => ['nullable', 'string'],
        ]);

        $client->update($request->only(
            'name', 'short_name', 'nip', 'bdo',
            'phone', 'email', 'street', 'postal_code', 'city', 'notes'
        ));

        return response()->json(['success' => true]);
    }

    public function zlecenia(Request $request)
    {
        $userId = auth()->user()->id;

        $query = PickupRequest::with(['client', 'order', 'items'])
            ->where('salesman_id', $userId)
            ->orderByDesc('created_at');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $zlecenia = $query->get();

        $klienci = Client::where('salesman_id', $userId)
            ->where('is_active', true)
            ->orderBy('short_name')
            ->get();

        return view('handlowiec.zlecenia', compact('zlecenia', 'klienci'));
    }

    public function historiaKlienta(Client $client)
    {
        $this->authorizujKlienta($client);

        $historia = PickupRequest::with(['items', 'order'])
            ->where('client_id', $client->id)
            ->where('salesman_id', auth()->user()->id)
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return response()->json($historia);
    }

    public function noweZlecenie()
    {
        $klienci = Client::where('salesman_id', auth()->user()->id)
            ->where('is_active', true)
            ->orderBy('short_name')
            ->get();

        return view('handlowiec.nowe_zlecenie', compact('klienci'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'      => ['required', 'exists:clients,id'],
            'requested_date' => ['required', 'date', 'after_or_equal:today'],
            'notes'          => ['nullable', 'string'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.nazwa'  => ['required', 'string', 'max:200'],
            'items.*.cena'   => ['nullable', 'numeric', 'min:0'],
            'items.*.ilosc'  => ['nullable', 'string', 'max:100'],
        ], [
            'client_id.required'            => 'Wybierz klienta.',
            'requested_date.required'       => 'Podaj datę odbioru.',
            'requested_date.after_or_equal' => 'Data nie może być w przeszłości.',
            'items.required'                => 'Dodaj co najmniej jeden towar.',
            'items.min'                     => 'Dodaj co najmniej jeden towar.',
            'items.*.nazwa.required'        => 'Podaj nazwę towaru.',
        ]);

        $client = Client::where('id', $request->client_id)
            ->where('salesman_id', auth()->user()->id)
            ->firstOrFail();

        DB::transaction(function () use ($request, $client) {
            $pickup = PickupRequest::create([
                'client_id'      => $client->id,
                'salesman_id'    => auth()->user()->id,
                'requested_date' => $request->requested_date,
                'notes'          => $request->notes,
                'status'         => 'nowe',
            ]);

            foreach ($request->items as $item) {
                PickupRequestItem::create([
                    'pickup_request_id' => $pickup->id,
                    'nazwa'             => $item['nazwa'],
                    'cena'              => $item['cena'] ?? null,
                    'ilosc'             => $item['ilosc'] ?? null,
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    private function authorizujKlienta(Client $client): void
    {
        if ((int)$client->salesman_id !== (int)auth()->user()->id) {
            abort(403);
        }
    }
}