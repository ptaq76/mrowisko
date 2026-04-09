@extends('layouts.ustawienia')
@section('title', 'Koszty transportu')
@section('module_name', 'BIURO')
@section('nav_menu') @include('biuro._nav') @endsection

@section('styles')
<style>
.wrap { padding:20px; }
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }

.layout-cols { display:flex;gap:24px;align-items:flex-start; }
.col-main { width:60%;min-width:0; }
.col-side { flex:1;min-width:220px; }

.page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.btn-add { padding:9px 18px;background:#1a1a1a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px; }

.table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden;margin-bottom:20px; }
table { width:100%;border-collapse:collapse;font-size:13px; }
thead tr { background:#1a1a1a;color:#fff; }
th { padding:10px 14px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-align:left; }
td { padding:9px 14px;border-bottom:1px solid #f0f2f5;vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr:hover td { background:#f8f9fa; }
.btn-edit { background:#eaf4fb;border:1px solid #cce0f5;border-radius:5px;padding:4px 8px;color:#2980b9;cursor:pointer;font-size:12px; }
.btn-del  { background:#fdecea;border:1px solid #f5c6cb;border-radius:5px;padding:4px 8px;color:#e74c3c;cursor:pointer;font-size:12px;margin-left:4px; }
.cena-badge { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;color:#27ae60; }
.td-name { font-size:13px;font-weight:700;color:#1a1a1a;font-family:var(--font-body); }
.arrow { color:#aaa;margin:0 4px; }

/* Przewoźnicy */
.side-box { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);padding:16px; }
.section-title { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;margin-bottom:12px;display:flex;align-items:center;gap:8px; }
.przewoznik-list { display:flex;flex-direction:column;gap:6px;margin-bottom:14px; }
.przewoznik-chip { display:flex;align-items:center;justify-content:space-between;background:#f4f5f7;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:600; }
.chip-del { background:none;border:none;color:#ccc;cursor:pointer;font-size:14px;padding:0 2px;line-height:1; }
.chip-del:hover { color:#e74c3c; }
.add-przewoznik { display:flex;gap:6px;align-items:center; }
.add-przewoznik input { flex:1;padding:7px 10px;border:1.5px solid #dde0e5;border-radius:8px;font-size:13px;outline:none; }
.add-przewoznik input:focus { border-color:#1a1a1a; }
.btn-add-sm { padding:7px 12px;background:#1a1a1a;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer; }

/* Modal */
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:12px;width:100%;max-width:480px;padding:24px;box-shadow:0 8px 32px rgba(0,0,0,.2);max-height:90vh;overflow-y:auto; }
.modal-title { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center; }
.m-label { display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:5px; }
.m-input,.m-select { width:100%;padding:9px 11px;border:1.5px solid #dde0e5;border-radius:8px;font-size:14px;outline:none;margin-bottom:12px; }
.m-input:focus,.m-select:focus { border-color:#1a1a1a; }
.modal-footer { display:flex;gap:10px;justify-content:flex-end; }
.btn-cancel { padding:9px 18px;background:#f4f5f7;color:#555;border:1px solid #dde0e5;border-radius:8px;font-size:13px;cursor:pointer; }
.btn-save   { padding:9px 18px;background:#1a1a1a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer; }
</style>
@endsection

@section('settings_content')
<div class="wrap">

    <div class="layout-cols">

        {{-- Lewa: tabela koszty --}}
        <div class="col-main">
            <div class="page-header">
                <div class="page-title"><i class="fas fa-route"></i> Koszty transportu</div>
                <button class="btn-add" onclick="openAdd()"><i class="fas fa-plus"></i> Dodaj trasę</button>
            </div>
            <div class="table-wrap">
                <table id="kosztTable">
                    <thead><tr>
                        <th>Start</th><th></th><th>Stop</th><th>Przewoźnik</th><th>Cena €/t</th><th style="width:80px">Akcje</th>
                    </tr></thead>
                    <tbody>
                    @forelse($koszty as $k)
                    <tr id="kr-{{ $k->id }}">
                        <td class="td-name">{{ $k->start?->short_name ?? '–' }}</td>
                        <td><i class="fas fa-arrow-right arrow"></i></td>
                        <td class="td-name">{{ $k->stop?->short_name ?? '–' }}</td>
                        <td>{{ $k->przewoznik?->nazwa ?? '–' }}</td>
                        <td><span class="cena-badge">{{ number_format($k->cena_eur, 2, ',', ' ') }} €</span></td>
                        <td>
                            <button class="btn-edit" onclick="openEdit({{ $k->id }}, {{ $k->start_id }}, {{ $k->stop_id }}, {{ $k->przewoznik_id ?? 'null' }}, {{ $k->cena_eur }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button class="btn-del" onclick="del({{ $k->id }})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Brak zdefiniowanych tras</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Prawa: przewoźnicy --}}
        <div class="col-side">
            <div class="side-box">
                <div class="section-title">
                    <i class="fas fa-truck" style="color:#2980b9"></i> Przewoźnicy
                </div>
                <div class="przewoznik-list" id="przewoznikList">
                    @foreach($przewoznicy as $p)
                    <div class="przewoznik-chip" id="pc-{{ $p->id }}">
                        <span>{{ $p->nazwa }}</span>
                        <button class="chip-del" onclick="deletePrzewoznik({{ $p->id }})" title="Usuń">×</button>
                    </div>
                    @endforeach
                </div>
                <div class="add-przewoznik">
                    <input type="text" id="newPrzewoznik" placeholder="Nowy przewoźnik..."
                           onkeydown="if(event.key==='Enter')addPrzewoznik()">
                    <button class="btn-add-sm" onclick="addPrzewoznik()"><i class="fas fa-plus"></i></button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal --}}
<div class="modal-overlay" id="kosztModal">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-title">
            <span id="modalTitle">Dodaj trasę</span>
            <button style="background:none;border:none;font-size:18px;cursor:pointer;color:#aaa" onclick="closeModal()">×</button>
        </div>
        <input type="hidden" id="editId">

        <label class="m-label">Start (miejsce załadunku) *</label>
        <select id="startId" class="m-select">
            <option value="">– wybierz –</option>
            @foreach($klienci as $c)
            <option value="{{ $c->id }}">{{ $c->short_name }}</option>
            @endforeach
        </select>

        <label class="m-label">Stop (odbiorca) *</label>
        <select id="stopId" class="m-select">
            <option value="">– wybierz –</option>
            @foreach($klienci as $c)
            <option value="{{ $c->id }}">{{ $c->short_name }}</option>
            @endforeach
        </select>

        <label class="m-label">Przewoźnik</label>
        <select id="przewoznikId" class="m-select">
            <option value="">– brak / dowolny –</option>
            @foreach($przewoznicy as $p)
            <option value="{{ $p->id }}">{{ $p->nazwa }}</option>
            @endforeach
        </select>

        <label class="m-label">Cena €/t *</label>
        <input type="number" id="cenaEur" class="m-input" step="0.01" min="0" placeholder="0.00">

        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal()">Anuluj</button>
            <button class="btn-save" onclick="save()"><i class="fas fa-check"></i> Zapisz</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

async function addPrzewoznik() {
    const nazwa = document.getElementById('newPrzewoznik').value.trim();
    if (!nazwa) return;
    const res  = await fetch('/biuro/przewoznicy', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ nazwa }),
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('newPrzewoznik').value = '';
        const list = document.getElementById('przewoznikList');
        const chip = document.createElement('div');
        chip.className = 'przewoznik-chip'; chip.id = 'pc-' + data.id;
        chip.innerHTML = `<span>${data.nazwa}</span><button class="chip-del" onclick="deletePrzewoznik(${data.id})" title="Usuń">×</button>`;
        list.appendChild(chip);
        const sel = document.getElementById('przewoznikId');
        if (sel) {
            const opt = document.createElement('option');
            opt.value = data.id; opt.textContent = data.nazwa;
            sel.appendChild(opt);
        }
    }
}

async function deletePrzewoznik(id) {
    const res  = await fetch(`/biuro/przewoznicy/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
    const data = await res.json();
    if (data.success) document.getElementById('pc-' + id)?.remove();
    else Swal.fire({ icon: 'error', title: 'Błąd', text: data.error });
}

function openAdd() {
    document.getElementById('editId').value = '';
    document.getElementById('startId').value = '';
    document.getElementById('stopId').value = '';
    document.getElementById('przewoznikId').value = '';
    document.getElementById('cenaEur').value = '';
    document.getElementById('modalTitle').textContent = 'Dodaj trasę';
    document.getElementById('kosztModal').classList.add('open');
}

function openEdit(id, startId, stopId, przewoznikId, cena) {
    document.getElementById('editId').value = id;
    document.getElementById('startId').value = startId;
    document.getElementById('stopId').value = stopId;
    document.getElementById('przewoznikId').value = przewoznikId ?? '';
    document.getElementById('cenaEur').value = cena;
    document.getElementById('modalTitle').textContent = 'Edytuj trasę';
    document.getElementById('kosztModal').classList.add('open');
}

function closeModal() { document.getElementById('kosztModal').classList.remove('open'); }

async function save() {
    const id   = document.getElementById('editId').value;
    const body = {
        start_id:      document.getElementById('startId').value,
        stop_id:       document.getElementById('stopId').value,
        przewoznik_id: document.getElementById('przewoznikId').value || null,
        cena_eur:      document.getElementById('cenaEur').value,
    };
    if (!body.start_id || !body.stop_id || !body.cena_eur) {
        Swal.fire({ icon: 'warning', title: 'Uzupełnij pola', timer: 1500, showConfirmButton: false });
        return;
    }
    const url    = id ? `/biuro/koszty-transportu/${id}` : '/biuro/koszty-transportu';
    const method = id ? 'PUT' : 'POST';
    const res    = await fetch(url, {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(body),
    });
    const data = await res.json();
    if (data.success) {
        closeModal();
        Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1200, showConfirmButton: false });
        setTimeout(() => location.reload(), 1200);
    } else {
        Swal.fire({ icon: 'error', title: 'Błąd', text: data.error ?? 'Błąd zapisu' });
    }
}

async function del(id) {
    const ok = await Swal.fire({ title: 'Usunąć trasę?', icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#e74c3c', confirmButtonText: 'Usuń', cancelButtonText: 'Anuluj' });
    if (!ok.isConfirmed) return;
    const res  = await fetch(`/biuro/koszty-transportu/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
    const data = await res.json();
    if (data.success) { document.getElementById('kr-' + id)?.remove(); }
    else Swal.fire({ icon: 'error', title: 'Błąd', text: data.error });
}

document.getElementById('kosztModal').addEventListener('click', closeModal);
</script>
@endsection