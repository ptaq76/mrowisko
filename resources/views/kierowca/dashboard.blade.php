@extends('layouts.kierowca')

@section('title', 'Zlecenia')

@section('styles')
<style>
.order-card {
    border-radius: 16px;
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: 0 3px 12px rgba(0,0,0,.10);
}

/* Kolor karty zależny od typu */
.order-card.type-sale   { background: #fff; border-top: 5px solid #f39c12; }
.order-card.type-pickup { background: #fff; border-top: 5px solid #27ae60; }
.order-card.is-closed   { border-top-color: #b2bec3; opacity: .6; }

/* Nagłówek */
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
}
.status-planned    { background: rgba(255,255,255,.3); color: #fff; }
.status-loaded     { background: rgba(255,255,255,.3); color: #fff; }
.status-weighed    { background: rgba(255,255,255,.3); color: #fff; }
.status-delivered  { background: rgba(255,255,255,.3); color: #fff; }
.status-closed     { background: rgba(255,255,255,.2); color: rgba(255,255,255,.8); }

/* Meta */
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

/* Nr rejestracyjne */
.plates-row {
    padding: 8px 16px; display: flex; gap: 8px; align-items: center;
    border-bottom: 1px solid #f0f2f5;
}
.nr-rej {
    display: inline-block; background: white; border: 2px solid black;
    padding: 3px 8px; border-radius: 5px; font-weight: 800;
    font-size: 16px; letter-spacing: .05em;
}

/* Szczegóły */
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

/* LS */
.ls-number { font-size: 16px; font-weight: 600; color: #1a1a1a; }

/* Belki załadowane */
.loaded-bales-bar {
    padding: 8px 16px; background: #fef9e7;
    border-top: 1px solid #f9d38c;
    display: flex; justify-content: space-between; align-items: center;
}
.lb-label { font-size: 11px; font-weight: 700; color: #d68910; text-transform: uppercase; letter-spacing: .06em; }
.lb-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900; color: #d68910; }

/* Masa netto */
.netto-bar {
    background: #e8f7e4; padding: 10px 16px;
    border-top: 1px solid #d4edda;
    display: flex; justify-content: space-between; align-items: center;
}
.netto-label { font-size: 12px; font-weight: 700; color: #2d7a1a; text-transform: uppercase; letter-spacing: .06em; }
.netto-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 24px; font-weight: 900; color: #2d7a1a; }

/* Waga odbiorcy */
.receiver-weight-bar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 16px; background: #eaf4fb; border-top: 1px solid #cce0f5;
}
.rw-label { font-size: 11px; font-weight: 700; color: #922b21; text-transform: uppercase; letter-spacing: .06em; }
.rw-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900; color: #922b21; }
.btn-rw   {
    background: #922b21; border: none; border-radius: 8px;
    padding: 8px 14px; color: #fff; cursor: pointer;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 15px; font-weight: 800; letter-spacing: .06em;
    text-transform: uppercase; display: flex; align-items: center; gap: 6px;
}
.btn-rw:active { filter: brightness(.9); }

/* Uwagi – wyróżnione na dole */
.notes-bar {
    padding: 9px 16px; background: #fff8e1;
    border-top: 2px solid #ffe082;
    font-size: 13px; font-weight: 600; color: #6d4c00;
    display: flex; align-items: center; gap: 8px;
}

/* Przycisk PODAJ WAGĘ */
.btn-weigh {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 75%; margin: 12px auto; padding: 15px 16px;
    background: #922b21; color: #fff;
    text-decoration: none;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 20px; font-weight: 900;
    letter-spacing: .08em; text-transform: uppercase;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(146,43,33,.35);
}
.btn-weigh:active { filter: brightness(.88); }

/* Brak zleceń */
.no-orders { text-align: center; padding: 48px 20px; color: #aaa; }
.no-orders i { font-size: 48px; margin-bottom: 12px; display: block; }
.no-orders p { font-size: 16px; font-weight: 600; }
</style>
@endsection

@section('content')

@if($orders->isEmpty())
    <div class="no-orders">
        <i class="fas fa-calendar-check"></i>
        <p>Brak zleceń na ten dzień</p>
    </div>
@else
    @foreach($orders as $order)
    @if(!$loop->first)
    <hr style="border:none;border-top:2px solid #e2e5e9;margin:0 0 20px">
    @endif
    @php
        $statusLabels = [
            'planned'   => 'Zaplanowane',
            'loaded'    => 'Załadowane',
            'weighed'   => 'Zważone',
            'delivered' => 'Dostarczone',
            'closed'    => 'Zamknięte',
        ];
        $statusLabel = $order->weight_netto ? 'Zważone' : ($statusLabels[$order->status] ?? $order->status);
        $statusClass = $order->weight_netto ? 'status-weighed' : 'status-' . ($order->status ?? 'planned');
        $isSale      = $order->type === 'sale';
        $totalBales  = $order->loadingItems?->sum('bales') ?? 0;
        $isLoaded    = $order->status === 'loaded';
        $isDone      = $order->status === 'closed' || (!$isSale && $order->status === 'weighed');
    @endphp

    <div class="order-card {{ $isSale ? 'type-sale' : 'type-pickup' }} {{ $isDone ? 'is-closed' : '' }}" id="order-{{ $order->id }}">

        {{-- Nagłówek --}}
        <div class="order-header {{ $isDone ? '' : ($isSale ? 'order-header-sale' : 'order-header-pickup') }}" style="{{ $isDone ? 'background:#b2bec3' : '' }}">
            <div class="order-client-name">{{ $order->client?->short_name ?? '?' }}</div>
            <span class="order-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>

        {{-- Meta --}}
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

        {{-- Nr rejestracyjne --}}
        <div class="plates-row">
            <span class="nr-rej">{{ $order->tractor?->plate ?? '–' }}</span>
            @if($order->trailer)
                <span style="font-size:18px;font-weight:300;color:#aaa">/</span>
                <span class="nr-rej">{{ $order->trailer->plate }}</span>
            @endif
        </div>

        {{-- Szczegóły: towary, LS, importer, okienko --}}
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
            <div class="order-detail-row">
                <span class="detail-label">Okienko</span>
                <span class="detail-value">{{ $ls->time_window }}</span>
            </div>
            @endif
        </div>
        @endif

        {{-- Belki załadowane (gdy załadunek zamknięty) --}}
        @if($isLoaded && $totalBales > 0)
        <div class="loaded-bales-bar">
            <span class="lb-label">Załadowane belki</span>
            <span class="lb-val">{{ $totalBales }} bel.</span>
        </div>
        @endif

        {{-- Masa netto --}}
        @if($order->weight_netto)
        <div class="netto-bar">
            <span class="netto-label">Masa netto</span>
            <span class="netto-val">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</span>
        </div>
        @endif

        {{-- Waga odbiorcy --}}
        @if($order->weight_receiver)
        <div class="receiver-weight-bar">
            <div>
                <div class="rw-label">Waga odbiorcy</div>
                <div class="rw-val">{{ number_format($order->weight_receiver, 3, ',', ' ') }} t</div>
            </div>
            <button class="btn-rw" onclick="openReceiverWeight({{ $order->id }}, {{ $order->weight_receiver }})">
                <i class="fas fa-edit"></i> Edytuj
            </button>
        </div>
        @endif

        {{-- Uwagi – zawsze na końcu, wyróżnione --}}
        @if($order->notes)
        <div class="notes-bar">
            <i class="fas fa-exclamation-circle" style="color:#f39c12;font-size:16px;flex-shrink:0"></i>
            {{ $order->notes }}
        </div>
        @endif

        {{-- Akcje --}}
        @if($isSale)
            @if(in_array($order->status, ['planned', 'in_progress', 'loaded']))
                {{-- Waga samochodowa jeszcze nie podana --}}
                <div style="padding:12px 16px;text-align:center">
                    <a href="{{ route('kierowca.orders.weigh', $order) }}" class="btn-weigh"
                       style="display:flex;align-items:center;justify-content:center;gap:10px;width:75%;margin:0 auto;padding:15px 16px;background:#922b21;color:#fff;text-decoration:none;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;border-radius:10px;box-shadow:0 4px 12px rgba(146,43,33,.35)">
                        <i class="fas fa-weight fa-lg"></i> PODAJ WAGĘ
                    </a>
                </div>
            @elseif($order->status === 'weighed')
                {{-- Waga podana – teraz waga odbiorcy --}}
                @if(!$order->weight_receiver)
                <div style="padding:12px 16px;text-align:center">
                    <button onclick="openReceiverWeight({{ $order->id }}, null)"
                            style="display:flex;align-items:center;justify-content:center;gap:10px;width:75%;margin:0 auto;padding:15px 16px;background:#922b21;color:#fff;border:none;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;border-radius:10px;cursor:pointer;box-shadow:0 4px 12px rgba(146,43,33,.35)">
                        <i class="fas fa-weight fa-lg"></i> WAGA ODBIORCY
                    </button>
                </div>
                @endif
            @endif
        @else
            {{-- Odbiór --}}
            @if(in_array($order->status, ['planned', 'in_progress']))
                <div style="padding:12px 16px;text-align:center">
                    <a href="{{ route('kierowca.orders.weigh', $order) }}" class="btn-weigh"
                       style="display:flex;align-items:center;justify-content:center;gap:10px;width:75%;margin:0 auto;padding:15px 16px;background:#922b21;color:#fff;text-decoration:none;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;border-radius:10px;box-shadow:0 4px 12px rgba(146,43,33,.35)">
                        <i class="fas fa-weight fa-lg"></i> PODAJ WAGĘ
                    </a>
                </div>
            @endif
        @endif

    </div>
    @endforeach
@endif

{{-- Panel wagi odbiorcy --}}
<div id="rwOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:flex-end">
    <div style="background:#fff;border-radius:16px 16px 0 0;width:100%;padding:20px;animation:slideUp .2s ease">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
            <span style="font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900">Waga odbiorcy</span>
            <button onclick="closeRW()" style="background:#f0f2f5;border:none;border-radius:50%;width:32px;height:32px;font-size:16px;cursor:pointer">×</button>
        </div>
        <input type="number" id="rwInput" step="0.001" min="0" inputmode="decimal"
               style="width:100%;padding:16px;border:3px solid #e2e5e9;border-radius:10px;font-family:'Barlow Condensed',sans-serif;font-size:42px;font-weight:900;text-align:center;outline:none;-moz-appearance:textfield;margin-bottom:14px"
               placeholder="0.000">
        <div style="text-align:center;font-size:11px;color:#aaa;margin-bottom:14px;text-transform:uppercase;letter-spacing:.08em">tony netto [t]</div>
        <button onclick="saveRW()" style="width:100%;padding:16px;background:#922b21;color:#fff;border:none;border-radius:10px;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;cursor:pointer">
            <i class="fas fa-check"></i> ZAPISZ
        </button>
    </div>
</div>

@endsection

@section('scripts')
<style>
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
</style>
<script>
let _rwOrderId = null;

function openReceiverWeight(orderId, currentVal) {
    _rwOrderId = orderId;
    document.getElementById('rwInput').value = currentVal ?? '';
    const overlay = document.getElementById('rwOverlay');
    overlay.style.display = 'flex';
    setTimeout(() => document.getElementById('rwInput').focus(), 300);
}

function closeRW() {
    document.getElementById('rwOverlay').style.display = 'none';
}

async function saveRW() {
    const val = parseFloat(document.getElementById('rwInput').value);
    if (isNaN(val) || val <= 0) {
        Swal.fire({ icon: 'warning', title: 'Podaj wagę', timer: 1500, showConfirmButton: false });
        return;
    }
    const res  = await fetch(`/kierowca/orders/${_rwOrderId}/receiver-weight`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json', 'Accept': 'application/json',
        },
        body: JSON.stringify({ weight_receiver: val }),
    });
    const data = await res.json();
    if (data.success) {
        closeRW();
        await Swal.fire({
            icon: 'success', title: 'Zapisano!',
            html: `Waga odbiorcy: <strong>${val.toFixed(3).replace('.', ',')} t</strong>`,
            timer: 2000, showConfirmButton: false,
        });
        location.reload();
    } else {
        Swal.fire({ icon: 'error', title: 'Błąd', text: data.message });
    }
}
</script>
@endsection