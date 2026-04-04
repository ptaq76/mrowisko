@extends('layouts.kierowca')

@section('title', 'Nowa produkcja')

@section('styles')
<style>
.back-btn {
    display:flex !important;
    align-items:center !important;
    justify-content:center !important;
    gap:10px !important;
    background:#1a1a1a !important;
    color:#fff !important;
    font-family:'Barlow Condensed',sans-serif !important;
    font-size:20px !important;
    font-weight:800 !important;
    letter-spacing:.06em !important;
    text-transform:uppercase !important;
    width:80% !important;
    margin:0 auto 14px auto !important;
    padding:16px !important;
    border-radius:12px !important;
    border:none !important;
    cursor:pointer !important;
    text-decoration:none !important;
}
.back-btn:hover,.back-btn:active { background:#333 !important;color:#fff !important; }
.page-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase;
    color: #1a1a1a; margin-bottom: 14px;
}

.form-card {
    background: #fff; border-radius: 12px;
    padding: 18px; margin-bottom: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,.07);
}

.field-label {
    display: block; font-size: 11px; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    color: #888; margin-bottom: 8px;
}

.field-select {
    width: 100%; padding: 14px 12px;
    border: 2px solid #e2e5e9; border-radius: 10px;
    font-family: 'Barlow', sans-serif; font-size: 15px;
    color: #1a1a1a; outline: none;
}
.field-select:focus { border-color: #2980b9; }

.row-inputs { display: flex; gap: 12px; }

.big-input {
    width: 100%; padding: 14px;
    border: 2px solid #e2e5e9; border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 36px; font-weight: 900; text-align: center;
    color: #1a1a1a; outline: none;
    -moz-appearance: textfield;
}
.big-input::-webkit-outer-spin-button,
.big-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.big-input:focus { border-color: #2980b9; }

.date-input {
    width: 100%; padding: 14px 12px;
    border: 2px solid #e2e5e9; border-radius: 10px;
    font-family: 'Barlow', sans-serif; font-size: 16px;
    color: #1a1a1a; outline: none;
}
.date-input:focus { border-color: #2980b9; }

.avg-info {
    text-align: center; font-size: 12px;
    color: #aaa; margin-top: 6px;
}

.btn-save {
    width: 100%; padding: 18px; background: #2980b9; color: #fff;
    border: none; border-radius: 12px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase;
    cursor: pointer; margin-bottom: 10px;
    display: flex; align-items: center; justify-content: center; gap: 10px;
}
.btn-save:active { filter: brightness(.9); }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.production.index') }}'"
        style="display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer">
    <i class="fas fa-home"></i> Powrót
</button>

<div class="page-title">Nowa produkcja</div>

<div class="form-card">
    <label class="field-label">Towar</label>
    <select id="fractionSel" class="field-select">
        <option value="">– wybierz –</option>
        @foreach($fractions as $f)
            <option value="{{ $f->id }}">{{ $f->name }}</option>
        @endforeach
    </select>
</div>

<div class="form-card">
    <div class="row-inputs">
        <div style="flex:1">
            <label class="field-label">Belki (szt.)</label>
            <input type="number" id="balesInput" class="big-input"
                   min="1" step="1" inputmode="numeric" placeholder="0"
                   oninput="updateAvg()">
        </div>
        <div style="flex:1">
            <label class="field-label">Waga (kg)</label>
            <input type="number" id="weightInput" class="big-input"
                   min="0" step="1" inputmode="numeric" placeholder="0"
                   oninput="updateAvg()">
        </div>
    </div>
    <div class="avg-info" id="avgInfo"></div>
</div>

<div class="form-card">
    <label class="field-label">Data</label>
    <input type="date" id="dateInput" class="date-input"
           value="{{ now()->format('Y-m-d') }}">
</div>

<button class="btn-save" onclick="save()">
    <i class="fas fa-check-circle"></i> ZAPISZ
</button>

@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function updateAvg() {
    const b = parseInt(document.getElementById('balesInput').value);
    const w = parseInt(document.getElementById('weightInput').value);
    const el = document.getElementById('avgInfo');
    el.textContent = (b > 0 && w > 0) ? `Średnia: ${Math.round(w / b)} kg/bel.` : '';
}

async function save() {
    const fracId = document.getElementById('fractionSel').value;
    const bales  = parseInt(document.getElementById('balesInput').value);
    const weight = parseFloat(document.getElementById('weightInput').value);
    const date   = document.getElementById('dateInput').value;

    if (!fracId) {
        Swal.fire({ icon: 'warning', title: 'Wybierz towar', timer: 1800, showConfirmButton: false });
        return;
    }
    if (!bales || bales < 1) {
        Swal.fire({ icon: 'warning', title: 'Podaj ilość belek', timer: 1800, showConfirmButton: false });
        return;
    }
    if (!weight || weight <= 0) {
        Swal.fire({ icon: 'warning', title: 'Podaj wagę', timer: 1800, showConfirmButton: false });
        return;
    }

    const res  = await fetch('{{ route('plac.production.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ fraction_id: fracId, bales, weight_kg: weight, date }),
    });
    const data = await res.json();

    if (data.success) {
        await Swal.fire({
            icon: 'success', title: 'Zapisano!',
            html: `<strong>${data.item.fraction}</strong><br>${data.item.bales} bel. · ${Math.round(data.item.weight_kg).toLocaleString('pl-PL')} kg`,
            timer: 2000, showConfirmButton: false,
        });
        window.location.href = '{{ route('plac.production.index') }}';
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}
</script>
@endsection
