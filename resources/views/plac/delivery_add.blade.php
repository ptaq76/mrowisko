@extends('layouts.plac')

@section('title', 'Dodaj towar')

@section('styles')
<style>
:root {
    --green: #27ae60;
    --green-dark: #1e8449;
    --green-light: #e8f7e4;
    --green-border: #d4edda;
}

.add-header {
    background: var(--green);
    border-radius: var(--radius-card);
    padding: 16px 18px;
    margin-bottom: 12px;
}
.ah-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 36px; font-weight: 900; color: #fff; line-height: 1;
    text-transform: uppercase;
}
.lh-weight {
    margin-top: 10px;
    background: rgba(0,0,0,.15);
    border-radius: 8px; padding: 8px 14px;
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.lhw-block { display: flex; flex-direction: column; align-items: flex-start; gap: 1px; }
.lhw-block.right { align-items: flex-end; }
.lhw-label { font-size: 10px; font-weight: 700; color: rgba(255,255,255,.65); text-transform: uppercase; letter-spacing: .06em; }
.lhw-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 20px; font-weight: 900; color: #fff; }
.lhw-val.negative { color: #ff8a80; }

.stock-strip {
    display: none; margin-top: 10px; padding-top: 10px;
    border-top: 1.5px solid var(--green-dark);
}
.stock-strip.show { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.stock-vals { display: flex; gap: 10px; align-items: center; }
.sv-item { display: flex; align-items: center; gap: 4px; }
.sv-ico    { color: #111; font-size: 13px; }
.sv-bales  { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900; color: #111; }
.sv-weight { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900; color: #aaa; }
.btn-load-all {
    background: var(--green); border: none; border-radius: 8px;
    height: 38px; padding: 0 12px;
    display: flex; align-items: center; gap: 6px;
    cursor: pointer; flex-shrink: 0;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 14px; font-weight: 900; color: #fff;
    text-transform: uppercase; letter-spacing: .04em;
}
.btn-load-all:active { filter: brightness(.9); }

.existing-card {
    background: var(--bg-card);
    border-radius: var(--radius-card);
    border: 1px solid var(--border);
    overflow: hidden;
    margin-bottom: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
}
.existing-table { width: 100%; border-collapse: collapse; }
.existing-table thead tr { background: var(--green-light); }
.existing-table th {
    padding: 8px 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: #1a7a3c; text-align: left;
}
.existing-table th.r { text-align: right; }
.existing-table td { padding: 9px 10px; border-bottom: 1px solid #f0f2f5; font-size: 16px; font-weight: 700; color: #111; }
.existing-table tr:last-child td { border-bottom: none; }
.existing-table .total-row td { background: #f8f9fa; font-family: 'Barlow Condensed', sans-serif; font-size: 16px; font-weight: 700; color: #555; }
.existing-table .total-row td:first-child { font-size: 11px; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: .06em; }

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
.f-select {
    width: 100%; padding: 14px 12px;
    border: 1.5px solid var(--border); border-radius: 10px;
    font-family: 'Barlow', sans-serif; font-size: 16px;
    color: #111; outline: none; background: #fff;
}
.f-select:focus { border-color: var(--green); }
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
.big-input:focus { border-color: var(--green); }
.i-unit {
    text-align: center; font-size: 11px; color: #aaa;
    font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; margin-top: 7px;
}
.btn-save-green {
    width: 100%; padding: 20px;
    background: var(--green); color: #fff;
    border: none; border-radius: var(--radius-btn);
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase;
    cursor: pointer; margin-bottom: 10px;
    display: flex; align-items: center; justify-content: center; gap: 10px;
}
.btn-save-green:active { filter: brightness(.9); }

/* ── Pasek opakowań w headerze ── */
.lh-pkg {
    margin-top: 8px;
    background: rgba(0,0,0,.12);
    border-radius: 8px; padding: 7px 12px;
    display: flex; align-items: center; gap: 8px;
    cursor: pointer;
}
.lh-pkg:active { background: rgba(0,0,0,.22); }
.lhp-icon { color: rgba(255,255,255,.7); font-size: 13px; }
.lhp-label { font-size: 11px; font-weight: 700; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .06em; flex: 1; }
.lhp-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 16px; font-weight: 900; color: #fff; }
.lhp-kg    { font-size: 12px; font-weight: 700; color: rgba(255,255,255,.55); }
.lhp-add   { font-size: 12px; font-weight: 700; color: rgba(255,255,255,.5); font-style: italic; }

/* ── SweetAlert formularz opakowań ── */
.sw-pkg-form { text-align: left; }
.sw-field { margin-bottom: 14px; }
.sw-label {
    display: block; font-size: 11px; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: #888; margin-bottom: 6px;
}
.sw-select {
    width: 100%; padding: 12px 10px;
    border: 1.5px solid #e2e5e9; border-radius: 8px;
    font-size: 16px; color: #111; outline: none; background: #fff;
}
.sw-select:focus { border-color: #3498db; }
.sw-num {
    width: 100%; padding: 14px;
    border: 2px solid #e2e5e9; border-radius: 8px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 40px; font-weight: 900; text-align: center; color: #111;
    outline: none; -moz-appearance: textfield;
}
.sw-num::-webkit-outer-spin-button,
.sw-num::-webkit-inner-spin-button { -webkit-appearance: none; }
.sw-num:focus { border-color: #3498db; }
.sw-num:disabled { background: #f8f9fa; color: #aaa; border-color: #e2e5e9; }
.sw-pkg-input {
    width: 72px; padding: 8px 6px;
    border: 2px solid #e2e5e9; border-radius: 8px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 28px; font-weight: 900; text-align: center; color: #111;
    outline: none; -moz-appearance: textfield; flex-shrink: 0;
}
.sw-pkg-input::-webkit-outer-spin-button,
.sw-pkg-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.sw-pkg-input:focus { border-color: #3498db; }
.sw-unit { text-align: center; font-size: 11px; color: #aaa; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; margin-top: 4px; }
.sw-waga-info {
    text-align: center; font-size: 13px; color: #27ae60;
    font-weight: 700; margin-top: 6px; min-height: 20px;
}
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.delivery.form', $order) }}'"
        class="btn-back">
    <i class="fas fa-arrow-left"></i> Powrót
</button>

{{-- Nagłówek klienta + opakowania --}}
@php
    $pkgItems     = $order->packaging;
    $hasPkg       = $pkgItems->isNotEmpty();
    $allConfirmed = $hasPkg && $pkgItems->every(fn($p) => $p->confirmed_at !== null);
    $totalPkgSzt  = $allConfirmed ? $pkgItems->sum(fn($p) => $p->qty_plac ?? $p->quantity ?? 0) : 0;
    $totalPkgKg   = $allConfirmed ? $pkgItems->sum(fn($p) => ($p->qty_plac ?? $p->quantity ?? 0) * (float)($p->opakowanie?->waga ?? 0)) : 0;
    $totalItemsT  = $order->loadingItems->sum('weight_kg') / 1000;
    // Waga kierowcy pomniejszona o opakowania
    $weightNetto       = $order->weight_netto ?? 0;
    $weightNettoPkg    = $allConfirmed && $totalPkgKg > 0 ? round($weightNetto - $totalPkgKg / 1000, 3) : null;
    $weightDisplay     = $weightNettoPkg ?? $weightNetto;
    $diff              = $weightDisplay - $totalItemsT;
@endphp

<div class="add-header">
    <div class="ah-client">{{ $order->client?->short_name }}</div>

    @if($order->weight_netto)
    <div class="lh-weight">
        {{-- Waga kierowcy (główna) --}}
        <div class="lhw-block">
            <span class="lhw-label">Waga kierowcy</span>
            <span class="lhw-val">{{ number_format($weightDisplay, 3, ',', ' ') }} t</span>
            @if($weightNettoPkg !== null)
            <span style="font-size:10px;color:rgba(255,255,255,.45);margin-top:1px">
                brutto {{ number_format($weightNetto, 3, ',', ' ') }} t
            </span>
            @endif
        </div>
        {{-- Pozostało --}}
        <div class="lhw-block" id="pozostaloBlock" data-kg="{{ (int) round($diff * 1000) }}" style="align-items:flex-end">
            <span class="lhw-label">Pozostało</span>
            <span class="lhw-val {{ $diff < 0 ? 'negative' : '' }}">{{ number_format($diff, 3, ',', ' ') }} t</span>
        </div>
    </div>
    @endif

    {{-- Pasek opakowań wewnątrz headera --}}
    <div class="lh-pkg" onclick="openPackagingForm()">
        @if($allConfirmed)
            <span class="lhp-icon"><i class="fas fa-check-circle"></i></span>
            <span class="lhp-label">Palety / BigBoxy</span>
            <span class="lhp-val">{{ $totalPkgSzt }} szt.</span>
            @if($totalPkgKg > 0)
            <span class="lhp-kg">{{ number_format($totalPkgKg, 0, ',', ' ') }} kg</span>
            @endif
        @else
            <span class="lhp-icon"><i class="fas fa-box"></i></span>
            <span class="lhp-label">Palety / BigBoxy</span>
            <span class="lhp-add">Podaj ilość →</span>
        @endif
    </div>
</div>

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
    <label class="f-label">Towar</label>
    <select id="fracSel" class="f-select" onchange="onFracChange()">
        <option value="">– wybierz –</option>

        @php
            $topFractions = collect($topFractionIds ?? [])
                ->map(fn($id) => $fractions->firstWhere('id', $id))
                ->filter();
        @endphp

        @if($topFractions->isNotEmpty())
        <optgroup label="Często używane">
            @foreach($topFractions as $f)
                <option value="{{ $f->id }}"
                        data-avg="{{ $stockData[$f->id]['avg'] ?? 0 }}"
                        data-bales="{{ $stockData[$f->id]['bales'] ?? 0 }}"
                        data-weight="{{ $stockData[$f->id]['weight'] ?? 0 }}"
                        data-bale="{{ stripos($f->name, 'BELKA') !== false ? '1' : '0' }}">
                    {{ $f->name }}
                </option>
            @endforeach
        </optgroup>
        <optgroup label="Wszystkie">
        @endif

        @foreach($fractions as $f)
            <option value="{{ $f->id }}"
                    data-avg="{{ $stockData[$f->id]['avg'] ?? 0 }}"
                    data-bales="{{ $stockData[$f->id]['bales'] ?? 0 }}"
                    data-weight="{{ $stockData[$f->id]['weight'] ?? 0 }}"
                    data-bale="{{ stripos($f->name, 'BELKA') !== false ? '1' : '0' }}">
                {{ $f->name }}
            </option>
        @endforeach

        @if($topFractions->isNotEmpty())
        </optgroup>
        @endif
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
        <button type="button" onclick="przeniesPozostalo()" class="btn-load-all">
            <i class="fas fa-arrow-right"></i> Przenieś
        </button>
    </div>
</div>

{{-- Belki --}}
<div class="form-card" id="balesCard" style="display:none">
    <label class="f-label">Ilość belek</label>
    <input type="text" id="balesInput" class="big-input js-numkey"
           placeholder="0"
           value="{{ $editItem ? $editItem->bales : '' }}"
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
    </div>
    <input type="text" id="weightInput" class="big-input js-numkey"
           placeholder="0"
           value="{{ $editItem ? round($editItem->weight_kg) : '' }}"
           data-keypad-label="Waga [kg]"
           data-decimal="false"
           data-min="0" data-max="50000">
    <div class="i-unit">kg</div>
</div>

<button class="btn-save-green" onclick="save()">
    <i class="fas fa-check-circle"></i>
    {{ $editItem ? 'ZAPISZ ZMIANY' : 'DODAJ' }}
</button>

@endsection

@php
$pkgDataForJs = $allOpakowania->map(function($o) use ($pkgItems) {
    $fromDriver = $pkgItems->firstWhere('opakowanie_id', $o->id);
    return [
        'id'       => $o->id,
        'name'     => $o->name,
        'waga'     => (float)$o->waga,
        'driver'   => $fromDriver?->quantity,
        'qty_plac' => $fromDriver?->qty_plac,
    ];
})->values();
@endphp

@section('scripts')
<script>
const ORDER_ID = {{ $order->id }};
const EDIT_ID  = {{ $editItem ? $editItem->id : 'null' }};
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const PKG_DATA = @json($pkgDataForJs);

@if($editItem)
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('fracSel').value = '{{ $editItem->fraction_id }}';
    onFracChange();
});
@endif

function isBale() {
    const opt = document.getElementById('fracSel').options[document.getElementById('fracSel').selectedIndex];
    return opt && opt.dataset.bale === '1';
}

function onFracChange() {
    const sel  = document.getElementById('fracSel');
    const opt  = sel.options[sel.selectedIndex];
    const info = document.getElementById('stockInfo');
    const card = document.getElementById('balesCard');
    if (!sel.value) { info.classList.remove('show'); card.style.display = 'none'; return; }
    document.getElementById('stockBales').textContent  = parseInt(opt.dataset.bales) || 0;
    document.getElementById('stockWeight').textContent = (parseInt(opt.dataset.weight) || 0).toLocaleString('pl-PL');
    info.classList.add('show');
    card.style.display = isBale() ? '' : 'none';
    if (!isBale()) document.getElementById('balesInput').value = 0;
}

function przeniesPozostalo() {
    const block = document.getElementById('pozostaloBlock');
    if (!block) {
        Swal.fire({ icon: 'warning', title: 'Brak wagi kierowcy', text: 'Nie można obliczyć pozostałej masy.', timer: 1800, showConfirmButton: false });
        return;
    }
    const kg = parseInt(block.dataset.kg) || 0;
    if (kg <= 0) {
        Swal.fire({ icon: 'info', title: 'Brak pozostałej masy', text: 'Cała masa już rozdzielona.', timer: 1800, showConfirmButton: false });
        return;
    }
    document.getElementById('weightInput').value = kg;
}

/* ── Formularz opakowań w SweetAlert – lista wierszy ── */
async function openPackagingForm() {
    if (!PKG_DATA.length) {
        Swal.fire({ icon: 'info', title: 'Brak opakowań', text: 'Nie zdefiniowano żadnych opakowań zwrotnych.' });
        return;
    }

    // Buduj wiersze: nazwa + waga jednostkowa + input ilości
    const rows = PKG_DATA.map(p => {
        const defaultQty = p.qty_plac ?? p.driver ?? 0;
        const driverInfo = (p.driver !== null && p.driver !== undefined)
            ? `<span style="font-size:10px;color:#d68910;font-weight:700">kier: ${p.driver}</span>`
            : '';
        return `
        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:10px 0;border-bottom:1px solid #f0f0f0">
            <div style="text-align:left">
                <div style="font-family:'Barlow Condensed',sans-serif;font-size:18px;
                            font-weight:800;color:#1a1a1a">${p.name}</div>
                <div style="font-size:11px;color:#aaa;display:flex;gap:6px;align-items:center">
                    ${p.waga > 0 ? Math.round(p.waga) + ' kg/szt.' : ''}
                    ${driverInfo}
                </div>
            </div>
            <input type="text" id="spkg_${p.id}"
                   data-id="${p.id}" data-waga="${p.waga}"
                   class="sw-pkg-input js-numkey"
                   value="${defaultQty}"
                   data-keypad-label="${p.name} [szt.]"
                   data-decimal="false"
                   data-min="0" data-max="9999"
                   oninput="swUpdateTotal()">
        </div>`;
    }).join('');

    const html = `
        <div style="text-align:left">
            ${rows}
            <div style="display:flex;justify-content:space-between;align-items:center;
                        padding:10px 0;margin-top:2px">
                <span style="font-size:11px;font-weight:700;color:#aaa;
                             text-transform:uppercase;letter-spacing:.06em">Łączna waga</span>
                <span id="swTotalKg" style="font-family:'Barlow Condensed',sans-serif;
                                            font-size:20px;font-weight:900;color:#3498db">0 kg</span>
            </div>
        </div>`;

    const result = await Swal.fire({
        title: 'Palety / BigBoxy',
        html,
        showCancelButton: true,
        confirmButtonText: 'Zapisz',
        cancelButtonText: 'Anuluj',
        confirmButtonColor: '#3498db',
        cancelButtonColor: '#aaa',
        reverseButtons: true,
        didOpen: () => setTimeout(() => swUpdateTotal(), 50),
        preConfirm: () => {
            return PKG_DATA.map(p => ({
                opakowanie_id: p.id,
                qty_plac: parseInt(document.getElementById(`spkg_${p.id}`)?.value) || 0,
            }));
        },
    });

    if (!result.isConfirmed) return;

    const payload = result.value; // wysyłamy wszystkie, kontroler pominie qty=0

    const res = await fetch(`/plac/delivery/${ORDER_ID}/packaging`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ packaging: payload }),
    });
    const data = await res.json();

    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1200, showConfirmButton: false });
        location.reload();
    } else {
        Swal.fire({ icon: 'error', title: 'Błąd', text: data.message ?? 'Spróbuj ponownie.' });
    }
}

/* Przelicz łączną wagę w SweetAlert */
function swUpdateTotal() {
    let total = 0;
    PKG_DATA.forEach(p => {
        const qty = parseInt(document.getElementById(`spkg_${p.id}`)?.value) || 0;
        total += qty * p.waga;
    });
    const el = document.getElementById('swTotalKg');
    if (el) el.textContent = Math.round(total) + ' kg';
}

function _UNUSED_swCalcWaga() {
    // zastąpiona przez swUpdateTotal
    const infoEl = document.getElementById('swWagaInfo');
    if (infoEl) infoEl.textContent = '';
    /*
        infoEl.textContent = `Kierowca podał: ${pkg.driver} szt.`;
    */
}

/* ── Dodaj towar ── */
async function save() {
    const fracId = document.getElementById('fracSel').value;
    const bales  = isBale() ? parseInt(document.getElementById('balesInput').value) : 0;
    const weight = parseInt(document.getElementById('weightInput').value);

    if (!fracId) {
        Swal.fire({ icon: 'warning', title: 'Wybierz towar', timer: 1500, showConfirmButton: false }); return;
    }
    if (isBale() && (isNaN(bales) || bales < 1)) {
        Swal.fire({ icon: 'warning', title: 'Podaj ilość belek', timer: 1500, showConfirmButton: false }); return;
    }
    if (isNaN(weight) || weight < 0) {
        Swal.fire({ icon: 'warning', title: 'Podaj wagę', timer: 1500, showConfirmButton: false }); return;
    }

    if (EDIT_ID) {
        await fetch(`/plac/delivery/${ORDER_ID}/items/${EDIT_ID}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
    }

    const res  = await fetch(`/plac/delivery/${ORDER_ID}/items`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ fraction_id: fracId, bales, weight_kg: weight }),
    });
    const data = await res.json();

    if (data.success) {
        await Swal.fire({ icon: 'success', title: EDIT_ID ? 'Zaktualizowano!' : 'Dodano!', timer: 1200, showConfirmButton: false });
        window.location.href = '{{ route('plac.delivery.form', $order) }}';
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}
</script>
@endsection