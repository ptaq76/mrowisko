@extends('layouts.kierowca')

@section('title', 'Waga hakowiec – ' . $order->client?->short_name)

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
    font-size: 28px;
    font-weight: 900;
    color: #fff;
    line-height: 1;
}
.weigh-sub {
    font-size: 13px;
    color: #888;
    margin-top: 4px;
}
.weigh-vehicles {
    font-size: 12px;
    color: #6EBF58;
    margin-top: 6px;
    font-weight: 700;
}

/* Sekcja pojazdu */
.vehicle-section {
    background: #fff;
    border-radius: 12px;
    margin-bottom: 16px;
    box-shadow: 0 2px 6px rgba(0,0,0,.06);
    overflow: hidden;
}

.vehicle-header {
    padding: 12px 16px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 18px;
    font-weight: 900;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
}
.vehicle-header.tractor { background: #2980b9; }
.vehicle-header.trailer { background: #8e44ad; }

.vehicle-plate {
    background: rgba(255,255,255,.2);
    padding: 2px 10px;
    border-radius: 4px;
    font-size: 14px;
    letter-spacing: .05em;
}

.vehicle-body {
    padding: 16px;
}

/* Select kontenera */
.container-label {
    font-size: 11px;
    font-weight: 700;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 8px;
    display: block;
}

.container-select {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e2e5e9;
    border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    background: #fff;
    outline: none;
    cursor: pointer;
    margin-bottom: 12px;
}
.container-select:focus { border-color: #3498db; }

/* Tara info */
.tare-display {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.tare-display .label { font-size: 11px; color: #888; font-weight: 700; text-transform: uppercase; }
.tare-display .value { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900; color: #1a1a1a; }

/* Input wagi */
.weight-input-wrap {
    margin-bottom: 8px;
}
.weight-input-wrap label {
    font-size: 11px;
    font-weight: 700;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 8px;
    display: block;
}
.weight-input {
    width: 100%;
    padding: 16px;
    border: 3px solid #e2e5e9;
    border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 36px;
    font-weight: 900;
    text-align: center;
    color: #1a1a1a;
    outline: none;
    -moz-appearance: textfield;
}
.weight-input::-webkit-outer-spin-button,
.weight-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.weight-input:focus { border-color: #3498db; }

.unit-hint {
    text-align: center;
    font-size: 12px;
    color: #aaa;
    font-weight: 600;
    margin-top: 6px;
    letter-spacing: .1em;
}

/* Wynik cząstkowy */
.partial-result {
    background: #eaf4fb;
    border: 2px solid #3498db;
    border-radius: 10px;
    padding: 12px 16px;
    margin-top: 12px;
    display: none;
}
.partial-result.show { display: block; }
.partial-result .pr-label { font-size: 11px; color: #2471a3; font-weight: 700; text-transform: uppercase; }
.partial-result .pr-value { 
    font-family: 'Barlow Condensed', sans-serif; 
    font-size: 28px; 
    font-weight: 900; 
    color: #2980b9; 
    text-align: center;
    margin-top: 4px;
}

/* Podsumowanie */
.summary-card {
    background: #e8f7e4;
    border: 3px solid #6EBF58;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    display: none;
}
.summary-card.show { display: block; }

.summary-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: #2d7a1a;
    text-transform: uppercase;
    letter-spacing: .08em;
    text-align: center;
    margin-bottom: 12px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid rgba(110,191,88,.3);
    font-size: 13px;
}
.summary-row:last-of-type { border-bottom: none; }
.summary-row .sr-label { color: #555; }
.summary-row .sr-value { font-weight: 700; color: #333; }

.summary-total {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 52px;
    font-weight: 900;
    color: #2d7a1a;
    text-align: center;
    line-height: 1;
    margin: 16px 0 6px;
}
.summary-total-unit {
    text-align: center;
    font-size: 13px;
    color: #2d7a1a;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
}

/* Przyciski */
.btn-calc {
    width: 100%;
    padding: 18px;
    background: #3498db;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 20px;
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
    font-size: 20px;
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

/* Brak kontenerów */
.no-containers {
    background: #fdecea;
    border: 2px solid #e74c3c;
    border-radius: 10px;
    padding: 16px;
    text-align: center;
}
.no-containers i { color: #e74c3c; font-size: 24px; margin-bottom: 8px; }
.no-containers .title { font-weight: 700; color: #c0392b; }
.no-containers .hint { font-size: 13px; color: #888; margin-top: 4px; }
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
    <div class="weigh-vehicles">
        <i class="fas fa-truck"></i> {{ $tractor->plate }}
        @if($trailer) + {{ $trailer->plate }} @endif
        <span style="color:#f39c12;margin-left:8px"><i class="fas fa-exclamation-triangle"></i> HAKOWIEC</span>
    </div>
</div>

@if($tractorSets->isEmpty())
    {{-- Brak zestawów dla samochodu --}}
    <div class="no-containers">
        <i class="fas fa-exclamation-triangle" style="display:block"></i>
        <div class="title">Brak zestawów dla {{ $tractor->plate }}</div>
        <div class="hint">Skontaktuj się z biurem, aby dodać kontenery.</div>
    </div>
    <button class="btn-back" onclick="history.back()" style="margin-top:16px">
        <i class="fas fa-arrow-left"></i> Powrót
    </button>
@else

{{-- SEKCJA: SAMOCHÓD --}}
<div class="vehicle-section">
    <div class="vehicle-header tractor">
        <i class="fas fa-truck"></i>
        SAMOCHÓD
        <span class="vehicle-plate">{{ $tractor->plate }}</span>
    </div>
    <div class="vehicle-body">
        <label class="container-label">Wybierz kontener</label>
        <select id="tractorSetSelect" class="container-select" onchange="onTractorSetChange()">
    <option value="">– wybierz kontener –</option>
    @foreach($tractorSets as $set)
        @php
            // Rozdzielamy label po znaku "/"
            $parts = explode('/', $set->label);
            // Jeśli jest "/" bierzemy to co po nim i usuwamy zbędne spacje. 
            // Jeśli nie ma "/", zostawiamy cały label.
            $displayName = isset($parts[1]) ? trim($parts[1]) : $set->label;
        @endphp
        <option value="{{ $set->id }}" data-tare="{{ $set->tare_kg }}" data-label="{{ $set->label }}">
            {{ $displayName }} ({{ number_format($set->tare_kg, 3, ',', ' ') }} t)
        </option>
    @endforeach
</select>

        <div class="tare-display" id="tractorTareDisplay" style="display:none">
            <span class="label">Tara zestawu</span>
            <span class="value" id="tractorTareValue">–</span>
        </div>

        <div class="weight-input-wrap">
            <label>Waga z wagi samochodowej (tony)</label>
            <input type="number" id="tractorBruttoInput" class="weight-input"
                   step="0.001" min="0" inputmode="decimal" placeholder="0,000">
            <div class="unit-hint">TONY [t]</div>
        </div>

        <div class="partial-result" id="tractorResult">
            <div class="pr-label">Netto samochodu</div>
            <div class="pr-value" id="tractorNettoValue">–</div>
        </div>
    </div>
</div>

@if($trailer && $trailerSets->isNotEmpty())
{{-- SEKCJA: NACZEPA --}}
<div class="vehicle-section">
    <div class="vehicle-header trailer">
        <i class="fas fa-trailer"></i>
        NACZEPA
        <span class="vehicle-plate">{{ $trailer->plate }}</span>
    </div>
    <div class="vehicle-body">
        <label class="container-label">Wybierz kontener</label>
        <select id="trailerSetSelect" class="container-select" onchange="onTrailerSetChange()">
    <option value="">– wybierz kontener –</option>
    @foreach($trailerSets as $set)
        @php
            $parts = explode('/', $set->label);
            $displayName = isset($parts[1]) ? trim($parts[1]) : $set->label;
        @endphp
        <option value="{{ $set->id }}" data-tare="{{ $set->tare_kg }}" data-label="{{ $set->label }}">
            {{ $displayName }} ({{ number_format($set->tare_kg, 3, ',', ' ') }} t)
        </option>
    @endforeach
</select>
        <div class="tare-display" id="trailerTareDisplay" style="display:none">
            <span class="label">Tara zestawu</span>
            <span class="value" id="trailerTareValue">–</span>
        </div>

        <div class="weight-input-wrap">
            <label>Waga z wagi samochodowej (tony)</label>
            <input type="number" id="trailerBruttoInput" class="weight-input"
                   step="0.001" min="0" inputmode="decimal" placeholder="0,000">
            <div class="unit-hint">TONY [t]</div>
        </div>

        <div class="partial-result" id="trailerResult">
            <div class="pr-label">Netto naczepy</div>
            <div class="pr-value" id="trailerNettoValue">–</div>
        </div>
    </div>
</div>
@endif

{{-- Przycisk oblicz --}}
<button class="btn-calc" onclick="calculate()">
    <i class="fas fa-calculator"></i> OBLICZ
</button>

{{-- Podsumowanie --}}
<div class="summary-card" id="summaryCard">
    <div class="summary-title">Podsumowanie ważenia</div>
    
    <div class="summary-row">
        <span class="sr-label">Samochód brutto</span>
        <span class="sr-value" id="sumTractorBrutto">–</span>
    </div>
    <div class="summary-row">
        <span class="sr-label">Samochód tara</span>
        <span class="sr-value" id="sumTractorTare">–</span>
    </div>
    <div class="summary-row">
        <span class="sr-label">Samochód netto</span>
        <span class="sr-value" id="sumTractorNetto">–</span>
    </div>

    @if($trailer && $trailerSets->isNotEmpty())
    <div class="summary-row" style="margin-top:8px;padding-top:8px;border-top:2px solid rgba(110,191,88,.3)">
        <span class="sr-label">Naczepa brutto</span>
        <span class="sr-value" id="sumTrailerBrutto">–</span>
    </div>
    <div class="summary-row">
        <span class="sr-label">Naczepa tara</span>
        <span class="sr-value" id="sumTrailerTare">–</span>
    </div>
    <div class="summary-row">
        <span class="sr-label">Naczepa netto</span>
        <span class="sr-value" id="sumTrailerNetto">–</span>
    </div>
    @endif

    <div class="summary-total" id="sumTotalNetto">–</div>
    <div class="summary-total-unit">ton netto łącznie</div>
</div>

{{-- Przycisk zapisz --}}
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
const ORDER_ID = {{ $order->id }};
const HAS_TRAILER = {{ ($trailer && $trailerSets->isNotEmpty()) ? 'true' : 'false' }};

let tractorData = { setId: null, tare: 0, brutto: 0, netto: 0, label: '' };
let trailerData = { setId: null, tare: 0, brutto: 0, netto: 0, label: '' };

function onTractorSetChange() {
    const sel = document.getElementById('tractorSetSelect');
    const opt = sel.options[sel.selectedIndex];
    const tareDisplay = document.getElementById('tractorTareDisplay');
    
    if (opt.value) {
        tractorData.setId = parseInt(opt.value);
        tractorData.tare = parseFloat(opt.dataset.tare);
        tractorData.label = opt.dataset.label;
        
        document.getElementById('tractorTareValue').textContent = tractorData.tare.toFixed(3).replace('.', ',') + ' t';
        tareDisplay.style.display = 'flex';
    } else {
        tractorData.setId = null;
        tractorData.tare = 0;
        tractorData.label = '';
        tareDisplay.style.display = 'none';
    }
}

function onTrailerSetChange() {
    const sel = document.getElementById('trailerSetSelect');
    if (!sel) return;
    
    const opt = sel.options[sel.selectedIndex];
    const tareDisplay = document.getElementById('trailerTareDisplay');
    
    if (opt.value) {
        trailerData.setId = parseInt(opt.value);
        trailerData.tare = parseFloat(opt.dataset.tare);
        trailerData.label = opt.dataset.label;
        
        document.getElementById('trailerTareValue').textContent = trailerData.tare.toFixed(3).replace('.', ',') + ' t';
        tareDisplay.style.display = 'flex';
    } else {
        trailerData.setId = null;
        trailerData.tare = 0;
        trailerData.label = '';
        tareDisplay.style.display = 'none';
    }
}

function calculate() {
    // Walidacja samochodu
    if (!tractorData.setId) {
        Swal.fire({ icon: 'warning', title: 'Wybierz kontener dla samochodu', timer: 2000, showConfirmButton: false });
        return;
    }
    
    const tractorBrutto = parseFloat(document.getElementById('tractorBruttoInput').value);
    if (isNaN(tractorBrutto) || tractorBrutto <= 0) {
        Swal.fire({ icon: 'warning', title: 'Podaj wagę samochodu', timer: 2000, showConfirmButton: false });
        return;
    }
    
    tractorData.brutto = tractorBrutto;
    tractorData.netto = Math.round((tractorBrutto - tractorData.tare) * 1000) / 1000;
    
    // Wynik cząstkowy samochodu
    document.getElementById('tractorNettoValue').textContent = tractorData.netto.toFixed(3).replace('.', ',') + ' t';
    document.getElementById('tractorResult').classList.add('show');
    
    // Naczepa (jeśli jest)
    if (HAS_TRAILER) {
        if (!trailerData.setId) {
            Swal.fire({ icon: 'warning', title: 'Wybierz kontener dla naczepy', timer: 2000, showConfirmButton: false });
            return;
        }
        
        const trailerBrutto = parseFloat(document.getElementById('trailerBruttoInput').value);
        if (isNaN(trailerBrutto) || trailerBrutto <= 0) {
            Swal.fire({ icon: 'warning', title: 'Podaj wagę naczepy', timer: 2000, showConfirmButton: false });
            return;
        }
        
        trailerData.brutto = trailerBrutto;
        trailerData.netto = Math.round((trailerBrutto - trailerData.tare) * 1000) / 1000;
        
        // Wynik cząstkowy naczepy
        document.getElementById('trailerNettoValue').textContent = trailerData.netto.toFixed(3).replace('.', ',') + ' t';
        document.getElementById('trailerResult').classList.add('show');
    }
    
    // Podsumowanie
    const totalNetto = tractorData.netto + trailerData.netto;
    
    document.getElementById('sumTractorBrutto').textContent = tractorData.brutto.toFixed(3).replace('.', ',') + ' t';
    document.getElementById('sumTractorTare').textContent = tractorData.tare.toFixed(3).replace('.', ',') + ' t';
    document.getElementById('sumTractorNetto').textContent = tractorData.netto.toFixed(3).replace('.', ',') + ' t';
    
    if (HAS_TRAILER) {
        document.getElementById('sumTrailerBrutto').textContent = trailerData.brutto.toFixed(3).replace('.', ',') + ' t';
        document.getElementById('sumTrailerTare').textContent = trailerData.tare.toFixed(3).replace('.', ',') + ' t';
        document.getElementById('sumTrailerNetto').textContent = trailerData.netto.toFixed(3).replace('.', ',') + ' t';
    }
    
    document.getElementById('sumTotalNetto').textContent = totalNetto.toFixed(3).replace('.', ',');
    
    document.getElementById('summaryCard').classList.add('show');
    document.getElementById('btnConfirm').classList.add('show');
    document.getElementById('summaryCard').scrollIntoView({ behavior: 'smooth' });
}

async function doConfirm() {
    const payload = {
        tractor_set_id: tractorData.setId,
        tractor_brutto: tractorData.brutto,
    };
    
    if (HAS_TRAILER && trailerData.setId) {
        payload.trailer_set_id = trailerData.setId;
        payload.trailer_brutto = trailerData.brutto;
    }
    
    const res = await fetch(`/kierowca/orders/${ORDER_ID}/weigh-confirm-hakowiec`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
    });
    
    const data = await res.json();
    
    if (data.success) {
        const totalNetto = tractorData.netto + trailerData.netto;
        await Swal.fire({
            icon: 'success',
            title: 'Zapisano!',
            html: `Masa netto łącznie:<br><strong style="font-size:32px">${totalNetto.toFixed(3).replace('.', ',')} t</strong>`,
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