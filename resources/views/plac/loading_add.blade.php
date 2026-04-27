@extends('layouts.plac')

@section('title', 'Dodaj towar')

@section('styles')
<style>
/* ── NAGŁÓWEK ── */
.add-header {
    background: var(--yellow);
    border-radius: var(--radius-card);
    padding: 16px 18px;
    margin-bottom: 12px;
}
.ah-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 36px; font-weight: 900; color: #111; line-height: 1;
    text-transform: uppercase;
}

/* Waga kierowcy */
.driver-weight {
    background: var(--bg-card);
    border-radius: var(--radius-card);
    border: 1px solid var(--border);
    padding: 11px 16px;
    margin-bottom: 12px;
    display: flex; align-items: center; gap: 10px;
}
.dw-label { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; }
.dw-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 24px; font-weight: 900; color: #111; }

/* Istniejące towary */
.existing-card {
    background: var(--bg-card);
    border-radius: var(--radius-card);
    border: 1px solid var(--border);
    overflow: hidden;
    margin-bottom: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
}
.existing-table { width: 100%; border-collapse: collapse; }
.existing-table thead tr { background: #fdebd0; }
.existing-table th {
    padding: 8px 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: #935810; text-align: left;
}
.existing-table th.r { text-align: right; }
.existing-table td { padding: 9px 10px; border-bottom: 1px solid #f0f2f5; font-size: 16px; font-weight: 700; color: #111; }
.existing-table tr:last-child td { border-bottom: none; }
.existing-table .total-row td { background: #f8f9fa; font-family: 'Barlow Condensed', sans-serif; font-size: 16px; font-weight: 700; color: #555; }
.existing-table .total-row td:first-child { font-size: 11px; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: .06em; }

/* ── KARTY FORMULARZA ── */
.form-card {
    background: var(--bg-card);
    border-radius: var(--radius-card);
    border: 1px solid var(--border);
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
}
.f-label {
    display: block;
    font-size: 13px; font-weight: 900; letter-spacing: .06em;
    text-transform: uppercase; color: #111;
    margin-bottom: 10px;
}
.f-label-row {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 10px;
}

/* Wybór frakcji */
.f-select {
    width: 100%; padding: 14px 12px;
    border: 1.5px solid var(--border); border-radius: 10px;
    font-family: 'Barlow', sans-serif; font-size: 16px;
    color: #111; outline: none; background: #fff;
}
.f-select:focus { border-color: var(--yellow); }

.btn-quick {
    background: #2980b9; border: none; border-radius: 8px;
    padding: 6px 13px; color: #fff;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 13px; font-weight: 900; letter-spacing: .04em;
    text-transform: uppercase; cursor: pointer;
    white-space: nowrap;
}
.btn-quick:active { background: #1f6799; }

/* Stan magazynu */
.stock-strip {
    display: none; margin-top: 10px; padding-top: 10px;
    border-top: 1.5px solid #16a085;
}
.stock-strip.show { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.stock-vals { display: flex; gap: 10px; align-items: center; }
.sv-item { display: flex; align-items: center; gap: 4px; }
.sv-ico  { color: #111; font-size: 13px; }
.sv-bales  { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900; color: #111; }
.sv-weight { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900; color: #aaa; }
.btn-load-all {
    background: var(--yellow); border: none; border-radius: 8px;
    height: 38px; padding: 0 12px;
    display: flex; align-items: center; gap: 6px;
    cursor: pointer; flex-shrink: 0;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 14px; font-weight: 900; color: #111;
    text-transform: uppercase; letter-spacing: .04em;
}
.btn-load-all i { color: #111; font-size: 14px; }
.btn-load-all:active { filter: brightness(.92); }

/* Duże inputy liczbowe */
.big-input {
    width: 100%; padding: 16px;
    border: 2px solid var(--border); border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 48px; font-weight: 900; text-align: center;
    color: #111; outline: none;
    -moz-appearance: textfield;
}
.big-input::-webkit-outer-spin-button,
.big-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.big-input:focus { border-color: var(--yellow); }
.i-unit {
    text-align: center; font-size: 11px; color: #aaa;
    font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; margin-top: 7px;
}

/* Przycisk przelicz */
.btn-calc {
    background: var(--yellow); border: none; border-radius: 8px;
    padding: 9px 16px; color: #111;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 15px; font-weight: 900; letter-spacing: .06em;
    text-transform: uppercase; cursor: pointer;
    display: flex; align-items: center; gap: 6px;
}
.btn-calc:active { filter: brightness(.92); }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.orders.loading', $order) }}'"
        class="btn-back">
    <i class="fas fa-arrow-left"></i> Powrót
</button>

{{-- Nagłówek klienta --}}
<div class="add-header">
    <div class="ah-client">{{ $order->client?->short_name }}</div>
</div>

{{-- Waga kierowcy --}}
@if($order->weight_netto)
<div class="driver-weight">
    <span class="dw-label">Waga kierowcy</span>
    <span class="dw-val">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</span>
</div>
@endif

{{-- Istniejące towary --}}
@if($order->loadingItems->isNotEmpty())
<div class="existing-card">
    <table class="existing-table">
        <thead>
            <tr>
                <th>Towar</th>
                <th class="r">Bel.</th>
                <th class="r">Waga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->loadingItems as $li)
            <tr>
                <td>{{ $li->fraction?->name }}</td>
                <td style="text-align:right">{{ $li->bales }}</td>
                <td style="text-align:right">{{ number_format($li->weight_kg, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
            @if($order->loadingItems->count() > 1)
            <tr class="total-row">
                <td>RAZEM</td>
                <td style="text-align:right">{{ $order->loadingItems->sum('bales') }}</td>
                <td style="text-align:right">{{ number_format($order->loadingItems->sum('weight_kg'), 0, ',', ' ') }}</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endif

{{-- Wybór towaru --}}
<div class="form-card">
    <div class="f-label-row">
        <span class="f-label" style="margin:0">Towar</span>
        <button type="button" onclick="selectFraction(46)" class="btn-quick">KARTON CZYSTY</button>
    </div>
    <select id="fracSel" class="f-select" onchange="onFracChange()">
        <option value="">– wybierz –</option>
        @foreach($fractions as $f)
            <option value="{{ $f->id }}"
                    data-bales="{{ $stockData[$f->id]['bales'] ?? 0 }}"
                    data-weight="{{ $stockData[$f->id]['weight'] ?? 0 }}"
                    data-avg="{{ $stockData[$f->id]['avg'] ?? 0 }}">
                {{ $f->name }}
            </option>
        @endforeach
    </select>

    <div class="stock-strip" id="stockInfo">
        <div class="stock-vals">
            <div class="sv-item">
                <i class="fas fa-boxes sv-ico"></i>
                <span class="sv-bales" id="stockBales">–</span>
            </div>
            <div class="sv-item">
                <i class="fas fa-balance-scale sv-ico"></i>
                <span class="sv-weight" id="stockWeight">–</span>
            </div>
        </div>
        <button type="button" onclick="zaladujWszystko()" class="btn-load-all" title="Załaduj wszystko">
            <i class="fas fa-download"></i> Wszystko
        </button>
    </div>
</div>

{{-- Belki --}}
<div class="form-card">
    <label class="f-label">Ilość belek</label>
    <input type="text" id="balesInput" class="big-input js-numkey"
           placeholder="0"
           value="{{ $editItem ? $editItem->bales : '' }}"
           oninput="calcAvg()"
           data-keypad-label="Ilość belek [szt.]"
           data-decimal="false"
           data-min="0" data-max="500">
    <div class="i-unit">szt.</div>
</div>

{{-- Waga --}}
<div class="form-card">
    <div class="f-label-row">
        <div style="display:flex;align-items:baseline;gap:8px">
            <span class="f-label" style="margin:0">Waga</span>
            <span style="font-size:11px;color:#bbb;font-weight:600">podaj w kg</span>
        </div>
        <button type="button" onclick="calcFromAvg()" class="btn-calc">
            <i class="fas fa-calculator"></i> PRZELICZ
        </button>
    </div>
    <input type="text" id="weightInput" class="big-input js-numkey"
           placeholder="0"
           value="{{ $editItem ? round($editItem->weight_kg) : '' }}"
           oninput="calcAvg()"
           data-keypad-label="Waga [kg]"
           data-decimal="false"
           data-min="0" data-max="50000">
    <div class="i-unit">kg</div>
</div>

<button class="btn-yellow" onclick="save()">
    <i class="fas fa-check-circle"></i>
    {{ $editItem ? 'ZAPISZ ZMIANY' : 'DODAJ' }}
</button>

@endsection

@section('scripts')
<script>
const ORDER_ID = {{ $order->id }};
const EDIT_ID  = {{ $editItem ? $editItem->id : 'null' }};
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;

@if($editItem)
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('fracSel').value = '{{ $editItem->fraction_id }}';
    onFracChange();
});
@endif

function selectFraction(id) {
    const sel = document.getElementById('fracSel');
    sel.value = id;
    onFracChange();
}

function zaladujWszystko() {
    const balesEl  = document.getElementById('stockBales');
    const weightEl = document.getElementById('stockWeight');
    const bales  = parseInt((balesEl.textContent  || '').replace(/\s/g, '')) || 0;
    const weight = parseInt((weightEl.textContent || '').replace(/\s/g, '')) || 0;
    if (!bales && !weight) return;
    document.getElementById('balesInput').value  = bales;
    document.getElementById('weightInput').value = weight;
}

function onFracChange() {
    const sel  = document.getElementById('fracSel');
    const opt  = sel.options[sel.selectedIndex];
    const info = document.getElementById('stockInfo');

    if (!sel.value) { info.classList.remove('show'); return; }

    const bales  = parseInt(opt.dataset.bales)  || 0;
    const weight = parseInt(opt.dataset.weight) || 0;

    document.getElementById('stockBales').textContent  = bales;
    document.getElementById('stockWeight').textContent = weight.toLocaleString('pl-PL');
    info.classList.add('show');
}

function calcFromAvg() {
    const sel = document.getElementById('fracSel');
    if (!sel.value) {
        Swal.fire({ icon: 'warning', title: 'Wybierz towar', timer: 1500, showConfirmButton: false });
        return;
    }
    const opt = sel.options[sel.selectedIndex];
    const avg = parseInt(opt.dataset.avg) || 0;
    const b   = parseInt(document.getElementById('balesInput').value);
    if (!b || b < 1) {
        Swal.fire({ icon: 'warning', title: 'Podaj najpierw ilość belek', timer: 1500, showConfirmButton: false });
        return;
    }
    if (avg === 0) {
        Swal.fire({ icon: 'warning', title: 'Brak danych o średniej wadze', text: 'Magazyn nie ma historii dla tej frakcji.', timer: 2000, showConfirmButton: false });
        return;
    }
    document.getElementById('weightInput').value = Math.round(avg * b);
}

function calcAvg() { /* display only */ }

async function save() {
    const fracId = document.getElementById('fracSel').value;
    const bales  = parseInt(document.getElementById('balesInput').value);
    const weight = parseInt(document.getElementById('weightInput').value);

    if (!fracId) {
        Swal.fire({ icon: 'warning', title: 'Wybierz towar', timer: 1500, showConfirmButton: false }); return;
    }
    if (isNaN(bales) || bales < 0) {
        Swal.fire({ icon: 'warning', title: 'Podaj ilość belek', timer: 1500, showConfirmButton: false }); return;
    }
    if (isNaN(weight) || weight < 0) {
        Swal.fire({ icon: 'warning', title: 'Podaj wagę', timer: 1500, showConfirmButton: false }); return;
    }

    if (EDIT_ID) {
        await fetch(`/plac/orders/${ORDER_ID}/loading/${EDIT_ID}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
    }

    const res  = await fetch(`/plac/orders/${ORDER_ID}/loading`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ fraction_id: fracId, bales, weight_kg: weight }),
    });
    const data = await res.json();

    if (data.success) {
        await Swal.fire({ icon: 'success', title: EDIT_ID ? 'Zaktualizowano!' : 'Dodano!', timer: 1200, showConfirmButton: false });
        window.location.href = '{{ route('plac.orders.loading', $order) }}';
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}
</script>
@endsection