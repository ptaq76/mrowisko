@extends('layouts.kierowca')

@section('title', 'Waga – ' . $order->client?->short_name)

@section('styles')
<style>
.weigh-header {
    background: #1a1a1a;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 16px;
}
.weigh-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 30px;
    font-weight: 900;
    color: #fff;
    line-height: 1;
}
.weigh-sub {
    font-size: 13px;
    color: #888;
    margin-top: 4px;
}
.weigh-set {
    font-size: 12px;
    color: #6EBF58;
    margin-top: 6px;
    font-weight: 700;
}

.tare-info {
    background: #fff;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 6px rgba(0,0,0,.06);
}
.tare-info .label { font-size: 12px; color: #888; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
.tare-info .value { font-family: 'Barlow Condensed', sans-serif; font-size: 28px; font-weight: 900; color: #1a1a1a; }

.input-card {
    background: #fff;
    border-radius: 12px;
    padding: 24px 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 6px rgba(0,0,0,.06);
    text-align: center;
}
.input-card label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 12px;
}
.brutto-input {
    width: 100%;
    padding: 18px;
    border: 3px solid #e2e5e9;
    border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 42px;
    font-weight: 900;
    text-align: center;
    color: #1a1a1a;
    outline: none;
    transition: border-color .2s;
    -moz-appearance: textfield;
}
.brutto-input::-webkit-outer-spin-button,
.brutto-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.brutto-input:focus { border-color: #3498db; }

.unit { font-size: 14px; color: #aaa; font-weight: 700; margin-top: 8px; letter-spacing: .1em; text-transform: uppercase; }

/* Wynik */
.result-card {
    background: #e8f7e4;
    border: 2px solid #6EBF58;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 16px;
    display: none;
}
.result-card.show { display: block; }

.result-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 5px 0;
    border-bottom: 1px solid rgba(110,191,88,.25);
}
.result-row:last-child { border-bottom: none; }
.result-row .rl { font-size: 12px; color: #666; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
.result-row .rv { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 800; color: #333; }

.netto-big {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 56px;
    font-weight: 900;
    color: #2d7a1a;
    text-align: center;
    line-height: 1;
    margin: 14px 0 4px;
}
.netto-unit { text-align: center; font-size: 13px; color: #2d7a1a; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }

/* Przyciski */
.btn-calc {
    width: 100%;
    padding: 18px;
    background: #3498db;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px;
    font-weight: 900;
    letter-spacing: .06em;
    text-transform: uppercase;
    cursor: pointer;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.btn-calc:active { filter: brightness(.9); }

.btn-confirm {
    width: 100%;
    padding: 18px;
    background: #6EBF58;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px;
    font-weight: 900;
    letter-spacing: .06em;
    text-transform: uppercase;
    cursor: pointer;
    margin-bottom: 10px;
    display: none;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.btn-confirm.show { display: flex; }
.btn-confirm:active { filter: brightness(.9); }

.btn-back {
    width: 100%;
    padding: 14px;
    background: none;
    color: #999;
    border: 1px solid #e2e5e9;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
}
</style>
@endsection

@section('content')

{{-- Nagłówek --}}
<div class="weigh-header">
    <div class="weigh-client">{{ $order->client?->short_name }}</div>
    <div class="weigh-sub">
        {{ $order->type === 'pickup' ? '↓ Odbiór' : '↑ Wysyłka' }}
        @if($order->planned_time) · {{ substr($order->planned_time, 0, 5) }} @endif
        @if($order->fractions_note) · {{ $order->fractions_note }} @endif
    </div>
    @if($vehicleSet)
    <div class="weigh-set"><i class="fas fa-truck-moving"></i> {{ $vehicleSet->label }}</div>
    @endif
</div>

@if(!$vehicleSet)
{{-- Brak tary --}}
<div style="background:#fdecea;border:2px solid #e74c3c;border-radius:12px;padding:16px;margin-bottom:16px;text-align:center">
    <i class="fas fa-exclamation-triangle" style="color:#e74c3c;font-size:24px;margin-bottom:8px;display:block"></i>
    <div style="font-weight:700;color:#c0392b">Brak tary dla tego zestawu</div>
    <div style="font-size:13px;color:#888;margin-top:4px">Skontaktuj się z biurem.</div>
</div>
<button class="btn-back" onclick="history.back()">
    <i class="fas fa-arrow-left"></i> Powrót
</button>
@else

{{-- Tara --}}
<div class="tare-info">
    <span class="label">Tara zestawu</span>
    <span class="value">{{ number_format($vehicleSet->tare_kg, 3, ',', ' ') }} t</span>
</div>

{{-- Wskazanie wagi --}}
<div class="input-card">
    <label>Wskazanie wagi (tony)</label>
    <input type="number" id="bruttoInput" class="brutto-input"
           step="0.001" min="0" inputmode="decimal" autofocus>
    <div class="unit">tony [t]</div>
</div>

{{-- Oblicz --}}
<button class="btn-calc" onclick="calculate()">
    <i class="fas fa-calculator"></i> OBLICZ
</button>

{{-- Wynik --}}
<div class="result-card" id="resultCard">
    <div class="result-row">
        <span class="rl">Brutto</span>
        <span class="rv" id="resBrutto">–</span>
    </div>
    <div class="result-row">
        <span class="rl">Tara</span>
        <span class="rv" id="resTare">–</span>
    </div>
    <div class="netto-big" id="resNetto">–</div>
    <div class="netto-unit">ton netto</div>
</div>

{{-- Potwierdź --}}
<button class="btn-confirm" id="btnConfirm" onclick="doConfirm()">
    <i class="fas fa-check"></i> ZAPISZ
</button>

<button class="btn-back" onclick="history.back()">
    <i class="fas fa-arrow-left"></i> Powrót
</button>

@endif
@endsection

@section('scripts')
<script>
const TARE     = {{ $vehicleSet ? $vehicleSet->tare_kg : 0 }};
const SET_ID   = {{ $vehicleSet ? $vehicleSet->id : 'null' }};
const ORDER_ID = {{ $order->id }};
let _netto  = null;
let _brutto = null;

function calculate() {
    const val = parseFloat(document.getElementById('bruttoInput').value);
    if (isNaN(val) || val <= 0) {
        Swal.fire({ icon: 'warning', title: 'Podaj wskazanie wagi', timer: 1800, showConfirmButton: false });
        return;
    }

    _brutto = val;
    _netto  = Math.round((val - TARE) * 1000) / 1000;

    document.getElementById('resBrutto').textContent = val.toFixed(3).replace('.', ',') + ' t';
    document.getElementById('resTare').textContent   = TARE.toFixed(3).replace('.', ',') + ' t';
    document.getElementById('resNetto').textContent  = _netto.toFixed(3).replace('.', ',');

    document.getElementById('resultCard').classList.add('show');
    document.getElementById('btnConfirm').classList.add('show');
    document.getElementById('resultCard').scrollIntoView({ behavior: 'smooth' });
}

async function doConfirm() {
    const res  = await fetch(`/kierowca/orders/${ORDER_ID}/weigh-confirm`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            weight_brutto:  _brutto,
            weight_netto:   _netto,
            vehicle_set_id: SET_ID,
        }),
    });
    const data = await res.json();

    if (data.success) {
        await Swal.fire({
            icon: 'success',
            title: 'Zapisano!',
            html: `Masa netto:<br><strong style="font-size:32px">${_netto.toFixed(3).replace('.', ',')} t</strong>`,
            confirmButtonText: 'OK',
            confirmButtonColor: '#6EBF58',
        });
        window.location.href = '{{ route('kierowca.dashboard') }}?data={{ $order->planned_date->format('Y-m-d') }}';
    } else {
        Swal.fire({ icon: 'error', title: 'Błąd', text: data.message ?? 'Spróbuj ponownie.' });
    }
}
</script>
@endsection
