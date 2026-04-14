@extends('layouts.kierowca')

@section('title', 'Moje kursy')

@section('styles')
<style>
.page-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase;
    color: #1a1a1a; margin-bottom: 14px;
}

/* Filtr */
.filter-bar {
    background: #fff;
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
}
.filter-bar select {
    width: 100%;
    padding: 10px 12px;
    border: 1.5px solid #dde0e5;
    border-radius: 8px;
    font-size: 15px;
    font-family: 'Barlow', sans-serif;
    outline: none;
    -webkit-appearance: none;
    background: #fff;
    color: #1a1a1a;
}

/* Karta kursu */
.kurs-card {
    background: #fff;
    border-radius: 12px;
    margin-bottom: 8px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
    display: flex;
    align-items: stretch;
}

.kurs-type-bar {
    width: 6px;
    flex-shrink: 0;
}
.type-sale-bar   { background: #f39c12; }
.type-pickup-bar { background: #27ae60; }

.kurs-body {
    flex: 1;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.kurs-date {
    font-size: 12px;
    color: #999;
    font-weight: 700;
    white-space: nowrap;
    min-width: 52px;
}

.kurs-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 20px;
    font-weight: 900;
    color: #1a1a1a;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.kurs-netto {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 16px;
    font-weight: 800;
    color: #555;
    white-space: nowrap;
    text-align: right;
    min-width: 64px;
}

.empty-state {
    text-align: center; padding: 48px 20px; color: #aaa;
}
.empty-state i { font-size: 40px; margin-bottom: 10px; display: block; }
</style>
@endsection

@section('content')

<div class="page-title"><i class="fas fa-route"></i> Moje kursy</div>

<div class="filter-bar">
    <select id="filterClient" onchange="filterKursy()">
        <option value="">– Wszyscy klienci –</option>
        @foreach($clients as $c)
        <option value="{{ $c->id }}">{{ $c->short_name }}</option>
        @endforeach
    </select>
</div>

<div id="kursyList">
    @forelse($orders as $order)
    @php $isSale = $order->type === 'sale'; @endphp
    <div class="kurs-card" data-client="{{ $order->client_id }}">
        <div class="kurs-type-bar {{ $isSale ? 'type-sale-bar' : 'type-pickup-bar' }}"></div>
        <div class="kurs-body">
            <div class="kurs-date">{{ $order->planned_date->format('d.m') }}</div>

            <div style="flex:1;min-width:0">
                <div class="kurs-client">{{ $order->client?->short_name ?? '?' }}</div>
                <div style="display:flex;gap:6px;align-items:center;margin-top:3px;flex-wrap:wrap">
                    @if($order->tractor)
                    <span style="font-size:10px;font-weight:800;background:#fff;border:1.5px solid #1a1a1a;padding:1px 5px;border-radius:3px;letter-spacing:.03em">{{ $order->tractor->plate }}</span>
                    @endif
                    @if($order->trailer)
                    <span style="font-size:10px;font-weight:800;background:#fff;border:1.5px solid #1a1a1a;padding:1px 5px;border-radius:3px;letter-spacing:.03em">{{ $order->trailer->plate }}</span>
                    @endif
                    @if($order->lieferschein)
                    <span style="font-size:10px;color:#aaa;font-weight:600">LS {{ $order->lieferschein->number }}</span>
                    @endif
                </div>
            </div>

            @if($order->weight_netto)
            <div class="kurs-netto">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</div>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-route"></i>
        <p>Brak kursów</p>
    </div>
    @endforelse
</div>

@endsection

@section('scripts')
<script>
function filterKursy() {
    const clientId = document.getElementById('filterClient').value;
    document.querySelectorAll('.kurs-card').forEach(card => {
        if (!clientId || card.dataset.client === clientId) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endsection
