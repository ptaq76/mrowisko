@extends('layouts.kierowca')

@section('title', 'Waga – ' . $order->client?->short_name)

@section('styles')
<style>
.weigh-header {
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 16px;
}
.weigh-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 34px;
    font-weight: 900;
    line-height: 1;
}
.weigh-sub {
    font-size: 13px;
    margin-top: 4px;
}
.weigh-plates {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    flex-wrap: wrap;
}
.nr-rej {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    border: 2px solid #1a1a1a;
    padding: 3px 10px;
    border-radius: 6px;
    font-weight: 900;
    font-size: 16px;
    color: #1a1a1a;
    letter-spacing: .04em;
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
    font-size: 52px;
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
.unit { font-size: 16px; color: #aaa; font-weight: 700; margin-top: 8px; letter-spacing: .1em; text-transform: uppercase; }

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

/* Opakowania w SweetAlert */
.pkg-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}
.pkg-row:last-child { border-bottom: none; }
.pkg-name {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 20px;
    font-weight: 800;
    color: #1a1a1a;
    text-align: left;
}
.pkg-sub { font-size: 11px; color: #aaa; font-weight: 600; }
.pkg-input {
    width: 80px;
    padding: 10px 8px;
    border: 2px solid #e2e5e9;
    border-radius: 8px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 26px;
    font-weight: 900;
    text-align: center;
    color: #1a1a1a;
    outline: none;
    -moz-appearance: textfield;
}
.pkg-input::-webkit-outer-spin-button,
.pkg-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.pkg-input:focus { border-color: #27ae60; }
</style>
@endsection

@section('content')

@php $isSale = $order->type === 'sale'; @endphp
<div class="weigh-header" style="background:{{ $isSale ? '#f39c12' : '#27ae60' }};padding:20px 20px 16px;border-radius:16px">
    <div class="weigh-client" style="color:#fff">{{ $order->client?->short_name }}</div>
    <div class="weigh-sub" style="color:{{ $isSale ? 'rgba(0,0,0,.55)' : 'rgba(255,255,255,.7)' }};margin-top:6px">
        {{ $order->type === 'pickup' ? '↓ Odbiór' : '↑ Wysyłka' }}
        @if($order->planned_time) · {{ substr($order->planned_time, 0, 5) }} @endif
        @if($order->fractions_note) · {{ $order->fractions_note }} @endif
    </div>
    <div class="weigh-plates" style="margin-top:12px">
        @if($order->tractor)
        <span class="nr-rej">{{ $order->tractor->plate }}</span>
        @endif
        @if($order->trailer)
        <span class="nr-rej">{{ $order->trailer->plate }}</span>
        @endif
    </div>
</div>

@if(!$vehicleSet)
<div style="background:#fdecea;border:2px solid #e74c3c;border-radius:12px;padding:16px;margin-bottom:16px;text-align:center">
    <i class="fas fa-exclamation-triangle" style="color:#e74c3c;font-size:24px;margin-bottom:8px;display:block"></i>
    <div style="font-weight:700;color:#c0392b">Brak tary dla tego zestawu</div>
    <div style="font-size:13px;color:#888;margin-top:4px">Skontaktuj się z biurem.</div>
</div>
<button class="btn-back" onclick="history.back()">
    <i class="fas fa-arrow-left"></i> Powrót
</button>
@else

<div class="tare-info">
    <span class="label">Tara zestawu</span>
    <span class="value">{{ number_format($vehicleSet->tare_kg * 1000, 0, ',', ' ') }} kg</span>
</div>

<div class="input-card">
    <label>Wskazanie wagi</label>
    <input type="number" id="bruttoInput" class="brutto-input"
           step="1" min="0" inputmode="numeric" autofocus placeholder="0">
    <div class="unit">kilogramy [kg]</div>
</div>

<button class="btn-calc" onclick="calculate()">
    <i class="fas fa-calculator"></i> OBLICZ
</button>

<div class="result-card" id="resultCard" style="{{ $isSale ? 'background:#fef9e7;border-color:#f39c12' : '' }}">
    <div class="result-row">
        <span class="rl">Brutto</span>
        <span class="rv" id="resBrutto">–</span>
    </div>
    <div class="result-row">
        <span class="rl">Tara</span>
        <span class="rv" id="resTare">–</span>
    </div>
    <div class="netto-big" id="resNetto" style="color:{{ $isSale ? '#d68910' : '#27ae60' }}">–</div>
    <div class="netto-unit" style="color:{{ $isSale ? '#d68910' : '#27ae60' }}">ton netto</div>
</div>

<button class="btn-confirm show" id="btnConfirm" onclick="doConfirm()"
        style="display:none;background:{{ $isSale ? '#f39c12' : '#27ae60' }}">
    <i class="fas fa-check"></i> ZAPISZ
</button>

<button class="btn-back" onclick="history.back()">
    <i class="fas fa-arrow-left"></i> Powrót
</button>

@endif
@endsection

@section('scripts')
<script>
const TARE_KG   = {{ $vehicleSet ? $vehicleSet->tare_kg * 1000 : 0 }};
const TARE_T    = {{ $vehicleSet ? $vehicleSet->tare_kg : 0 }};
const SET_ID    = {{ $vehicleSet ? $vehicleSet->id : 'null' }};
const ORDER_ID  = {{ $order->id }};
const IS_PICKUP = {{ $order->type === 'pickup' ? 'true' : 'false' }};
const BTN_COLOR = IS_PICKUP ? '#27ae60' : '#f39c12';
const OPAKOWANIA = @json($opakowania->map(fn($o) => ['id' => $o->id, 'name' => $o->name, 'waga' => $o->waga]));
const RETURN_URL = '{{ route('kierowca.dashboard') }}?data={{ $order->planned_date->format('Y-m-d') }}';

let _netto  = null;
let _brutto = null;

function fmtT(t) {
    return t.toFixed(3).replace('.', ',') + ' t';
}

function calculate() {
    const kg = parseInt(document.getElementById('bruttoInput').value);
    if (isNaN(kg) || kg <= 0) {
        Swal.fire({ icon: 'warning', title: 'Podaj wskazanie wagi', timer: 1800, showConfirmButton: false });
        return;
    }

    _brutto = kg / 1000;
    _netto  = Math.round(kg - TARE_KG) / 1000;

    document.getElementById('resBrutto').textContent = fmtT(_brutto);
    document.getElementById('resTare').textContent   = fmtT(TARE_T);
    document.getElementById('resNetto').textContent  = fmtT(_netto);

    document.getElementById('resultCard').classList.add('show');
    document.getElementById('btnConfirm').classList.add('show');
    document.getElementById('btnConfirm').style.display = 'flex';
    document.getElementById('resultCard').scrollIntoView({ behavior: 'smooth' });
}

async function doConfirm() {
    // 1. Zapisz wagę
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

    if (!data.success) {
        Swal.fire({ icon: 'error', title: 'Błąd', text: data.message ?? 'Spróbuj ponownie.' });
        return;
    }

    // 2. Dla pickup — pytaj o opakowania; dla sale — od razu wróć
    if (!IS_PICKUP) {
        await Swal.fire({
            icon: 'success',
            title: 'Zapisano!',
            html: `Masa netto:<br><strong style="font-size:32px;color:${BTN_COLOR}">${fmtT(_netto)}</strong>`,
            confirmButtonText: 'OK',
            confirmButtonColor: BTN_COLOR,
        });
        window.location.href = RETURN_URL;
        return;
    }

    // 3. Pickup — SweetAlert z opakowaniami
    const pkgHtml = `
        <div style="text-align:left;margin-top:8px">
            ${OPAKOWANIA.map(o => `
            <div class="pkg-row">
                <div>
                    <div class="pkg-name">${o.name}</div>
                    <div class="pkg-sub">${parseFloat(o.waga).toFixed(0)} kg/szt.</div>
                </div>
                <input type="number" class="pkg-input" id="pkg_${o.id}"
                       min="0" step="1" inputmode="numeric" value="0">
            </div>`).join('')}
        </div>`;

    const pkgResult = await Swal.fire({
        icon: 'success',
        title: 'Zapisano!',
        html: `
            <div style="margin-bottom:16px">
                Masa netto:<br>
                <strong style="font-size:32px;color:${BTN_COLOR}">${fmtT(_netto)}</strong>
            </div>
            <div style="font-size:13px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px">
                Ilość opakowań zwrotnych
            </div>
            ${pkgHtml}`,
        showCancelButton: true,
        confirmButtonText: 'Zapisz opakowania',
        cancelButtonText: 'Pomiń',
        confirmButtonColor: BTN_COLOR,
        cancelButtonColor: '#aaa',
        reverseButtons: true,
        allowOutsideClick: false,
        preConfirm: () => {
            return OPAKOWANIA.map(o => ({
                opakowanie_id: o.id,
                quantity: parseInt(document.getElementById(`pkg_${o.id}`)?.value) || 0,
            })).filter(p => p.quantity > 0);
        },
    });

    // 4. Wyślij opakowania jeśli coś wpisano
    if (pkgResult.isConfirmed && pkgResult.value && pkgResult.value.length > 0) {
        await fetch(`/kierowca/orders/${ORDER_ID}/packaging`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ packaging: pkgResult.value }),
        });
    }

    window.location.href = RETURN_URL;
}
</script>
@endsection