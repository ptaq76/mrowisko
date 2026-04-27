@extends('layouts.ustawienia')

@section('title', 'Kontenery')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
.stats-row { display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap }
.stat-pill { background:#fff;border:1px solid #e2e5e9;border-radius:10px;padding:8px 14px;font-size:13px;display:inline-flex;align-items:center;gap:8px }
.stat-pill .stat-num { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;color:#1a1a1a }
.stat-pill.plac .stat-num { color:#27ae60 }
.stat-pill.client .stat-num { color:#d68910 }
.tabs-row { display:flex;gap:8px;margin-bottom:14px;border-bottom:2px solid #e2e5e9 }
.tab-link { padding:10px 18px;font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:15px;letter-spacing:.04em;text-transform:uppercase;text-decoration:none;color:#888;border-bottom:3px solid transparent;margin-bottom:-2px }
.tab-link.active { color:#1a1a1a;border-bottom-color:#d68910 }
.filters-card { background:#fff;border:1px solid #e2e5e9;border-radius:10px;padding:12px 14px;margin-bottom:14px }
.filters-card .form-label { font-size:11px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px }
.history-row td { font-size:12px }
.dir-drop { background:#fff3cd;color:#856404;padding:2px 8px;border-radius:4px;font-weight:700;font-size:11px }
.dir-pickup { background:#d4edda;color:#155724;padding:2px 8px;border-radius:4px;font-weight:700;font-size:11px }
.qty-num { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900 }
.qty-plac { color:#27ae60 }
.qty-client { color:#d68910 }
.qty-zero { color:#bbb;font-weight:600 }
</style>
@endsection

@section('settings_content')

<div class="page-header">
    <div class="page-title"><i class="fa-solid fa-dumpster"></i> Kontenery</div>
    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addContainerModal">
        <i class="fa-solid fa-plus"></i> Nowy typ
    </button>
</div>

<div class="stats-row">
    <div class="stat-pill plac">
        <i class="fa-solid fa-warehouse" style="color:#27ae60"></i>
        Na placu: <span class="stat-num">{{ $stats['plac'] }}</span>
    </div>
    <div class="stat-pill client">
        <i class="fa-solid fa-handshake" style="color:#d68910"></i>
        U klientów: <span class="stat-num">{{ $stats['client'] }}</span>
    </div>
    <div class="stat-pill">
        <i class="fa-solid fa-layer-group" style="color:#888"></i>
        Razem: <span class="stat-num">{{ $stats['total'] }}</span>
    </div>
</div>

<div class="tabs-row">
    <a href="{{ route('biuro.containers.index') }}" class="tab-link active">
        <i class="fa-solid fa-list"></i> Lista typów
    </a>
    <a href="{{ route('biuro.containers.byClient') }}" class="tab-link">
        <i class="fa-solid fa-people-group"></i> Według klientów
    </a>
</div>

<form method="GET" action="{{ route('biuro.containers.index') }}" class="filters-card">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Szukaj nazwy</label>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control form-control-sm" placeholder="np. CZARNY">
        </div>
        <div class="col-md-3">
            <label class="form-label">Stan</label>
            <select name="location" class="form-select form-select-sm">
                <option value="">— wszystkie —</option>
                <option value="plac" @selected(($filters['location'] ?? '') === 'plac')>Z stanem na placu</option>
                <option value="client" @selected(($filters['location'] ?? '') === 'client')>Z stanem u klienta</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Typ</label>
            <select name="type" class="form-select form-select-sm">
                <option value="">— wszystkie —</option>
                <option value="zwykly" @selected(($filters['type'] ?? '') === 'zwykly')>Zwykły</option>
                <option value="prasokontener" @selected(($filters['type'] ?? '') === 'prasokontener')>Prasokontener</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fa-solid fa-filter"></i> Filtruj</button>
            <a href="{{ route('biuro.containers.index') }}" class="btn btn-secondary btn-sm" title="Wyczyść"><i class="fa-solid fa-xmark"></i></a>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th style="width:90px">Tara (kg)</th>
                    <th style="width:140px">Typ</th>
                    <th style="width:80px;text-align:center">Plac</th>
                    <th style="width:80px;text-align:center">Klienci</th>
                    <th style="width:80px;text-align:center">Razem</th>
                    <th>Notatki</th>
                    <th style="width:80px">Status</th>
                    <th style="width:170px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($containers as $c)
                <tr class="{{ $c->is_active ? '' : 'text-muted' }}">
                    <td><strong>{{ $c->name }}</strong></td>
                    <td>{{ number_format($c->tare_kg, 2, ',', '') }}</td>
                    <td>
                        @if($c->type === 'prasokontener')
                            <span class="badge bg-info">Prasokontener</span>
                        @else
                            <span class="badge bg-secondary">Zwykły</span>
                        @endif
                    </td>
                    <td class="text-center"><span class="qty-num {{ $c->plac_qty > 0 ? 'qty-plac' : 'qty-zero' }}">{{ $c->plac_qty }}</span></td>
                    <td class="text-center"><span class="qty-num {{ $c->client_qty > 0 ? 'qty-client' : 'qty-zero' }}">{{ $c->client_qty }}</span></td>
                    <td class="text-center"><span class="qty-num">{{ $c->total_qty }}</span></td>
                    <td><small class="text-muted">{{ $c->notes }}</small></td>
                    <td>
                        @if($c->is_active)
                            <span class="badge bg-success">Aktywny</span>
                        @else
                            <span class="badge bg-secondary">Nieaktywny</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary btn-adjust d-inline-flex"
                                data-id="{{ $c->id }}"
                                data-name="{{ $c->name }}"
                                data-plac="{{ $c->plac_qty }}"
                                title="Korekta stanu">
                            <i class="fa-solid fa-scale-balanced"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary btn-history d-inline-flex ms-1"
                                data-id="{{ $c->id }}"
                                data-name="{{ $c->name }}"
                                title="Historia">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </button>
                        <button class="btn btn-edit btn-sm ms-1 d-inline-flex"
                                data-bs-toggle="modal" data-bs-target="#editContainerModal"
                                data-id="{{ $c->id }}"
                                data-name="{{ $c->name }}"
                                data-tare="{{ $c->tare_kg }}"
                                data-type="{{ $c->type }}"
                                data-active="{{ $c->is_active ? '1' : '0' }}"
                                data-notes="{{ $c->notes }}"
                                title="Edytuj">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete ms-1 d-inline-flex"
                                data-id="{{ $c->id }}"
                                data-name="{{ $c->name }}"
                                title="Usuń">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-3">Brak kontenerów spełniających kryteria.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal: nowy --}}
<div class="modal fade" id="addContainerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i> Nowy typ kontenera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContainerForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nazwa <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tara (kg) <span class="text-danger">*</span></label>
                            <input type="number" name="tare_kg" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Typ <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="zwykly">Zwykły</option>
                                <option value="prasokontener">Prasokontener</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stan startowy na placu</label>
                            <input type="number" name="plac_qty" class="form-control" min="0" value="1">
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

{{-- Modal: korekta stanu --}}
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-scale-balanced me-2"></i> Korekta stanu — <span id="adj_name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="adjustStockForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Lokalizacja <span class="text-danger">*</span></label>
                            <select name="client_id" id="adj_client" class="form-select">
                                <option value="">Plac (aktualnie: <span id="adj_plac_qty">0</span> szt.)</option>
                                @foreach($clients as $cl)
                                    <option value="{{ $cl->id }}">{{ $cl->short_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Korekta (szt.) <span class="text-danger">*</span></label>
                            <input type="number" name="delta" class="form-control" step="1" required placeholder="np. +1, -2">
                            <small class="text-muted">Dodatnia liczba zwiększa, ujemna zmniejsza stan.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Anuluj
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Zapisz korektę
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: historia --}}
<div class="modal fade" id="historyContainerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-clock-rotate-left me-2"></i> Historia kontenera <span id="hist_name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="hist_loading" class="text-center text-muted py-3">Ładowanie...</div>
                <div id="hist_empty" class="text-center text-muted py-3" style="display:none">Brak wpisów w historii.</div>
                <table class="table table-sm history-row" id="hist_table" style="display:none">
                    <thead>
                        <tr>
                            <th style="width:140px">Data</th>
                            <th style="width:120px">Kierunek</th>
                            <th style="width:90px">Slot</th>
                            <th>Klient</th>
                            <th style="width:80px">Zlecenie</th>
                        </tr>
                    </thead>
                    <tbody id="hist_tbody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal: edycja --}}
<div class="modal fade" id="editContainerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Edycja typu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editContainerForm">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nazwa <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tara (kg) <span class="text-danger">*</span></label>
                            <input type="number" name="tare_kg" id="edit_tare" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Typ <span class="text-danger">*</span></label>
                            <select name="type" id="edit_type" class="form-select" required>
                                <option value="zwykly">Zwykły</option>
                                <option value="prasokontener">Prasokontener</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" id="edit_active" class="form-check-input">
                                <label class="form-check-label" for="edit_active">Aktywny</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notatki</label>
                            <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Stan magazynowy edytujesz przyciskiem <i class="fa-solid fa-scale-balanced"></i> w wierszu listy.</small>
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
let adjId = null;

document.getElementById('editContainerModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    editId = btn.dataset.id;
    document.getElementById('edit_name').value     = btn.dataset.name || '';
    document.getElementById('edit_tare').value     = btn.dataset.tare || '';
    document.getElementById('edit_type').value     = btn.dataset.type || 'zwykly';
    document.getElementById('edit_notes').value    = btn.dataset.notes || '';
    document.getElementById('edit_active').checked = btn.dataset.active === '1';
});

document.getElementById('addContainerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const res = await fetch('{{ route('biuro.containers.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this),
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('addContainerModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Dodano!', text: data.message, timer: 1800, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message ?? 'Błąd.');
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
});

document.getElementById('editContainerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!editId) return;

    const fd = new FormData(this);
    fd.set('is_active', document.getElementById('edit_active').checked ? '1' : '0');

    const res = await fetch(`/biuro/containers/${editId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: fd,
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('editContainerModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Zapisano!', text: data.message, timer: 1800, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message ?? 'Błąd.');
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
});

document.querySelectorAll('.btn-adjust').forEach(btn => {
    btn.addEventListener('click', function() {
        adjId = this.dataset.id;
        document.getElementById('adj_name').textContent = this.dataset.name;
        document.getElementById('adj_plac_qty').textContent = this.dataset.plac;
        document.getElementById('adjustStockForm').reset();
        document.getElementById('adj_client').value = '';
        new bootstrap.Modal(document.getElementById('adjustStockModal')).show();
    });
});

document.getElementById('adjustStockForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!adjId) return;

    const res = await fetch(`/biuro/containers/${adjId}/adjust-stock`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this),
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('adjustStockModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Zapisano!', text: data.message, timer: 1800, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message ?? 'Błąd.');
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
});

document.querySelectorAll('.btn-history').forEach(btn => {
    btn.addEventListener('click', async function() {
        const id = this.dataset.id;
        const name = this.dataset.name;

        document.getElementById('hist_name').textContent = '„' + name + '"';
        document.getElementById('hist_loading').style.display = 'block';
        document.getElementById('hist_empty').style.display = 'none';
        document.getElementById('hist_table').style.display = 'none';
        document.getElementById('hist_tbody').innerHTML = '';

        const modal = new bootstrap.Modal(document.getElementById('historyContainerModal'));
        modal.show();

        const res = await fetch(`/biuro/containers/${id}/history`, {
            headers: { 'Accept': 'application/json' },
        });
        const data = await res.json();

        document.getElementById('hist_loading').style.display = 'none';

        if (!data.success || !data.entries.length) {
            document.getElementById('hist_empty').style.display = 'block';
            return;
        }

        const tbody = document.getElementById('hist_tbody');
        for (const e of data.entries) {
            const tr = document.createElement('tr');
            const dirCls = e.direction === 'drop' ? 'dir-drop' : 'dir-pickup';
            const dirLabel = e.direction === 'drop' ? '↓ Pozostawienie' : '↑ Zabranie';
            const slotLabel = e.slot === 'tractor' ? 'Samochód' : 'Naczepa';
            tr.innerHTML = `
                <td>${e.created_at}</td>
                <td><span class="${dirCls}">${dirLabel}</span></td>
                <td>${slotLabel}</td>
                <td>${e.client ?? '–'}</td>
                <td><small class="text-muted">#${e.order_id}</small></td>
            `;
            tbody.appendChild(tr);
        }
        document.getElementById('hist_table').style.display = 'table';
    });
});

document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', async function() {
        const id   = this.dataset.id;
        const name = this.dataset.name;
        const result = await Swal.fire({
            icon: 'warning',
            title: 'Usuń kontener',
            text: `Czy na pewno usunąć typ „${name}"? Możliwe tylko gdy stan = 0.`,
            showCancelButton: true,
            confirmButtonText: 'Tak, usuń',
            cancelButtonText: 'Anuluj',
            confirmButtonColor: '#e74c3c',
        });
        if (!result.isConfirmed) return;

        const fd = new FormData();
        fd.append('_method', 'DELETE');
        fd.append('_token', '{{ csrf_token() }}');

        const res = await fetch(`/biuro/containers/${id}`, {
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
