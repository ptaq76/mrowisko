@extends('layouts.ustawienia')

@section('title', 'Pojazdy')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('settings_content')

<div class="page-header">
    <h1><i class="fa-solid fa-truck-moving"></i> Pojazdy</h1>
    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
        <i class="fa-solid fa-plus"></i> Nowy pojazd
    </button>
</div>

<div class="row g-3">

    {{-- Ciągniki --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-truck me-2"></i> Ciągniki
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Nr rej.</th>
                            <th>Typ</th>
                            <th>Tara</th>
                            <th style="width:60px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tractors as $v)
                        <tr class="{{ $v->is_active ? '' : 'text-muted' }}">
                            <td>
                                <strong>{{ $v->plate }}</strong>
                                @if(!$v->is_active)
                                    <span class="badge bg-secondary ms-1" style="font-size:10px">nieaktywny</span>
                                @endif
                            </td>
                            <td>{{ $v->subtypeName() ?: '–' }}</td>
                            <td>{{ $v->tare_kg > 0 ? number_format($v->tare_kg, 0, ',', ' ') . ' kg' : '–' }}</td>
                            <td>
                                <button class="btn btn-edit btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#editVehicleModal"
                                        data-id="{{ $v->id }}"
                                        data-plate="{{ $v->plate }}"
                                        data-type="{{ $v->type }}"
                                        data-subtype="{{ $v->subtype }}"
                                        data-brand="{{ $v->brand }}"
                                        data-tare="{{ $v->tare_kg }}"
                                        data-active="{{ $v->is_active ? '1' : '0' }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Brak.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Naczepy --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-trailer me-2"></i> Naczepy
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Nr rej.</th>
                            <th>Typ</th>
                            <th>Tara</th>
                            <th style="width:60px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trailers as $v)
                        <tr class="{{ $v->is_active ? '' : 'text-muted' }}">
                            <td>
                                <strong>{{ $v->plate }}</strong>
                                @if(!$v->is_active)
                                    <span class="badge bg-secondary ms-1" style="font-size:10px">nieaktywny</span>
                                @endif
                            </td>
                            <td>{{ $v->subtypeName() ?: '–' }}</td>
                            <td>{{ $v->tare_kg > 0 ? number_format($v->tare_kg, 0, ',', ' ') . ' kg' : '–' }}</td>
                            <td>
                                <button class="btn btn-edit btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#editVehicleModal"
                                        data-id="{{ $v->id }}"
                                        data-plate="{{ $v->plate }}"
                                        data-type="{{ $v->type }}"
                                        data-subtype="{{ $v->subtype }}"
                                        data-brand="{{ $v->brand }}"
                                        data-tare="{{ $v->tare_kg }}"
                                        data-active="{{ $v->is_active ? '1' : '0' }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Brak.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Solo --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-car-side me-2"></i> Solo
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Nr rej.</th>
                            <th>Marka</th>
                            <th>Tara</th>
                            <th style="width:60px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solos as $v)
                        <tr class="{{ $v->is_active ? '' : 'text-muted' }}">
                            <td><strong>{{ $v->plate }}</strong></td>
                            <td>{{ $v->brand ?: '–' }}</td>
                            <td>{{ $v->tare_kg > 0 ? number_format($v->tare_kg, 0, ',', ' ') . ' kg' : '–' }}</td>
                            <td>
                                <button class="btn btn-edit btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#editVehicleModal"
                                        data-id="{{ $v->id }}"
                                        data-plate="{{ $v->plate }}"
                                        data-type="{{ $v->type }}"
                                        data-subtype="{{ $v->subtype }}"
                                        data-brand="{{ $v->brand }}"
                                        data-tare="{{ $v->tare_kg }}"
                                        data-active="{{ $v->is_active ? '1' : '0' }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Brak.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Modal: nowy pojazd --}}
<div class="modal fade" id="addVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-truck me-2"></i> Nowy pojazd</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('biuro.vehicles.store') }}">
                @csrf
                <div class="modal-body">
                    @include('biuro.vehicles._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Anuluj
                    </button>
                    <button type="submit" class="btn btn-add">
                        <i class="fa-solid fa-plus"></i> Dodaj
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: edycja pojazdu --}}
<div class="modal fade" id="editVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Edycja pojazdu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editVehicleForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    @include('biuro.vehicles._form', ['edit' => true])
                    <div class="mt-3">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="edit_is_active"
                                   class="form-check-input" value="1">
                            <label class="form-check-label" for="edit_is_active">Aktywny</label>
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

@endsection

@section('scripts')
<script>
const editModal = document.getElementById('editVehicleModal');
editModal.addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('editVehicleForm').action = `/biuro/vehicles/${btn.dataset.id}`;
    document.getElementById('edit_plate').value   = btn.dataset.plate   || '';
    document.getElementById('edit_type').value    = btn.dataset.type    || '';
    document.getElementById('edit_subtype').value = btn.dataset.subtype || '';
    document.getElementById('edit_brand').value   = btn.dataset.brand   || '';
    document.getElementById('edit_tare').value    = btn.dataset.tare    || '';
    document.getElementById('edit_is_active').checked = btn.dataset.active === '1';
});
</script>
@endsection
