@extends('layouts.hakowiec')

@section('title', 'Plan dnia')

@section('styles')
<style>
.order-card {
    border-radius: 16px;
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: 0 3px 12px rgba(0,0,0,.10);
}

.order-card.type-sale   { background: #fff; border-top: 5px solid #f39c12; }
.order-card.type-pickup { background: #fff; border-top: 5px solid #27ae60; }
.order-card.is-closed   { border-top-color: #b2bec3; opacity: .6; }

.order-header-sale   { background: #f39c12; }
.order-header-pickup { background: #27ae60; }

.order-header {
    padding: 12px 16px;
    display: flex; align-items: center; justify-content: space-between;
}

.order-client-name {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 28px; font-weight: 900;
    letter-spacing: .03em; color: #fff; line-height: 1;
}

.order-status-badge {
    font-size: 10px; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    padding: 3px 10px; border-radius: 20px; white-space: nowrap;
    background: rgba(255,255,255,.3); color: #fff;
}

.order-meta {
    padding: 8px 16px;
    display: flex; gap: 12px; align-items: center;
    border-bottom: 1px solid #f0f2f5; flex-wrap: wrap;
    font-size: 13px;
}
.order-type-label {
    font-weight: 800; font-size: 12px; text-transform: uppercase;
    letter-spacing: .06em;
}
.type-sale-text   { color: #e67e22; }
.type-pickup-text { color: #27ae60; }

.order-time-display {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 800; color: #1a1a1a;
}

.plates-row {
    padding: 8px 16px; display: flex; gap: 8px; align-items: center;
    border-bottom: 1px solid #f0f2f5;
}
.nr-rej {
    display: inline-block; background: white; border: 2px solid black;
    padding: 3px 8px; border-radius: 5px; font-weight: 800;
    font-size: 16px; letter-spacing: .05em;
}

.order-details {
    padding: 10px 16px; border-bottom: 1px solid #e9ecef;
}
.order-detail-row {
    display: flex; gap: 8px; margin-bottom: 6px;
    font-size: 13px; align-items: flex-start;
}
.order-detail-row:last-child { margin-bottom: 0; }
.detail-label {
    font-weight: 700; color: #aaa; font-size: 11px;
    text-transform: uppercase; letter-spacing: .06em;
    min-width: 70px; padding-top: 1px;
}
.detail-value { color: #1a1a1a; font-size: 14px; line-height: 1.4; }

.ls-number { font-size: 16px; font-weight: 600; color: #1a1a1a; }

.netto-bar {
    background: #e8f7e4; padding: 10px 16px;
    border-top: 1px solid #d4edda;
    display: flex; justify-content: space-between; align-items: center;
}
.netto-label { font-size: 12px; font-weight: 700; color: #2d7a1a; text-transform: uppercase; letter-spacing: .06em; }
.netto-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 24px; font-weight: 900; color: #2d7a1a; }

.notes-bar {
    padding: 9px 16px; background: #fff8e1;
    border-top: 2px solid #ffe082;
    font-size: 13px; font-weight: 600; color: #6d4c00;
    display: flex; align-items: center; gap: 8px;
}

.containers-bar {
    padding: 8px 16px; background: #f0f4ff;
    border-top: 1px solid #d6e0ff;
    font-size: 12px; color: #1a3672;
}
.containers-bar .ct-label { font-weight: 700; text-transform: uppercase; letter-spacing: .06em; font-size: 10px; color: #5468a8; margin-bottom: 4px; }
.containers-bar .ct-row { display: flex; gap: 6px; align-items: center; margin-bottom: 2px; }
.containers-bar .ct-tag { background: #fff; border: 1px solid #c4cee8; border-radius: 4px; padding: 2px 6px; font-weight: 700; font-size: 11px; }
.containers-bar .ct-dir-drop { color: #c0392b; }
.containers-bar .ct-dir-pick { color: #27ae60; }

.btn-action {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 75%; margin: 12px auto; padding: 15px 16px;
    color: #fff; text-decoration: none;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 18px; font-weight: 900;
    letter-spacing: .08em; text-transform: uppercase;
    border-radius: 10px;
    border: none; cursor: pointer;
}
.btn-action.btn-start  { background: #27ae60; box-shadow: 0 4px 12px rgba(39,174,96,.35); }
.btn-action.btn-swap   { background: #2980b9; box-shadow: 0 4px 12px rgba(41,128,185,.35); }
.btn-action.btn-weigh  { background: #922b21; box-shadow: 0 4px 12px rgba(146,43,33,.35); }
.btn-action:active { filter: brightness(.88); }
.btn-action[disabled], .btn-action.disabled { background: #bbb; box-shadow: none; cursor: not-allowed; }

.no-orders { text-align: center; padding: 48px 20px; color: #aaa; }
.no-orders i { font-size: 48px; margin-bottom: 12px; display: block; }
.no-orders p { font-size: 16px; font-weight: 600; }
</style>
@endsection

@section('content')

@if(!$driver)
    <div class="alert alert-warning">Brak przypisanego kierowcy dla tego konta.</div>
@endif

{{-- ── ZADANIA ── --}}
@if($zadania->isNotEmpty())
<div style="background:#fff8e1;border:2px solid #f9d38c;border-radius:12px;margin-bottom:20px;overflow:hidden">
    <div style="padding:10px 14px;background:#f9d38c;font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#6d4c00">
        <i class="fas fa-tasks"></i> Zadania na dziś
    </div>
    @foreach($zadania as $z)
    <div style="padding:12px 14px;border-top:1px solid #f0e0a0;display:flex;align-items:center;gap:10px;{{ $z->status === 'done' ? 'opacity:.5' : '' }}">
        @if($z->status === 'done')
            <i class="fas fa-check-circle text-success" style="font-size:20px"></i>
        @else
            <i class="far fa-circle text-muted" style="font-size:20px"></i>
        @endif
        <span style="flex:1;font-size:14px;font-weight:600;{{ $z->status === 'done' ? 'text-decoration:line-through;color:#888' : 'color:#1a1a1a' }}">
            {{ $z->tresc }}
        </span>
        @if($z->status === 'pending')
        <button onclick="wykonajZadanie({{ $z->id }})"
                style="background:#27ae60;color:#fff;border:none;border-radius:8px;padding:8px 14px;font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;cursor:pointer">
            <i class="fas fa-check"></i> Wykonaj
        </button>
        @endif
    </div>
    @endforeach
</div>
@endif

{{-- ── ORDERS ── --}}
@if($driver && $orders->isEmpty())
    <div class="no-orders">
        <i class="fas fa-calendar-check"></i>
        <p>Brak zleceń na ten dzień</p>
    </div>
@endif

@foreach($orders as $order)
@php
    $statusLabels = [
        'planned'     => 'Zaplanowane',
        'in_progress' => 'W trakcie',
        'loading'     => 'Załadunek',
        'loaded'      => 'Załadowane',
        'weighed'     => 'Zważone',
        'classified'  => 'Sklasyfikowane',
        'delivered'   => 'Dostarczone',
        'closed'      => 'Zamknięte',
    ];
    $statusLabel = $statusLabels[$order->status] ?? $order->status;
    $isSale      = $order->type === 'sale';
    $isDone      = $order->status === 'closed' || (!$isSale && $order->status === 'weighed');

    $dropContainers = $order->orderContainers->where('direction', 'drop');
    $pickContainers = $order->orderContainers->where('direction', 'pickup');
@endphp

<div class="order-card {{ $isSale ? 'type-sale' : 'type-pickup' }} {{ $isDone ? 'is-closed' : '' }}" id="order-{{ $order->id }}">

    <div class="order-header {{ $isDone ? '' : ($isSale ? 'order-header-sale' : 'order-header-pickup') }}" style="{{ $isDone ? 'background:#b2bec3' : '' }}">
        <div class="order-client-name">{{ $order->client?->short_name ?? '?' }}</div>
        <span class="order-status-badge">{{ $statusLabel }}</span>
    </div>

    <div class="order-meta">
        <span class="order-type-label {{ $isSale ? 'type-sale-text' : 'type-pickup-text' }}">
            <i class="fas {{ $isSale ? 'fa-arrow-up-from-bracket' : 'fa-arrow-down-to-bracket' }}"></i>
            {{ $isSale ? 'Wysyłka' : 'Odbiór' }}
        </span>
        @if($order->planned_time)
            <div class="order-time-display">{{ substr($order->planned_time, 0, 5) }}</div>
        @endif
        @if($order->startClient)
            <span style="font-size:12px;color:#888">
                <i class="fas fa-map-marker-alt"></i> {{ $order->startClient->short_name }}
            </span>
        @endif
    </div>

    <div class="plates-row">
        <span class="nr-rej">{{ $order->tractor?->plate ?? '–' }}</span>
        @if($order->trailer)
            <span style="font-size:18px;font-weight:300;color:#aaa">/</span>
            <span class="nr-rej">{{ $order->trailer->plate }}</span>
        @endif
    </div>

    @if($order->fractions_note || $order->lieferschein)
    <div class="order-details">
        @if($order->fractions_note)
        <div class="order-detail-row">
            <span class="detail-label">Towary</span>
            <span class="detail-value">{{ $order->fractions_note }}</span>
        </div>
        @endif

        @if($order->lieferschein)
        @php $ls = $order->lieferschein; @endphp
        <div class="order-detail-row">
            <span class="detail-label">LS</span>
            <span class="ls-number">{{ $ls->number }}</span>
        </div>
        <div class="order-detail-row">
            <span class="detail-label">Importer</span>
            <span class="detail-value">{{ $ls->importer?->name ?? '–' }}</span>
        </div>
        @endif
    </div>
    @endif

    {{-- Kontenery wymiany --}}
    @if($dropContainers->isNotEmpty() || $pickContainers->isNotEmpty())
    <div class="containers-bar">
        @if($dropContainers->isNotEmpty())
        <div class="ct-label">Zostawia u klienta</div>
        @foreach($dropContainers as $oc)
            <div class="ct-row ct-dir-drop">
                <i class="fas fa-arrow-down"></i>
                <span class="ct-tag">{{ $oc->container?->name ?? '?' }}</span>
                <small>({{ $oc->slot === 'tractor' ? 'ciągnik' : 'przyczepa' }})</small>
            </div>
        @endforeach
        @endif

        @if($pickContainers->isNotEmpty())
        <div class="ct-label" style="margin-top:6px">Zabiera od klienta</div>
        @foreach($pickContainers as $oc)
            <div class="ct-row ct-dir-pick">
                <i class="fas fa-arrow-up"></i>
                <span class="ct-tag">{{ $oc->container?->name ?? '?' }}</span>
                <small>({{ $oc->slot === 'tractor' ? 'ciągnik' : 'przyczepa' }})</small>
            </div>
        @endforeach
        @endif
    </div>
    @endif

    @if($order->weight_netto)
    <div class="netto-bar">
        <span class="netto-label">Masa netto</span>
        <span class="netto-val">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</span>
    </div>
    @endif

    @if($order->notes)
    <div class="notes-bar">
        <i class="fas fa-exclamation-circle" style="color:#f39c12;font-size:16px;flex-shrink:0"></i>
        {{ $order->notes }}
    </div>
    @endif

    {{-- Akcje --}}
    @if(!$isDone)
        @if($order->status === 'planned')
            <div style="padding:0 16px">
                <button class="btn-action btn-start" onclick="rozpocznijZlecenie({{ $order->id }})">
                    <i class="fas fa-play"></i> Rozpocznij
                </button>
            </div>
        @endif

        @if(!$isSale && $order->status === 'in_progress' && $pickContainers->isEmpty())
            <div style="padding:0 16px">
                <button class="btn-action btn-swap disabled" disabled title="W przygotowaniu (Krok 6b)">
                    <i class="fas fa-exchange-alt"></i> Wymiana kontenerów
                </button>
            </div>
        @endif

        @if($order->status === 'in_progress' && (!$isSale ? $pickContainers->isNotEmpty() : true))
            <div style="padding:0 16px">
                <button class="btn-action btn-weigh disabled" disabled title="W przygotowaniu (Krok 6c)">
                    <i class="fas fa-weight"></i> Podaj wagę
                </button>
            </div>
        @endif
    @endif

</div>
@endforeach

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
    await fetch(`/hakowiec/zadania/${id}/wykonaj`, { method: 'POST', body: fd });
    location.reload();
}

async function rozpocznijZlecenie(orderId) {
    const result = await Swal.fire({
        title: 'Rozpocząć zlecenie?',
        text: 'Status zmieni się na „W trakcie".',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Tak, rozpocznij',
        cancelButtonText: 'Anuluj',
        confirmButtonColor: '#27ae60',
    });
    if (!result.isConfirmed) return;

    const res = await fetch(`/hakowiec/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ status: 'in_progress' }),
    });
    const data = await res.json();
    if (data.success) {
        location.reload();
    } else {
        Swal.fire({ icon: 'error', title: 'Błąd', text: data.message || 'Nie udało się.' });
    }
}
</script>
@endsection
