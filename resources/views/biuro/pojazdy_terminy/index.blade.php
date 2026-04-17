@extends('layouts.ustawienia')

@section('title', 'Pojazdy – Terminy')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
.section-label { font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:8px; }

/* Kafelek zbliżającego się terminu */
.deadline-card {
    background:#fff;border-radius:10px;padding:12px 16px;
    box-shadow:0 1px 4px rgba(0,0,0,.07);
    border-left:4px solid #e2e5e9;
    display:flex;align-items:center;justify-content:space-between;gap:12px;
}
.deadline-card.overdue  { border-left-color:#e74c3c;background:#fdf2f2; }
.deadline-card.soon     { border-left-color:#e67e22;background:#fef9e7; }
.deadline-card.upcoming { border-left-color:#27ae60;background:#f0faf5; }

.dc-left   { flex:1;min-width:0; }
.dc-vehicle { font-weight:800;font-size:14px;color:#1a1a1a; }
.dc-type    { font-size:12px;color:#888;margin-top:2px; }
.dc-right   { text-align:right;flex-shrink:0; }
.dc-days    { font-family:'Barlow Condensed',sans-serif;font-weight:900;line-height:1; }
.dc-days.overdue  { color:#e74c3c; }
.dc-days.soon     { color:#e67e22; }
.dc-days.upcoming { color:#27ae60; }
.dc-date    { font-size:11px;color:#aaa; }

/* Przyciski typów akcji */
.action-type-btn {
    padding:3px 10px;border:1.5px solid #dde0e5;border-radius:20px;
    background:#fff;font-size:12px;font-weight:600;cursor:pointer;
    color:#555;transition:all .12s;
}
.action-type-btn:hover { border-color:#3498db;color:#3498db;background:#eaf4fb; }
</style>
@endsection

@section('settings_content')

<div class="page-header">
    <div class="page-title"><i class="fa-solid fa-calendar-check"></i> Pojazdy – Terminy</div>
    <div class="d-flex gap-2">
        <button class="btn btn-secondary" onclick="openVehicleList()">
            <i class="fa-solid fa-truck"></i> Pojazdy
        </button>
        <button class="btn btn-add" onclick="openAdd()">
            <i class="fa-solid fa-plus"></i> Nowy termin
        </button>
    </div>
</div>

{{-- ── ZBLIŻAJĄCE SIĘ TERMINY ── --}}
@if($upcoming->isNotEmpty())
<div class="section-label mb-2">
    <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
    Zbliżające się i przeterminowane
</div>
<div class="row g-2 mb-4">
    @foreach($upcoming as $a)
    @php
        $days = $a->days_until_deadline;
        $cls  = ($days < 0 || $days < 14) ? 'overdue' : ($days < 30 ? 'soon' : 'upcoming');
        $daysLabel = $days < 0 ? abs($days) : $days;
    @endphp
    <div class="col-md-4 col-lg-3">
        <div class="deadline-card {{ $cls }}">
            <div class="dc-left">
                <div class="dc-vehicle">{{ $a->pojazd?->nr_rej }}</div>
                <div class="dc-type">{{ $a->action_type }}</div>
                @if($a->pojazd?->marka)
                <div style="font-size:11px;color:#aaa">{{ $a->pojazd->marka }} · {{ $a->pojazd->wlasciciel }}</div>
                @endif
            </div>
            <div class="dc-right">
                <div class="dc-days {{ $cls }}" style="font-size:32px">{{ $daysLabel }}</div>
                <div class="dc-date">{{ $a->deadline_date?->format('d.m.Y') }}</div>
                <div class="d-flex gap-1 justify-content-end mt-1">
                    <button class="btn btn-edit btn-sm edit-akcja-btn"
                            data-id="{{ $a->id }}"
                            data-pojazd="{{ $a->pojazd_id }}"
                            data-type="{{ e($a->action_type) }}"
                            data-completed="{{ $a->completed_date?->format('Y-m-d') }}"
                            data-deadline="{{ $a->deadline_date?->format('Y-m-d') }}"
                            data-notes="{{ e($a->notes ?? '') }}"
                            onclick="openEditFromBtn(this)">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ── FILTRY ── --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#888">Pojazd</label>
                <select name="pojazd_id" class="form-select form-select-sm">
                    <option value="">– wszyscy –</option>
                    @foreach($pojazdy as $p)
                    <option value="{{ $p->id }}" {{ request('pojazd_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->nr_rej }} – {{ $p->marka }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#888">Typ akcji</label>
                <select name="action_type" class="form-select form-select-sm">
                    <option value="">– wszystkie –</option>
                    @foreach($actionTypes as $t)
                    <option value="{{ $t }}" {{ request('action_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-confirm">Filtruj</button>
                <a href="{{ route('biuro.pojazdy-terminy.index') }}" class="btn btn-sm btn-secondary ms-1">Wyczyść</a>
            </div>
        </form>
    </div>
</div>

{{-- ── WSZYSTKIE AKCJE ── --}}
<div class="section-label">Wszystkie terminy</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
            <thead>
                <tr>
                    <th>Pojazd</th>
                    <th>Marka</th>
                    <th>Typ akcji</th>
                    <th>Wykonano</th>
                    <th>Termin</th>
                    <th>Zostało</th>
                    <th>Uwagi</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($all as $a)
                @php
                    $days = $a->days_until_deadline;
                    $badgeClass = $a->status_color;
                @endphp
                <tr id="ar-{{ $a->id }}">
                    <td><strong>{{ $a->pojazd?->nr_rej }}</strong></td>
                    <td style="font-size:12px;color:#888">{{ $a->pojazd?->marka }}</td>
                    <td>{{ $a->action_type }}</td>
                    <td style="font-size:12px">{{ $a->completed_date?->format('d.m.Y') ?? '–' }}</td>
                    <td style="font-size:12px;font-weight:700">{{ $a->deadline_date?->format('d.m.Y') ?? '–' }}</td>
                    <td>
                        @if($days !== null)
                        @php
                            $color = $days < 0 ? '#e74c3c' : ($days < 14 ? '#e74c3c' : ($days < 30 ? '#e67e22' : '#27ae60'));
                        @endphp
                        <span style="font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:{{ $color }}">
                            {{ $days < 0 ? '-'.abs($days) : $days }}
                        </span>
                        @else
                        <span class="text-muted">–</span>
                        @endif
                    </td>
                    <td style="font-size:11px;color:#aaa;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                        title="{{ $a->notes }}">{{ $a->notes ?? '' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-edit btn-sm edit-akcja-btn"
                                    data-id="{{ $a->id }}"
                                    data-pojazd="{{ $a->pojazd_id }}"
                                    data-type="{{ e($a->action_type) }}"
                                    data-completed="{{ $a->completed_date?->format('Y-m-d') }}"
                                    data-deadline="{{ $a->deadline_date?->format('Y-m-d') }}"
                                    data-notes="{{ e($a->notes ?? '') }}"
                                    onclick="openEditFromBtn(this)">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteAkcja({{ $a->id }})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">Brak wpisów.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── MODAL ── --}}
<div class="modal fade" id="akcjaModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="akcjaModalTitle">
                    <i class="fa-solid fa-calendar-check me-2"></i> Nowy termin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="aId">

                <div class="mb-3">
                    <label class="form-label">Pojazd <span class="text-danger">*</span></label>
                    <select id="aPojazd" class="form-select">
                        <option value="">– wybierz –</option>
                        @foreach($pojazdy as $p)
                        <option value="{{ $p->id }}">{{ $p->nr_rej }} – {{ $p->marka }} ({{ $p->rodzaj }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Typ akcji <span class="text-danger">*</span></label>
                    @if($actionTypes->isNotEmpty())
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @foreach($actionTypes as $t)
                        <button type="button" class="action-type-btn" onclick="setActionType('{{ addslashes($t) }}')">
                            {{ $t }}
                        </button>
                        @endforeach
                    </div>
                    @endif
                    <input type="text" id="aActionType" class="form-control"
                           placeholder="np. Przegląd OC, Ubezpieczenie, Tachograf...">
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Data wykonania</label>
                        <input type="date" id="aCompleted" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Termin następnego</label>
                        <input type="date" id="aDeadline" class="form-control">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Uwagi</label>
                    <textarea id="aNotes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                <button type="button" class="btn btn-save" onclick="saveAkcja()">
                    <i class="fa-solid fa-floppy-disk"></i> Zapisz
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL POJAZDY ── --}}
<div class="modal fade" id="vehicleListModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-truck me-2"></i> Pojazdy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-add btn-sm" onclick="openAddVehicle()">
                        <i class="fa-solid fa-plus"></i> Nowy pojazd
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead><tr>
                            <th>Nr rej.</th><th>Rodzaj</th><th>Marka</th><th>Właściciel</th><th>Rok</th><th>Opis</th><th style="width:60px"></th>
                        </tr></thead>
                        <tbody>
                            @foreach($pojazdy as $p)
                            <tr id="vr-{{ $p->id }}">
                                <td><strong>{{ $p->nr_rej }}</strong></td>
                                <td style="font-size:12px">{{ $p->rodzaj }}</td>
                                <td style="font-size:12px">{{ $p->marka }}</td>
                                <td style="font-size:12px;color:#888">{{ $p->wlasciciel }}</td>
                                <td style="font-size:12px;color:#888">{{ $p->rok_prod }}</td>
                                <td style="font-size:12px;color:#888">{{ $p->opis }}</td>
                                <td>
                                    <button class="btn btn-edit btn-sm"
                                            data-id="{{ $p->id }}"
                                        data-nr="{{ e($p->nr_rej) }}"
                                        data-rodzaj="{{ e($p->rodzaj) }}"
                                        data-marka="{{ e($p->marka) }}"
                                        data-wlasciciel="{{ e($p->wlasciciel) }}"
                                        data-rok="{{ $p->rok_prod }}"
                                        data-opis="{{ e($p->opis ?? '') }}"
                                        onclick="openEditVehicleFromBtn(this)">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Formularz dodaj/edytuj pojazd --}}
                <div id="vehicleForm" style="display:none;margin-top:16px;padding-top:16px;border-top:2px solid #e2e5e9">
                    <div style="font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px" id="vehicleFormTitle">Nowy pojazd</div>
                    <input type="hidden" id="vId">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:#888">Nr rej. *</label>
                            <input type="text" id="vNrRej" class="form-control form-control-sm" style="text-transform:uppercase">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:#888">Rodzaj *</label>
                            <input type="text" id="vRodzaj" class="form-control form-control-sm" placeholder="np. Ciągnik, VAN">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:#888">Marka *</label>
                            <input type="text" id="vMarka" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:#888">Właściciel</label>
                            <input type="text" id="vWlasciciel" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:#888">Rok prod.</label>
                            <input type="number" id="vRokProd" class="form-control form-control-sm" placeholder="2024">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:#888">VIN</label>
                            <input type="text" id="vVin" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:#888">Opis</label>
                            <input type="text" id="vOpis" class="form-control form-control-sm" placeholder="np. Radek, Jola">
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-1">
                            <button class="btn btn-save btn-sm w-100" onclick="saveVehicle()">Zapisz</button>
                            <button class="btn btn-secondary btn-sm" onclick="hideVehicleForm()">✕</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function setActionType(val) {
    document.getElementById('aActionType').value = val;
}

function openAdd() {
    document.getElementById('aId').value          = '';
    document.getElementById('aPojazd').value      = '';
    document.getElementById('aActionType').value  = '';
    document.getElementById('aCompleted').value   = '';
    document.getElementById('aDeadline').value    = '';
    document.getElementById('aNotes').value       = '';
    document.getElementById('akcjaModalTitle').innerHTML =
        '<i class="fa-solid fa-calendar-check me-2"></i> Nowy termin';
    new bootstrap.Modal(document.getElementById('akcjaModal')).show();
}

function openEditFromBtn(btn) {
    openEdit(
        btn.dataset.id,
        btn.dataset.pojazd,
        btn.dataset.type,
        btn.dataset.completed,
        btn.dataset.deadline,
        btn.dataset.notes
    );
}

function openEditVehicleFromBtn(btn) {
    openEditVehicle(
        btn.dataset.id,
        btn.dataset.nr,
        btn.dataset.rodzaj,
        btn.dataset.marka,
        btn.dataset.wlasciciel,
        btn.dataset.rok,
        btn.dataset.opis
    );
}

function openEdit(id, pojazd, actionType, completed, deadline, notes) {
    document.getElementById('aId').value          = id;
    document.getElementById('aPojazd').value      = pojazd;
    document.getElementById('aActionType').value  = actionType;
    document.getElementById('aCompleted').value   = completed || '';
    document.getElementById('aDeadline').value    = deadline  || '';
    document.getElementById('aNotes').value       = notes     || '';
    document.getElementById('akcjaModalTitle').innerHTML =
        '<i class="fa-solid fa-pen me-2"></i> Edycja terminu';
    new bootstrap.Modal(document.getElementById('akcjaModal')).show();
}

async function saveAkcja() {
    const id         = document.getElementById('aId').value;
    const pojazd_id  = document.getElementById('aPojazd').value;
    const actionType = document.getElementById('aActionType').value.trim();

    if (!pojazd_id)  { Swal.fire({ icon:'warning', title:'Wybierz pojazd',    timer:1500, showConfirmButton:false }); return; }
    if (!actionType) { Swal.fire({ icon:'warning', title:'Podaj typ akcji',   timer:1500, showConfirmButton:false }); return; }

    const payload = {
        pojazd_id,
        action_type:    actionType,
        completed_date: document.getElementById('aCompleted').value || null,
        deadline_date:  document.getElementById('aDeadline').value  || null,
        notes:          document.getElementById('aNotes').value     || null,
    };

    const url    = id ? `/biuro/pojazdy-terminy/${id}` : '/biuro/pojazdy-terminy';
    const method = id ? 'PUT' : 'POST';

    const res  = await fetch(url, {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type':'application/json', 'Accept':'application/json' },
        body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('akcjaModal')).hide();
        Swal.fire({ icon:'success', title:'Zapisano!', timer:1200, showConfirmButton:false });
        setTimeout(() => location.reload(), 1200);
    } else {
        const err = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon:'error', title:'Błąd', text:err });
    }
}

function openVehicleList() {
    new bootstrap.Modal(document.getElementById('vehicleListModal')).show();
}

function openAddVehicle() {
    document.getElementById('vId').value        = '';
    document.getElementById('vNrRej').value     = '';
    document.getElementById('vRodzaj').value    = '';
    document.getElementById('vMarka').value     = '';
    document.getElementById('vWlasciciel').value= '';
    document.getElementById('vRokProd').value   = '';
    document.getElementById('vVin').value       = '';
    document.getElementById('vOpis').value      = '';
    document.getElementById('vehicleFormTitle').textContent = 'Nowy pojazd';
    document.getElementById('vehicleForm').style.display = 'block';
}

function openEditVehicle(id, nrRej, rodzaj, marka, wlasciciel, rokProd, opis) {
    document.getElementById('vId').value         = id;
    document.getElementById('vNrRej').value      = nrRej;
    document.getElementById('vRodzaj').value     = rodzaj;
    document.getElementById('vMarka').value      = marka;
    document.getElementById('vWlasciciel').value = wlasciciel;
    document.getElementById('vRokProd').value    = rokProd;
    document.getElementById('vOpis').value       = opis;
    document.getElementById('vehicleFormTitle').textContent = 'Edycja pojazdu';
    document.getElementById('vehicleForm').style.display = 'block';
}

function hideVehicleForm() {
    document.getElementById('vehicleForm').style.display = 'none';
}

async function saveVehicle() {
    const id      = document.getElementById('vId').value;
    const nr_rej  = document.getElementById('vNrRej').value.trim().toUpperCase();
    const rodzaj  = document.getElementById('vRodzaj').value.trim();
    const marka   = document.getElementById('vMarka').value.trim();

    if (!nr_rej)  { Swal.fire({ icon:'warning', title:'Podaj numer rejestracyjny', timer:1500, showConfirmButton:false }); return; }
    if (!rodzaj)  { Swal.fire({ icon:'warning', title:'Podaj rodzaj', timer:1500, showConfirmButton:false }); return; }
    if (!marka)   { Swal.fire({ icon:'warning', title:'Podaj markę', timer:1500, showConfirmButton:false }); return; }

    const payload = {
        nr_rej,
        rodzaj,
        marka,
        wlasciciel: document.getElementById('vWlasciciel').value.trim() || null,
        rok_prod:   document.getElementById('vRokProd').value   || null,
        vin:        document.getElementById('vVin').value.trim() || null,
        opis:       document.getElementById('vOpis').value.trim() || null,
    };

    const url    = id ? `/biuro/pojazdy-terminy/pojazdy/${id}` : '/biuro/pojazdy-terminy/pojazdy';
    const method = id ? 'PUT' : 'POST';

    const res  = await fetch(url, {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type':'application/json', 'Accept':'application/json' },
        body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
        Swal.fire({ icon:'success', title:'Zapisano!', timer:1000, showConfirmButton:false });
        setTimeout(() => location.reload(), 1000);
    } else {
        const err = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon:'error', title:'Błąd', text:err });
    }
}

async function deleteAkcja(id) {
    const ok = await Swal.fire({
        title:'Usunąć wpis?', icon:'warning', showCancelButton:true,
        confirmButtonColor:'#e74c3c', confirmButtonText:'Usuń', cancelButtonText:'Anuluj',
    });
    if (!ok.isConfirmed) return;

    const res  = await fetch(`/biuro/pojazdy-terminy/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('ar-' + id)?.remove();
        Swal.fire({ icon:'success', title:'Usunięto', timer:1000, showConfirmButton:false });
    }
}
</script>
@endsection