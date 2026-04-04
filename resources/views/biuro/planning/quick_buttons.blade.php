@extends('layouts.app')
@section('title', 'Przyciski szybkie')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('content')
<div class="page-header">
    <h1>Konfiguracja przycisków szybkich</h1>
    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addBtnModal">
        <i class="fas fa-plus"></i> Nowy przycisk
    </button>
</div>

<div class="row g-3" style="max-width:800px">
    @foreach(['goods' => 'Towary', 'notes' => 'Uwagi'] as $type => $label)
    <div class="col-6">
        <div class="card">
            <div class="card-header">{{ $label }}</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Etykieta</th>
                            <th style="width:70px">Aktywny</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buttons->where('type', $type) as $btn)
                        <tr class="{{ $btn->is_active ? '' : 'text-muted' }}">
                            <td>{{ $btn->label }}</td>
                            <td>
                                @if($btn->is_active)
                                    <i class="fas fa-circle fa-sm dot-on"></i>
                                @else
                                    <i class="fas fa-circle fa-sm dot-off"></i>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-edit btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#editBtnModal"
                                        data-id="{{ $btn->id }}"
                                        data-label="{{ $btn->label }}"
                                        data-active="{{ $btn->is_active ? '1' : '0' }}">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-muted text-center py-3">Brak.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Modal dodaj --}}
<div class="modal fade" id="addBtnModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nowy przycisk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addBtnForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Etykieta</label>
                        <input type="text" name="label" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Typ</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="goods">Towary</option>
                            <option value="notes">Uwagi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-add btn-sm">Dodaj</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal edytuj --}}
<div class="modal fade" id="editBtnModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edycja przycisku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBtnForm">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Etykieta</label>
                        <input type="text" name="label" id="edit_btn_label" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="edit_btn_active" class="form-check-input" value="1">
                        <label class="form-check-label" for="edit_btn_active">Aktywny</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm me-auto" id="deleteBtnSubmit">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-save btn-sm">Zapisz</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let editBtnId = null;

document.getElementById('editBtnModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    editBtnId = btn.dataset.id;
    document.getElementById('edit_btn_label').value  = btn.dataset.label;
    document.getElementById('edit_btn_active').checked = btn.dataset.active === '1';
});

document.getElementById('addBtnForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const res  = await fetch('{{ route('biuro.orders.quickButtons.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this),
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('addBtnModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Dodano!', timer: 1500, showConfirmButton: false });
        location.reload();
    }
});

document.getElementById('editBtnForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const res  = await fetch(`/biuro/orders/quick-buttons/${editBtnId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this),
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('editBtnModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1500, showConfirmButton: false });
        location.reload();
    }
});

document.getElementById('deleteBtnSubmit').addEventListener('click', async function() {
    const result = await Swal.fire({
        title: 'Usunąć przycisk?', icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        confirmButtonText: 'Usuń', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;
    const res  = await fetch(`/biuro/orders/quick-buttons/${editBtnId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('editBtnModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Usunięto!', timer: 1500, showConfirmButton: false });
        location.reload();
    }
});
</script>
@endsection
