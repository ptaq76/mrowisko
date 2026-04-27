@extends('layouts.plac')

@section('title', 'Raport – Załadunki')
@section('hide_datebar', '1')

@section('styles')
<style>
    :root {
        --green: #27ae60;
        --green-dark: #1e8449;
        --green-light: #eafaf1;
    }

    .page-title {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 22px; font-weight: 900; letter-spacing: .08em;
        text-transform: uppercase; color: var(--text-primary);
        margin: 4px 0 12px 0; display: flex; align-items: center; gap: 8px;
    }
    .page-title i { color: var(--yellow-dark); }
    .badge-count {
        display: inline-block; background: var(--yellow); color: #111;
        font-size: 11px; font-weight: 800;
        padding: 2px 9px; border-radius: 10px;
    }

    /* ── FILTRY ── */
    .filter-bar {
        background: #fff;
        border-radius: var(--radius-card);
        padding: 11px 13px;
        margin-bottom: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 2px 6px rgba(0,0,0,.04);
        display: flex; flex-direction: column; gap: 7px;
    }
    .filter-bar label {
        font-size: 11px; font-weight: 700; color: #777;
        text-transform: uppercase; letter-spacing: .06em;
    }
    .filter-bar select {
        padding: 9px 12px; border: 1px solid #d5d8dc; border-radius: 9px;
        font-family: 'Barlow', sans-serif; font-size: 15px; font-weight: 600;
        color: var(--text-primary); outline: none; background: #fff;
    }
    .filter-bar select:focus { border-color: var(--yellow-dark); }
    .filter-actions { display: flex; gap: 7px; }
    .btn-clear {
        padding: 10px 14px; border: 1px solid var(--border); border-radius: 9px;
        background: #fff; color: #777; font-weight: 700; font-size: 13px;
        cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 4px;
    }

    /* ── KAFELEK ── */
    .tile {
        background: #fff;
        border-radius: var(--radius-card);
        margin-bottom: 12px;
        overflow: hidden;
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
    }
    .tile-head {
        background: var(--yellow);
        color: #111;
        padding: 10px 14px;
        display: flex; align-items: center; gap: 10px;
        flex-wrap: wrap;
    }
    .tile-date {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 18px; font-weight: 800; letter-spacing: .04em;
    }
    .tile-client {
        flex: 1; min-width: 140px;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 22px; font-weight: 900; line-height: 1;
        text-transform: uppercase;
        text-align: right;
    }

    .tile-meta {
        padding: 8px 14px;
        display: flex; flex-wrap: wrap; gap: 8px; align-items: center;
        border-bottom: 1px solid #f0f2f5;
        background: #fafbfc;
    }
    .meta-driver { font-size: 13px; font-weight: 700; color: #555; display: flex; align-items: center; gap: 5px; }
    .meta-driver i { color: #aaa; font-size: 11px; }
    .meta-plates { display: flex; gap: 5px; margin-left: auto; }
    .nr-rej {
        display: inline-block; background: #fff; border: 2px solid #111;
        padding: 1px 6px; border-radius: 4px;
        font-weight: 800; font-size: 11px; color: #111;
    }

    /* Sekcje */
    .tile-section { padding: 9px 14px; border-bottom: 1px solid #f0f2f5; }
    .tile-section:last-child { border-bottom: none; }
    .section-label {
        font-size: 10px; font-weight: 800; color: #888;
        text-transform: uppercase; letter-spacing: .08em;
        margin-bottom: 6px; display: flex; align-items: center; gap: 6px;
    }
    .section-label i { color: var(--green); font-size: 11px; }

    /* Tabelka towarów */
    .goods-table { width: 100%; border-collapse: collapse; border: 1px solid #e8eaed; border-radius: 6px; overflow: hidden; }
    .goods-table tr { border-bottom: 1px solid #e8eaed; }
    .goods-table tr:last-child { border-bottom: none; }
    .goods-table td { padding: 5px 8px; font-size: 12px; border-right: 1px solid #e8eaed; }
    .goods-table td:last-child { border-right: none; }
    .g-name { color: #111; font-weight: 700; }
    .g-bales { color: #111; font-family: 'Barlow Condensed', sans-serif; font-size: 14px; font-weight: 800; text-align: right; white-space: nowrap; width: 50px; }
    .g-weight { color: #111; text-align: right; white-space: nowrap; width: 78px; font-weight: 600; }
    .goods-sum { background: var(--green-light); }
    .goods-sum td { font-weight: 800; font-size: 12px; }

    /* Opakowania */
    .packaging-list {
        display: flex; flex-wrap: wrap; gap: 5px;
    }
    .pack-pill {
        background: #fff8e1; border: 1px solid #ffe082;
        padding: 3px 9px; border-radius: 14px;
        font-size: 12px; color: #6d4c00; font-weight: 600;
    }
    .pack-pill .qty { font-weight: 800; color: var(--yellow-dark); }

    /* Waga kierowcy */
    .weight-driver {
        display: flex; justify-content: space-between; align-items: center;
        background: #fef9e7; border: 1px solid #fdebd0;
        padding: 7px 12px; border-radius: 8px;
    }
    .wd-label { font-size: 11px; font-weight: 700; color: #935810; text-transform: uppercase; letter-spacing: .06em; }
    .wd-val { font-family: 'Barlow Condensed', sans-serif; font-size: 18px; font-weight: 900; color: #935810; }
    .wd-empty { color: #aaa; font-size: 12px; }

    .empty-state {
        text-align: center; padding: 40px 20px;
        background: #fff; border-radius: var(--radius-card);
        border: 1px dashed var(--border); color: #aaa;
    }
    .empty-state i { font-size: 38px; display: block; margin-bottom: 8px; }
</style>
@endsection

@section('content')
<div class="page-title">
    <i class="fas fa-truck-loading"></i>
    Raport Załadunków
    <span class="badge-count">{{ $orders->count() }}</span>
</div>

<form method="GET" action="{{ route('plac.reports.loadings') }}">
    <div class="filter-bar">
        <div>
            <label>Klient (odbiorca)</label>
            <select name="client_id" style="width:100%" onchange="this.form.submit()">
                <option value="">– wszyscy –</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->short_name }}
                    </option>
                @endforeach
            </select>
        </div>
        @if(request('client_id'))
        <div class="filter-actions">
            <a href="{{ route('plac.reports.loadings') }}" class="btn-clear" style="flex:1; justify-content:center">
                <i class="fas fa-times"></i> Wyczyść filtr
            </a>
        </div>
        @endif
    </div>
</form>

@if($orders->isEmpty())
    <div class="empty-state">
        <i class="fas fa-truck-loading"></i>
        <p style="font-size:14px; font-weight:600; margin:0">Brak załadunków do wyświetlenia</p>
    </div>
@else
    @foreach($orders as $order)
        @php
            $totalBales = $order->loadingItems->sum('bales');
            $totalKg    = $order->loadingItems->sum('weight_kg');
        @endphp
        <div class="tile">
            <div class="tile-head">
                <div class="tile-date">
                    <i class="fas fa-calendar-day" style="font-size:13px; opacity:.85"></i>
                    {{ $order->planned_date->format('d.m.Y') }}
                </div>
                <div class="tile-client">{{ $order->client?->short_name }}</div>
            </div>

            <div class="tile-meta">
                <span class="meta-driver">
                    <i class="fas fa-user"></i>
                    {{ $order->driver?->name ?? '—' }}
                </span>
                <span class="meta-plates">
                    @if($order->tractor)<span class="nr-rej">{{ $order->tractor->plate }}</span>@endif
                    @if($order->trailer)<span class="nr-rej">{{ $order->trailer->plate }}</span>@endif
                </span>
            </div>

            {{-- Towary --}}
            @if($order->loadingItems->isNotEmpty())
            <div class="tile-section">
                <div class="section-label">
                    <i class="fas fa-cubes"></i> Towary
                </div>
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
                        <td style="color:#888; font-size:10px; font-weight:800">SUMA</td>
                        <td class="g-bales">{{ $totalBales }}</td>
                        <td class="g-weight">{{ number_format($totalKg / 1000, 3, ',', ' ') }} t</td>
                    </tr>
                    @endif
                </table>
            </div>
            @endif

            {{-- Opakowania --}}
            @if($order->packaging->isNotEmpty())
            <div class="tile-section">
                <div class="section-label">
                    <i class="fas fa-box"></i> Opakowania
                </div>
                <div class="packaging-list">
                    @foreach($order->packaging as $p)
                        <span class="pack-pill">
                            {{ $p->opakowanie?->name ?? '—' }}
                            <span class="qty">{{ $p->quantity }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Waga kierowcy --}}
            <div class="tile-section">
                <div class="weight-driver">
                    <span class="wd-label"><i class="fas fa-weight" style="font-size:10px"></i> Waga kierowcy</span>
                    @if($order->weight_netto)
                        <span class="wd-val">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</span>
                    @else
                        <span class="wd-empty">brak</span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
