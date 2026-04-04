@extends('layouts.kierowca')
@section('title', 'Paliwo')

@section('styles')
<style>
/* Stan zbiornika */
.tank-card {
    background:#1a1a1a;border-radius:14px;padding:20px 16px;margin-bottom:14px;
    display:flex;align-items:center;justify-content:space-between;
}
.tank-label { font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#aaa; }
.tank-level { font-family:'Barlow Condensed',sans-serif;font-size:52px;font-weight:900;color:#fff;line-height:1; }
.tank-unit  { font-size:16px;color:#aaa;margin-top:4px; }
.tank-bar-wrap { width:80px;height:80px;position:relative; }
.tank-bar-wrap svg { transform:rotate(-90deg); }
.tank-bar-bg  { fill:none;stroke:#333;stroke-width:8; }
.tank-bar-fill{ fill:none;stroke:#6EBF58;stroke-width:8;stroke-linecap:round;transition:stroke-dashoffset .5s; }
.tank-bar-fill.low { stroke:#e74c3c; }
.tank-bar-fill.med { stroke:#f39c12; }

/* Przyciski akcji */
.action-btns { display:flex;gap:10px;margin-bottom:14px; }
.btn-action {
    flex:1;padding:16px 8px;border:none;border-radius:12px;
    font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;
    letter-spacing:.04em;text-transform:uppercase;cursor:pointer;
    display:flex;align-items:center;justify-content:center;gap:8px;
}
.btn-tankowanie { background:#f39c12;color:#fff; }
.btn-dostawa    { background:#27ae60;color:#fff; }
.btn-inventar   { background:#7f8c8d;color:#fff; }

/* Lista transakcji */
.trans-section-title { font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#aaa;margin-bottom:8px; }
.trans-list { display:flex;flex-direction:column;gap:6px; }
.trans-item {
    background:#fff;border-radius:10px;padding:12px 14px;
    display:flex;align-items:center;justify-content:space-between;
    box-shadow:0 1px 3px rgba(0,0,0,.07);
}
.ti-left  { display:flex;flex-direction:column;gap:2px; }
.ti-type  { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900; }
.ti-type.tankowanie  { color:#f39c12; }
.ti-type.dostawa     { color:#27ae60; }
.ti-type.inwentaryzacja { color:#7f8c8d; }
.ti-vehicle { font-size:12px;color:#888; }
.ti-date  { font-size:11px;color:#ccc; }
.ti-right { text-align:right; }
.ti-liters { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900; }
.ti-liters.minus { color:#e74c3c; }
.ti-liters.plus  { color:#27ae60; }
.ti-liters.zero  { color:#7f8c8d; }
.ti-after { font-size:11px;color:#aaa; }
.btn-undo { background:none;border:none;color:#ccc;cursor:pointer;padding:4px;font-size:14px; }

/* Modal */
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1000;align-items:flex-end;justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-sheet {
    background:#fff;border-radius:18px 18px 0 0;width:100%;max-width:480px;
    padding:24px 20px 36px;
    animation: slideUp .25s ease;
}
@keyframes slideUp { from{transform:translateY(100%)} to{transform:translateY(0)} }
.sheet-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;text-transform:uppercase;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center; }
.sheet-close { background:none;border:none;font-size:22px;color:#aaa;cursor:pointer; }
.s-label { display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:6px; }
.s-input  { width:100%;padding:14px;border:1.5px solid #dde0e5;border-radius:10px;font-size:18px;font-weight:700;outline:none;margin-bottom:14px; }
.s-input:focus { border-color:#1a1a1a; }
.s-select { width:100%;padding:14px;border:1.5px solid #dde0e5;border-radius:10px;font-size:16px;outline:none;margin-bottom:14px; }
.btn-confirm {
    width:100%;padding:18px;border:none;border-radius:12px;
    font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;
    letter-spacing:.06em;text-transform:uppercase;cursor:pointer;color:#fff;
}
.btn-cancel-sheet { width:100%;padding:14px;background:none;border:1px solid #dde0e5;border-radius:12px;font-size:15px;font-weight:600;cursor:pointer;margin-top:8px;color:#555; }
</style>
@endsection

@section('content')

<button onclick="window.location='{{ route('plac.dashboard') }}'"
        style="display:flex;align-items:center;gap:8px;background:#1a1a1a;color:#fff;border:none;border-radius:10px;padding:12px 20px;font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;width:100%;margin-bottom:12px">
    <i class="fas fa-home"></i> Dashboard
</button>

{{-- Stan zbiornika --}}
@php
    $capacity  = 5000; // litry - można zrobić konfigurowalne
    $pct       = $capacity > 0 ? min(100, round($level / $capacity * 100)) : 0;
    $r         = 30; $circ = 2 * M_PI * $r;
    $offset    = $circ * (1 - $pct / 100);
    $barClass  = $pct <= 15 ? 'low' : ($pct <= 30 ? 'med' : '');
@endphp

<div class="tank-card">
    <div>
        <div class="tank-label">Stan zbiornika</div>
        <div class="tank-level" id="tankLevel">{{ number_format($level, 0, ',', ' ') }}</div>
        <div class="tank-unit">litrów</div>
    </div>
    <div class="tank-bar-wrap">
        <svg width="80" height="80" viewBox="0 0 80 80">
            <circle class="tank-bar-bg"  cx="40" cy="40" r="{{ $r }}"/>
            <circle class="tank-bar-fill {{ $barClass }}" id="tankArc"
                    cx="40" cy="40" r="{{ $r }}"
                    stroke-dasharray="{{ $circ }}"
                    stroke-dashoffset="{{ $offset }}"/>
        </svg>
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#fff" id="tankPct">{{ $pct }}%</div>
    </div>
</div>

{{-- Przyciski akcji --}}
<div class="action-btns">
    <button class="btn-action btn-tankowanie" onclick="openSheet('tankowanie')">
        <i class="fas fa-gas-pump"></i> Tankowanie
    </button>
    <button class="btn-action btn-dostawa" onclick="openSheet('dostawa')">
        <i class="fas fa-truck"></i> Dostawa
    </button>
</div>
<div style="margin-bottom:14px">
    <button class="btn-action btn-inventar" style="width:100%;padding:12px" onclick="openSheet('inwentaryzacja')">
        <i class="fas fa-balance-scale"></i> Inwentaryzacja (korekta stanu)
    </button>
</div>

{{-- Lista transakcji --}}
<div class="trans-section-title">Ostatnie transakcje</div>
<div class="trans-list" id="transList">
    @forelse($transactions as $t)
    <div class="trans-item" id="tr-{{ $t->id }}">
        <div class="ti-left">
            <span class="ti-type {{ $t->type }}">
                @if($t->type==='tankowanie') ⛽ Tankowanie
                @elseif($t->type==='dostawa') 🚛 Dostawa
                @else ⚖ Inwentaryzacja
                @endif
            </span>
            @if($t->vehicle)
            <span class="ti-vehicle">{{ $t->vehicle->plate }} – {{ $t->vehicle->name }}</span>
            @endif
            <span class="ti-date">{{ $t->created_at->format('d.m.Y H:i') }}</span>
        </div>
        <div class="ti-right" style="display:flex;align-items:center;gap:8px">
            <div>
                <div class="ti-liters {{ $t->type==='tankowanie' ? 'minus' : ($t->type==='dostawa' ? 'plus' : 'zero') }}">
                    {{ $t->type==='tankowanie' ? '-' : ($t->type==='dostawa' ? '+' : '=') }}{{ number_format($t->liters, 0, ',', ' ') }} L
                </div>
                <div class="ti-after">→ {{ number_format($t->tank_after, 0, ',', ' ') }} L</div>
            </div>
            @if($loop->first)
            <button class="btn-undo" onclick="undoLast({{ $t->id }})" title="Cofnij">
                <i class="fas fa-undo"></i>
            </button>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center;color:#ccc;padding:24px">Brak transakcji</div>
    @endforelse
</div>

{{-- Modal --}}
<div class="modal-overlay" id="fuelModal">
    <div class="modal-sheet" onclick="event.stopPropagation()">
        <div class="sheet-title">
            <span id="sheetTitle">Tankowanie</span>
            <button class="sheet-close" onclick="closeSheet()">×</button>
        </div>

        <div id="vehicleRow">
            <label class="s-label">Pojazd</label>
            <select id="vehicleSelect" class="s-select">
                <option value="">– wybierz pojazd –</option>
                @foreach($groups as $group)
                @if($group->vehicles->isNotEmpty())
                <optgroup label="{{ $group->nazwa }}">
                    @foreach($group->vehicles as $v)
                    <option value="{{ $v->id }}">{{ $v->nazwa }}</option>
                    @endforeach
                </optgroup>
                @endif
                @endforeach
            </select>
        </div>

        <label class="s-label" id="litersLabel">Liczba litrów</label>
        <input type="number" id="litersInput" class="s-input" min="1" inputmode="numeric" placeholder="0">

        <div id="inventarInfo" style="display:none;background:#fef9e7;border-radius:8px;padding:10px 12px;margin-bottom:14px;font-size:13px;color:#7d6608">
            <i class="fas fa-info-circle"></i>
            Wpisz aktualną ilość litrów w zbiorniku. Stan zostanie ustawiony na tę wartość.
        </div>

        <button class="btn-confirm" id="confirmBtn" onclick="confirmAction()">Zapisz</button>
        <button class="btn-cancel-sheet" onclick="closeSheet()">Anuluj</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const CAPACITY = {{ $capacity }};
let currentType = null;

function openSheet(type) {
    currentType = type;
    const titles = { tankowanie:'⛽ Tankowanie', dostawa:'🚛 Dostawa', inwentaryzacja:'⚖ Inwentaryzacja' };
    const colors = { tankowanie:'#f39c12', dostawa:'#27ae60', inwentaryzacja:'#7f8c8d' };

    document.getElementById('sheetTitle').textContent = titles[type];
    document.getElementById('confirmBtn').style.background = colors[type];
    document.getElementById('vehicleRow').style.display = type === 'tankowanie' ? 'block' : 'none';
    document.getElementById('inventarInfo').style.display = type === 'inwentaryzacja' ? 'block' : 'none';
    document.getElementById('litersLabel').textContent = type === 'inwentaryzacja' ? 'Aktualny stan zbiornika (litry)' : 'Liczba litrów';
    document.getElementById('litersInput').value = '';
    document.getElementById('vehicleSelect').value = '';
    document.getElementById('fuelModal').classList.add('open');
    setTimeout(() => document.getElementById('litersInput').focus(), 300);
}

function closeSheet() {
    document.getElementById('fuelModal').classList.remove('open');
}

async function confirmAction() {
    const liters  = parseInt(document.getElementById('litersInput').value);
    const vehicle = document.getElementById('vehicleSelect').value;

    if (!liters || liters < 1) {
        Swal.fire({ icon:'warning', title:'Podaj liczbę litrów', timer:1500, showConfirmButton:false });
        return;
    }
    if (currentType === 'tankowanie' && !vehicle) {
        Swal.fire({ icon:'warning', title:'Wybierz pojazd', timer:1500, showConfirmButton:false });
        return;
    }

    const res  = await fetch('/plac/fuel', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type':'application/json', 'Accept':'application/json' },
        body: JSON.stringify({ type: currentType, liters, fuel_vehicle_id: vehicle || null }),
    });
    const data = await res.json();

    if (data.success) {
        closeSheet();
        updateTankDisplay(data.tank_after);
        Swal.fire({ icon:'success', title:'Zapisano!', timer:1500, showConfirmButton:false });
        setTimeout(() => location.reload(), 1600);
    } else {
        Swal.fire({ icon:'error', title:'Błąd', text: data.error ?? 'Nieznany błąd' });
    }
}

async function undoLast(id) {
    const ok = await Swal.fire({
        title: 'Cofnąć ostatnią transakcję?', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#e74c3c',
        confirmButtonText:'Cofnij', cancelButtonText:'Anuluj',
    });
    if (!ok.isConfirmed) return;

    const res  = await fetch(`/plac/fuel/${id}`, {
        method:'DELETE',
        headers:{ 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('tr-' + id)?.remove();
        updateTankDisplay(data.tank_after);
        Swal.fire({ icon:'success', title:'Cofnięto!', timer:1200, showConfirmButton:false });
    } else {
        Swal.fire({ icon:'error', title:'Błąd', text: data.error });
    }
}

function updateTankDisplay(level) {
    const pct = Math.min(100, Math.round(level / CAPACITY * 100));
    document.getElementById('tankLevel').textContent = level.toLocaleString('pl');
    document.getElementById('tankPct').textContent   = pct + '%';

    const r     = 30;
    const circ  = 2 * Math.PI * r;
    const arc   = document.getElementById('tankArc');
    arc.style.strokeDashoffset = circ * (1 - pct / 100);
    arc.className = 'tank-bar-fill' + (pct <= 15 ? ' low' : pct <= 30 ? ' med' : '');
}

// Zamknij po kliknięciu tła
document.getElementById('fuelModal').addEventListener('click', closeSheet);
document.querySelector('.modal-sheet').addEventListener('click', e => e.stopPropagation());
</script>
@endsection
