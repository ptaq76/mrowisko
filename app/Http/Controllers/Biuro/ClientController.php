<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientAddress;
use App\Models\ClientContact;
use App\Models\User;
use App\Services\GusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function data()
    {
        $clients = Client::with('salesman')
            ->orderBy('short_name')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'short_name' => $c->short_name,
                'nip' => $c->nip,
                'city' => $c->city,
                'street' => $c->street,
                'type' => $c->type,
                'salesman_id' => $c->salesman_id,
                'salesman_name' => $c->salesman?->name,
                'is_active' => $c->is_active,
            ]);

        return response()->json($clients);
    }

    public function index(Request $request)
    {
        return view('biuro.clients.index');
    }

    public function create()
    {
        $salesmen = User::where('module', 'handlowiec')->orderBy('name')->get();
        $types = Client::TYPES;
        $countries = Client::COUNTRIES;

        return view('biuro.clients.create', compact('salesmen', 'types', 'countries'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['required', 'string', 'max:255', 'unique:clients,short_name'],
            'nip' => ['nullable', 'string', 'max:50', 'unique:clients,nip'],
            'bdo' => ['nullable', 'string', 'max:50'],
            'country' => ['required', Rule::in(['PL', 'DE'])],
            'type' => ['required', Rule::in(['pickup', 'sale', 'both'])],
            'street' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
            'salesman_id' => ['required', 'exists:users,id'],
            'add_address' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Pełna nazwa jest wymagana.',
            'short_name.required' => 'Nazwa skrócona jest wymagana.',
            'short_name.unique' => 'Ta nazwa skrócona jest już zajęta.',
            'type.required' => 'Wybierz typ kontrahenta.',
            'street.required' => 'Ulica jest wymagana.',
            'postal_code.required' => 'Kod pocztowy jest wymagany.',
            'salesman_id.required' => 'Handlowiec jest wymagany.',
            'nip.unique' => 'Ten NIP jest już zarejestrowany w systemie.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $addAddress = $request->boolean('add_address');
        unset($data['add_address']);

        $client = Client::create($data);

        // Automatycznie dodaj adres na podstawie danych głównych
        if ($addAddress && $client->street && $client->city) {
            $client->addresses()->create([
                'city' => $client->city,
                'postal_code' => $client->postal_code,
                'street' => $client->street,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kontrahent został dodany.',
            'redirect' => route('biuro.clients.show', $client),
            'client_id' => $client->id,
            'short_name' => $client->short_name,
        ]);
    }

    public function show(Client $client)
    {
        $client->load([
            'salesman',
            'addresses',
            'contacts' => function ($q) {
                $q->orderBy('category')->orderBy('name');
            },
        ]);

        $contactsByCategory = $client->contacts->groupBy('category');

        return view('biuro.clients.show', compact('client', 'contactsByCategory'));
    }

    public function edit(Client $client)
    {
        $salesmen = User::where('module', 'handlowiec')->orderBy('name')->get();
        $types = Client::TYPES;
        $countries = Client::COUNTRIES;

        return view('biuro.clients.edit', compact('client', 'salesmen', 'types', 'countries'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['required', 'string', 'max:255', Rule::unique('clients', 'short_name')->ignore($client->id)],
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('clients', 'nip')->ignore($client->id)],
            'bdo' => ['nullable', 'string', 'max:50'],
            'country' => ['required', Rule::in(['PL', 'DE'])],
            'type' => ['required', Rule::in(['pickup', 'sale', 'both'])],
            'street' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
            'salesman_id' => ['required', 'exists:users,id'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $client->update($data);

        return redirect()->route('biuro.clients.show', $client)
            ->with('success', 'Dane kontrahenta zostały zaktualizowane.');
    }

    // ── GUS ───────────────────────────────────────────────────────────────────

    public function gusLookup(Request $request, GusService $gus)
    {
        $nip = $request->input('nip', '');
        $data = $gus->getByNip($nip);

        return response()->json($data);
    }

    // ── Adresy ────────────────────────────────────────────────────────────────

    public function storeAddress(Request $request, Client $client)
    {
        $request->validate([
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'street' => ['required', 'string', 'max:255'],
            'hours' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'distance_km' => ['nullable', 'integer'],
        ]);

        $client->addresses()->create($request->all());

        return redirect()->route('biuro.clients.show', $client)
            ->with('success', 'Adres został dodany.');
    }

    public function updateAddress(Request $request, Client $client, ClientAddress $address)
    {
        $request->validate([
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'street' => ['required', 'string', 'max:255'],
            'hours' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'distance_km' => ['nullable', 'integer'],
        ]);

        $address->update($request->all());

        return redirect()->route('biuro.clients.show', $client)
            ->with('success', 'Adres został zaktualizowany.');
    }

    public function destroyAddress(Client $client, ClientAddress $address)
    {
        $address->delete();

        return redirect()->route('biuro.clients.show', $client)
            ->with('success', 'Adres został usunięty.');
    }

    // ── Kontakty ──────────────────────────────────────────────────────────────

    public function storeContact(Request $request, Client $client)
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(['awizacje', 'faktury', 'handlowe'])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $client->contacts()->create($data);

        return redirect()->route('biuro.clients.show', $client)
            ->with('success', 'Kontakt został dodany.');
    }

    public function destroyContact(Client $client, ClientContact $contact)
    {
        $contact->delete();

        return redirect()->route('biuro.clients.show', $client)
            ->with('success', 'Kontakt został usunięty.');
    }
}
