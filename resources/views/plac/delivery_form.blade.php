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

.load-header {
    background: var(--green);
    border-radius: var(--radius-card);
    padding: 16px 18px;
    margin-bottom: 12px;
}
.lh-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 36px; font-weight: 900; color: #fff; line-height: 1;
    text-transform: uppercase;
}
.lh-meta {
    margin-top: 8px;
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
}
.lh-driver { font-size: 13px; font-weight: 600; color: rgba(255,255,255,.75); }
.lh-weight {
    margin-top: 10px;
    background: rgba(0,0,0,.15);
    border-radius: 8px; padding: 8px 14px;
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.lhw-block { display: flex; flex-direction: column; align-items: flex-start; gap: 1px; }
.lhw-label { font-size: 10px; font-weight: 700; color: rgba(255,255,255,.65); text-transform: uppercase; letter-spacing: .06em; }
.lhw-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 20px; font-weight: 900; color: #fff; }
.lhw-val.negative { color: #ff8a80; }

.items-card {
    background: var(--bg-card);
    border-radius: var(--radius-card);
    overflow: hidden;
    border: 1px solid var(--border);
    box-shadow: 0 2px 6px rgba(0,0,0,.06);
    margin-bottom: 12px;
}
.items-table { width: 100%; border-collapse: collapse; }
.items-table thead tr { background: var(--green-light); }
.items-table th {
    padding: 9px 8px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: #1a7a3c; text-align: left;
}
.items-table th.r { text-align: right; }
.items-table td { padding: 11px 8px; border-bottom: 1px solid #f0f2f5; vertical-align: middle; }
.items-table tr:last-child td { border-bottom: none; }
.it-name   { font-weight: 700; font-size: 16px; color: #111; }
.it-bales  { font-family: 'Barlow Condensed', sans-serif; font-size: 18px; font-weight: 900; color: #111; }
.it-weight { font-family: 'Barlow Condensed', sans-serif; font-size: 16px; font-weight: 900; color: #444; display: block; text-align: right; white-space: nowrap; }
.del-btn {
    background: #fdecea; border: none; border-radius: 7px;
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    color: #e74c3c; cursor: pointer; font-size: 15px; margin-left: auto;
}
.del-btn:active { background: #e74c3c; color: #fff; }
.summary-row {
    background: #f8f9fa; padding: 10px 16px;
    display: flex; justify-content: space-between; align-items: center;
    border-top: 2px solid var(--green);
}
.sum-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; }
.sum-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 18px; font-weight: 900; color: #111; }
.press-hint { text-align: center; font-size: 11px; color: #bbb; padding: 7px; font-style: italic; }
.empty-items { text-align: center; padding: 28px; color: #ccc; font-size: 14px; }
.item-row { touch-action: none; user-select: none; transition: background .1s; }
.item-row.pressing { background: var(--green-light) !important; }

/* ── OPAKOWANIA read-only ── */
.pkg-card {
    background: var(--bg-card);
    border-radius: var(--radius-card);
    border: 1px solid var(--border);
    overflow: hidden;
    margin-bottom: 12px;
}
.pkg-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 14px; background: #f4f6f8;
    border-bottom: 1px solid var(--border);
}
.pkg-head-title { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #777; }
.pkg-source      { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #aaa; }
.pkg-source.driver { color: #d68910; }
.pkg-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 14px; border-bottom: 1px solid #f4f5f7;
}
.pkg-row:last-child { border-bottom: none; }
.pkg-name  { font-weight: 700; font-size: 14px; color: #111; }
.pkg-right { display: flex; gap: 12px; align-items: baseline; }
.pkg-qty   { font-family: 'Barlow Condensed', sans-serif; font-size: 20px; font-weight: 900; color: #111; }
.pkg-kg    { font-size: 12px; font-weight: 700; color: #aaa; }
.pkg-empty { padding: 14px; text-align: center; font-size: 13px; color: #ccc; }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.delivery.index') }}'"
        class="btn-back">
    <i class="fas fa-home"></i> Powrót
</button>

{{-- Nagłówek --}}
<div class="load-header">
    <div class="lh-client">{{ $order->client?->short_name ?? '?' }}</div>
    <div class="lh-meta">
        @if($order->driver)<span class="lh-driver">{{ $order->driver->name }}</span>@endif
        @if($order->tractor)<span class="plate-badge" style="border-color:rgba(255,255,255,.5);color:#fff;background:rgba(255,255,255,.15)">{{ $order->tractor->plate }}</span>@endif
        @if($order->trailer)<span class="plate-badge" style="border-color:rgba(255,255,255,.5);color:#fff;background:rgba(255,255,255,.15)">{{ $order->trailer->plate }}</span>@endif
    </div>
    @if($order->weight_netto)
    @php
        $totalItemsT = $order->loadingItems->sum('weight_kg') / 1000;
        $diff = $order->weight_netto - $totalItemsT;
    @endphp
    <div class="lh-weight">
        <div class="lhw-block">
            <span class="lhw-label">Waga kierowcy brutto</span>
            <span class="lhw-val">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</span>
        </div>
        <div class="lhw-block" style="align-items:flex-end">
            <span class="lhw-label">Pozostało</span>
            <span class="lhw-val {{ $diff < 0 ? 'negative' : '' }}">{{ number_format($diff, 3, ',', ' ') }} t</span>
        </div>
    </div>
    @endif
</div>

{{-- ── OPAKOWANIA – tylko podsumowanie ── --}}
@php
    $pkgItems      = $order->packaging;
    $hasPkg        = $pkgItems->isNotEmpty();
    $placConfirmed = $hasPkg && $pkgItems->some(fn($p) => $p->confirmed_at !== null);
    $onlyDriver    = $hasPkg && !$placConfirmed;
@endphp
<div class="pkg-card">
    <div class="pkg-head">
        <span class="pkg-head-title"><i class="fas fa-box"></i> Opakowania</span>
        @if(!$hasPkg)
            <span class="pkg-source">Brak informacji</span>
        @elseif($onlyDriver)
            <span class="pkg-source driver">Od kierowcy</span>
        @endif
        {{-- plac potwierdził → bez komentarza --}}
    </div>
    @if($hasPkg)
        @foreach($pkgItems as $pkg)
        @php
            $qty     = $pkg->qty_plac ?? $pkg->quantity ?? 0;
            $wagaJed = (float)($pkg->opakowanie?->waga ?? 0);
            $totalKg = $qty * $wagaJed;
        @endphp
        <div class="pkg-row">
            <span class="pkg-name">{{ $pkg->opakowanie?->name }}</span>
            <div class="pkg-right">
                <span class="pkg-qty">{{ $qty }} szt.</span>
                @if($totalKg > 0)
                <span class="pkg-kg">{{ number_format($totalKg, 0, ',', ' ') }} kg</span>
                @endif
            </div>
        </div>
        @endforeach
    @else
        <div class="pkg-empty">–</div>
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
                data-edit="{{ route('plac.delivery.edit', [$order, $item]) }}"
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
<a href="{{ route('plac.delivery.add', $order) }}" class="btn-green">
    <i class="fas fa-plus-circle"></i> DODAJ TOWAR
</a>

<button class="btn-red" onclick="closeDelivery()">
    <i class="fas fa-check-double"></i> ZAMKNIJ DOSTAWĘ
</button>

<button class="btn-gray" onclick="history.back()">
    <i class="fas fa-arrow-left"></i> Wstecz
</button>

@endsection

@section('scripts')
<style>
.btn-green {
    width: 100%; padding: 20px;
    background: var(--green); color: #fff;
    border: none; border-radius: var(--radius-btn);
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase;
    cursor: pointer; margin-bottom: 10px;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    text-decoration: none;
}
.btn-green:hover { background: var(--green-dark); color: #fff; }
.btn-green:active { filter: brightness(.9); }
</style>
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

    const res  = await fetch(`/plac/delivery/${ORDER_ID}/items/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Usunięto', timer: 1200, showConfirmButton: false });
        location.reload();
    }
}

async function closeDelivery() {
    const rows = document.querySelectorAll('#itemsBody tr[id^="ir-"]').length;
    if (rows === 0) {
        Swal.fire({ icon: 'warning', title: 'Brak towarów', text: 'Dodaj przynajmniej jeden towar.', timer: 2000, showConfirmButton: false });
        return;
    }
    const result = await Swal.fire({
        title: 'Zamknąć dostawę?',
        text: 'Status zlecenia zmieni się na: Dostarczone.',
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        confirmButtonText: 'Zamknij', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;

    const res  = await fetch(`/plac/delivery/${ORDER_ID}/close`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Dostawa zamknięta!', timer: 1800, showConfirmButton: false });
        window.location.href = '{{ route('plac.delivery.index') }}';
    }
}
</script>
@endsection