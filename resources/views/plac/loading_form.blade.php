@extends('layouts.plac')

@section('title', 'Załadunek')

@section('styles')
<style>
/* ── NAGŁÓWEK ZLECENIA ── */
.load-header {
    background: var(--yellow);
    border-radius: var(--radius-card);
    padding: 16px 18px;
    margin-bottom: 12px;
}
.lh-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 36px; font-weight: 900; color: #111; line-height: 1;
    text-transform: uppercase;
}
.lh-meta {
    margin-top: 8px;
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
}
.lh-driver { font-size: 13px; font-weight: 600; color: rgba(0,0,0,.6); }
.lh-weight {
    margin-top: 10px;
    background: rgba(0,0,0,.12);
    border-radius: 8px; padding: 8px 14px;
    display: flex; align-items: center; gap: 10px;
}
.lhw-label { font-size: 11px; font-weight: 700; color: rgba(0,0,0,.55); text-transform: uppercase; letter-spacing: .06em; }
.lhw-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 28px; font-weight: 900; color: #111; }

/* ── TABELA TOWARÓW ── */
.items-card {
    background: var(--bg-card);
    border-radius: var(--radius-card);
    overflow: hidden;
    border: 1px solid var(--border);
    box-shadow: 0 2px 6px rgba(0,0,0,.06);
    margin-bottom: 12px;
}
.items-table { width: 100%; border-collapse: collapse; }

.items-table thead tr { background: #fdebd0; }
.items-table th {
    padding: 9px 8px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: #935810;
    text-align: left;
}
.items-table th.r { text-align: right; }

.items-table td {
    padding: 11px 8px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}
.items-table tr:last-child td { border-bottom: none; }

.it-name   { font-weight: 700; font-size: 16px; color: #111; }
.it-bales  { font-family: 'Barlow Condensed', sans-serif; font-size: 18px; font-weight: 900; color: #111; }
.it-weight {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 16px; font-weight: 900; color: #444;
    display: block; text-align: right; white-space: nowrap;
}

.del-btn {
    background: #fdecea; border: none; border-radius: 7px;
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    color: #e74c3c; cursor: pointer; font-size: 15px;
    margin-left: auto;
}
.del-btn:active { background: #e74c3c; color: #fff; }

/* Suma */
.summary-row {
    background: #f8f9fa;
    padding: 10px 16px;
    display: flex; justify-content: space-between; align-items: center;
    border-top: 2px solid var(--yellow);
}
.sum-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; }
.sum-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 18px; font-weight: 900; color: #111; }

.press-hint {
    text-align: center; font-size: 11px; color: #bbb;
    padding: 7px; font-style: italic;
}
.empty-items { text-align: center; padding: 28px; color: #ccc; font-size: 14px; }

/* Row press state */
.item-row { touch-action: none; user-select: none; transition: background .1s; }
.item-row.pressing { background: #fef9e7 !important; }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.loading.index') }}'"
        class="btn-back">
    <i class="fas fa-home"></i> Powrót
</button>

{{-- Nagłówek --}}
<div class="load-header">
    <div class="lh-client">{{ $order->client?->short_name ?? '?' }}</div>
    <div class="lh-meta">
        @if($order->driver)<span class="lh-driver">{{ $order->driver->name }}</span>@endif
        @if($order->tractor)<span class="plate-badge" style="border-color:rgba(0,0,0,.4)">{{ $order->tractor->plate }}</span>@endif
        @if($order->trailer)<span class="plate-badge" style="border-color:rgba(0,0,0,.4)">{{ $order->trailer->plate }}</span>@endif
    </div>
    @if($order->weight_netto)
    <div class="lh-weight">
        <span class="lhw-label">Waga kierowcy</span>
        <span class="lhw-val">{{ number_format($order->weight_netto * 1000, 0, ',', ' ') }} kg</span>
    </div>
    @endif
</div>

{{-- Tabela towarów --}}
<div class="items-card">
    <table class="items-table">
        <thead>
            <tr>
                <th>Towar</th>
                <th>Bel.</th>
                <th class="r">Waga&nbsp;kg</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="itemsBody">
            @forelse($order->loadingItems as $item)
            <tr id="ir-{{ $item->id }}"
                class="item-row"
                data-edit="{{ route('plac.orders.loading.edit', [$order, $item]) }}"
                onpointerdown="startPress(this)"
                onpointerup="endPress()"
                onpointerleave="endPress()">
                <td><span class="it-name">{{ $item->fraction?->name ?? '?' }}</span></td>
                <td><span class="it-bales">{{ $item->bales }}</span></td>
                <td><span class="it-weight">{{ number_format($item->weight_kg, 0, ',', ' ') }}</span></td>
                <td>
                    <button class="del-btn" onclick="deleteItem({{ $item->id }})" title="Usuń">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-items">Brak towarów – dodaj pierwszą pozycję</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($order->loadingItems->isNotEmpty())
    <div class="press-hint"><i class="fas fa-hand-pointer"></i> Przytrzymaj wiersz aby edytować</div>
    @if($order->loadingItems->count() > 1)
    <div class="summary-row">
        <span class="sum-label">Razem</span>
        <span class="sum-val">
            {{ $order->loadingItems->sum('bales') }} bel.
            &nbsp;·&nbsp;
            {{ number_format($order->loadingItems->sum('weight_kg'), 0, ',', ' ') }} kg
        </span>
    </div>
    @endif
    @endif
</div>

{{-- Akcje --}}
<a href="{{ route('plac.orders.loading.add', $order) }}" class="btn-yellow">
    <i class="fas fa-plus-circle"></i> DODAJ TOWAR
</a>

<button class="btn-red" onclick="closeLoading()">
    <i class="fas fa-check-double"></i> ZAMKNIJ ZAŁADUNEK
</button>

<button class="btn-gray" onclick="history.back()">
    <i class="fas fa-arrow-left"></i> Wstecz
</button>

@endsection

@section('scripts')
<script>
const ORDER_ID = {{ $order->id }};
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
let _pressTimer = null;

function startPress(row) {
    row.classList.add('pressing');
    _pressTimer = setTimeout(() => {
        row.classList.remove('pressing');
        window.location.href = row.dataset.edit;
    }, 1200);
}

function endPress() {
    clearTimeout(_pressTimer);
    document.querySelectorAll('.item-row.pressing').forEach(r => r.classList.remove('pressing'));
}

async function deleteItem(id) {
    const result = await Swal.fire({
        title: 'Usunąć pozycję?', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#e74c3c',
        confirmButtonText: 'Usuń', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;

    const res  = await fetch(`/plac/orders/${ORDER_ID}/loading/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Usunięto', timer: 1200, showConfirmButton: false });
        location.reload();
    }
}

async function closeLoading() {
    const rows = document.querySelectorAll('#itemsBody tr[id^="ir-"]').length;
    if (rows === 0) {
        Swal.fire({ icon: 'warning', title: 'Brak towarów', text: 'Dodaj przynajmniej jeden towar.', timer: 2000, showConfirmButton: false });
        return;
    }
    const result = await Swal.fire({
        title: 'Zamknąć załadunek?',
        text: 'Status zlecenia zmieni się na: Załadowane.',
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        confirmButtonText: 'Zamknij', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;

    const res  = await fetch(`/plac/orders/${ORDER_ID}/close`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Załadunek zamknięty!', timer: 1800, showConfirmButton: false });
        window.location.href = '{{ route('plac.loading.index') }}';
    }
}
</script>
@endsection