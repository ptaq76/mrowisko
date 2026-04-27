@extends('layouts.ustawienia')

@section('title', 'Prasy')
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
    <div class="page-title"><i class="fa-solid fa-compress"></i> Prasy</div>
    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addPrasaModal">
        <i class="fa-solid fa-plus"></i> Nowa prasa
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th style="width:240px">Klient</th>
                    <th>Notatki</th>
                    <th style="width:80px">Status</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($prasy as $p)
                <tr class="{{ $p->is_active ? '' : 'text-muted' }}">
                    <td><strong>{{ $p->name }}</strong></td>
                    <td>
                        @if($p->client)
                            <span class="badge bg-warning text-dark">U klienta</span>
                            <span class="ms-1">{{ $p->client->short_name }}</span>
                        @else
                            <span class="badge bg-success">Plac</span>
                        @endif
                    </td>
                    <td><small class="text-muted">{{ $p->notes }}</small></td>
                    <td>
                        @if($p->is_active)
                            <span class="badge bg-success">Aktywna</span>
                        @else
                            <span class="badge bg-secondary">Nieaktywna</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button class="btn btn-edit btn-sm d-inline-flex"
                                data-bs-toggle="modal" data-bs-target="#editPrasaModal"
                                data-id="{{ $p->id }}"
                                data-name="{{ $p->name }}"
                                data-client-id="{{ $p->client_id }}"
                                data-active="{{ $p->is_active ? '1' : '0' }}"
                                data-notes="{{ $p->notes }}"
                                title="Edytuj">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete ms-1 d-inline-flex"
                                data-id="{{ $p->id }}"
                                data-name="{{ $p->name }}"
                                title="Usuń">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Brak pras.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal: nowa --}}
<div class="modal fade" id="addPrasaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i> Nowa prasa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPrasaForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nazwa <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Klient (jeśli u klienta)</label>
                            <select name="client_id" class="form-select">
                                <option value="">— plac firmowy —</option>
                                @foreach($clients as $cl)
                                    <option value="{{ $cl->id }}">{{ $cl->short_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notatki</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
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
<div class="modal fade" id="editPrasaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Edycja prasy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPrasaForm">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nazwa <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Klient (jeśli u klienta)</label>
                            <select name="client_id" id="edit_client" class="form-select">
                                <option value="">— plac firmowy —</option>
                                @foreach($clients as $cl)
                                    <option value="{{ $cl->id }}">{{ $cl->short_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notatki</label>
                            <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" id="edit_active" class="form-check-input">
                                <label class="form-check-label" for="edit_active">Aktywna</label>
                            </div>
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
let editId = null;

document.getElementById('editPrasaModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    editId = btn.dataset.id;
    document.getElementById('edit_name').value     = btn.dataset.name || '';
    document.getElementById('edit_client').value   = btn.dataset.clientId || '';
    document.getElementById('edit_notes').value    = btn.dataset.notes || '';
    document.getElementById('edit_active').checked = btn.dataset.active === '1';
});

document.getElementById('addPrasaForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const res = await fetch('{{ route('biuro.prasy.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this),
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('addPrasaModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Dodano!', text: data.message, timer: 1800, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
});

document.getElementById('editPrasaForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!editId) return;

    const fd = new FormData(this);
    fd.set('is_active', document.getElementById('edit_active').checked ? '1' : '0');

    const res = await fetch(`/biuro/prasy/${editId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: fd,
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('editPrasaModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Zapisano!', text: data.message, timer: 1800, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
});

document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', async function() {
        const id   = this.dataset.id;
        const name = this.dataset.name;
        const result = await Swal.fire({
            icon: 'warning',
            title: 'Usuń prasę',
            text: `Czy na pewno usunąć „${name}"?`,
            showCancelButton: true,
            confirmButtonText: 'Tak, usuń',
            cancelButtonText: 'Anuluj',
            confirmButtonColor: '#e74c3c',
        });
        if (!result.isConfirmed) return;

        const fd = new FormData();
        fd.append('_method', 'DELETE');
        fd.append('_token', '{{ csrf_token() }}');

        const res = await fetch(`/biuro/prasy/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: fd,
        });
        const data = await res.json();
        if (data.success) {
            await Swal.fire({ icon: 'success', title: 'Usunięto!', text: data.message, timer: 1800, showConfirmButton: false });
            location.reload();
        } else {
            Swal.fire({ icon: 'error', title: 'Błąd', text: data.message || 'Nie można usunąć.' });
        }
    });
});
</script>
@endsection
