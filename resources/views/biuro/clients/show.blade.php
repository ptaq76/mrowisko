@extends('layouts.app')

@section('title', $client->short_name)
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1>{{ $client->short_name }}</h1>
        <span class="text-muted" style="font-size:14px">{{ $client->name }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('biuro.clients.edit', $client) }}" class="btn btn-edit">
            <i class="fa-solid fa-pen"></i> Edytuj
        </a>
        <a href="{{ route('biuro.clients.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Powrót
        </a>
    </div>
</div>

<div class="row g-3" style="max-width:75%">

    {{-- Dane podstawowe --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Dane podstawowe</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted ps-3" style="width:140px">Skrót</td>
                        <td><strong>{{ $client->short_name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">NIP</td>
                        <td>{{ $client->nip ?: '–' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">BDO</td>
                        <td>{{ $client->bdo ?: '–' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Ulica</td>
                        <td>{{ $client->street ?: '–' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Kod / Miasto</td>
                        <td>{{ trim($client->postal_code . ' ' . $client->city) ?: '–' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Typ</td>
                        <td><span class="badge {{ $client->typeColor() }}">{{ $client->typeName() }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Kraj</td>
                        <td>
                            <span class="badge {{ $client->country === 'DE' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                {{ $client->country === 'DE' ? 'Niemcy' : 'Polska' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Status</td>
                        <td>
                            @if($client->is_active)
                                <span class="badge bg-success">Aktywny</span>
                            @else
                                <span class="badge bg-danger">Nieaktywny</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Handlowiec</td>
                        <td>{{ $client->salesman?->name ?? '–' }}</td>
                    </tr>
                    @if($client->notes)
                    <tr>
                        <td class="text-muted ps-3">Uwagi</td>
                        <td class="small">{{ $client->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Kontakty --}}
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Osoby kontaktowe</span>
                <button class="btn btn-add btn-sm" data-bs-toggle="modal" data-bs-target="#addContactModal">
                    <i class="fa-solid fa-plus"></i> Dodaj
                </button>
            </div>
            <div class="card-body">
                @foreach(\App\Models\ClientContact::CATEGORIES as $cat => $catLabel)
                    @if(isset($contactsByCategory[$cat]) && $contactsByCategory[$cat]->count() > 0)
                    <div class="mb-3">
                        <div class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing:.06em">
                            {{ $catLabel }}
                        </div>
                        <div class="row g-2">
                            @foreach($contactsByCategory[$cat] as $contact)
                            <div class="col-md-3 col-6">
                                <div class="d-flex justify-content-between align-items-start bg-light rounded p-2 h-100">
                                    <div style="min-width:0">
                                        <div class="fw-500 text-truncate">{{ $contact->name }}</div>
                                        @if($contact->phone)
                                            <div class="text-muted small text-truncate">
                                                <i class="fa-solid fa-phone fa-xs"></i> {{ $contact->phone }}
                                            </div>
                                        @endif
                                        @if($contact->email)
                                            <div class="text-muted small text-truncate">
                                                <i class="fa-solid fa-envelope fa-xs"></i> {{ $contact->email }}
                                            </div>
                                        @endif
                                    </div>
                                    <form method="POST"
                                          action="{{ route('biuro.clients.contacts.destroy', [$client, $contact]) }}"
                                          onsubmit="return confirmDelete(this)"
                                          class="ms-1 flex-shrink-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
                @if($client->contacts->isEmpty())
                    <div class="text-center text-muted py-3">Brak osób kontaktowych.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Adresy odbioru/dostawy --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Adresy odbioru / dostawy</span>
                <button class="btn btn-add btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="fa-solid fa-plus"></i> Dodaj adres
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Miasto</th>
                            <th>Adres</th>
                            <th>Godziny</th>
                            <th>Dystans</th>
                            <th>Uwagi</th>
                            <th style="width:80px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($client->addresses as $addr)
                        <tr>
                            <td>{{ $addr->postal_code }} {{ $addr->city }}</td>
                            <td>{{ $addr->street }}</td>
                            <td>{{ $addr->hours ?: '–' }}</td>
                            <td>{{ $addr->distance_km ? $addr->distance_km . ' km' : '–' }}</td>
                            <td class="text-muted small">{{ $addr->notes ?: '' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-edit btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editAddressModal"
                                            data-id="{{ $addr->id }}"
                                            data-city="{{ $addr->city }}"
                                            data-postal="{{ $addr->postal_code }}"
                                            data-street="{{ $addr->street }}"
                                            data-hours="{{ $addr->hours }}"
                                            data-distance="{{ $addr->distance_km }}"
                                            data-notes="{{ $addr->notes }}"
                                            title="Edytuj">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <form method="POST"
                                          action="{{ route('biuro.clients.addresses.destroy', [$client, $addr]) }}"
                                          onsubmit="return confirmDelete(this)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Usuń">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Brak adresów.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Modal: edycja adresu --}}
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Edycja adresu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAddressForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kod pocztowy</label>
                            <input type="text" name="postal_code" id="editPostal" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Miasto</label>
                            <input type="text" name="city" id="editCity" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ulica i numer</label>
                            <input type="text" name="street" id="editStreet" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Godziny odbioru</label>
                            <input type="text" name="hours" id="editHours" class="form-control" placeholder="np. 7:00-15:00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dystans (km)</label>
                            <input type="number" name="distance_km" id="editDistance" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Uwagi</label>
                            <textarea name="notes" id="editNotes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Anuluj
                    </button>
                    <button type="submit" class="btn btn-save">
                        <i class="fa-solid fa-floppy-disk"></i> Zapisz zmiany
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: dodaj adres --}}
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-location-dot me-2"></i> Nowy adres</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('biuro.clients.addresses.store', $client) }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kod pocztowy</label>
                            <input type="text" name="postal_code" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Miasto</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ulica i numer</label>
                            <input type="text" name="street" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Godziny odbioru</label>
                            <input type="text" name="hours" class="form-control" placeholder="np. 7:00-15:00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dystans (km)</label>
                            <input type="number" name="distance_km" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Uwagi</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Anuluj
                    </button>
                    <button type="submit" class="btn btn-save">
                        <i class="fa-solid fa-floppy-disk"></i> Zapisz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: dodaj kontakt --}}
<div class="modal fade" id="addContactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2"></i> Nowy kontakt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('biuro.clients.contacts.store', $client) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kategoria</label>
                        <select name="category" class="form-select" required>
                            @foreach(\App\Models\ClientContact::CATEGORIES as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imię i nazwisko</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Anuluj
                    </button>
                    <button type="submit" class="btn btn-save">
                        <i class="fa-solid fa-floppy-disk"></i> Zapisz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const editModal = document.getElementById('editAddressModal');
editModal.addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    const id  = btn.dataset.id;
    document.getElementById('editAddressForm').action =
        `/biuro/clients/{{ $client->id }}/addresses/${id}`;
    document.getElementById('editCity').value     = btn.dataset.city     || '';
    document.getElementById('editPostal').value   = btn.dataset.postal   || '';
    document.getElementById('editStreet').value   = btn.dataset.street   || '';
    document.getElementById('editHours').value    = btn.dataset.hours    || '';
    document.getElementById('editDistance').value = btn.dataset.distance || '';
    document.getElementById('editNotes').value    = btn.dataset.notes    || '';
});
</script>
@endsection
