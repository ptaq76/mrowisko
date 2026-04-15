@extends('layouts.plac')

@section('title', 'Magazyn')

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
    color: #1a1a1a; margin-bottom: 12px;
}

/* Tabela stanów */
.stock-table {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,.07);
    margin-bottom: 16px;
}

.stock-table table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.stock-table thead tr {
    background: #1a1a1a;
    color: #fff;
}

.stock-table th {
    padding: 10px 12px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    text-align: left;
}

.stock-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.stock-table tr:last-child td { border-bottom: none; }
.stock-table tr:hover td { background: #f8f9fa; }

.fraction-name { font-weight: 700; color: #1a1a1a; font-size: 13px; }
.bales-val { font-family: 'Barlow Condensed', sans-serif; font-size: 20px; font-weight: 900; color: #1a1a1a; }
.weight-val { font-size: 12px; color: #555; font-weight: 600; }

.info-btn {
    background: #eaf4fb;
    border: none;
    border-radius: 6px;
    padding: 6px 10px;
    color: #2980b9;
    cursor: pointer;
    font-size: 14px;
}
.info-btn:active { background: #2980b9; color: #fff; }

/* Modal historii */
.hist-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 200;
    align-items: flex-end;
}
.hist-modal-overlay.open { display: flex; }

.hist-modal {
    background: #fff;
    border-radius: 16px 16px 0 0;
    width: 100%;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    animation: slideUp .25s ease;
}

@keyframes slideUp {
    from { transform: translateY(100%); }
    to   { transform: translateY(0); }
}

.hist-header {
    padding: 16px 16px 10px;
    border-bottom: 1px solid #f0f2f5;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.hist-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 18px;
    font-weight: 900;
    color: #1a1a1a;
}

.hist-close {
    background: #f0f2f5;
    border: none;
    border-radius: 50%;
    width: 32px; height: 32px;
    font-size: 16px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
}

.hist-body {
    overflow-y: auto;
    flex: 1;
}

.hist-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.hist-table thead tr { background: #f4f5f7; }

.hist-table th {
    padding: 8px 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #888;
    text-align: left;
    white-space: nowrap;
}

.hist-table td {
    padding: 9px 10px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.hist-table tr:last-child td { border-bottom: none; }

.origin-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 10px;
    white-space: nowrap;
}
.origin-production { background: #e8f7e4; color: #2d7a1a; }
.origin-loading    { background: #fef9e7; color: #d68910; }
.origin-delivery   { background: #eaf4fb; color: #2471a3; }
.origin-inventory  { background: #f9ebea; color: #c0392b; }

.positive { color: #27ae60; font-weight: 700; }
.negative { color: #e74c3c; font-weight: 700; }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.dashboard') }}'"
        style="display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer">
    <i class="fas fa-home"></i> Powrót
</button>


<div class="stock-table">
    <table>
        <thead>
            <tr>
                <th>Towar</th>
                <th>Belki</th>
                <th>Waga</th>
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
                    <button class="info-btn"
                            onclick="showHistory({{ $row->fraction_id }}, '{{ addslashes($row->fraction?->name) }}')"
                            title="Historia">
                        <i class="fas fa-history"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Podsumowanie --}}
<div style="background:#16a085;border-radius:10px;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;color:#fff">
    <span style="font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;letter-spacing:.08em;text-transform:uppercase">Razem</span>
    <span style="font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900">
        {{ $stock->sum('total_bales') }} bel. &nbsp;·&nbsp; {{ number_format($stock->sum('total_weight') / 1000, 3, ',', ' ') }} t
    </span>
</div>

{{-- Modal historii --}}
<div class="hist-modal-overlay" id="histOverlay" onclick="closeHistory(event)">
    <div class="hist-modal">
        <div class="hist-header">
            <span class="hist-title" id="histTitle">Historia</span>
            <button class="hist-close" onclick="closeHistModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="hist-body">
            <table class="hist-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Belki</th>
                        <th>Waga kg</th>
                        <th>Typ</th>
                        <th>Operator</th>
                    </tr>
                </thead>
                <tbody id="histBody">
                    <tr><td colspan="5" style="text-align:center;padding:20px;color:#ccc">Ładowanie...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function showHistory(fractionId, name) {
    document.getElementById('histTitle').textContent = name;
    document.getElementById('histBody').innerHTML =
        '<tr><td colspan="5" style="text-align:center;padding:20px;color:#ccc">Ładowanie...</td></tr>';
    document.getElementById('histOverlay').classList.add('open');

    const res  = await fetch(`/plac/warehouse/${fractionId}/history`, {
        headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();

    const tbody = document.getElementById('histBody');
    if (!data.history || data.history.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:20px;color:#ccc">Brak historii</td></tr>';
        return;
    }

    const originClass = {
        'Produkcja': 'origin-production',
        'Załadunek': 'origin-loading',
        'Dostawa':   'origin-delivery',
        'Inwentaryzacja': 'origin-inventory',
    };

    tbody.innerHTML = data.history.map(h => {
        const isPos    = h.bales >= 0;
        const balesStr = (isPos ? '+' : '') + h.bales;
        const weightStr= (isPos ? '+' : '') + (parseFloat(h.weight)/1000).toLocaleString('pl-PL', {minimumFractionDigits:3, maximumFractionDigits:3}) + ' t';
        const cls      = isPos ? 'positive' : 'negative';
        const badge    = originClass[h.origin] ?? '';
        return `<tr>
            <td>${h.date}</td>
            <td class="${cls}">${balesStr}</td>
            <td class="${cls}">${weightStr}</td>
            <td><span class="origin-badge ${badge}">${h.origin}</span></td>
            <td style="color:#888;font-size:11px">${h.operator}</td>
        </tr>`;
    }).join('');
}

function closeHistModal() {
    document.getElementById('histOverlay').classList.remove('open');
}

function closeHistory(e) {
    if (e.target === document.getElementById('histOverlay')) closeHistModal();
}
</script>
@endsection