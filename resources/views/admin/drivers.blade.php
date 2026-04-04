@extends('layouts.app')
@section('title', 'Zlecenia kierowcy – ' . $driver->name)
@section('module_name', 'ADMINISTRATOR')
@section('nav_menu') @include('admin._nav') @endsection

@section('styles')
<style>
.wrap { padding: 20px; }
.page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
.filters { background:#fff;border-radius:10px;padding:12px 16px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.07);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end; }
.filter-group { display:flex;flex-direction:column;gap:4px; }
.filter-group label { font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em; }
.filter-group select, .filter-group input { padding:7px 10px;border:1px solid #dde0e5;border-radius:7px;font-size:13px;outline:none; }
.btn-filter { padding:8px 16px;background:#1a1a1a;color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer; }
.driver-tabs { display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap; }
.driver-tab { padding:8px 16px;border:2px solid #dde0e5;border-radius:20px;font-size:13px;font-weight:700;color:#888;text-decoration:none;cursor:pointer; }
.driver-tab.active { border-color:#1a1a1a;background:#1a1a1a;color:#fff; }
.driver-tab:hover { border-color:#1a1a1a;color:#1a1a1a; }
.w-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden; }
.w-table { width:100%;border-collapse:collapse;font-size:13px; }
.w-table thead tr { background:#1a1a1a;color:#fff; }
.w-table th { padding:10px 12px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left; }
.w-table td { padding:10px 12px;border-bottom:1px solid #f0f2f5;vertical-align:middle; }
.w-table tr:last-child td { border-bottom:none; }
.w-table tr:hover td { background:#f8f9fa; }
.badge { font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px;display:inline-block; }
.s-planned   { background:#f4f5f7;color:#888; }
.s-loaded    { background:#fef9e7;color:#d68910; }
.s-weighed   { background:#e8f4fd;color:#2471a3; }
.s-delivered { background:#e8f7e4;color:#1a7a3a; }
.s-closed    { background:#eee;color:#aaa; }
.nr-rej { display:inline-block;background:#fff;border:2px solid #1a1a1a;padding:1px 5px;border-radius:4px;font-weight:800;font-size:11px; }
.empty-state { text-align:center;padding:40px;color:#ccc; }
</style>
@endsection

@section('content')
<div class="wrap">
    <div class="page-header">
        <div class="page-title"><i class="fas fa-truck-moving"></i> Zlecenia kierowców</div>
    </div>

    {{-- Zakładki kierowców --}}
    <div class="driver-tabs">
        @foreach($drivers as $d)
        <a href="{{ route('admin.drivers.show', $d) }}"
           class="driver-tab {{ $d->id === $driver->id ? 'active' : '' }}">
            {{ $d->name }}
        </a>
        @endforeach
    </div>

    {{-- Filtry --}}
    <form method="GET" action="{{ route('admin.drivers.show', $driver) }}">
        <div class="filters">
            <div class="filter-group">
                <label>Data od</label>
                <input type="date" name="date_from" value="{{ request('date_from', now()->subDays(7)->format('Y-m-d')) }}">
            </div>
            <div class="filter-group">
                <label>Data do</label>
                <input type="date" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}">
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select name="status">
                    <option value="">– wszystkie –</option>
                    <option value="planned"   {{ request('status')==='planned'   ? 'selected' : '' }}>Zaplanowane</option>
                    <option value="loaded"    {{ request('status')==='loaded'    ? 'selected' : '' }}>Załadowane</option>
                    <option value="weighed"   {{ request('status')==='weighed'   ? 'selected' : '' }}>Zważone</option>
                    <option value="delivered" {{ request('status')==='delivered' ? 'selected' : '' }}>Dostarczone</option>
                    <option value="closed"    {{ request('status')==='closed'    ? 'selected' : '' }}>Zamknięte</option>
                </select>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filtruj</button>
        </div>
    </form>

    @if($orders->isEmpty())
    <div class="empty-state"><i class="fas fa-calendar-times" style="font-size:36px;margin-bottom:8px;display:block"></i>Brak zleceń</div>
    @else
    <div class="w-table-wrap">
        <table class="w-table">
            <thead><tr>
                <th>Data</th><th>Klient</th><th>Typ</th>
                <th>Pojazdy</th><th>Status</th>
                <th>Brutto</th><th>Netto</th>
            </tr></thead>
            <tbody>
            @foreach($orders as $o)
            <tr>
                <td style="font-weight:700;white-space:nowrap">{{ $o->planned_date->format('d.m.Y') }}<br>
                    <span style="font-size:11px;color:#aaa">{{ $o->planned_time ? substr($o->planned_time,0,5) : '' }}</span>
                </td>
                <td style="font-weight:700">{{ $o->client?->short_name ?? '–' }}</td>
                <td>
                    <span style="color:{{ $o->type==='sale'?'#f39c12':'#27ae60' }};font-weight:700">
                        {{ $o->type === 'sale' ? '↑ Wysyłka' : '↓ Odbiór' }}
                    </span>
                </td>
                <td>
                    @if($o->tractor)<span class="nr-rej">{{ $o->tractor->plate }}</span>@endif
                    @if($o->trailer) <span class="nr-rej">{{ $o->trailer->plate }}</span>@endif
                </td>
                <td><span class="badge s-{{ $o->status }}">{{ ['planned'=>'Zaplanowane','loaded'=>'Załadowane','weighed'=>'Zważone','delivered'=>'Dostarczone','closed'=>'Zamknięte'][$o->status] ?? $o->status }}</span></td>
                <td style="font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:800">
                    {{ $o->weight_brutto ? number_format($o->weight_brutto,3,',','') . ' t' : '–' }}
                </td>
                <td style="font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;color:#27ae60">
                    {{ $o->weight_netto ? number_format($o->weight_netto,3,',','') . ' t' : '–' }}
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
