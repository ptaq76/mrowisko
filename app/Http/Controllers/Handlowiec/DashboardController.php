<?php

namespace App\Http\Controllers\Handlowiec;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientAddress;
use App\Models\ClientContact;
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

    // ── Klienci ──────────────────────────────────────────────────────────────

    public function klienci()
    {
        $klienci = Client::where('salesman_id', auth()->user()->id)
            ->where('is_active', true)
            ->orderBy('short_name')
            ->get();

        return view('handlowiec.klienci', compact('klienci'));
    }

    public function nowyKlient()
    {
        return view('handlowiec.nowy_klient');
    }

    public function storeKlient(Request $request)
    {
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'short_name'       => ['required', 'string', 'max:255', 'unique:clients,short_name'],
            'nip'              => ['nullable', 'string', 'max:50', 'unique:clients,nip'],
            'type'             => ['required', 'in:pickup,sale,both'],
            'street'           => ['required', 'string', 'max:255'],
            'postal_code'      => ['required', 'string', 'max:20'],
            'city'             => ['nullable', 'string', 'max:255'],
            'addr_street'      => ['required', 'string', 'max:255'],
            'addr_postal_code' => ['required', 'string', 'max:20'],
            'addr_city'        => ['required', 'string', 'max:255'],
            'addr_hours'       => ['nullable', 'string', 'max:100'],
            'addr_notes'       => ['nullable', 'string'],
            'contact_category' => ['required', 'in:awizacje,faktury,handlowe'],
            'contact_name'     => ['required', 'string', 'max:255'],
            'contact_phone'    => ['nullable', 'string', 'max:50'],
            'contact_email'    => ['nullable', 'email', 'max:255'],
        ], [
            'name.required'             => 'Pełna nazwa jest wymagana.',
            'short_name.required'       => 'Nazwa skrócona jest wymagana.',
            'short_name.unique'         => 'Ta nazwa skrócona jest już zajęta.',
            'nip.unique'                => 'Ten NIP jest już zarejestrowany w systemie.',
            'type.required'             => 'Wybierz typ kontrahenta.',
            'street.required'           => 'Ulica firmy jest wymagana.',
            'postal_code.required'      => 'Kod pocztowy firmy jest wymagany.',
            'addr_street.required'      => 'Ulica punktu odbioru jest wymagana.',
            'addr_postal_code.required' => 'Kod pocztowy punktu odbioru jest wymagany.',
            'addr_city.required'        => 'Miasto punktu odbioru jest wymagane.',
            'contact_name.required'     => 'Imię i nazwisko kontaktu jest wymagane.',
        ]);

        DB::transaction(function () use ($request) {
            $client = Client::create([
                'name'        => $request->name,
                'short_name'  => $request->short_name,
                'nip'         => $request->nip ?: null,
                'type'        => $request->type,
                'street'      => $request->street,
                'postal_code' => $request->postal_code,
                'city'        => $request->city,
                'salesman_id' => auth()->user()->id,
                'is_active'   => true,
                'country'     => 'PL',
            ]);

            $client->addresses()->create([
                'street'      => $request->addr_street,
                'postal_code' => $request->addr_postal_code,
                'city'        => $request->addr_city,
                'hours'       => $request->addr_hours ?: null,
                'notes'       => $request->addr_notes ?: null,
            ]);

            $client->contacts()->create([
                'category' => $request->contact_category,
                'name'     => $request->contact_name,
                'phone'    => $request->contact_phone ?: null,
                'email'    => $request->contact_email ?: null,
            ]);
        });

        return response()->json(['success' => true]);
    }

    public function klientEdit(Client $client)
    {
        $this->authorizujKlienta($client);
        $client->load(['addresses', 'contacts']);
        return view('handlowiec.klient_edit', compact('client'));
    }

    public function klientUpdate(Request $request, Client $client)
    {
        $this->authorizujKlienta($client);

        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'short_name'  => ['required', 'string', 'max:255'],
            'nip'         => ['nullable', 'string', 'max:50'],
            'type'        => ['required', 'in:pickup,sale,both'],
            'street'      => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city'        => ['nullable', 'string', 'max:255'],
        ]);

        $client->update($request->only('name','short_name','nip','type','street','postal_code','city'));

        return response()->json(['success' => true]);
    }

    // ── Adresy ────────────────────────────────────────────────────────────────

    public function storeAddress(Request $request, Client $client)
    {
        $this->authorizujKlienta($client);
        $request->validate([
            'street'      => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city'        => ['required', 'string', 'max:255'],
            'hours'       => ['nullable', 'string', 'max:100'],
            'notes'       => ['nullable', 'string'],
        ]);
        $client->addresses()->create($request->only('street','postal_code','city','hours','notes'));
        return response()->json(['success' => true]);
    }

    public function updateAddress(Request $request, Client $client, ClientAddress $address)
    {
        $this->authorizujKlienta($client);
        $request->validate([
            'street'      => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city'        => ['required', 'string', 'max:255'],
            'hours'       => ['nullable', 'string', 'max:100'],
            'notes'       => ['nullable', 'string'],
        ]);
        $address->update($request->only('street','postal_code','city','hours','notes'));
        return response()->json(['success' => true]);
    }

    public function destroyAddress(Client $client, ClientAddress $address)
    {
        $this->authorizujKlienta($client);
        $address->delete();
        return response()->json(['success' => true]);
    }

    // ── Kontakty ──────────────────────────────────────────────────────────────

    public function storeContact(Request $request, Client $client)
    {
        $this->authorizujKlienta($client);
        $request->validate([
            'category' => ['required', 'in:awizacje,faktury,handlowe'],
            'name'     => ['required', 'string', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:50'],
            'email'    => ['nullable', 'email', 'max:255'],
        ]);
        $client->contacts()->create($request->only('category','name','phone','email'));
        return response()->json(['success' => true]);
    }

    public function destroyContact(Client $client, ClientContact $contact)
    {
        $this->authorizujKlienta($client);
        $contact->delete();
        return response()->json(['success' => true]);
    }

    // ── Moje zlecenia ─────────────────────────────────────────────────────────

    public function zlecenia(Request $request)
    {
        $userId = auth()->user()->id;

        $query = PickupRequest::with(['client', 'order', 'items'])
            ->where('salesman_id', $userId)
            ->orderByDesc('created_at');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

    // ── Nowe zlecenie ─────────────────────────────────────────────────────────

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

    // ── Sprawdź NIP ───────────────────────────────────────────────────────────

    public function checkNip(Request $request)
    {
        $nip    = $request->input('nip', '');
        $exists = Client::where('nip', $nip)->exists();
        return response()->json(['exists' => $exists]);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function authorizujKlienta(Client $client): void
    {
        if ((int)$client->salesman_id !== (int)auth()->user()->id) {
            abort(403);
        }
    }
}