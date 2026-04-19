@extends('layouts.app')

@section('title', 'Raport – Planowanie')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.report-wrap { padding: 20px; }
.report-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.report-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;display:flex;align-items:center;gap:8px; }
.badge-count { display:inline-block;background:#1a1a1a;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px; }

.filters { background:#fff;border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.07);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end; }
.filter-group { display:flex;flex-direction:column;gap:4px; }
.filter-group label { font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em; }
.filter-group select,
.filter-group input { padding:7px 10px;border:1px solid #dde0e5;border-radius:7px;font-size:13px;color:#1a1a1a;outline:none;min-width:160px; }
.filter-group select:focus,
.filter-group input:focus { border-color:#1a1a1a; }

.btn-copy-date { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;cursor:pointer;background:#f4f5f7;color:#555;font-size:14px;border:1px solid #dde0e5;transition:all .15s ease; }
.btn-copy-date:hover { background:#1a1a1a;color:#fff;border-color:#1a1a1a; }

.btn-filter { padding:8px 18px;background:#1a1a1a;color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer; }
.btn-filter:hover { background:#333; }

.report-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden; }
.report-table { width:100%;border-collapse:collapse;font-size:13px; }
.report-table thead tr { background:#1a1a1a;color:#fff; }
.report-table th { padding:10px 12px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left; }
.report-table td { padding:10px 12px;border-bottom:1px solid #e8eaed;vertical-align:middle; }
.report-table tr:last-child td { border-bottom:none; }
.report-table tr:hover td { background:#fafafa; }

.cell-date { font-weight:700;white-space:nowrap;color:#1a1a1a;font-size:13px; }
.cell-client { font-weight:800;font-size:14px;color:#1a1a1a; }
.cell-sub { font-size:11px;color:#888;display:flex;align-items:center;gap:5px;flex-wrap:wrap;margin-top:3px; }
.nr-rej { display:inline-block;background:#fff;border:2px solid #1a1a1a;padding:1px 5px;border-radius:4px;font-weight:800;font-size:11px; }

/* Typ zlecenia */
.type-badge { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.04em;white-space:nowrap; }
.type-sale   { background:#fff3e0;color:#e65100; }
.type-pickup { background:#e8f5e9;color:#1b5e20; }

/* Status badge */
.status-badge { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap; }
.s-planned   { background:#f0f4ff;color:#3b5bdb; }
.s-loaded    { background:#fff3e0;color:#f39c12; }
.s-weighed   { background:#e3f2fd;color:#3498db; }
.s-delivered { background:#f3e5f5;color:#9c27b0; }
.s-closed    { background:#e8f5e9;color:#27ae60; }

/* Towary */
.goods-list { display:flex;flex-direction:column;gap:2px; }
.goods-item { display:flex;align-items:center;gap:6px;font-size:12px; }
.g-dot { width:7px;height:7px;border-radius:50%;background:#1a1a1a;flex-shrink:0; }
.g-name { color:#333;font-weight:600; }
.g-count { color:#888;font-size:11px; }

.empty-state { text-align:center;padding:48px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }

.summary-bar { background:#fff;border-radius:10px;padding:12px 16px;margin-bottom:12px;box-shadow:0 1px 4px rgba(0,0,0,.07);display:flex;gap:24px;flex-wrap:wrap;align-items:center; }
.summary-item { display:flex;flex-direction:column;gap:2px; }
.summary-label { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#aaa; }
.summary-value { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;color:#1a1a1a; }
.summary-value.green { color:#27ae60; }
.summary-value.orange { color:#f39c12; }
</style>
@endsection

@section('content')
<div class="report-wrap">

    <div class="report-header">
        <div class="report-title">
            <i class="fas fa-calendar-check" style="color:#1a1a1a"></i>
            Raport Planowania
            <span class="badge-count">{{ $orders->count() }}</span>
        </div>
    </div>

    <form method="GET" action="{{ route('biuro.reports.planning') }}">
        <div class="filters">
            <div class="filter-group">
                <label>Data od</label>
                <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="filter-group" style="justify-content:flex-end">
                <span class="btn-copy-date" onclick="copyDateTo()" title="Ustaw datę do = data od">
                    <i class="fas fa-angle-double-right"></i>
                </span>
            </div>
            <div class="filter-group">
                <label>Data do</label>
                <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}">
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
                <label>Typ zlecenia</label>
                <select name="type">
                    <option value="">– wszystkie –</option>
                    @foreach($types as $val => $label)
                        <option value="{{ $val }}" {{ request('type') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select name="status">
                    <option value="">– wszystkie –</option>
                    @foreach($statuses as $val => $label)
                        <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Kierowca</label>
                <select name="driver_id">
                    <option value="">– wszyscy –</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Filtruj
            </button>
            @if(request()->hasAny(['client_id','type','status','driver_id','date_from','date_to']))
            <a href="{{ route('biuro.reports.planning') }}" style="font-size:12px;color:#aaa;align-self:center">
                <i class="fas fa-times"></i> Wyczyść
            </a>
            @endif
        </div>
    </form>

    @if($orders->isNotEmpty())
    @php
        $countSale   = $orders->where('type', 'sale')->count();
        $countPickup = $orders->where('type', 'pickup')->count();
        $countClosed = $orders->whereIn('status', ['closed'])->count();
    @endphp
    <div class="summary-bar">
        <div class="summary-item">
            <span class="summary-label">Wszystkich</span>
            <span class="summary-value">{{ $orders->count() }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Załadunki</span>
            <span class="summary-value orange">{{ $countSale }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Dostawy</span>
            <span class="summary-value green">{{ $countPickup }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Zamknięte</span>
            <span class="summary-value" style="color:#27ae60">{{ $countClosed }}</span>
        </div>
    </div>
    @endif

    @if($orders->isEmpty())
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <p style="font-size:15px;font-weight:600">Brak zleceń w wybranym zakresie</p>
    </div>
    @else
    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Typ</th>
                    <th>Klient</th>
                    <th>Kierowca / Pojazd</th>
                    <th>Status</th>
                    <th>Towar</th>
                    <th>Uwagi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                @php
                    $statusMap = [
                        'planned'   => ['label' => 'Zaplanowane',  'class' => 's-planned'],
                        'loaded'    => ['label' => 'Załadowane',   'class' => 's-loaded'],
                        'weighed'   => ['label' => 'Zważone',      'class' => 's-weighed'],
                        'delivered' => ['label' => 'Dostarczone',  'class' => 's-delivered'],
                        'closed'    => ['label' => 'Zamknięte',    'class' => 's-closed'],
                    ];
                    $s = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 's-planned'];
                    $frakcjeList = $order->loadingItems->groupBy('fraction_id');
                @endphp
                <tr>
                    <td class="cell-date">
                        {{ $order->planned_date->format('d.m.Y') }}
                        @if($order->planned_time)
                            <div style="font-size:11px;color:#888;font-weight:400">
                                {{ \Carbon\Carbon::parse($order->planned_time)->format('H:i') }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($order->type === 'sale')
                            <span class="type-badge type-sale">
                                <i class="fas fa-truck-loading" style="font-size:10px"></i> Załadunek
                            </span>
                        @else
                            <span class="type-badge type-pickup">
                                <i class="fas fa-boxes" style="font-size:10px"></i> Dostawa
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="cell-client">{{ $order->client?->short_name ?? '–' }}</div>
                    </td>
                    <td>
                        @if($order->driver)
                            <div style="font-size:13px;font-weight:600">{{ $order->driver->name }}</div>
                        @endif
                        <div class="cell-sub">
                            @if($order->tractor)
                                <span class="nr-rej">{{ $order->tractor->plate }}</span>
                            @endif
                            @if($order->trailer)
                                <span class="nr-rej">{{ $order->trailer->plate }}</span>
                            @endif
                            @if(!$order->driver && !$order->tractor && !$order->trailer)
                                <span style="color:#ccc">–</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="status-badge {{ $s['class'] }}">{{ $s['label'] }}</span>
                    </td>
                    <td>
                        @if($order->loadingItems->isNotEmpty())
                        <div class="goods-list">
                            @foreach($order->loadingItems as $item)
                            <div class="goods-item">
                                <span class="g-dot"></span>
                                <span class="g-name">{{ $item->fraction?->name }}</span>
                                @if($item->bales)
                                    <span class="g-count">{{ $item->bales }} szt.</span>
                                @endif
                                @if($item->weight_kg)
                                    <span class="g-count">{{ number_format($item->weight_kg / 1000, 2, ',', ' ') }} t</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                            <span style="color:#ccc;font-size:12px">–</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#555;max-width:180px">
                        {{ $order->notes ?? '' }}
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
</script>
@endsection
