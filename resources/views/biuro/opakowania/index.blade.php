@extends('layouts.ustawienia')

@section('title', 'Opakowania')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
</style>
@endsection

@section('settings_content')

<div class="page-header">
    <div class="page-title"><i class="fa-solid fa-box"></i> Opakowania</div>
    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addOpakowaniModal">
        <i class="fa-solid fa-plus"></i> Nowe opakowanie
    </button>
</div>

<div class="card" style="max-width:500px">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th style="width:100px">Waga (kg)</th>
                    <th style="width:80px">Status</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($opakowania as $op)
                <tr class="{{ $op->is_active ? '' : 'text-muted' }}">
                    <td><strong>{{ $op->name }}</strong></td>
                    <td>{{ number_format($op->waga, 2, ',', '') }} kg</td>
                    <td>
                        @if($op->is_active)
                            <span class="badge bg-success">Aktywne</span>
                        @else
                            <span class="badge bg-secondary">Nieaktywne</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button class="btn btn-edit btn-sm d-inline-flex"
                                data-bs-toggle="modal" data-bs-target="#editOpakowaniModal"
                                data-id="{{ $op->id }}"
                                data-name="{{ $op->name }}"
                                data-waga="{{ $op->waga }}"
                                data-active="{{ $op->is_active ? '1' : '0' }}"
                                title="Edytuj">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete ms-1 d-inline-flex"
                                data-id="{{ $op->id }}"
                                data-name="{{ $op->name }}"
                                title="Usuń">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Brak opakowań.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal: nowe opakowanie --}}
<div class="modal fade" id="addOpakowaniModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i> Nowe opakowanie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addOpakowaniForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nazwa <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="add_name" class="form-control" required>
                        <div id="add_name_error" class="text-danger small mt-1" style="display:none"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Waga (kg) <span class="text-danger">*</span></label>
                        <input type="number" name="waga" id="add_waga" class="form-control"
                               step="0.01" min="0" required>
                        <div id="add_waga_error" class="text-danger small mt-1" style="display:none"></div>
                    </div>
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

{{-- Modal: edycja --}}
<div class="modal fade" id="editOpakowaniModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Edycja opakowania</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editOpakowaniForm">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nazwa <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                        <div id="edit_name_error" class="text-danger small mt-1" style="display:none"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Waga (kg) <span class="text-danger">*</span></label>
                        <input type="number" name="waga" id="edit_waga" class="form-control"
                               step="0.01" min="0" required>
                        <div id="edit_waga_error" class="text-danger small mt-1" style="display:none"></div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="edit_active" class="form-check-input">
                        <label class="form-check-label" for="edit_active">Aktywne</label>
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
let editId = null;

// Wypełnienie modala edycji
document.getElementById('editOpakowaniModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    editId = btn.dataset.id;
    document.getElementById('edit_name').value     = btn.dataset.name   || '';
    document.getElementById('edit_waga').value     = btn.dataset.waga   || '';
    document.getElementById('edit_active').checked = btn.dataset.active === '1';
    document.getElementById('edit_name_error').style.display = 'none';
    document.getElementById('edit_waga_error').style.display = 'none';
    document.getElementById('edit_name').classList.remove('is-invalid');
    document.getElementById('edit_waga').classList.remove('is-invalid');
});

// Dodawanie
document.getElementById('addOpakowaniForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const res  = await fetch('{{ route('biuro.opakowania.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this),
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('addOpakowaniModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Dodano!', text: data.message, timer: 1800, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Blad.';
        Swal.fire({ icon: 'error', title: 'Blad', text: errors });
    }
});

// Edycja — is_active budujemy ręcznie żeby uniknąć konfliktu dwóch pól name="is_active"
// Edycja
document.getElementById('editOpakowaniForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!editId) { console.error('editId is null!'); return; }

    const fd = new FormData(this);
    fd.set('is_active', document.getElementById('edit_active').checked ? '1' : '0');

    console.log('editId:', editId);
    console.log('is_active:', document.getElementById('edit_active').checked ? '1' : '0');
    for (let [k, v] of fd.entries()) console.log(k, '=', v);

    const res = await fetch(`/biuro/opakowania/${editId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: fd,
    });
    console.log('status:', res.status, res.url);
    const data = await res.json();
    console.log('response:', data);

    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('editOpakowaniModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Zaktualizowano!', text: data.message, timer: 1800, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Blad.';
        Swal.fire({ icon: 'error', title: 'Blad', text: errors });
    }
});
// Usuwanie
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', async function() {
        const id   = this.dataset.id;
        const name = this.dataset.name;
        const result = await Swal.fire({
            icon: 'warning',
            title: 'Usun opakowanie',
            text: `Czy na pewno chcesz usunac "${name}"?`,
            showCancelButton: true,
            confirmButtonText: 'Tak, usun',
            cancelButtonText: 'Anuluj',
            confirmButtonColor: '#e74c3c',
        });
        if (!result.isConfirmed) return;

        const fd = new FormData();
        fd.append('_method', 'DELETE');
        fd.append('_token', '{{ csrf_token() }}');

        const res  = await fetch(`/biuro/opakowania/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: fd,
        });
        const data = await res.json();
        if (data.success) {
            await Swal.fire({ icon: 'success', title: 'Usunieto!', text: data.message, timer: 1800, showConfirmButton: false });
            location.reload();
        } else {
            Swal.fire({ icon: 'error', title: 'Blad', text: data.message || 'Nie mozna usunac.' });
        }
    });
});
</script>
@endsection