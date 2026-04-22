@extends('layouts.plac')

@section('title', 'Załadunki')

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
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;margin-bottom:12px; }
.order-card { background:#fff;border-radius:12px;margin-bottom:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08); }
.order-bar { display:flex;align-items:center;padding:11px 14px;gap:10px; }
.bar-planned  { background:#f39c12; }
.bar-progress { background:#e67e22; }
.bar-done     { background:#b2bec3; }
.bar-client { font-family:'Barlow Condensed',sans-serif;font-size:26px;font-weight:900;color:#1a1a1a;line-height:1;flex:1; }
.bar-status { font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:rgba(0,0,0,.15);color:#1a1a1a;padding:3px 10px;border-radius:20px;white-space:nowrap; }
.bar-action { background:#fff;border:none;border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;color:#1a1a1a;font-size:16px;cursor:pointer;text-decoration:none;flex-shrink:0; }
.bar-action:active { background:#f0f0f0; }
.order-meta { padding:8px 16px;display:flex;gap:12px;align-items:center;border-bottom:1px solid #f0f2f5;flex-wrap:wrap;font-size:13px; }
.nr-rej { display:inline-block;background:#fff;border:2px solid #1a1a1a;padding:1px 6px;border-radius:4px;font-weight:800;font-size:12px; }
.items-list { padding:8px 16px;border-bottom:1px solid #f0f2f5; }
.item-row { display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #f8f9fa;font-size:13px; }
.item-row:last-child { border-bottom:none; }
.item-name { font-weight:700; }
.item-bales { font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;color:#1a1a1a; }
.summary { padding:8px 16px;display:flex;justify-content:space-between;border-top:2px solid #e2e5e9;font-size:12px;font-weight:700;color:#555; }
.summary-val { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#1a1a1a; }
.weight-bar { padding:8px 16px;background:#fef9e7;border-top:1px solid #fdebd0;display:flex;justify-content:space-between;align-items:center; }
.wl { font-size:11px;font-weight:700;color:#935810;text-transform:uppercase;letter-spacing:.06em; }
.wv { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;color:#935810; }
.section-sep { font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#aaa;margin:14px 0 8px; }
.empty-state { text-align:center;padding:48px 20px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.dashboard') }}'"
        style="display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer">
    <i class="fas fa-home"></i> Powrót
</button>

<div class="page-title">Załadunki</div>

@php
    $activeOrders = $orders->filter(fn($o) => $o->status !== 'loaded');
    $closedOrders = $orders->filter(fn($o) => $o->status === 'loaded');
@endphp

@if($activeOrders->isEmpty() && $closedOrders->isEmpty())
<div class="empty-state">
    <i class="fas fa-truck-moving"></i>
    <p style="font-size:15px;font-weight:600">Brak załadunków</p>
</div>
@endif

@foreach($activeOrders as $order)
@php $st = $placStatus($order); @endphp
<div class="order-card">
    <div class="order-bar {{ $order->loadingItems->isNotEmpty() ? 'bar-progress' : 'bar-planned' }}">
        <div class="bar-client">{{ $order->client?->short_name }}</div>
        <span class="bar-status">{{ $st['label'] }}</span>
        <a href="{{ route('plac.orders.loading', $order) }}" class="bar-action">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="order-meta">
        @if($order->driver)<span style="font-size:13px;color:#555">{{ $order->driver->name }}</span>@endif
        @if($order->tractor)<span class="nr-rej">{{ $order->tractor->plate }}</span>@endif
        @if($order->trailer)<span class="nr-rej">{{ $order->trailer->plate }}</span>@endif
    </div>
    @if($order->weight_netto)
    <div class="weight-bar">
        <span class="wl">Waga kierowcy</span>
        <span class="wv">{{ number_format($order->weight_netto * 1000, 0, ',', ' ') }} kg</span>
    </div>
    @endif
    @if($order->loadingItems->isNotEmpty())
    <div class="items-list">
        @foreach($order->loadingItems as $item)
        <div class="item-row">
            <span class="item-name">{{ $item->fraction?->name }}</span>
            <div style="display:flex;gap:10px;align-items:center">
                <span class="item-bales">{{ $item->bales }}</span>
                <span style="font-size:14px;font-weight:700;color:#1a1a1a">{{ number_format($item->weight_kg/1000, 3, ',', ' ') }} t</span>
            </div>
        </div>
        @endforeach
    </div>
    @if($order->loadingItems->count() > 1)
    <div class="summary">
        <span>Razem</span>
        <span class="summary-val">{{ $order->loadingItems->sum('bales') }} bel. · {{ number_format($order->loadingItems->sum('weight_kg')/1000, 3, ',', ' ') }} t</span>
    </div>
    @endif
    @endif
</div>
@endforeach

@if($closedOrders->isNotEmpty())
<div class="section-sep">✓ Zamknięte</div>
@foreach($closedOrders as $order)
<div class="order-card" style="opacity:.55">
    <div class="order-bar bar-done">
        <div class="bar-client" style="color:#1a1a1a">{{ $order->client?->short_name }}</div>
        <span class="bar-status" style="background:rgba(0,0,0,.1);color:#555">Zamknięty</span>
    </div>
    <div class="order-meta">
        @if($order->tractor)<span class="nr-rej">{{ $order->tractor->plate }}</span>@endif
        @if($order->trailer)<span class="nr-rej">{{ $order->trailer->plate }}</span>@endif
    </div>
    @if($order->loadingItems->isNotEmpty())
    <div class="summary" style="padding:10px 16px">
        <span>Razem</span>
        <span class="summary-val">{{ $order->loadingItems->sum('bales') }} bel. · {{ number_format($order->loadingItems->sum('weight_kg')/1000, 3, ',', ' ') }} t</span>
    </div>
    @endif
</div>
@endforeach
@endif

@endsection