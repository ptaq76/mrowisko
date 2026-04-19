@extends('layouts.app')

@section('title', 'Stan magazynu')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.report-wrap { padding: 20px; }
.report-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.report-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;display:flex;align-items:center;gap:8px; }

.report-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden;width:50%;margin:0 auto; }
.report-table { width:100%;border-collapse:collapse;font-size:13px; }
.report-table thead tr { background:#1a1a1a;color:#fff; }
.report-table th { padding:10px 14px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left; }
.report-table td { padding:10px 14px;border-bottom:2px solid #d8dce3;vertical-align:middle; }
.report-table tr:last-child td { border-bottom:none; }
.report-table tr:hover td { background:#fffbf4; }

.fraction-name { font-weight:700;font-size:14px;color:#1a1a1a; }
.bales-val { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;color:#1a1a1a; }
.weight-val { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:700;color:#555; }
.zero { color:#ddd; }

.btn-history { background:#f4f5f7;border:1px solid #dde0e5;border-radius:6px;padding:5px 10px;color:#555;cursor:pointer;font-size:12px;display:inline-flex;align-items:center;gap:4px; }
.btn-history:hover { background:#1a1a1a;color:#fff;border-color:#1a1a1a; }

.btn-fav {
    background: none; border: none; cursor: pointer;
    font-size: 18px; line-height: 1; padding: 2px 4px;
    transition: transform .15s;
}
.btn-fav:hover { transform: scale(1.2); }
.btn-fav.active { color: #f39c12; }
.btn-fav.inactive { color: #ddd; }

/* Modal historii */
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:12px;width:100%;max-width:640px;padding:0;box-shadow:0 8px 32px rgba(0,0,0,.2);max-height:85vh;display:flex;flex-direction:column; }
.modal-head { padding:16px 20px;border-bottom:1px solid #f0f2f5;display:flex;justify-content:space-between;align-items:center; }
.modal-head-title { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;text-transform:uppercase; }
.modal-body-scroll { overflow-y:auto;flex:1;padding:0; }
.modal-close { background:none;border:none;font-size:20px;cursor:pointer;color:#aaa;line-height:1; }
.modal-close:hover { color:#1a1a1a; }

.hist-table { width:100%;border-collapse:collapse;font-size:13px; }
.hist-table th { padding:8px 14px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;border-bottom:2px solid #f0f2f5;text-align:left; }
.hist-table td { padding:8px 14px;border-bottom:1px solid #f0f2f5;vertical-align:middle; }
.hist-table tr:last-child td { border-bottom:none; }
.hist-bales { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900; }
.hist-bales.pos { color:#27ae60; }
.hist-bales.neg { color:#e74c3c; }
.hist-origin { display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;background:#f4f5f7;color:#555; }

.empty-state { text-align:center;padding:48px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }
</style>
@endsection

@section('content')
<div class="report-wrap">

    <div class="report-header" style="width:50%;margin:0 auto 16px auto">
        <div class="report-title">
            <i class="fas fa-warehouse" style="color:#f39c12"></i>
            Stan magazynu
        </div>
    </div>

    @if($stock->isEmpty())
    <div class="empty-state">
        <i class="fas fa-warehouse"></i>
        <p style="font-size:15px;font-weight:600">Brak frakcji w magazynie</p>
    </div>
    @else
    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width:36px">★</th>
                    <th>Frakcja</th>
                    <th style="text-align:right">Belki</th>
                    <th style="text-align:right">Waga</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($stock as $row)
                <tr>
                    <td>
                        <button class="btn-fav {{ $row->fraction->fav_biuro ? 'active' : 'inactive' }}"
                                onclick="toggleFav({{ $row->fraction_id }}, this)"
                                title="{{ $row->fraction->fav_biuro ? 'Usuń z ulubionych' : 'Dodaj do ulubionych' }}">
                            ★
                        </button>
                    </td>
                    <td class="fraction-name">{{ $row->fraction->name }}</td>
                    <td style="text-align:right">
                        @if($row->total_bales > 0)
                            <span class="bales-val">{{ $row->total_bales }}</span>
                        @else
                            <span class="zero">0</span>
                        @endif
                    </td>
                    <td style="text-align:right">
                        @if($row->total_weight > 0)
                            <span class="weight-val">{{ number_format($row->total_weight / 1000, 3, ',', ' ') }} t</span>
                        @else
                            <span class="zero">–</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn-history" onclick="showHistory({{ $row->fraction_id }})">
                            <i class="fas fa-history"></i> Historia
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

{{-- Modal historii --}}
<div class="modal-overlay" id="histModal">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-head">
            <div class="modal-head-title" id="histTitle">Historia</div>
            <button class="modal-close" onclick="closeHistory()">×</button>
        </div>
        <div class="modal-body-scroll">
            <table class="hist-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Typ</th>
                        <th style="text-align:right">Belki</th>
                        <th style="text-align:right">Waga</th>
                        <th>Operator</th>
                    </tr>
                </thead>
                <tbody id="histBody">
                    <tr><td colspan="5" style="text-align:center;padding:24px;color:#ccc">Ładowanie...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function toggleFav(fractionId, btn) {
    const isActive = btn.classList.contains('active');
    const res = await fetch(`/biuro/reports/warehouse/${fractionId}/fav`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({ module: 'biuro' }),
    });
    const data = await res.json();
    if (data.success) {
        btn.classList.toggle('active', data.fav);
        btn.classList.toggle('inactive', !data.fav);
        btn.title = data.fav ? 'Usuń z ulubionych' : 'Dodaj do ulubionych';
    }
}

async function showHistory(fractionId) {
    document.getElementById('histTitle').textContent = 'Historia';
    document.getElementById('histBody').innerHTML = '<tr><td colspan="5" style="text-align:center;padding:24px;color:#ccc">Ładowanie...</td></tr>';
    document.getElementById('histModal').classList.add('open');

    try {
        const res  = await fetch(`/biuro/reports/warehouse/${fractionId}/history`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        document.getElementById('histTitle').textContent = data.fraction;

        if (!data.history.length) {
            document.getElementById('histBody').innerHTML = '<tr><td colspan="5" style="text-align:center;padding:24px;color:#ccc">Brak wpisów</td></tr>';
            return;
        }

        document.getElementById('histBody').innerHTML = data.history.map(h => {
            const isPos = h.bales >= 0;
            return `<tr>
                <td>${h.date}</td>
                <td><span class="hist-origin">${h.origin}</span></td>
                <td style="text-align:right"><span class="hist-bales ${isPos ? 'pos' : 'neg'}">${isPos ? '+' : ''}${h.bales}</span></td>
                <td style="text-align:right">${(h.weight / 1000).toFixed(3).replace('.', ',')} t</td>
                <td>${h.operator}</td>
            </tr>`;
        }).join('');
    } catch (e) {
        document.getElementById('histBody').innerHTML = '<tr><td colspan="5" style="text-align:center;padding:24px;color:#e74c3c">Błąd ładowania danych</td></tr>';
    }
}

function closeHistory() {
    document.getElementById('histModal').classList.remove('open');
}

document.getElementById('histModal').addEventListener('click', closeHistory);
</script>
@endsection