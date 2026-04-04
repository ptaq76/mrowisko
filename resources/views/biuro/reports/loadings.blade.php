@extends('layouts.app')

@section('title', 'Raport – Załadunki')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.report-wrap { padding: 20px; }
.report-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.report-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;display:flex;align-items:center;gap:8px; }
.badge-count { display:inline-block;background:#f39c12;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px; }

.filters { background:#fff;border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.07);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end; }
.filter-group { display:flex;flex-direction:column;gap:4px; }
.filter-group label { font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em; }
.filter-group select,
.filter-group input { padding:7px 10px;border:1px solid #dde0e5;border-radius:7px;font-size:13px;color:#1a1a1a;outline:none;min-width:160px; }
.filter-group select:focus,
.filter-group input:focus { border-color:#f39c12; }

.btn-copy-date { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;cursor:pointer;background:#fef9e7;color:#f39c12;font-size:14px;border:1px solid #f9d38c;transition:all .15s ease; }
.btn-copy-date:hover { background:#f39c12;color:#fff;border-color:#f39c12; }

.btn-filter { padding:8px 18px;background:#f39c12;color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer; }
.btn-filter:hover { background:#d68910; }

.btn-archived { padding:8px 16px;background:#f4f5f7;color:#555;border:1px solid #dde0e5;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;display:flex;align-items:center;gap:6px; }
.btn-archived:hover { background:#e8e9ec; }

.report-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden; }
.report-table { width:100%;border-collapse:collapse;font-size:13px; }
.report-table thead tr { background:#f39c12;color:#fff; }
.report-table th { padding:10px 12px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left; }
.report-table td { padding:10px 12px;border-bottom:1px solid #f0f2f5;vertical-align:top; }
.report-table tr:last-child td { border-bottom:none; }
.report-table tr:hover td { background:#fffbf4; }

.cell-date  { font-weight:700;white-space:nowrap;color:#1a1a1a;font-size:13px; }
.cell-client { font-weight:800;font-size:14px;color:#1a1a1a; }
.cell-plate { font-size:11px;font-weight:700;color:#555; }
.cell-driver { font-size:11px;color:#888; }
.nr-rej { display:inline-block;background:#fff;border:2px solid #1a1a1a;padding:1px 5px;border-radius:4px;font-weight:800;font-size:11px; }

.goods-table { width:100%;border-collapse:collapse;border:1px solid #e8eaed; }
.goods-table tr { border-bottom:1px solid #e8eaed; }
.goods-table tr:last-child { border-bottom:none; }
.goods-table td { padding:3px 7px;font-size:12px;border-right:1px solid #e8eaed; }
.goods-table td:last-child { border-right:none; }
.g-name { color:#1a1a1a;font-weight:600; }
.g-bales { color:#f39c12;font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:800;text-align:right;white-space:nowrap; }
.g-weight { color:#555;text-align:right;white-space:nowrap; }
.goods-sum { background:#fef9e7; }
.goods-sum td { font-weight:800;font-size:12px; }

.weight-est  { font-size:12px;color:#888; }
.weight-real { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#2d7a1a; }
.weight-none { color:#ddd;font-size:12px; }

.btn-revert { background:#fef9e7;border:1px solid #f9d38c;border-radius:6px;padding:6px 10px;color:#d68910;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:5px;white-space:nowrap; }
.btn-revert:hover { background:#f39c12;color:#fff; }
.btn-archive { background:#fef9e7;border:1px solid #f9d38c;border-radius:6px;padding:6px 10px;color:#d68910;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:5px;white-space:nowrap; }
.btn-archive:hover { background:#f39c12;color:#fff; }

.empty-state { text-align:center;padding:48px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }
</style>
@endsection

@section('content')
<div class="report-wrap">

    <div class="report-header">
        <div class="report-title">
            <i class="fas fa-truck-loading" style="color:#f39c12"></i>
            Raport Załadunki
            <span class="badge-count">{{ $orders->count() }}</span>
        </div>
        <a href="{{ route('biuro.reports.loadings.archived') }}" class="btn-archived">
            <i class="fas fa-archive"></i> Archiwum
        </a>
    </div>

    <form method="GET" action="{{ route('biuro.reports.loadings') }}">
        <div class="filters">
            <div class="filter-group">
                <label>Data od</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}">
            </div>
            <div class="filter-group" style="justify-content:flex-end">
                <span class="btn-copy-date" onclick="copyDateTo()" title="Ustaw datę do = data od">
                    <i class="fas fa-angle-double-right"></i>
                </span>
            </div>
            <div class="filter-group">
                <label>Data do</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}">
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
            <a href="{{ route('biuro.reports.loadings') }}" style="font-size:12px;color:#aaa;align-self:center">
                <i class="fas fa-times"></i> Wyczyść
            </a>
            @endif
        </div>
    </form>

    @if($orders->isEmpty())
    <div class="empty-state">
        <i class="fas fa-truck-loading"></i>
        <p style="font-size:15px;font-weight:600">Brak załadunków do wyświetlenia</p>
    </div>
    @else
    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Odbiorca</th>
                    <th>Pojazd</th>
                    <th>Towary</th>
                    <th>Waga wyjazdowa</th>
                    <th>Waga odbiorcy</th>
                    <th style="width:160px">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                @php
                    $totalBales = $order->loadingItems->sum('bales');
                    $totalEstKg = $order->loadingItems->sum('weight_kg');
                @endphp
                <tr id="row-{{ $order->id }}">
                    <td class="cell-date">{{ $order->planned_date->format('d.m.Y') }}</td>
                    <td>
                        <div class="cell-client">{{ $order->client?->short_name }}</div>
                        <div class="cell-driver">
                            <i class="fas fa-user" style="font-size:10px;color:#aaa"></i>
                            {{ $order->driver?->name }}
                        </div>
                    </td>
                    <td>
                        <div style="white-space:nowrap">
                            @if($order->tractor)<span class="nr-rej">{{ $order->tractor->plate }}</span>@endif
                            @if($order->trailer) <span class="nr-rej">{{ $order->trailer->plate }}</span>@endif
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
                                <td style="color:#aaa;font-size:10px">∑</td>
                                <td class="g-bales">{{ $totalBales }}</td>
                                <td class="g-weight" style="font-weight:800">{{ number_format($totalEstKg / 1000, 3, ',', ' ') }} t</td>
                            </tr>
                            @endif
                        </table>
                        @else
                        <span style="color:#ccc;font-size:12px">brak pozycji</span>
                        @endif
                    </td>
                    <td>
                        @if($order->weight_netto)
                            <div class="weight-real">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</div>
                        @else
                            <span class="weight-none">brak ważenia</span>
                        @endif
                    </td>
                    <td>
                        @if($order->weight_receiver)
                            <div class="weight-real" style="color:#2471a3">{{ number_format($order->weight_receiver, 3, ',', ' ') }} t</div>
                        @else
                            <span class="weight-none">–</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;flex-direction:row;gap:5px;flex-wrap:wrap">
                            <button class="btn-revert" onclick="revertOrder({{ $order->id }})" title="Cofnij zamknięcie załadunku">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button class="btn-archive" onclick="archiveOrder({{ $order->id }})" title="Przenieś do archiwum">
                                <i class="fas fa-archive"></i>
                            </button>
                        </div>
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
const CSRF = '{{ csrf_token() }}';

function copyDateTo() {
    const from = document.getElementById('date_from').value;
    if (from) document.getElementById('date_to').value = from;
}

async function revertOrder(id) {
    const result = await Swal.fire({
        title: 'Cofnąć zamknięcie załadunku?',
        html: 'Status zlecenia wróci do <b>Zaplanowane</b>.<br>Stany magazynowe zostaną przywrócone.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#f39c12',
        confirmButtonText: 'Tak, cofnij', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;
    const res  = await fetch(`/biuro/reports/loadings/${id}/revert`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('row-' + id).remove();
        Swal.fire({ icon: 'success', title: 'Cofnięto!', text: 'Zlecenie wróciło do statusu Zaplanowane.', timer: 2000, showConfirmButton: false });
    }
}

async function archiveOrder(id) {
    const result = await Swal.fire({
        title: 'Przenieść do archiwum?',
        text: 'Zlecenie zniknie z listy aktywnych załadunków.',
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#f39c12',
        confirmButtonText: 'Tak, archiwizuj', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;
    const res  = await fetch(`/biuro/reports/loadings/${id}/archive`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('row-' + id).remove();
        Swal.fire({ icon: 'success', title: 'Zarchiwizowano!', timer: 1500, showConfirmButton: false });
    }
}
</script>
@endsection
