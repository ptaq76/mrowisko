@extends('layouts.plac')

@section('title', 'Plan dnia')

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

<button type="button"
        onclick="window.location.href='{{ route('plac.dashboard') }}'"
        style="display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer">
    <i class="fas fa-home"></i> Powrót
</button>

<div class="page-title">Plan dnia</div>

<div id="poll-area">
{{-- ══ ZADANIA ══ --}}
@if(isset($zadania) && $zadania->isNotEmpty())
<div style="background:#fff8e1;border:2px solid #f9d38c;border-radius:12px;margin-bottom:14px;overflow:hidden">
    <div style="padding:8px 12px;background:#f9d38c;font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#6d4c00">
        <i class="fas fa-tasks"></i> Zadania
    </div>
    @foreach($zadania as $z)
    <div style="padding:10px 12px;border-top:1px solid #f0e0a0;display:flex;align-items:center;gap:10px;{{ $z->status === 'done' ? 'opacity:.5' : '' }}">
        @if($z->status === 'done')
            <i class="fas fa-check-circle text-success" style="font-size:18px"></i>
        @else
            <i class="far fa-circle text-muted" style="font-size:18px"></i>
        @endif
        <span style="flex:1;font-size:13px;font-weight:600;{{ $z->status === 'done' ? 'text-decoration:line-through;color:#888' : 'color:#1a1a1a' }}">
            {{ $z->tresc }}
        </span>
        @if($z->status === 'pending')
        <button onclick="wykonajZadanie({{ $z->id }})"
                style="background:#27ae60;color:#fff;border:none;border-radius:8px;padding:6px 12px;font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;cursor:pointer">
            <i class="fas fa-check"></i> Wykonaj
        </button>
        @endif
    </div>
    @endforeach
</div>
@endif

@php
    $realToday   = now()->format('Y-m-d');
    $selectedDay = $date->format('Y-m-d');
    $isToday     = $selectedDay === $realToday;

    $activeOrders  = $orders->filter(fn($o) => $o->status !== 'loaded');
    $closedOrders  = $orders->filter(fn($o) => $o->status === 'loaded');
    $selectedOrders = $activeOrders->filter(fn($o) => optional($o->plac_date)->format('Y-m-d') === $selectedDay);
    $overdueOrders  = $isToday
        ? $activeOrders->filter(fn($o) => optional($o->plac_date)->format('Y-m-d') < $realToday)
        : collect();

    $placStatus = function($order) {
        // Wysyłka
        if ($order->type === 'sale') {
            if ($order->status === 'loaded') return ['label' => 'Załadowane', 'done' => true];
            if ($order->status === 'weighed') {
                if ($order->loadingItems->isNotEmpty()) return ['label' => 'W trakcie', 'done' => false];
                return ['label' => 'Zważone', 'done' => false];
            }
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

@if($selectedOrders->isNotEmpty())
@if($overdueOrders->isNotEmpty())<div class="section-sep">Dziś</div>@endif
@foreach($selectedOrders as $order)
    @include('plac._plan_card', ['order' => $order, 'placStatus' => $placStatus])
@endforeach
@elseif($overdueOrders->isEmpty())
<div class="empty-state">
    <i class="fas fa-calendar-check"></i>
    <p style="font-size:15px;font-weight:600">Brak zleceń na ten dzień</p>
</div>
@endif

@if($closedOrders->isNotEmpty())
<div class="section-sep" style="color:#bbb;margin-top:20px">✓ Zamknięte</div>
@foreach($closedOrders as $order)
    @include('plac._plan_card', ['order' => $order, 'placStatus' => $placStatus])
@endforeach
@endif

</div>{{-- /poll-area --}}

@endsection

@section('scripts')
<script>
async function wykonajZadanie(id) {
    const result = await Swal.fire({
        title: 'Czy zapisać wykonanie zadania?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Tak',
        cancelButtonText: 'Nie',
        confirmButtonColor: '#27ae60',
    });
    if (!result.isConfirmed) return;
    const fd = new FormData();
    fd.append('_token', '{{ csrf_token() }}');
    await fetch(`/plac/zadania/${id}/wykonaj`, { method: 'POST', body: fd });
    location.reload();
}

// POLLING: Plan dnia odświeża się sam co 5s
if (window.pollPageFragment) {
    window.pollPageFragment('poll-area', 5000);
}
</script>
@endsection