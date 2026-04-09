@extends('layouts.app')
@section('title', 'Archiwum – Dostawy')
@section('module_name', 'BIURO')
@section('nav_menu') @include('biuro._nav') @endsection

@section('styles')
<style>
.report-wrap { padding: 20px; }
.report-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.report-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;display:flex;align-items:center;gap:8px; }
.badge-count { display:inline-block;background:#7f8c8d;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px; }

.filters { background:#fff;border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.07);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end; }
.filter-group { display:flex;flex-direction:column;gap:4px; }
.filter-group label { font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em; }
.filter-group select,
.filter-group input { padding:7px 10px;border:1px solid #dde0e5;border-radius:7px;font-size:13px;color:#1a1a1a;outline:none;min-width:160px; }
.filter-group select:focus,
.filter-group input:focus { border-color:#27ae60; }
.btn-copy-date { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;cursor:pointer;background:#eef8f2;color:#27ae60;font-size:14px;border:1px solid #dde0e5;transition:all .15s ease; }
.btn-copy-date:hover { background:#27ae60;color:#fff;border-color:#27ae60; }
.btn-filter { padding:8px 18px;background:#27ae60;color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer; }
.btn-filter:hover { background:#219a52; }
.btn-back { padding:8px 16px;background:#eaf8f0;color:#219a52;border:1px solid #a9dfbf;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;display:flex;align-items:center;gap:6px; }
.btn-back:hover { background:#27ae60;color:#fff; }

.report-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden;width:80%;margin:0 auto; }
.report-table { width:100%;border-collapse:collapse;font-size:13px; }
.report-table thead tr { background:#7f8c8d;color:#fff; }
.report-table th { padding:10px 12px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left; }
.report-table td { padding:10px 12px;border-bottom:2px solid #d8dce3;vertical-align:top; }
.report-table tr:last-child td { border-bottom:none; }
.report-table tr:hover td { background:#f8f9fa; }
.th-right { text-align:right !important; }
.td-right { text-align:right; }

.cell-date { font-weight:700;white-space:nowrap;color:#888; }
.cell-client { font-weight:800;font-size:14px;color:#555; }
.cell-driver { font-size:11px;color:#aaa;display:flex;align-items:center;gap:5px;flex-wrap:wrap;margin-top:3px; }
.cell-driver-plates { display:flex;gap:4px;margin-left:auto; }
.nr-rej { display:inline-block;background:#fff;border:2px solid #aaa;padding:1px 5px;border-radius:4px;font-weight:800;font-size:11px;color:#888; }

.goods-table { width:100%;border-collapse:collapse;border:1px solid #e8eaed; }
.goods-table tr { border-bottom:1px solid #e8eaed; }
.goods-table tr:last-child { border-bottom:none; }
.goods-table td { padding:3px 7px;font-size:12px;border-right:1px solid #e8eaed;color:#aaa; }
.goods-table td:last-child { border-right:none; }
.g-name { font-weight:600;width:140px;min-width:140px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.g-bales { font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:800;text-align:right;white-space:nowrap;width:55px;min-width:55px; }
.g-weight { text-align:right;white-space:nowrap;width:80px;min-width:80px; }
.goods-sum { background:#f4f5f7; }
.goods-sum td { font-weight:800;font-size:12px; }

.weight-real { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#888; }
.weight-none { color:#ddd;font-size:12px; }

.btn-unarchive { background:#fef9e7;border:1px solid #f9d38c;border-radius:6px;padding:6px 10px;color:#d68910;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:5px;white-space:nowrap; }
.btn-unarchive:hover { background:#f39c12;color:#fff; }
.empty-state { text-align:center;padding:48px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }
</style>
@endsection

@section('content')
<div class="report-wrap">

    <div class="report-header">
        <div class="report-title">
            <i class="fas fa-archive" style="color:#27ae60"></i>
            Archiwum Dostaw
            <span class="badge-count">{{ $orders->count() }}</span>
        </div>
        <a href="{{ route('biuro.reports.deliveries') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Powrót do raportu
        </a>
    </div>

    <form method="GET" action="{{ route('biuro.reports.deliveries.archived') }}">
        <div class="filters">
            <div class="filter-group">
                <label>Data od</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from', $dateFrom) }}">
            </div>
            <div class="filter-group" style="justify-content:flex-end">
                <span class="btn-copy-date" onclick="copyDateTo()" title="Ustaw datę do = data od">
                    <i class="fas fa-angle-double-right"></i>
                </span>
            </div>
            <div class="filter-group">
                <label>Data do</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to', $dateTo) }}">
            </div>
            <div class="filter-group">
                <label>Klient</label>
                <select name="client_id">
                    <option value="">– wszyscy –</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->short_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Towar</label>
                <select name="fraction_id">
                    <option value="">– wszystkie –</option>
                    @foreach($fractions as $f)
                        <option value="{{ $f->id }}" {{ request('fraction_id') == $f->id ? 'selected' : '' }}>
                            {{ $f->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Filtruj
            </button>
            @if(request()->hasAny(['client_id','fraction_id','date_from','date_to']))
            <a href="{{ route('biuro.reports.deliveries.archived') }}" style="font-size:12px;color:#aaa;align-self:center">
                <i class="fas fa-times"></i> Wyczyść
            </a>
            @endif
        </div>
    </form>

    @if($orders->isEmpty())
    <div class="empty-state">
        <i class="fas fa-archive"></i>
        <p style="font-size:15px;font-weight:600">Archiwum jest puste</p>
    </div>
    @else
    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Dostawca</th>
                    <th>Towary</th>
                    <th class="th-right">Waga kierowcy</th>
                    <th style="width:100px">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                @php
                    $totalBales = $order->loadingItems->sum('bales');
                    $totalKg    = $order->loadingItems->sum('weight_kg');
                @endphp
                <tr id="row-{{ $order->id }}">
                    <td class="cell-date">{{ $order->planned_date->format('d.m.Y') }}</td>
                    <td>
                        <div class="cell-client">{{ $order->client?->short_name }}</div>
                        <div class="cell-driver">
                            <i class="fas fa-user" style="font-size:10px;color:#ccc"></i>
                            {{ $order->driver?->name }}
                            <span class="cell-driver-plates">
                                @if($order->tractor)<span class="nr-rej">{{ $order->tractor->plate }}</span>@endif
                                @if($order->trailer)<span class="nr-rej">{{ $order->trailer->plate }}</span>@endif
                            </span>
                        </div>
                    </td>
                    <td>
                        @if($order->loadingItems->isNotEmpty())
                        <table class="goods-table">
                            @foreach($order->loadingItems as $item)
                            <tr>
                                <td class="g-name">{{ $item->fraction?->name }}</td>
                                <td class="g-bales">{{ $item->bales }}</td>
                                <td class="g-weight">{{ number_format($item->weight_kg / 1000, 3, ',', ' ') }} t</td>
                            </tr>
                            @endforeach
                            @if($order->loadingItems->count() > 1)
                            <tr class="goods-sum">
                                <td style="color:#bbb;font-size:10px">&#8721;</td>
                                <td class="g-bales">{{ $totalBales }}</td>
                                <td class="g-weight" style="font-weight:800">{{ number_format($totalKg / 1000, 3, ',', ' ') }} t</td>
                            </tr>
                            @endif
                        </table>
                        @else
                        <span class="weight-none">brak pozycji</span>
                        @endif
                    </td>
                    <td class="td-right">
                        @if($order->weight_netto)
                            <div class="weight-real">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</div>
                        @else
                            <span class="weight-none">–</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn-unarchive" onclick="unarchive({{ $order->id }})" title="Przywróć">
                            <i class="fas fa-undo"></i> Przywróć
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
function copyDateTo() {
    const from = document.getElementById('date_from').value;
    if (from) document.getElementById('date_to').value = from;
}

async function unarchive(id) {
    const conf = await Swal.fire({
        title: 'Przywrócić dostawę?',
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#27ae60',
        confirmButtonText: 'Tak, przywróć', cancelButtonText: 'Anuluj',
    });
    if (!conf.isConfirmed) return;
    const res  = await fetch(`/biuro/reports/deliveries/${id}/unarchive`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('row-' + id).remove();
        Swal.fire({ icon: 'success', title: 'Przywrócono!', timer: 1500, showConfirmButton: false });
    }
}
</script>
@endsection
