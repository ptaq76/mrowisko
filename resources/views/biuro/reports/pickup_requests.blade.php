@extends('layouts.app')

@section('title', 'Raport – Zlecenia handlowców')
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
.filter-group select { padding:7px 10px;border:1px solid #dde0e5;border-radius:7px;font-size:13px;color:#1a1a1a;outline:none;min-width:160px; }
.filter-group select:focus { border-color:#1a1a1a; }
.btn-filter { padding:8px 18px;background:#1a1a1a;color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer; }
.btn-filter:hover { background:#333; }

.report-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden;width:85%;margin:0 auto; }
.report-table { width:100%;border-collapse:collapse;font-size:13px; }
.report-table thead tr { background:#1a1a1a;color:#fff; }
.report-table th { padding:10px 12px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left; }
.report-table td { padding:10px 12px;border-bottom:2px solid #d8dce3;vertical-align:top; }
.report-table tr:last-child td { border-bottom:none; }
.report-table tr:hover td { background:#f9f9f9; }

.cell-client { font-weight:800;font-size:14px;color:#1a1a1a; }
.cell-date { font-weight:700;white-space:nowrap;color:#555;font-size:12px; }
.cell-salesman { font-size:11px;color:#888;margin-top:3px; }

.items-table { width:100%;border-collapse:collapse;border:1px solid #e8eaed; }
.items-table tr { border-bottom:1px solid #e8eaed; }
.items-table tr:last-child { border-bottom:none; }
.items-table td { padding:3px 7px;font-size:12px;border-right:1px solid #e8eaed; }
.items-table td:last-child { border-right:none; }
.i-name   { font-weight:600;color:#1a1a1a; }
.i-ilosc  { color:#555;text-align:right;white-space:nowrap;width:60px; }
.i-cena   { color:#1a1a1a;font-weight:700;text-align:right;white-space:nowrap;width:90px; }

.status-pill { display:inline-block;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:700;color:#fff;white-space:nowrap; }

.empty-state { text-align:center;padding:48px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }
</style>
@endsection

@section('content')
<div class="report-wrap">

    <div class="report-header">
        <div class="report-title">
            <i class="fas fa-handshake" style="color:#1a1a1a"></i>
            Zlecenia handlowców
            <span class="badge-count">{{ $zlecenia->count() }}</span>
        </div>
    </div>

    <form method="GET" action="{{ route('biuro.reports.pickup-requests') }}">
        <div class="filters">
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
                <label>Handlowiec</label>
                <select name="salesman_id">
                    <option value="">– wszyscy –</option>
                    @foreach($handlowcy as $h)
                        <option value="{{ $h->id }}" {{ request('salesman_id') == $h->id ? 'selected' : '' }}>
                            {{ $h->name }}
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
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Filtruj
            </button>
            @if(request()->hasAny(['client_id','salesman_id','status']))
            <a href="{{ route('biuro.reports.pickup-requests') }}" style="font-size:12px;color:#aaa;align-self:center">
                <i class="fas fa-times"></i> Wyczyść
            </a>
            @endif
        </div>
    </form>

    @if($zlecenia->isEmpty())
    <div class="empty-state">
        <i class="fas fa-handshake"></i>
        <p style="font-size:15px;font-weight:600">Brak zleceń do wyświetlenia</p>
    </div>
    @else
    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Klient</th>
                    <th>Termin</th>
                    <th>Towary</th>
                    <th>Uwagi</th>
                    <th>Status</th>
                    <th>Zlecenie</th>
                </tr>
            </thead>
            <tbody>
                @foreach($zlecenia as $z)
                @php
                    $colors = [
                        'nowe'            => '#f39c12',
                        'przyjete'        => '#2980b9',
                        'zrealizowane'    => '#27ae60',
                        'anulowane'       => '#e74c3c',
                        'odrzucone_biuro' => '#8e44ad',
                    ];
                    $kolor = $colors[$z->status] ?? '#aaa';
                @endphp
                <tr>
                    <td>
                        <div class="cell-client">{{ $z->client?->short_name ?? '–' }}</div>
                        <div class="cell-salesman">
                            <i class="fas fa-user" style="font-size:9px"></i>
                            {{ $z->salesman?->name ?? '–' }}
                        </div>
                    </td>
                    <td class="cell-date">
                        {{ $z->requested_date?->format('d.m.Y') }}
                        @php $diff = now()->startOfDay()->diffInDays($z->requested_date->startOfDay(), false); @endphp
                        @if($z->status === 'nowe')
                            @if($diff < 0)
                                <div style="font-size:10px;color:#e74c3c;font-weight:700">+{{ abs($diff) }}d po terminie</div>
                            @elseif($diff === 0)
                                <div style="font-size:10px;color:#f39c12;font-weight:700">dziś</div>
                            @else
                                <div style="font-size:10px;color:#27ae60">za {{ $diff }}d</div>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($z->items->isNotEmpty())
                        <table class="items-table">
                            @foreach($z->items as $item)
                            <tr>
                                <td class="i-name">{{ $item->nazwa }}</td>
                                <td class="i-ilosc">{{ $item->ilosc ?? '–' }}</td>
                                <td class="i-cena">
                                    @if($item->cena)
                                        {{ number_format($item->cena, 2, ',', ' ') }} zł/t
                                    @else
                                        –
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        @else
                            <span style="color:#ccc;font-size:12px">brak</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#888;max-width:160px">{{ $z->notes ?? '–' }}</td>
                    <td>
                        <span class="status-pill" style="background:{{ $kolor }}">
                            {{ $z->statusLabel() }}
                        </span>
                        <div style="font-size:10px;color:#ccc;margin-top:4px">
                            {{ $z->created_at->format('d.m.Y H:i') }}
                        </div>
                    </td>
                    <td>
                        @if($z->order)
                            <a href="{{ route('biuro.planning.index', ['data' => $z->order->planned_date?->format('Y-m-d')]) }}"
                               style="font-size:11px;color:#2980b9;text-decoration:none;font-weight:600">
                                <i class="fas fa-link"></i>
                                {{ $z->order->planned_date?->format('d.m.Y') }}
                            </a>
                            <div style="font-size:10px;color:#888;margin-top:2px">{{ $z->order->status }}</div>
                        @else
                            <span style="color:#ccc;font-size:11px">–</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
