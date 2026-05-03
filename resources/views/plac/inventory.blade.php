@extends('layouts.plac')

@section('title', 'Inwentaryzacja')

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
    color: #1a1a1a; margin-bottom: 4px;
}
.page-sub { font-size: 12px; color: #aaa; margin-bottom: 14px; }

.stock-table {
    background: #fff; border-radius: 12px;
    overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,.07);
}
.stock-table table { width: 100%; border-collapse: collapse; font-size: 13px; }
.stock-table thead tr { background: #c0392b; color: #fff; }
.stock-table th {
    padding: 10px 12px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase; text-align: left;
}
.stock-table td { padding: 10px 12px; border-bottom: 1px solid #f0f2f5; vertical-align: middle; }
.stock-table tr:last-child td { border-bottom: none; }
.stock-table tr:hover td { background: #fdf8f8; }

.fraction-name { font-weight: 700; color: #1a1a1a; font-size: 13px; }
.bales-val { font-family: 'Barlow Condensed', sans-serif; font-size: 20px; font-weight: 900; color: #1a1a1a; }
.weight-val { font-size: 12px; color: #555; font-weight: 600; }

.arrow-btn {
    background: #f9ebea; border: none; border-radius: 6px;
    padding: 6px 10px; color: #c0392b; cursor: pointer; font-size: 14px;
}
.arrow-btn:active { background: #c0392b; color: #fff; }

/* Panel inwentaryzacji - wysuwa się od dołu */
.inv-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.5); z-index: 200; align-items: flex-end;
}
.inv-overlay.open { display: flex; }

.inv-panel {
    background: #fff; border-radius: 16px 16px 0 0;
    width: 100%; padding: 20px;
    animation: slideUp .25s ease;
}
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }

.inv-panel-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 16px;
}
.inv-panel-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 20px; font-weight: 900; color: #1a1a1a;
}
.inv-close {
    background: #f0f2f5; border: none; border-radius: 50%;
    width: 32px; height: 32px; font-size: 16px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
}

.current-state {
    background: #f8f9fa; border-radius: 8px;
    padding: 10px 14px; margin-bottom: 16px;
    display: flex; justify-content: space-between; align-items: center;
    font-size: 13px;
}
.cs-label { color: #888; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; }
.cs-val { font-family: 'Barlow Condensed', sans-serif; font-size: 20px; font-weight: 900; color: #555; }

.inv-inputs { display: flex; flex-direction: column; gap: 12px; margin-bottom: 14px; }
.inv-input-wrap { flex: 1; }
.inv-input-wrap label {
    display: block; font-size: 10px; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase; color: #888; margin-bottom: 6px;
}
.inv-input {
    width: 100%; padding: 14px;
    border: 3px solid #e2e5e9; border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 34px; font-weight: 900; text-align: center;
    color: #1a1a1a; outline: none;
    -moz-appearance: textfield;
}
.inv-input::-webkit-outer-spin-button,
.inv-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.inv-input:focus { border-color: #c0392b; }

.btn-inv-save {
    width: 100%; padding: 16px; background: #c0392b; color: #fff;
    border: none; border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 20px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 10px;
}
.btn-inv-save:active { filter: brightness(.9); }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.dashboard') }}'"
        style="display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer">
    <i class="fas fa-home"></i> Powrót
</button>

<div class="page-title">Inwentaryzacja</div>
<div class="page-sub">Wprowadź rzeczywisty stan – system obliczy korektę</div>

@if($stock->isEmpty())
    <div style="text-align:center;padding:48px 20px;color:#ccc">
        <i class="fas fa-clipboard-list" style="font-size:48px;margin-bottom:12px;display:block"></i>
        <p style="font-size:15px;font-weight:600">Magazyn jest pusty</p>
    </div>
@else
<div class="stock-table">
    <table>
        <thead>
            <tr>
                <th>Towar</th>
                <th>Belki</th>
                <th>Waga (t)</th>
                <th style="width:40px"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($stock as $row)
            <tr>
                <td><span class="fraction-name">{{ $row->fraction?->name ?? '?' }}</span></td>
                <td><span class="bales-val">{{ $row->total_bales }}</span></td>
                <td><span class="weight-val">{{ number_format($row->total_weight / 1000, 3, ',', ' ') }} t</span></td>
                <td>
                    <button class="arrow-btn"
                            onclick="openAdjust({{ $row->fraction_id }}, '{{ addslashes($row->fraction?->name) }}', {{ $row->total_bales }}, {{ $row->total_weight }})"
                            title="Wprowadź stan">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Panel inwentaryzacji --}}
<div class="inv-overlay" id="invOverlay" onclick="closeAdjust(event)">
    <div class="inv-panel">
        <div class="inv-panel-header">
            <span class="inv-panel-title" id="invTitle">Inwentaryzacja</span>
            <button class="inv-close" onclick="closeInvPanel()"><i class="fas fa-times"></i></button>
        </div>

        <div class="current-state">
            <div>
                <div class="cs-label">Stan obecny</div>
                <div class="cs-val" id="currentState">–</div>
            </div>
        </div>

        <div class="inv-inputs">
            <div class="inv-input-wrap">
                <label>Belki (szt.)</label>
                <input type="text" id="invBales" class="inv-input js-numkey"
                       data-keypad-label="Belki [szt.]"
                       data-decimal="false"
                       data-min="0" data-max="99999">
            </div>
            <div class="inv-input-wrap">
                <label>Waga (kg)</label>
                <input type="text" id="invWeight" class="inv-input js-numkey"
                       placeholder="0"
                       data-keypad-label="Waga [kg]"
                       data-decimal="false"
                       data-min="0" data-max="9999999">
            </div>
        </div>

        <button class="btn-inv-save" onclick="saveAdjust()">
            <i class="fas fa-check"></i> ZAPISZ STAN
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
let _fracId   = null;
let _fracName = null;
let _curBales = 0;
let _curWeight= 0;
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function openAdjust(fracId, name, curBales, curWeight) {
    _fracId    = fracId;
    _fracName  = name;
    _curBales  = curBales;
    _curWeight = curWeight;

    document.getElementById('invTitle').textContent      = name;
    document.getElementById('currentState').textContent  = `${curBales} bel. / ${(curWeight/1000).toLocaleString('pl-PL', {minimumFractionDigits:3, maximumFractionDigits:3})} t`;
    // Pola startują puste — operator musi świadomie wpisać stan, żeby uniknąć przypadkowego zapisu pre-filla
    document.getElementById('invBales').value            = '';
    document.getElementById('invWeight').value           = '';

    document.getElementById('invOverlay').classList.add('open');
    setTimeout(() => document.getElementById('invBales').focus(), 300);
}

function closeInvPanel() {
    document.getElementById('invOverlay').classList.remove('open');
}

function closeAdjust(e) {
    if (e.target === document.getElementById('invOverlay')) closeInvPanel();
}

async function saveAdjust() {
    const bales  = parseInt(document.getElementById('invBales').value);
    const weight = parseFloat(document.getElementById('invWeight').value);

    if (isNaN(bales) || bales < 0) {
        Swal.fire({ icon: 'warning', title: 'Podaj ilość belek', timer: 1800, showConfirmButton: false });
        return;
    }
    if (isNaN(weight) || weight < 0) {
        Swal.fire({ icon: 'warning', title: 'Podaj wagę', timer: 1800, showConfirmButton: false });
        return;
    }

    const diffBales  = bales  - _curBales;
    const diffWeight = weight - _curWeight;

    // Pokaż podsumowanie korekty
    const diffBalesStr  = (diffBales  >= 0 ? '+' : '') + diffBales;
    const diffWeightStr = (diffWeight >= 0 ? '+' : '') + Math.round(diffWeight).toLocaleString('pl-PL');

    const confirm = await Swal.fire({
        title: 'Potwierdzenie korekty',
        html: `
            <div style="text-align:left;font-size:14px">
                <b>${_fracName}</b><br><br>
                Było: ${_curBales} bel. / ${(_curWeight/1000).toFixed(3).replace('.',',')} t<br>
                Będzie: ${bales} bel. / ${(weight/1000).toFixed(3).replace('.',',')} t<br><br>
                <span style="color:${diffBales >= 0 ? '#27ae60' : '#e74c3c'}">
                    Korekta: ${diffBalesStr} bel. / ${(diffWeight/1000).toFixed(3).replace('.',',')} t
                </span>
            </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#c0392b',
        confirmButtonText: 'Zapisz',
        cancelButtonText: 'Anuluj',
    });

    if (!confirm.isConfirmed) return;

    const res  = await fetch(`/plac/inventory/${_fracId}/adjust`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ bales, weight_kg: weight }),
    });
    const data = await res.json();

    if (data.success) {
        closeInvPanel();
        await Swal.fire({
            icon: 'success', title: 'Zaktualizowano!',
            html: `${data.fraction}<br><strong>${data.new_bales} bel. / ${Math.round(data.new_weight).toLocaleString('pl-PL')} kg</strong>`,
            timer: 2000, showConfirmButton: false,
        });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}
</script>
@endsection