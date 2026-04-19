@extends('layouts.plac')

@section('title', 'Załadunek')

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

.load-header {
    background: #f39c12; border-radius: 12px;
    padding: 14px 18px; margin-bottom: 12px;
}
.load-client {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 32px; font-weight: 900; color: #fff; line-height: 1;
}
.load-sub { font-size: 13px; color: #888; margin-top: 3px; }
.load-weight {
    margin-top: 10px; background: #fff; border-radius: 8px;
    padding: 8px 14px; display: flex; align-items: center; gap: 10px;
}
.lw-label { font-size: 11px; font-weight: 700; color: rgba(0,0,0,.5); text-transform: uppercase; letter-spacing: .06em; }
.nr-rej-y { display:inline-block;background:#fff;border:2px solid rgba(0,0,0,.3);padding:1px 7px;border-radius:4px;font-weight:900;font-size:13px;color:#1a1a1a;letter-spacing:.04em; }
.lw-val   { font-family: 'Barlow Condensed', sans-serif; font-size: 28px; font-weight: 900; color: #2d7a1a; }

.order-notes {
    background: #fff3cd; border-left: 4px solid #f39c12;
    border-radius: 8px; padding: 10px 14px;
    font-size: 13px; color: #856404; margin-bottom: 12px; font-weight: 600;
}

/* Tabela */
.items-card {
    background: #fff; border-radius: 12px;
    overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,.07); margin-bottom: 12px;
}
.items-table { width: 100%; border-collapse: collapse; }
.items-table thead tr { background: #fdebd0; }
.items-table th {
    padding: 10px 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: #935810; text-align: left;
}
.items-table td { padding: 12px 10px; border-bottom: 1px solid #f0f2f5; vertical-align: middle; }
.items-table tr:last-child td { border-bottom: none; }

.it-name  { font-weight: 700; font-size: 14px; color: #1a1a1a; }
.it-bales { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900; color: #1a1a1a; }
.it-weight{ font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 900; color: #1a1a1a; display:block; text-align:right; }

.act-btns { display: flex; gap: 6px; }
.edit-btn {
    background: #eaf4fb; border: none; border-radius: 6px;
    padding: 8px 10px; color: #2980b9; cursor: pointer; font-size: 14px;
    text-decoration: none; display: flex; align-items: center;
}
.del-btn  {
    background: #fdecea; border: none; border-radius: 6px;
    padding: 8px 10px; color: #e74c3c; cursor: pointer; font-size: 14px;
}
.edit-btn:active { background: #2980b9; color: #fff; }
.del-btn:active  { background: #e74c3c; color: #fff; }

.summary-row { background:#f0f2f5;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid #e2e5e9; }
.sum-label { font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.06em; }
.sum-val   { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:700;color:#555; }

.empty-items { text-align: center; padding: 24px; color: #ccc; font-size: 14px; }

/* Przyciski */
.btn-add {
    width: 100%; padding: 18px; background: #f39c12; color: #fff;
    border: none; border-radius: 12px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase;
    cursor: pointer; margin-bottom: 10px;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    text-decoration: none; box-shadow: 0 3px 8px rgba(243,156,18,.3);
}
.btn-add:active { filter: brightness(.9); }

.btn-close {
    width: 100%; padding: 18px; background: #e74c3c; color: #fff;
    border: none; border-radius: 12px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase;
    cursor: pointer; margin-bottom: 10px;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    box-shadow: 0 3px 8px rgba(231,76,60,.3);
}
.btn-close:active { filter: brightness(.9); }

.btn-back2 {
    width: 100%; padding: 14px; background: #7f8c8d; color: #fff;
    border: none; border-radius: 12px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 18px; font-weight: 800; letter-spacing: .06em;
    text-transform: uppercase; cursor: pointer;
}
.btn-back2:active { filter: brightness(.9); }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.loading.index') }}'"
        style="display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer">
    <i class="fas fa-home"></i> Powrót
</button>

{{-- Nagłówek --}}
<div class="load-header">
    <div class="load-client">{{ $order->client?->short_name ?? '?' }}</div>
    <div class="load-sub" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:6px">
        @if($order->driver)<span style="color:rgba(0,0,0,.6);font-size:13px">{{ $order->driver->name }}</span>@endif
        @if($order->tractor)<span class="nr-rej-y">{{ $order->tractor->plate }}</span>@endif
        @if($order->trailer)<span class="nr-rej-y">{{ $order->trailer->plate }}</span>@endif
    </div>
    @if($order->weight_netto)
    <div style="margin-top:8px;display:flex;align-items:center;gap:8px">
        <span style="font-size:11px;font-weight:700;color:rgba(0,0,0,.5);text-transform:uppercase;letter-spacing:.06em">Waga:</span>
        <span style="font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:#1a1a1a">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</span>
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
                <th style="text-align:right">Waga</th>
                <th style="width:76px">Akcje</th>
            </tr>
        </thead>
        <tbody id="itemsBody">
            @forelse($order->loadingItems as $item)
            <tr id="ir-{{ $item->id }}" class="item-row" data-edit="{{ route('plac.orders.loading.edit', [$order, $item]) }}" onpointerdown="startPress(this)" onpointerup="endPress()" onpointerleave="endPress()">
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
    <div class="summary-row">
        <span class="sum-label">Razem</span>
        <span class="sum-val">
            {{ $order->loadingItems->sum('bales') }} bel.
            &nbsp;·&nbsp;
            {{ number_format($order->loadingItems->sum('weight_kg'), 0, ',', ' ') }} kg
        </span>
    </div>
    @endif
</div>

{{-- Akcje --}}
<a href="{{ route('plac.orders.loading.add', $order) }}" class="btn-add">
    <i class="fas fa-plus-circle"></i> DODAJ TOWAR
</a>

<button class="btn-close" onclick="closeLoading()">
    <i class="fas fa-check-double"></i> ZAMKNIJ ZAŁADUNEK
</button>

<button class="btn-back2" onclick="history.back()"><i class="fas fa-home"></i> Powrót</button>

@endsection

@section('scripts')
<style>
.item-row { touch-action: none; user-select: none; transition: background .1s; }
.item-row.pressing { background: #eaf4fb !important; }
.press-hint {
    text-align: center; font-size: 11px; color: #aaa;
    padding: 6px; font-style: italic;
}
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
    }, 600);
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