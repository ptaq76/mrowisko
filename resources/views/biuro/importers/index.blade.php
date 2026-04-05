@extends('layouts.ustawienia')

@section('title', 'Importerzy')
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
    <div class="page-title"><i class="fa-solid fa-industry"></i> Importerzy</div>
    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addImporterModal">
        <i class="fa-solid fa-plus"></i> Nowy importer
    </button>
</div>

<div class="card" style="max-width:500px">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th style="width:80px">Kraj</th>
                    <th style="width:80px">Status</th>
                    <th style="width:50px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($importers as $imp)
                <tr class="{{ $imp->is_active ? '' : 'text-muted' }}">
                    <td><strong>{{ $imp->name }}</strong></td>
                    <td>
                        <span class="badge {{ $imp->country === 'DE' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                            {{ $imp->country }}
                        </span>
                    </td>
                    <td>
                        @if($imp->is_active)
                            <span class="badge bg-success">Aktywny</span>
                        @else
                            <span class="badge bg-secondary">Nieaktywny</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-edit btn-sm"
                                data-bs-toggle="modal" data-bs-target="#editImporterModal"
                                data-id="{{ $imp->id }}"
                                data-name="{{ $imp->name }}"
                                data-country="{{ $imp->country }}"
                                data-active="{{ $imp->is_active ? '1' : '0' }}"
                                title="Edytuj">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Brak importerów.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal: nowy importer --}}
<div class="modal fade" id="addImporterModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i> Nowy importer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addImporterForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nazwa <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="add_name" class="form-control" required>
                        <div id="add_name_error" class="text-danger small mt-1" style="display:none"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kraj <span class="text-danger">*</span></label>
                        <select name="country" class="form-select">
                            <option value="DE">Niemcy</option>
                            <option value="PL">Polska</option>
                        </select>
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
<div class="modal fade" id="editImporterModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Edycja importera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editImporterForm">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nazwa <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                        <div id="edit_name_error" class="text-danger small mt-1" style="display:none"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kraj</label>
                        <select name="country" id="edit_country" class="form-select">
                            <option value="DE">Niemcy</option>
                            <option value="PL">Polska</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="edit_active"
                               class="form-check-input" value="1">
                        <label class="form-check-label" for="edit_active">Aktywny</label>
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

// Edycja modal
document.getElementById('editImporterModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    editId = btn.dataset.id;
    document.getElementById('edit_name').value       = btn.dataset.name    || '';
    document.getElementById('edit_country').value    = btn.dataset.country || 'DE';
    document.getElementById('edit_active').checked   = btn.dataset.active  === '1';
    document.getElementById('edit_name_error').style.display = 'none';
    document.getElementById('edit_name').classList.remove('is-invalid');
});

// Zapis nowego
document.getElementById('addImporterForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const res  = await fetch('{{ route('biuro.importers.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this),
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('addImporterModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Dodano!', text: data.message, timer: 1800, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
});

// Zapis edycji
document.getElementById('editImporterForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const res  = await fetch(`/biuro/importers/${editId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this),
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('editImporterModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Zaktualizowano!', text: data.message, timer: 1800, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
});
</script>
@endsection
