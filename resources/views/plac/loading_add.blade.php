@extends('layouts.plac')

@section('title', 'Dodaj towar')

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

.order-bar {
    background: #f9d38c; border-radius: 12px;
    padding: 12px 16px; margin-bottom: 14px;
}
.order-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 26px; font-weight: 900; color: #1a1a1a;
}
.order-sub { font-size: 12px; color: #888; margin-top: 2px; }

.form-card {
    background: #fff; border-radius: 12px;
    padding: 18px; margin-bottom: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,.07);
}
.f-label {
    display: block; font-size: 11px; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    color: #888; margin-bottom: 8px;
}
.f-select {
    width: 100%; padding: 14px 12px;
    border: 2px solid #e2e5e9; border-radius: 10px;
    font-family: 'Barlow', sans-serif; font-size: 15px;
    color: #1a1a1a; outline: none;
}
.f-select:focus { border-color: #f39c12; }
.inv-input {
    width: 100%; padding: 14px;
    border: 3px solid #e2e5e9; border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 42px; font-weight: 900; text-align: center;
    color: #1a1a1a; outline: none;
    -moz-appearance: textfield;
}
.inv-input::-webkit-outer-spin-button,
.inv-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.inv-input:focus { border-color: #f39c12; }

/* Stan magazynu po wyborze frakcji */
.stock-info { display: none; margin-top: 8px; }
.stock-info.show { display: flex; flex-direction: column; border-top: 2px solid #16a085; margin-top: 10px; padding-top: 10px; }
.si-group { text-align: center; }
.si-label { font-size: 10px; font-weight: 700; color: #2d7a1a; text-transform: uppercase; letter-spacing: .06em; }
.si-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 26px; font-weight: 900; color: #2d7a1a; }

/* Belki i waga */
.big-input {
    width: 100%; padding: 16px;
    border: 3px solid #e2e5e9; border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 42px; font-weight: 900; text-align: center;
    color: #1a1a1a; outline: none;
    -moz-appearance: textfield;
}
.big-input::-webkit-outer-spin-button,
.big-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.big-input:focus { border-color: #3498db; }
.i-unit { text-align: center; font-size: 11px; color: #aaa; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; margin-top: 6px; }
.i-avg  { text-align: center; font-size: 13px; color: #888; margin-top: 8px; }

.btn-save {
    width: 100%; padding: 18px; background: #f39c12; color: #1a1a1a;
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
        onclick="window.location.href='{{ route('plac.orders.loading', $order) }}'"
        style="display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer">
    <i class="fas fa-home"></i> Powrót
</button>

<div style="background:#f39c12;border-radius:14px;padding:16px 18px;margin-bottom:14px">
    <div style="font-family:'Barlow Condensed',sans-serif;font-size:32px;font-weight:900;color:#1a1a1a;line-height:1">{{ $order->client?->short_name }}</div>
</div>

{{-- Waga kierowcy --}}
@if($order->weight_netto)
<div style="background:#fff;border-radius:12px;padding:12px 16px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,.07);display:flex;align-items:center;gap:8px">
    <span style="font-size:12px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.06em">Waga kierowcy:</span>
    <span style="font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:#1a1a1a">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</span>
</div>
@endif

{{-- Istniejące towary --}}
@if($order->loadingItems->isNotEmpty())
<div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.07);margin-bottom:14px">
    <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
            <tr style="background:#fdebd0">
                <th style="padding:8px 10px;font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#935810;text-align:left">Towar</th>
                <th style="padding:8px 10px;font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#935810;text-align:right">Bel.</th>
                <th style="padding:8px 10px;font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#935810;text-align:right">Waga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->loadingItems as $li)
            <tr style="border-bottom:1px solid #f0f2f5">
                <td style="padding:8px 10px;color:#555">{{ $li->fraction?->name }}</td>
                <td style="padding:8px 10px;text-align:right;color:#555;font-size:14px">{{ $li->bales }}</td>
                <td style="padding:8px 10px;text-align:right;color:#555;font-size:14px">{{ number_format($li->weight_kg, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
            <tr style="background:#f0f2f5">
                <td style="padding:8px 10px;font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.06em">RAZEM</td>
                <td style="padding:8px 10px;text-align:right;font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:700;color:#555">{{ $order->loadingItems->sum('bales') }}</td>
                <td style="padding:8px 10px;text-align:right;font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:700;color:#555">{{ number_format($order->loadingItems->sum('weight_kg'), 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

{{-- Wybór towaru --}}
<div class="form-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
        <label class="f-label" style="margin-bottom:0">Towar</label>
        <button type="button" onclick="selectFraction(46)"
                style="background:#2980b9;border:none;border-radius:8px;padding:5px 12px;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:900;letter-spacing:.04em;text-transform:uppercase;cursor:pointer">KARTON CZYSTY</button>
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

    {{-- Stan magazynu --}}
    <div class="stock-info" id="stockInfo">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:10px;padding-top:8px;border-top:1.5px solid #16a085;gap:10px">
            <div style="display:flex;gap:16px;align-items:center">
                <div style="display:flex;align-items:center;gap:5px">
                    <i class="fas fa-layer-group" style="color:#16a085;font-size:13px"></i>
                    <span style="font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:#1a1a1a" id="stockBales">–</span>
                </div>
                <div style="display:flex;align-items:center;gap:5px">
                    <i class="fas fa-weight-hanging" style="color:#16a085;font-size:13px"></i>
                    <span style="font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:#1a1a1a" id="stockWeight">–</span>
                </div>
            </div>
            <button type="button" onclick="zaladujWszystko()"
                    style="background:#16a085;border:none;border-radius:8px;padding:7px 12px;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:900;letter-spacing:.04em;text-transform:uppercase;cursor:pointer;white-space:nowrap;flex-shrink:0">
                <i class="fas fa-arrow-down"></i> ZAŁADUJ WSZYSTKO
            </button>
        </div>
    </div>
</div>

{{-- Belki --}}
<div class="form-card">
    <label class="f-label">Ilość belek</label>
    <input type="number" id="balesInput" class="inv-input"
           min="0" step="1" inputmode="numeric" placeholder="0"
           value="{{ $editItem ? $editItem->bales : '' }}"
           oninput="calcAvg()">
    <div class="i-unit">szt.</div>
</div>

{{-- Waga --}}
<div class="form-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
        <div style="display:flex;align-items:baseline;gap:8px">
            <label class="f-label" style="margin-bottom:0">Waga</label>
            <span style="font-size:11px;color:#aaa;font-weight:600">podaj w kg</span>
        </div>
        <button type="button" onclick="calcFromAvg()"
                style="background:#f39c12;border:none;border-radius:8px;padding:8px 16px;color:#1a1a1a;font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;cursor:pointer">
            <i class="fas fa-calculator"></i> PRZELICZ
        </button>
    </div>
    <input type="number" id="weightInput" class="inv-input"
           min="0" step="1" inputmode="numeric" placeholder="0"
           value="{{ $editItem ? round($editItem->weight_kg) : '' }}"
           oninput="calcAvg()">
    <div class="i-unit">kg</div>
</div>

<button class="btn-save" onclick="save()">
    <i class="fas fa-check-circle"></i> {{ $editItem ? 'ZAPISZ ZMIANY' : 'DODAJ' }}
</button>

@endsection

@section('scripts')
<script>
const ORDER_ID  = {{ $order->id }};
const EDIT_ID   = {{ $editItem ? $editItem->id : 'null' }};
const CSRF      = document.querySelector('meta[name="csrf-token"]').content;

@if($editItem)
// Ustaw frakcję przy edycji
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
    const avg    = parseInt(opt.dataset.avg)    || 0;

    document.getElementById('stockBales').textContent  = bales;
    document.getElementById('stockWeight').textContent = weight.toLocaleString('pl-PL');
    info.classList.add('show');
    calcAvg();
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

    if (!fracId) { Swal.fire({ icon: 'warning', title: 'Wybierz towar', timer: 1500, showConfirmButton: false }); return; }
    if (bales === '' || bales < 0) { Swal.fire({ icon: 'warning', title: 'Podaj ilość belek', timer: 1500, showConfirmButton: false }); return; }
    if (isNaN(weight) || weight < 0) { Swal.fire({ icon: 'warning', title: 'Podaj wagę', timer: 1500, showConfirmButton: false }); return; }

    // Jeśli edycja – najpierw usuń stary wpis
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