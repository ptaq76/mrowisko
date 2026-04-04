@php
    $st = $placStatus($order);
    $isDone   = $st['done'];
    $daysAgo  = (int) $order->planned_date->diffInDays(now(), false);
    $daysLabel= $daysAgo === 0 ? 'Dziś' : ($daysAgo > 0 ? "+{$daysAgo}d" : '');

    if ($isDone) {
        $barBg = '#b2bec3';
    } elseif ($order->type === 'sale') {
        $barBg = $order->loadingItems->isNotEmpty() ? '#e67e22' : '#f39c12'; // ciemniejszy gdy w trakcie
    } else {
        $barBg = $order->loadingItems->isNotEmpty() ? '#27ae60' : '#2ecc71'; // odbiór zielony
    }

    $isExport = $order->type === 'sale' && $order->lieferschein;
@endphp

<div class="order-card" style="{{ $isDone ? 'opacity:.55' : '' }}">

    {{-- Górna belka --}}
    <div class="order-bar" style="background:{{ $barBg }}">
        <div class="bar-days">{{ $daysLabel }}</div>

        <div class="bar-type">
            <i class="fas {{ $order->type === 'pickup' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
            {{ $order->type === 'pickup' ? 'ODBIÓR' : 'ZAŁADUNEK' }}
            <span style="font-size:10px;font-weight:700;background:rgba(255,255,255,.25);padding:2px 8px;border-radius:10px;margin-left:6px;letter-spacing:.06em">
                {{ $st['label'] }}
            </span>
        </div>

        @if(!$isDone)
        <a href="{{ $order->type === 'sale' ? route('plac.orders.loading', $order) : route('plac.delivery.form', $order) }}"
           class="bar-action" title="Realizuj">
            <i class="fas fa-arrow-right"></i>
        </a>
        @else
        <span class="bar-action" style="opacity:.4">
            <i class="fas fa-check"></i>
        </span>
        @endif
    </div>

    {{-- Szczegóły --}}
    <div class="order-details">

        <div class="detail-row">
            <i class="fas fa-building detail-icon"></i>
            <div>
                <span class="detail-text">{{ $order->client?->short_name ?? '?' }}</span>
                @if($order->planned_time)
                    <span class="detail-sub"> · {{ substr($order->planned_time, 0, 5) }}</span>
                @endif
            </div>
        </div>

        @if($order->loadingItems->isNotEmpty())
        <div class="detail-row">
            <i class="fas fa-boxes detail-icon"></i>
            <div>
                @foreach($order->loadingItems as $li)
                <span class="detail-text">{{ $li->fraction?->name }}</span>
                <span class="detail-sub"> {{ $li->bales }} bel.</span>
                @if(!$loop->last) · @endif
                @endforeach
            </div>
        </div>
        @elseif($order->fractions_note)
        <div class="detail-row">
            <i class="fas fa-box detail-icon"></i>
            <span class="detail-text">{{ $order->fractions_note }}</span>
        </div>
        @endif

        @if($order->tractor || $order->trailer)
        <div class="detail-row">
            <i class="fas fa-truck detail-icon"></i>
            <div>
                @if($order->tractor)
                    <span class="nr-rej">{{ $order->tractor->plate }}</span>
                @endif
                @if($order->trailer)
                    <span style="color:#ccc;margin:0 3px">/</span>
                    <span class="nr-rej">{{ $order->trailer->plate }}</span>
                @endif
            </div>
        </div>
        @endif

        @if($isExport)
        <div class="detail-row">
            <i class="fas fa-ship detail-icon"></i>
            <div>
                <span class="detail-text">{{ $order->lieferschein->importer?->name }}</span>
                <span class="detail-sub"> · LS {{ $order->lieferschein->number }}</span>
            </div>
        </div>
        @endif

        @if($order->notes)
        <div class="detail-row">
            <i class="fas fa-comment detail-icon"></i>
            <span class="detail-sub">{{ $order->notes }}</span>
        </div>
        @endif

    </div>
</div>
