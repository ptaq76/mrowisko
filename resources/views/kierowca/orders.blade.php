@extends('layouts.kierowca')

@section('title', 'Plan dnia')

@section('styles')
<style>
.back-btn { display:flex;align-items:center;gap:8px;color:#888;font-size:14px;font-weight:600;text-decoration:none;margin-bottom:14px; }
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;margin-bottom:12px; }
.order-card { background:#fff;border-radius:12px;margin-bottom:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08); }
.order-bar { display:flex;align-items:center;padding:11px 14px;gap:10px; }
.bar-days { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:rgba(255,255,255,.85);min-width:36px; }
.bar-type { display:flex;align-items:center;gap:6px;font-family:'Barlow Condensed',sans-serif;font-size:17px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:#fff;flex:1; }
.bar-action { background:rgba(255,255,255,.2);border:none;border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;cursor:pointer;text-decoration:none;flex-shrink:0; }
.bar-action:active { background:rgba(255,255,255,.35); }
.order-details { padding:12px 14px;display:flex;flex-direction:column;gap:6px; }
.detail-row { display:flex;align-items:flex-start;gap:8px;font-size:13px; }
.detail-icon { color:#aaa;width:16px;text-align:center;flex-shrink:0;margin-top:1px; }
.detail-text { color:#1a1a1a;font-weight:600; }
.detail-sub  { color:#888;font-size:12px; }
.nr-rej { display:inline-block;background:#fff;border:2px solid #1a1a1a;padding:1px 6px;border-radius:4px;font-weight:800;font-size:12px; }
.section-sep { font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#aaa;margin:14px 0 8px; }
.empty-state { text-align:center;padding:48px 20px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }
</style>
@endsection

@section('content')

<a href="{{ route('plac.dashboard') }}" class="back-btn">
    <i class="fas fa-arrow-left"></i> Powrót
</a>

<div class="page-title">Plan dnia</div>

@php
    $today = isset($date) ? $date->format('Y-m-d') : now()->format('Y-m-d');

    $activeOrders  = $orders->filter(fn($o) => $o->status !== 'loaded');
    $closedOrders  = $orders->filter(fn($o) => $o->status === 'loaded');
    $todayOrders   = $activeOrders->filter(fn($o) => $o->planned_date->format('Y-m-d') === $today);
    $overdueOrders = $activeOrders->filter(fn($o) => $o->planned_date->format('Y-m-d') < $today);

    $placStatus = function($order) {
        // Wysyłka
        if ($order->type === 'sale') {
            if ($order->status === 'loaded') return ['label' => 'Załadowane', 'done' => true];
            if ($order->loadingItems->isNotEmpty()) return ['label' => 'W trakcie', 'done' => false];
            return ['label' => 'Zaplanowane', 'done' => false];
        }
        // Odbiór
        if (in_array($order->status, ['delivered', 'closed'])) return ['label' => 'Dostarczone', 'done' => true];
        if ($order->status === 'weighed') return ['label' => 'Do przyjęcia', 'done' => false];
        return ['label' => 'Zaplanowane', 'done' => false];
    };
@endphp

@if($overdueOrders->isNotEmpty())
<div class="section-sep">⚠ Zaległe</div>
@foreach($overdueOrders as $order)
    @include('plac._plan_card', ['order' => $order, 'placStatus' => $placStatus])
@endforeach
@endif

@if($todayOrders->isNotEmpty())
@if($overdueOrders->isNotEmpty())<div class="section-sep">Dziś</div>@endif
@foreach($todayOrders as $order)
    @include('plac._plan_card', ['order' => $order, 'placStatus' => $placStatus])
@endforeach
@elseif($overdueOrders->isEmpty())
<div class="empty-state">
    <i class="fas fa-calendar-check"></i>
    <p style="font-size:15px;font-weight:600">Brak zleceń na dziś</p>
</div>
@endif

@if($closedOrders->isNotEmpty())
<div class="section-sep" style="color:#bbb;margin-top:20px">✓ Zamknięte</div>
@foreach($closedOrders as $order)
    @include('plac._plan_card', ['order' => $order, 'placStatus' => $placStatus])
@endforeach
@endif

@endsection
