@extends('layouts.ustawienia')

@section('title', 'Tary zestawów')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
.plate-btn {
    padding:5px 12px;border:1.5px solid #dde0e5;border-radius:6px;
    background:#fff;font-size:13px;font-weight:700;cursor:pointer;
    color:#1a1a1a;transition:all .12s;white-space:nowrap;width:100%;text-align:left;
    margin-bottom:4px;
}
.plate-btn:hover  { border-color:#3498db;background:#eaf4fb;color:#2471a3; }
.plate-btn.active { border-color:#3498db;background:#3498db;color:#fff; }
.plate-btn.trailer-btn:hover  { border-color:#f39c12;background:#fef9e7;color:#d68910; }
.plate-btn.trailer-btn.active { border-color:#f39c12;background:#f39c12;color:#fff; }
.tare-val { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900; }
</style>
@endsection

@section('settings_content')

<div class="page-header">
    <div class="page-title"><i class="fa-solid fa-weight-hanging"></i> Tary zestawów</div>
    <button class="btn btn-add" onclick="openAdd()">
        <i class="fa-solid fa-plus"></i> Nowy zestaw
    </button>
</div>

{{-- Tabela zestawów --}}
<div style="max-width:50%">
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
            <thead>
                <tr>
                    <th>Zestaw</th>
                    <th>Tara [t]</th>
                    <th>Status</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody id="setsTable">
                @forelse($sets as $s)
                <tr id="sr-{{ $s->id }}" class="{{ $s->is_active ? '' : 'text-muted' }}">
                    <td><strong>{{ $s->label }}</strong></td>
                    <td><span class="tare-val">{{ number_format($s->tare_kg, 3, ',', ' ') }}</span></td>
                    <td>
                        @if($s->is_active)
                            <span class="badge bg-success">Aktywny</span>
                        @else
                            <span class="badge bg-secondary">Nieaktywny</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-edit btn-sm"
                                    onclick="openEdit({{ $s->id }}, '{{ addslashes($s->label) }}', {{ $s->tractor_id ?? 'null' }}, {{ $s->trailer_id ?? 'null' }}, '{{ $s->tare_kg }}', {{ $s->is_active ? 'true' : 'false' }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteSet({{ $s->id }})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Brak zestawów.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>

{{-- Modal: dodaj / edytuj --}}
<div class="modal fade" id="setModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="setModalTitle"><i class="fa-solid fa-weight-hanging me-2"></i> Nowy zestaw</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="sId">

                {{-- Przyciski pojazdów --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#888">
                            <i class="fa-solid fa-truck text-primary me-1"></i> Ciągnik
                        </label>
                        <div id="tractorBtns">
                            @foreach($tractors as $v)
                            <button type="button" class="plate-btn"
                                    data-id="{{ $v->id }}" data-plate="{{ $v->plate }}"
                                    onclick="selectTractor(this)">
                                {{ $v->plate }}
                                @if($v->brand) <span style="font-weight:400;color:#aaa;font-size:11px">{{ $v->brand }}</span>@endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#888">
                            <i class="fa-solid fa-trailer text-warning me-1"></i> Naczepa <span style="font-weight:400">(opcjonalna)</span>
                        </label>
                        <div id="trailerBtns">
                            <button type="button" class="plate-btn trailer-btn active" data-id="" data-plate=""
                                    onclick="selectTrailer(this)">
                                – brak naczepy –
                            </button>
                            @foreach($trailers as $v)
                            <button type="button" class="plate-btn trailer-btn"
                                    data-id="{{ $v->id }}" data-plate="{{ $v->plate }}"
                                    onclick="selectTrailer(this)">
                                {{ $v->plate }}
                                @if($v->brand) <span style="font-weight:400;color:#aaa;font-size:11px">{{ $v->brand }}</span>@endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Pola formularza --}}
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Zestaw (etykieta) <span class="text-danger">*</span></label>
                        <input type="text" id="sLabel" class="form-control"
                               placeholder="np. PNT81294 / WGM5564P">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tara [t] <span class="text-danger">*</span></label>
                        <input type="number" id="sTare" class="form-control"
                               step="0.001" min="0" placeholder="np. 14.000">
                    </div>
                    <div class="col-12" id="sActiveRow" style="display:none">
                        <div class="form-check">
                            <input type="checkbox" id="sActive" class="form-check-input" checked>
                            <label class="form-check-label" for="sActive">Aktywny</label>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="sTractorId">
                <input type="hidden" id="sTrailerId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Anuluj
                </button>
                <button type="button" class="btn btn-save" onclick="saveSet()">
                    <i class="fa-solid fa-floppy-disk"></i> Zapisz
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function openAdd() {
    document.getElementById('sId').value      = '';
    document.getElementById('sLabel').value   = '';
    document.getElementById('sTare').value    = '';
    document.getElementById('sTractorId').value = '';
    document.getElementById('sTrailerId').value = '';
    document.getElementById('sActiveRow').style.display = 'none';
    document.getElementById('setModalTitle').innerHTML = '<i class="fa-solid fa-weight-hanging me-2"></i> Nowy zestaw';
    // reset buttons
    document.querySelectorAll('.plate-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.trailer-btn[data-id=""]').classList.add('active');
    new bootstrap.Modal(document.getElementById('setModal')).show();
}

function openEdit(id, label, tractorId, trailerId, tare, isActive) {
    document.getElementById('sId').value        = id;
    document.getElementById('sLabel').value     = label;
    document.getElementById('sTare').value      = tare;
    document.getElementById('sTractorId').value = tractorId ?? '';
    document.getElementById('sTrailerId').value = trailerId ?? '';
    document.getElementById('sActive').checked  = isActive;
    document.getElementById('sActiveRow').style.display = 'block';
    document.getElementById('setModalTitle').innerHTML = '<i class="fa-solid fa-pen me-2"></i> Edycja zestawu';

    // Highlight buttons
    document.querySelectorAll('.plate-btn:not(.trailer-btn)').forEach(b => {
        b.classList.toggle('active', parseInt(b.dataset.id) === tractorId);
    });
    document.querySelectorAll('.trailer-btn').forEach(b => {
        const bid = b.dataset.id === '' ? null : parseInt(b.dataset.id);
        b.classList.toggle('active', bid === trailerId);
    });

    new bootstrap.Modal(document.getElementById('setModal')).show();
}

let _selectedTractor = '';
let _selectedTrailer = '';

function selectTractor(btn) {
    document.querySelectorAll('.plate-btn:not(.trailer-btn)').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    _selectedTractor = btn.dataset.plate;
    document.getElementById('sTractorId').value = btn.dataset.id;
    rebuildLabel();
}

function selectTrailer(btn) {
    document.querySelectorAll('.trailer-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    _selectedTrailer = btn.dataset.plate;
    document.getElementById('sTrailerId').value = btn.dataset.id;
    rebuildLabel();
}

function rebuildLabel() {
    const t1 = document.getElementById('sTractorId').value;
    const t2 = document.getElementById('sTrailerId').value;
    const p1 = document.querySelector(`.plate-btn:not(.trailer-btn)[data-id="${t1}"]`)?.dataset.plate ?? '';
    const p2 = document.querySelector(`.trailer-btn[data-id="${t2}"]`)?.dataset.plate ?? '';
    if (p1 && p2) {
        document.getElementById('sLabel').value = p1 + ' / ' + p2;
    } else if (p1) {
        document.getElementById('sLabel').value = p1;
    }
}

async function saveSet() {
    const id    = document.getElementById('sId').value;
    const label = document.getElementById('sLabel').value.trim();
    const tare  = document.getElementById('sTare').value.trim();

    if (!label) { Swal.fire({ icon:'warning', title:'Podaj etykietę zestawu', timer:1500, showConfirmButton:false }); return; }
    if (!tare)  { Swal.fire({ icon:'warning', title:'Podaj tarę', timer:1500, showConfirmButton:false }); return; }

    const payload = {
        label,
        tare_kg:    tare,
        tractor_id: document.getElementById('sTractorId').value || null,
        trailer_id: document.getElementById('sTrailerId').value || null,
        is_active:  document.getElementById('sActive').checked ? 1 : 0,
        _token:     CSRF,
    };

    const url    = id ? `/biuro/vehicle-sets/${id}` : '/biuro/vehicle-sets';
    const method = id ? 'PUT' : 'POST';

    const res  = await fetch(url, {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type':'application/json', 'Accept':'application/json' },
        body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('setModal')).hide();
        Swal.fire({ icon:'success', title:'Zapisano!', timer:1200, showConfirmButton:false });
        setTimeout(() => location.reload(), 1200);
    } else {
        Swal.fire({ icon:'error', title:'Błąd', text: data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd zapisu.' });
    }
}

async function deleteSet(id) {
    const ok = await Swal.fire({
        title:'Usunąć zestaw?', icon:'warning', showCancelButton:true,
        confirmButtonColor:'#e74c3c', confirmButtonText:'Usuń', cancelButtonText:'Anuluj',
    });
    if (!ok.isConfirmed) return;

    const res  = await fetch(`/biuro/vehicle-sets/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('sr-' + id)?.remove();
        Swal.fire({ icon:'success', title:'Usunięto', timer:1000, showConfirmButton:false });
    }
}
</script>
@endsection