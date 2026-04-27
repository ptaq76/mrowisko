@extends('layouts.kierowca')

@section('title', 'Dodaj towar')

@section('styles')
<style>
.back-btn {
    display: flex; align-items: center; gap: 8px;
    color: #888; font-size: 14px; font-weight: 600;
    text-decoration: none; margin-bottom: 14px;
}

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
.f-select:focus { border-color: #3498db; }

/* Stan magazynu po wyborze frakcji */
.stock-info {
    display: none;
    background: #e8f7e4; border-radius: 8px;
    padding: 10px 14px; margin-top: 10px;
    display: none;
}
.stock-info.show { display: flex; justify-content: space-between; align-items: center; }
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
    width: 100%; padding: 18px; background: #3498db; color: #fff;
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

<a href="{{ route('plac.orders.loading', $order) }}" class="back-btn">
    <i class="fas fa-arrow-left"></i> Powrót
</a>

<div class="order-bar">
    <div class="order-client">{{ $order->client?->short_name }}</div>
    <div class="order-sub">{{ $order->type === 'pickup' ? '↓ Odbiór' : '↑ Załadunek' }}
        @if($order->planned_time) · {{ substr($order->planned_time, 0, 5) }} @endif
    </div>
</div>

{{-- Wybór towaru --}}
<div class="form-card">
    <label class="f-label">Towar</label>
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
        <div class="si-group">
            <div class="si-label">Stan mag.</div>
            <div class="si-val" id="stockBales">–</div>
            <div style="font-size:11px;color:#2d7a1a">belek</div>
        </div>
        <div class="si-group">
            <div class="si-label">Śr. waga belki</div>
            <div class="si-val" id="stockAvg">–</div>
            <div style="font-size:11px;color:#2d7a1a">kg/bel.</div>
        </div>
        <div class="si-group">
            <div class="si-label">Razem kg</div>
            <div class="si-val" id="stockWeight">–</div>
            <div style="font-size:11px;color:#2d7a1a">kg</div>
        </div>
    </div>
</div>

{{-- Belki --}}
<div class="form-card">
    <label class="f-label">Ilość belek</label>
    <input type="text" id="balesInput" class="big-input js-numkey"
           value="{{ $editItem ? $editItem->bales : '' }}"
           oninput="calcAvg()"
           data-keypad-label="Ilość belek [szt.]"
           data-decimal="false"
           data-min="0" data-max="500">
    <div class="i-unit">szt.</div>
</div>

{{-- Waga --}}
<div class="form-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
        <label class="f-label" style="margin-bottom:0">Waga</label>
        <button type="button" onclick="calcFromAvg()"
                style="background:#f39c12;border:none;border-radius:6px;padding:6px 14px;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;cursor:pointer">
            <i class="fas fa-calculator"></i> PRZELICZ
        </button>
    </div>
    <input type="text" id="weightInput" class="big-input js-numkey"
           value="{{ $editItem ? round($editItem->weight_kg) : '' }}"
           oninput="calcAvg()"
           data-keypad-label="Waga [kg]"
           data-decimal="false"
           data-min="0" data-max="50000">
    <div class="i-unit">kg</div>
    <div class="i-avg" id="avgDisplay"></div>
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

function onFracChange() {
    const sel  = document.getElementById('fracSel');
    const opt  = sel.options[sel.selectedIndex];
    const info = document.getElementById('stockInfo');

    if (!sel.value) { info.classList.remove('show'); return; }

    const bales  = parseInt(opt.dataset.bales)  || 0;
    const weight = parseInt(opt.dataset.weight) || 0;
    const avg    = parseInt(opt.dataset.avg)    || 0;

    document.getElementById('stockBales').textContent  = bales;
    document.getElementById('stockAvg').textContent    = avg;
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
    calcAvg();
}

function calcAvg() {
    const b = parseInt(document.getElementById('balesInput').value);
    const w = parseInt(document.getElementById('weightInput').value);
    const el = document.getElementById('avgDisplay');
    el.textContent = (b > 0 && w > 0) ? `Średnia: ${Math.round(w/b)} kg/bel.` : '';
}

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
