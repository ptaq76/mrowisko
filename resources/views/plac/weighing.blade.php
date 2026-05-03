@extends('layouts.plac')

@section('title', 'Waga')

@section('styles')
<style>
:root {
    --weighing-bg: #34495e;
    --weighing-dark: #2c3e50;
}

.page-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase;
    color: #1a1a1a; margin-bottom: 4px;
}
.page-sub { font-size: 12px; color: #aaa; margin-bottom: 14px; }

.orders-list {
    display: flex; flex-direction: column; gap: 8px;
    margin-bottom: 16px;
}
.order-card {
    width: 100%; text-align: left;
    background: #fff; border: 2px solid var(--border, #e2e5e9);
    border-radius: 10px; padding: 12px 14px;
    display: flex; flex-direction: column; gap: 4px;
    cursor: pointer; transition: all .15s;
}
.order-card:active { background: #f4f5f7; }
.order-card.selected {
    border-color: var(--weighing-bg);
    background: #ecf0f1;
    box-shadow: 0 2px 8px rgba(52,73,94,.25);
}
.oc-row1 { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.oc-arrow { font-size: 16px; font-weight: 900; }
.oc-arrow.sale { color: #f39c12; }
.oc-arrow.pickup { color: #27ae60; }
.oc-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900; color: #1a1a1a;
    text-transform: uppercase; line-height: 1;
}
.oc-meta { font-size: 11px; color: #888; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.plate-badge {
    display: inline-block;
    background: #fff; border: 2px solid #1a1a1a;
    padding: 1px 5px; border-radius: 4px;
    font-weight: 800; font-size: 11px;
}
.oc-partial {
    margin-top: 4px; padding: 6px 10px;
    background: #fff5e6; border-radius: 6px;
    font-size: 12px; font-weight: 700; color: #d35400;
    display: flex; align-items: center; gap: 6px;
}
.oc-status {
    margin-left: auto;
    font-size: 10px; font-weight: 700;
    padding: 2px 8px; border-radius: 10px;
    text-transform: uppercase; letter-spacing: .04em;
}
.oc-status.loaded { background: #fff5e6; color: #d35400; }
.oc-status.delivered { background: #e8f7e4; color: #1e8449; }

.empty-state {
    text-align: center; padding: 32px 16px; color: #aaa;
}
.empty-state i { font-size: 36px; margin-bottom: 8px; display: block; color: #ccc; }

.weight-card {
    position: sticky; bottom: 0;
    background: #fff;
    border: 2px solid var(--weighing-bg); border-radius: 12px;
    padding: 16px;
    box-shadow: 0 4px 14px rgba(0,0,0,.18);
    margin-bottom: 12px;
}
.wc-selected {
    margin-bottom: 12px; padding: 8px 12px;
    background: #ecf0f1; border-radius: 8px;
    font-size: 13px; font-weight: 700; color: #2c3e50;
}
.wc-selected .wc-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 18px; font-weight: 900; color: #1a1a1a;
}
.wc-partial {
    margin-top: 4px; font-size: 12px; font-weight: 700; color: #d35400;
}
.f-label {
    display: block; font-size: 13px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase;
    color: #111; margin-bottom: 8px;
}
.big-input {
    width: 100%; padding: 16px;
    border: 2px solid #e2e5e9; border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 48px; font-weight: 900; text-align: center;
    color: #1a1a1a; outline: none;
    -moz-appearance: textfield;
}
.big-input::-webkit-outer-spin-button,
.big-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.big-input:focus { border-color: var(--weighing-bg); }
.i-unit {
    text-align: center; font-size: 11px; color: #aaa;
    font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; margin-top: 7px; margin-bottom: 12px;
}
.btn-save-weight {
    width: 100%; padding: 18px;
    background: var(--weighing-bg); color: #fff;
    border: none; border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 10px;
}
.btn-save-weight:active { filter: brightness(.9); }
.btn-save-weight:disabled { background: #bcc4ce; cursor: not-allowed; }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.dashboard') }}'"
        class="btn-back">
    <i class="fas fa-home"></i> Powrót
</button>

<div class="page-title">Waga</div>
<div class="page-sub">Wybierz zlecenie i wpisz wagę z wagi samochodowej</div>

@if($orders->isEmpty())
<div class="empty-state">
    <i class="fas fa-weight"></i>
    <p style="font-size:14px;font-weight:600">Brak zleceń do zważenia</p>
</div>
@else
<div class="orders-list">
    @foreach($orders as $o)
    @php
        $hasPartial = $o->weight_brutto !== null;
        $statusLabel = match($o->status) {
            'loaded' => 'Załadowane',
            'delivered' => 'Dostarczone',
            'weighed' => 'Zważone',
            default => null,
        };
    @endphp
    <button type="button" class="order-card"
            data-order-id="{{ $o->id }}"
            data-client="{{ $o->client?->short_name }}"
            data-type="{{ $o->type }}"
            data-partial="{{ $hasPartial ? (float) $o->weight_brutto : '' }}"
            onclick="selectOrder({{ $o->id }})">
        <div class="oc-row1">
            <span class="oc-arrow {{ $o->type }}">{{ $o->type === 'sale' ? '↑' : '↓' }}</span>
            <span class="oc-client">{{ $o->client?->short_name ?? '?' }}</span>
            @if($statusLabel)
                <span class="oc-status {{ $o->status }}">{{ $statusLabel }}</span>
            @endif
        </div>
        <div class="oc-meta">
            @if($o->tractor)<span class="plate-badge">{{ $o->tractor->plate }}</span>@endif
            @if($o->trailer)<span class="plate-badge">{{ $o->trailer->plate }}</span>@endif
            <span style="margin-left:auto;font-size:11px;color:#aaa">
                {{ optional($o->planned_date)->format('d.m.Y') }}
                @if($o->planned_time) {{ \Illuminate\Support\Carbon::parse($o->planned_time)->format('H:i') }} @endif
            </span>
        </div>
        @if($hasPartial)
        <div class="oc-partial">
            <i class="fas fa-hourglass-half"></i>
            Pierwsza waga: {{ number_format($o->weight_brutto, 3, ',', ' ') }} t — czeka na drugą
        </div>
        @endif
    </button>
    @endforeach
</div>
@endif

<div class="weight-card" id="weightForm" style="display:none">
    <div class="wc-selected">
        <div class="wc-client" id="wcClient">–</div>
        <div id="wcPartial" class="wc-partial" style="display:none"></div>
    </div>

    <label class="f-label" id="wcLabel">Waga z wagi [kg]</label>
    <input type="text" id="weightInput" class="big-input js-numkey"
           placeholder="0"
           data-keypad-label="Waga [kg]"
           data-decimal="false"
           data-min="0" data-max="60000">
    <div class="i-unit">kg</div>

    <button type="button" class="btn-save-weight" onclick="saveWeight()" id="btnSave">
        <i class="fas fa-check-circle"></i> ZAPISZ
    </button>
</div>

@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let _selectedOrderId = null;

function selectOrder(id) {
    _selectedOrderId = id;
    document.querySelectorAll('.order-card').forEach(c => c.classList.remove('selected'));
    const card = document.querySelector(`.order-card[data-order-id="${id}"]`);
    card.classList.add('selected');

    const client = card.dataset.client || '?';
    const partial = card.dataset.partial;

    document.getElementById('wcClient').textContent = client;
    const partialEl = document.getElementById('wcPartial');
    const labelEl = document.getElementById('wcLabel');
    if (partial) {
        const partialT = parseFloat(partial).toFixed(3).replace('.', ',');
        partialEl.innerHTML = '<i class="fas fa-hourglass-half"></i> Pierwsza waga: <b>'+partialT+' t</b> — wpisz drugą';
        partialEl.style.display = '';
        labelEl.textContent = 'Druga waga z wagi [kg]';
    } else {
        partialEl.style.display = 'none';
        labelEl.textContent = 'Pierwsza waga z wagi [kg]';
    }

    document.getElementById('weightInput').value = '';
    document.getElementById('weightForm').style.display = '';
    setTimeout(() => {
        document.getElementById('weightInput').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 100);
}

async function saveWeight() {
    if (!_selectedOrderId) {
        Swal.fire({ icon: 'warning', title: 'Wybierz zlecenie', timer: 1500, showConfirmButton: false });
        return;
    }
    const weight = document.getElementById('weightInput').value.trim();
    if (!weight || parseFloat(weight) <= 0) {
        Swal.fire({ icon: 'warning', title: 'Podaj wagę', timer: 1500, showConfirmButton: false });
        return;
    }

    // Wartość w polu jest w kg, backend oczekuje wartości w tonach (jak orders.weight_brutto/netto)
    const weightTons = parseFloat(weight) / 1000;

    const btn = document.getElementById('btnSave');
    btn.disabled = true;

    const res = await fetch(`/plac/weighing/${_selectedOrderId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ weight: weightTons }),
    });
    const data = await res.json();
    btn.disabled = false;

    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1300, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}
</script>
@endsection
