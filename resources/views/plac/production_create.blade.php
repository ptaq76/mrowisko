@extends('layouts.plac')

@section('title', 'Produkcja')
@section('hide_datebar', '1')

@section('styles')
<style>
.quick-btns {
    display: flex;
    gap: 10px;
    margin-bottom: 14px;
}
.btn-quick {
    flex: 1;
    padding: 22px 8px;
    border: none;
    border-radius: 14px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 26px;
    font-weight: 900;
    letter-spacing: .08em;
    text-transform: uppercase;
    cursor: pointer;
    transition: filter .12s, transform .1s;
    box-shadow: 0 3px 10px rgba(0,0,0,.15);
    outline: 4px solid transparent;
}
.btn-quick:active { filter: brightness(.88); transform: scale(.97); }
.btn-czysty { background: #2980b9; color: #fff; }
.btn-brudny { background: #f39c12; color: #fff; }
.btn-czysty.selected { outline-color: #1a1a1a; }
.btn-brudny.selected { outline-color: #1a1a1a; }

.form-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px;
    margin-bottom: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,.07);
}
.f-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #888;
    margin-bottom: 10px;
}
.f-select {
    width: 100%;
    padding: 16px 14px;
    border: 2px solid #e2e5e9;
    border-radius: 12px;
    font-family: 'Barlow', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: #1a1a1a;
    outline: none;
    -webkit-appearance: none;
    appearance: none;
    background: #fff;
}
.f-select:focus { border-color: #1a1a1a; }

.big-input {
    width: 100%;
    padding: 18px;
    border: 2px solid #e2e5e9;
    border-radius: 12px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 52px;
    font-weight: 900;
    text-align: center;
    color: #1a1a1a;
    outline: none;
    -moz-appearance: textfield;
}
.big-input::-webkit-outer-spin-button,
.big-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.big-input:focus { border-color: #1a1a1a; }

.i-unit {
    text-align: center;
    font-size: 12px;
    color: #aaa;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-top: 8px;
}
.avg-info {
    text-align: center;
    font-size: 14px;
    color: #888;
    margin-top: 10px;
    font-weight: 600;
}

.btn-save {
    width: 100%;
    padding: 22px;
    background: #1a1a1a;
    color: #fff;
    border: none;
    border-radius: 14px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 24px;
    font-weight: 900;
    letter-spacing: .06em;
    text-transform: uppercase;
    cursor: pointer;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,.15);
}
.btn-save:active { filter: brightness(.88); }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.production.index') }}'"
        style="display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer">
    <i class="fas fa-home"></i> Powrót
</button>

{{-- Szybki wybór --}}
<div class="quick-btns">
    <button class="btn-quick btn-czysty" id="btnCzysty" onclick="quickSelect(46, this)">
        CZYSTY
    </button>
    <button class="btn-quick btn-brudny" id="btnBrudny" onclick="quickSelect(91, this)">
        BRUDNY
    </button>
</div>

{{-- Wybór towaru --}}
<div class="form-card">
    <label class="f-label">Towar</label>
    <select id="fractionSel" class="f-select" onchange="onFracChange()">
        <option value="">– wybierz –</option>
        @foreach($fractions as $f)
            <option value="{{ $f->id }}">{{ $f->name }}</option>
        @endforeach
    </select>
</div>

{{-- Belki --}}
<div class="form-card">
    <label class="f-label">Ilość belek</label>
    <input type="number" id="balesInput" class="big-input"
           min="1" step="1" inputmode="numeric" placeholder="0"
           oninput="onBalesChange()">
    <div class="i-unit">szt.</div>
</div>

{{-- Waga --}}
<div class="form-card">
    <label class="f-label">Waga</label>
    <input type="number" id="weightInput" class="big-input"
           min="0" step="1" inputmode="numeric" placeholder="0"
           oninput="updateAvg()">
    <div class="i-unit">kg</div>
    <div class="avg-info" id="avgInfo"></div>
</div>

{{-- Data --}}
<div class="form-card">
    <label class="f-label">Data</label>
    <input type="date" id="dateInput"
           style="width:100%;padding:16px 14px;border:2px solid #e2e5e9;border-radius:12px;font-size:18px;font-weight:600;color:#1a1a1a;outline:none"
           value="{{ now()->format('Y-m-d') }}">
</div>

<button class="btn-save" onclick="save()">
    <i class="fas fa-check-circle"></i> ZAPISZ
</button>

@endsection

@section('scripts')
<script>
const CSRF          = document.querySelector('meta[name="csrf-token"]').content;
const QUICK_IDS     = [46, 91];
const QUICK_WEIGHT  = 500;
const LAST_FRAC_KEY = 'plac_last_fraction';

document.addEventListener('DOMContentLoaded', () => {
    const last = localStorage.getItem(LAST_FRAC_KEY);
    if (last) {
        document.getElementById('fractionSel').value = last;
        highlightQuick(parseInt(last));
    }
});

function quickSelect(id, btn) {
    document.getElementById('fractionSel').value = id;
    highlightQuick(id);
    onBalesChange();
}

function highlightQuick(id) {
    document.getElementById('btnCzysty').classList.toggle('selected', id === 46);
    document.getElementById('btnBrudny').classList.toggle('selected', id === 91);
}

function onFracChange() {
    const id = parseInt(document.getElementById('fractionSel').value);
    highlightQuick(id);
    onBalesChange();
}

function onBalesChange() {
    const id    = parseInt(document.getElementById('fractionSel').value);
    const bales = parseInt(document.getElementById('balesInput').value);
    if (QUICK_IDS.includes(id) && bales > 0) {
        document.getElementById('weightInput').value = bales * QUICK_WEIGHT;
    }
    updateAvg();
}

function updateAvg() {
    const b  = parseInt(document.getElementById('balesInput').value);
    const w  = parseInt(document.getElementById('weightInput').value);
    document.getElementById('avgInfo').textContent =
        (b > 0 && w > 0) ? `Średnia: ${Math.round(w / b)} kg/bel.` : '';
}

async function save() {
    const fracId = document.getElementById('fractionSel').value;
    const bales  = parseInt(document.getElementById('balesInput').value);
    const weight = parseFloat(document.getElementById('weightInput').value);
    const date   = document.getElementById('dateInput').value;

    if (!fracId) {
        Swal.fire({ icon:'warning', title:'Wybierz towar', timer:1800, showConfirmButton:false });
        return;
    }
    if (!bales || bales < 1) {
        Swal.fire({ icon:'warning', title:'Podaj ilość belek', timer:1800, showConfirmButton:false });
        return;
    }
    if (!weight || weight <= 0) {
        Swal.fire({ icon:'warning', title:'Podaj wagę', timer:1800, showConfirmButton:false });
        return;
    }

    const res  = await fetch('{{ route('plac.production.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ fraction_id: fracId, bales, weight_kg: weight, date }),
    });
    const data = await res.json();

    if (data.success) {
        localStorage.setItem(LAST_FRAC_KEY, fracId);
        window.location.href = '{{ route('plac.production.index') }}';
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon:'error', title:'Błąd', text:errors });
    }
}
</script>
@endsection