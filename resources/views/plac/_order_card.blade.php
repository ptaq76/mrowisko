<div class="order-card">
    <div class="order-header {{ $order->status ?? 'planned' }}">
        <div class="order-client">{{ $order->client?->short_name ?? '?' }}</div>
        <span class="order-status-pill">{{ $statusLabels[$order->status] ?? $order->status }}</span>
    </div>

    <div class="order-meta">
        <span class="order-type {{ $order->type === 'pickup' ? 'type-pickup' : 'type-sale' }}">
            {{ $order->type === 'pickup' ? '↓ Odbiór' : '↑ Wysyłka' }}
        </span>
        @if($order->planned_time)
            <span>{{ substr($order->planned_time, 0, 5) }}</span>
        @endif
        @if($order->tractor)
            <span class="nr-rej">{{ $order->tractor->plate }}</span>
            @if($order->trailer)
                <span style="color:#ccc">/</span>
                <span class="nr-rej">{{ $order->trailer->plate }}</span>
            @endif
        @endif
        @if($order->driver)
            <span style="color:#888">{{ $order->driver->name }}</span>
        @endif
    </div>

    {{-- Pozycje załadunku --}}
    <div class="loading-items" id="items-{{ $order->id }}">
        @if($order->loadingItems->isEmpty())
            <div class="no-items">Brak pozycji załadunku</div>
        @else
            @foreach($order->loadingItems as $item)
            <div class="loading-item-row" id="item-row-{{ $item->id }}">
                <div>
                    <div class="loading-fraction">{{ $item->fraction?->name ?? '?' }}</div>
                    @if($item->notes)
                        <div style="font-size:11px;color:#aaa">{{ $item->notes }}</div>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="text-align:right">
                        <div class="loading-bales">{{ $item->bales }} bel.</div>
                        @if($item->weight_kg > 0)
                            <div class="loading-weight">≈ {{ number_format($item->weight_kg, 0, ',', ' ') }} kg</div>
                        @endif
                    </div>
                    <button onclick="removeItem({{ $order->id }}, {{ $item->id }})"
                            style="background:#fdecea;border:none;border-radius:6px;padding:6px 8px;color:#e74c3c;cursor:pointer">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
            @endforeach

            {{-- Podsumowanie --}}
            <div style="padding:8px 0 2px;display:flex;justify-content:space-between;font-size:12px;border-top:1px solid #e2e5e9;margin-top:6px">
                <span style="font-weight:700;color:#555">RAZEM</span>
                <span style="font-weight:800;color:#1a1a1a">
                    {{ $order->loadingItems->sum('bales') }} bel.
                    / ≈ {{ number_format($order->loadingItems->sum('weight_kg'), 0, ',', ' ') }} kg
                </span>
            </div>
        @endif
    </div>

    {{-- Przycisk dodaj --}}
    <a href="{{ route('plac.orders.loading', $order) }}" class="btn-load">
        <i class="fas fa-plus-circle"></i> DODAJ DO ZAŁADUNKU
    </a>
</div>
