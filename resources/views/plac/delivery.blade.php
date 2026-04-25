@extends('layouts.plac')

@section('title', 'Przyjęcie towaru')

@section('styles')
<style>
:root {
    --green: #27ae60;
    --green-dark: #1e8449;
    --green-light: #e8f7e4;
    --green-border: #d4edda;
}

.page-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 24px; font-weight: 900; letter-spacing: .08em;
    text-transform: uppercase; color: var(--text-primary);
    margin-bottom: 14px;
}

/* ── KARTA ZLECENIA ── */
.order-card {
    background: var(--bg-card);
    border-radius: var(--radius-card);
    margin-bottom: 12px;
    overflow: hidden;
    border: 1px solid var(--border);
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}

.order-bar {
    display: flex; align-items: center;
    padding: 13px 16px; gap: 10px;
}
.bar-planned  { background: var(--green); }
.bar-progress { background: #1a5c3a; }
.bar-done     { background: #d5d8db; }

.bar-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 30px; font-weight: 900;
    color: #fff; line-height: 1; flex: 1;
    text-transform: uppercase;
}
.bar-planned .bar-client,
.bar-progress .bar-client { color: #fff; }
.bar-done .bar-client { color: #555; }

.bar-status {
    font-size: 10px; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase;
    background: rgba(255,255,255,.25); color: #fff;
    padding: 3px 10px; border-radius: 20px; white-space: nowrap;
}
.bar-done .bar-status { background: rgba(0,0,0,.1); color: #555; }

.bar-action {
    background: rgba(255,255,255,.25);
    border: none; border-radius: 8px;
    width: 38px; height: 38px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 16px; cursor: pointer;
    text-decoration: none; flex-shrink: 0;
}
.bar-action:active { background: rgba(255,255,255,.45); }

.order-meta {
    padding: 9px 16px;
    display: flex; gap: 8px; align-items: center;
    border-bottom: 1px solid #f0f2f5; flex-wrap: wrap;
}
.driver-name { font-size: 13px; color: var(--text-muted); font-weight: 600; }

/* Waga kierowcy */
.weight-strip {
    background: var(--green-light);
    border-top: 1px solid var(--green-border);
    padding: 9px 16px;
    display: flex; justify-content: space-between; align-items: center;
}
.ws-label { font-size: 11px; font-weight: 700; color: #1a7a3c; text-transform: uppercase; letter-spacing: .06em; }
.ws-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900; color: #1a7a3c; }

/* Lista towarów */
.items-list { padding: 6px 16px 0; }
.item-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 7px 0; border-bottom: 1px solid #f4f5f7; font-size: 14px;
}
.item-row:last-child { border-bottom: none; }
.item-name  { font-weight: 700; color: #222; }
.item-right { display: flex; gap: 12px; align-items: center; }
.item-bales { font-family: 'Barlow Condensed', sans-serif; font-size: 16px; font-weight: 900; color: #111; }
.item-weight{ font-size: 14px; font-weight: 700; color: var(--text-muted); }

/* Suma */
.order-sum {
    margin: 0 16px;
    border-top: 2px solid var(--green);
    padding: 9px 0;
    display: flex; justify-content: space-between; align-items: center;
}
.sum-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; }
.sum-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 20px; font-weight: 900; color: #111; }

/* Separatory sekcji */
.section-sep {
    font-size: 11px; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: #aaa;
    margin: 16px 0 8px;
}

/* Pusty stan */
.empty-state {
    text-align: center; padding: 52px 20px; color: #ccc;
}
.empty-state i { font-size: 52px; margin-bottom: 14px; display: block; }
.empty-state p { font-size: 15px; font-weight: 600; }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.dashboard') }}'"
        class="btn-back">
    <i class="fas fa-home"></i> Powrót
</button>

<div class="page-title">Przyjęcie towaru</div>

@php
    $activeOrders = $orders->filter(fn($o) => !in_array($o->status, ['delivered','closed']));
    $doneOrders   = $orders->filter(fn($o) => in_array($o->status, ['delivered','closed']));
@endphp

@if($activeOrders->isEmpty() && $doneOrders->isEmpty())
<div class="empty-state">
    <i class="fas fa-truck-loading"></i>
    <p>Brak dostaw</p>
</div>
@endif

@foreach($activeOrders as $order)
@php $st = $placStatus($order); @endphp
<div class="order-card">
    <div class="order-bar {{ $order->loadingItems->isNotEmpty() ? 'bar-progress' : 'bar-planned' }}">
        <div class="bar-client">{{ $order->client?->short_name }}</div>
        <span class="bar-status">{{ $st['label'] }}</span>
        <a href="{{ route('plac.delivery.form', $order) }}" class="bar-action">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="order-meta">
        @if($order->driver)<span class="driver-name">{{ $order->driver->name }}</span>@endif
        @if($order->tractor)<span class="plate-badge">{{ $order->tractor->plate }}</span>@endif
        @if($order->trailer)<span class="plate-badge">{{ $order->trailer->plate }}</span>@endif
    </div>

    @if($order->weight_netto)
    <div class="weight-strip">
        <span class="ws-label">Waga kierowcy</span>
        <span class="ws-val">{{ number_format($order->weight_netto * 1000, 0, ',', ' ') }} kg</span>
    </div>
    @endif

    @if($order->loadingItems->isNotEmpty())
    <div class="items-list">
        @foreach($order->loadingItems as $item)
        <div class="item-row">
            <span class="item-name">{{ $item->fraction?->name }}</span>
            <div class="item-right">
                <span class="item-bales">{{ $item->bales }}</span>
                <span class="item-weight">{{ number_format($item->weight_kg / 1000, 3, ',', ' ') }} t</span>
            </div>
        </div>
        @endforeach
    </div>
    @if($order->loadingItems->count() > 1)
    <div class="order-sum">
        <span class="sum-label">Razem</span>
        <span class="sum-val">
            {{ $order->loadingItems->sum('bales') }} bel.
            &nbsp;·&nbsp;
            {{ number_format($order->loadingItems->sum('weight_kg') / 1000, 3, ',', ' ') }} t
        </span>
    </div>
    @endif
    @endif
</div>
@endforeach

@if($doneOrders->isNotEmpty())
<div class="section-sep">✓ Zamknięte</div>
@foreach($doneOrders as $order)
<div class="order-card" style="opacity:.5">
    <div class="order-bar bar-done">
        <div class="bar-client">{{ $order->client?->short_name }}</div>
        <span class="bar-status">Zamknięte</span>
    </div>
    <div class="order-meta">
        @if($order->tractor)<span class="plate-badge">{{ $order->tractor->plate }}</span>@endif
        @if($order->trailer)<span class="plate-badge">{{ $order->trailer->plate }}</span>@endif
    </div>
    @if($order->loadingItems->isNotEmpty())
    <div class="order-sum" style="border-color:#ccc">
        <span class="sum-label">Razem</span>
        <span class="sum-val" style="color:#555">
            {{ $order->loadingItems->sum('bales') }} bel.
            &nbsp;·&nbsp;
            {{ number_format($order->loadingItems->sum('weight_kg') / 1000, 3, ',', ' ') }} t
        </span>
    </div>
    @endif
</div>
@endforeach
@endif

@endsection