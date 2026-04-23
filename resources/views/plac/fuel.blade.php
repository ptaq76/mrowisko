@extends('layouts.plac')
@section('title', 'Paliwo')

@section('styles')
<style>
/* Stan zbiornika */
.tank-card {
    background: #1a1a1a;
    border-radius: 16px;
    padding: 20px 20px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.tank-label { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #6EBF58; margin-bottom: 4px; }
.tank-level { font-family: 'Barlow Condensed', sans-serif; font-size: 52px; font-weight: 900; color: #fff; line-height: 1; }
.tank-unit  { font-size: 14px; color: #aaa; margin-top: 4px; }
.tank-bar-wrap { width: 80px; height: 80px; position: relative; flex-shrink: 0; }
.tank-bar-wrap svg { transform: rotate(-90deg); }
.tank-bar-bg   { fill: none; stroke: #333; stroke-width: 8; }
.tank-bar-fill { fill: none; stroke: #6EBF58; stroke-width: 8; stroke-linecap: round; transition: stroke-dashoffset .5s; }
.tank-bar-fill.low { stroke: #e74c3c; }
.tank-bar-fill.med { stroke: #f39c12; }
.tank-pct-label {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 18px; font-weight: 900; color: #fff;
}

/* Przyciski akcji */
.action-btns { display: flex; gap: 10px; margin-bottom: 10px; }
.btn-action {
    flex: 1; padding: 18px 8px; border: none; border-radius: 14px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 19px; font-weight: 900;
    letter-spacing: .04em; text-transform: uppercase; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,.12);
    transition: filter .12s;
}
.btn-action:active { filter: brightness(.9); }
.btn-tankowanie { background: #f39c12; color: #fff; }
.btn-dostawa    { background: #2980b9; color: #fff; }
.btn-inventar   { background: #7f8c8d; color: #fff; width: 100%; margin-bottom: 14px; padding: 14px; }

/* Select pojazdu */
.s-select {
    width: 100%; padding: 14px; border: 2px solid #e2e5e9; border-radius: 12px;
    font-size: 16px; outline: none; margin-bottom: 14px;
    background: #fff; color: #1a1a1a;
    -webkit-appearance: none; appearance: none;
}
.s-select:focus { border-color: #f39c12; }

/* Lista transakcji */
.trans-section-title {
    font-size: 11px; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: #aaa; margin-bottom: 8px;
}
.trans-list { display: flex; flex-direction: column; gap: 6px; }
.trans-item {
    background: #fff; border-radius: 12px; padding: 12px 14px;
    display: flex; align-items: center; justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,.07);
}
.ti-left  { display: flex; flex-direction: column; gap: 2px; flex: 1; min-width: 0; }
.ti-type  { font-family: 'Barlow Condensed', sans-serif; font-size: 16px; font-weight: 900; }
.ti-type.tankowanie    { color: #f39c12; }
.ti-type.dostawa       { color: #2980b9; }
.ti-type.inwentaryzacja{ color: #7f8c8d; }
.ti-meta  { font-size: 11px; color: #aaa; margin-top: 1px; }
.ti-right { text-align: right; display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.ti-liters { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900; }
.ti-liters.minus { color: #e74c3c; }
.ti-liters.plus  { color: #27ae60; }
.ti-liters.zero  { color: #7f8c8d; }
.ti-after  { font-size: 11px; color: #aaa; }
.btn-undo  { background: none; border: none; color: #ccc; cursor: pointer; padding: 6px; font-size: 16px; }

/* Modal */
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.6); z-index: 1000; }
.modal-overlay.open { display: block; }
.modal-sheet {
    background: #fff; border-radius: 20px 20px 0 0; width: 100%;
    padding: 24px 20px 40px;
    animation: slideUp .25s ease;
    position: fixed; bottom: 0; left: 0; right: 0;
    max-height: 85vh; overflow-y: auto;
    box-sizing: border-box;
}
@keyframes slideUp { from { transform: translateY(100%) } to { transform: translateY(0) } }
.sheet-title {
    font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900;
    text-transform: uppercase; margin-bottom: 18px;
    display: flex; justify-content: space-between; align-items: center;
}
.sheet-close { background: none; border: none; font-size: 24px; color: #aaa; cursor: pointer; }
.s-label { display: block; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #888; margin-bottom: 8px; }
.s-input  {
    width: 100%; padding: 16px; border: 2px solid #e2e5e9; border-radius: 12px;
    font-family: 'Barlow Condensed', sans-serif; font-size: 42px; font-weight: 900;
    text-align: center; outline: none; margin-bottom: 16px;
    -moz-appearance: textfield;
}
.s-input::-webkit-outer-spin-button,
.s-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.s-input:focus { border-color: #1a1a1a; }
.btn-confirm {
    width: 100%; padding: 18px; border: none; border-radius: 14px;
    font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase; cursor: pointer; color: #fff;
    box-shadow: 0 3px 10px rgba(0,0,0,.15);
}
.btn-cancel-sheet {
    width: 100%; padding: 14px; background: none; border: 1px solid #dde0e5;
    border-radius: 12px; font-size: 15px; font-weight: 600;
    cursor: pointer; margin-top: 8px; color: #555;
}
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.dashboard') }}'"
        style="display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer">
    <i class="fas fa-home"></i> Powrót
</button>

@php
    $capacity = 5000;
    $pct      = $capacity > 0 ? min(100, round($level / $capacity * 100)) : 0;
    $r        = 30; $circ = 2 * M_PI * $r;
    $offset   = $circ * (1 - $pct / 100);
    $barClass = $pct <= 15 ? 'low' : ($pct <= 30 ? 'med' : '');

    // Spłaszcz pojazdy ze wszystkich grup do jednej listy
    $allVehicles = $groups->flatMap(fn($g) => $g->vehicles);
@endphp

{{-- Stan zbiornika --}}
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
        <div class="tank-pct-label" id="tankPct">{{ $pct }}%</div>
    </div>
</div>

{{-- Przyciski akcji --}}
<div class="action-btns">
    <button class="btn-action btn-tankowanie" onclick="openSheet('tankowanie')">
        <i class="fas fa-gas-pump"></i> Tankowanie
    </button>
    <button class="btn-action btn-dostawa" onclick="openSheet('dostawa')">
        <i class="fas fa-truck-moving"></i> Dostawa
    </button>
</div>
<div style="margin-bottom:14px">
    <button class="btn-action btn-inventar" onclick="openSheet('inwentaryzacja')">
        <i class="fas fa-balance-scale"></i> Korekta stanu
    </button>
</div>

{{-- Lista transakcji --}}
<div class="trans-section-title">Ostatnie transakcje</div>
<div class="trans-list" id="transList">
    @forelse($transactions as $t)
    <div class="trans-item" id="tr-{{ $t->id }}">
        <div class="ti-left">
            <span class="ti-type {{ $t->type }}">
                @if($t->type==='tankowanie')
                    <i class="fas fa-gas-pump fa-xs"></i>
                    <span style="color:#1a1a1a">{{ $t->vehicle?->nazwa ?? $t->vehicle?->plate ?? 'Tankowanie' }}</span>
                @elseif($t->type==='dostawa') <i class="fas fa-truck-moving fa-xs"></i> Dostawa
                @else <i class="fas fa-balance-scale fa-xs"></i> Korekta
                @endif
            </span>
            <span class="ti-meta">
                {{ $t->created_at->format('d.m H:i') }}
                @if($t->operator) · {{ $t->operator }} @endif
                @if($t->vehicle && $t->type !== 'tankowanie') · {{ $t->vehicle->plate }} @endif
            </span>
        </div>
        <div class="ti-right">
            <div>
                <div class="ti-liters {{ $t->type==='tankowanie' ? 'minus' : ($t->type==='dostawa' ? 'plus' : 'zero') }}">
                    {{ $t->type==='tankowanie' ? '−' : ($t->type==='dostawa' ? '+' : '=') }}{{ number_format($t->liters, 0, ',', ' ') }} L
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

        {{-- Wybór pojazdu --}}
        <div id="vehicleRow">
            <label class="s-label">Pojazd</label>
            <select id="vehicleSelect" class="s-select" onchange="selectedVehicleId = this.value">
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
        <input type="number" id="litersInput" class="s-input"
               min="1" inputmode="numeric" placeholder="0">

        <div id="inventarInfo" style="display:none;background:#fef9e7;border-radius:10px;padding:12px 14px;margin-bottom:14px;font-size:13px;color:#7d6608">
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
let currentType      = null;
let selectedVehicleId = null;

function openSheet(type) {
    currentType = type;
    selectedVehicleId = null;
    const vs = document.getElementById('vehicleSelect'); if(vs) vs.value = '';

    const titles = { tankowanie:'⛽ Tankowanie', dostawa:'🚛 Dostawa', inwentaryzacja:'⚖ Korekta stanu' };
    const colors = { tankowanie:'#f39c12', dostawa:'#2980b9', inwentaryzacja:'#7f8c8d' };

    document.getElementById('sheetTitle').textContent      = titles[type];
    document.getElementById('confirmBtn').style.background = colors[type];
    document.getElementById('vehicleRow').style.display    = type === 'tankowanie' ? 'block' : 'none';
    document.getElementById('inventarInfo').style.display  = type === 'inwentaryzacja' ? 'block' : 'none';
    document.getElementById('litersLabel').textContent     = type === 'inwentaryzacja'
        ? 'Aktualny stan zbiornika (litry)' : 'Liczba litrów';
    document.getElementById('litersInput').value = '';
    document.getElementById('fuelModal').classList.add('open');
    setTimeout(() => document.getElementById('litersInput').focus(), 300);
}

function closeSheet() {
    document.getElementById('fuelModal').classList.remove('open');
}

async function confirmAction() {
    const liters = parseInt(document.getElementById('litersInput').value);

    if (!liters || liters < 1) {
        Swal.fire({ icon:'warning', title:'Podaj liczbę litrów', timer:1500, showConfirmButton:false });
        return;
    }
    if (currentType === 'tankowanie' && !selectedVehicleId) {
        Swal.fire({ icon:'warning', title:'Wybierz pojazd', timer:1500, showConfirmButton:false });
        return;
    }

    const res  = await fetch('/plac/fuel', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type':'application/json', 'Accept':'application/json' },
        body: JSON.stringify({ type: currentType, liters, fuel_vehicle_id: selectedVehicleId || null }),
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
        title:'Cofnąć ostatnią transakcję?', icon:'warning',
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
    const pct  = Math.min(100, Math.round(level / CAPACITY * 100));
    const r    = 30;
    const circ = 2 * Math.PI * r;

    document.getElementById('tankLevel').textContent = level.toLocaleString('pl');
    document.getElementById('tankPct').textContent   = pct + '%';

    const arc = document.getElementById('tankArc');
    arc.style.strokeDashoffset = circ * (1 - pct / 100);
    arc.className = 'tank-bar-fill' + (pct <= 15 ? ' low' : pct <= 30 ? ' med' : '');
}

document.getElementById('fuelModal').addEventListener('click', closeSheet);
</script>
@endsection