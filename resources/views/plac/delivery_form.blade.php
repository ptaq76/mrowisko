@extends('layouts.plac')

@section('title', 'Przyjęcie – ' . $order->client?->short_name)

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
.load-header { background:#27ae60;border-radius:12px;padding:14px 16px;margin-bottom:12px; }
.load-client { font-family:'Barlow Condensed',sans-serif;font-size:32px;font-weight:900;color:#fff;line-height:1; }
.load-sub { font-size:13px;color:rgba(255,255,255,.75);margin-top:3px; }
.driver-weight { margin-top:10px;background:rgba(255,255,255,.2);border-radius:8px;padding:8px 12px;display:flex;justify-content:space-between;align-items:center; }
.dw-label { font-size:11px;font-weight:700;color:rgba(255,255,255,.8);text-transform:uppercase;letter-spacing:.06em; }
.nr-rej-w { display:inline-block;background:#fff;border:2px solid rgba(255,255,255,.6);padding:1px 7px;border-radius:4px;font-weight:900;font-size:13px;color:#1a1a1a;letter-spacing:.04em; }
.dw-val   { font-family:'Barlow Condensed',sans-serif;font-size:26px;font-weight:900;color:#fff; }

.items-card { background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.07);margin-bottom:12px; }
.items-table { width:100%;border-collapse:collapse; }
.items-table thead tr { background:#d4efdf; }
.items-table th { padding:10px 10px;font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#1a7a3c;text-align:left; }
.items-table td { padding:12px 10px;border-bottom:1px solid #f0f2f5;vertical-align:middle; }
.items-table tr:last-child td { border-bottom:none; }
.it-name  { font-weight:700;font-size:14px;color:#1a1a1a; }
.it-bales { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:#1a1a1a; }
.it-weight { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:#1a1a1a;display:block;text-align:right; }
.del-btn  { background:#fdecea;border:none;border-radius:6px;padding:8px 10px;color:#e74c3c;cursor:pointer;font-size:14px; }
.del-btn:active { background:#e74c3c;color:#fff; }
.summary-row { background:#f8f9fa;padding:12px 14px;display:flex;justify-content:space-between;align-items:center;border-top:2px solid #e2e5e9; }
.sum-label { font-size:12px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em; }
.sum-val   { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:#1a1a1a; }
.empty-items { text-align:center;padding:24px;color:#ccc;font-size:14px; }

.btn-add { display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:18px;background:#27ae60;color:#fff;border:none;border-radius:12px;font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;margin-bottom:10px;text-decoration:none;box-shadow:0 3px 8px rgba(39,174,96,.3); }
.btn-add:active { filter:brightness(.9); }
.btn-close { display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:18px;background:#e74c3c;color:#fff;border:none;border-radius:12px;font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;margin-bottom:10px;box-shadow:0 3px 8px rgba(231,76,60,.3); }
.btn-close:active { filter:brightness(.9); }
.btn-back2 { display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer; }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.delivery.index') }}'"
        style="display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer">
    <i class="fas fa-home"></i> Powrót
</button>

<div class="load-header">
    <div class="load-client">{{ $order->client?->short_name }}</div>
    <div class="load-sub" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:6px">
        @if($order->driver)<span>{{ $order->driver->name }}</span>@endif
        @if($order->tractor)<span class="nr-rej-w">{{ $order->tractor->plate }}</span>@endif
        @if($order->trailer)<span class="nr-rej-w">{{ $order->trailer->plate }}</span>@endif
    </div>
    @if($order->weight_netto)
    <div class="driver-weight">
        <div>
            <div class="dw-label">Waga kierowcy</div>
            <div class="dw-val">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</div>
        </div>
    </div>
    @endif
</div>


<div class="items-card">
    <table class="items-table">
        <thead>
            <tr>
                <th>Towar</th>
                <th>Bel.</th>
                <th>Waga</th>
                <th style="width:50px"></th>
            </tr>
        </thead>
        <tbody id="itemsBody">
            @forelse($order->loadingItems as $item)
            <tr id="ir-{{ $item->id }}" class="item-row"
                data-edit="{{ route('plac.delivery.edit', [$order, $item]) }}"
                onpointerdown="startPress(this)" onpointerup="endPress()" onpointerleave="endPress()">
                <td><span class="it-name">{{ $item->fraction?->name }}</span></td>
                <td><span class="it-bales">{{ $item->bales }}</span></td>
                <td><span class="it-weight">{{ number_format($item->weight_kg, 0, ',', ' ') }}</span></td>
                <td>
                    <button class="del-btn" onclick="deleteItem({{ $item->id }})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-items">Brak pozycji – dodaj towar</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($order->loadingItems->isNotEmpty())
    <div class="summary-row">
        <span class="sum-label">Razem</span>
        <span class="sum-val">
            {{ $order->loadingItems->sum('bales') }} bel.
            &nbsp;·&nbsp;
            {{ number_format($order->loadingItems->sum('weight_kg'), 0, ',', ' ') }} kg
        </span>
    </div>
    @if($order->loadingItems->isNotEmpty())
    <div style="text-align:center;font-size:11px;color:#aaa;padding:6px;font-style:italic">
        <i class="fas fa-hand-pointer"></i> Przytrzymaj wiersz aby edytować
    </div>
    @endif
    @endif
</div>

<a href="{{ route('plac.delivery.add', $order) }}" class="btn-add">
    <i class="fas fa-plus-circle"></i> DODAJ TOWAR
</a>

<button class="btn-close" onclick="closeDelivery()">
    <i class="fas fa-check-double"></i> ZAMKNIJ PRZYJĘCIE
</button>

<button class="btn-back2" onclick="history.back()"><i class="fas fa-home"></i> Powrót</button>

@endsection

@section('scripts')
<style>
.item-row { touch-action:none;user-select:none;transition:background .1s; }
.item-row.pressing { background:#e8f5e9 !important; }
@keyframes slideUp { from { transform:translateY(100%); } to { transform:translateY(0); } }
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
    const res  = await fetch(`/plac/delivery/${ORDER_ID}/items/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('ir-' + id)?.remove();
        Swal.fire({ icon: 'success', title: 'Usunięto', timer: 1200, showConfirmButton: false });
    }
}

async function closeDelivery() {
    const rows = document.querySelectorAll('#itemsBody tr[id^="ir-"]').length;
    if (rows === 0) {
        Swal.fire({ icon: 'warning', title: 'Brak towarów', text: 'Dodaj przynajmniej jeden towar.', timer: 2000, showConfirmButton: false });
        return;
    }
    const result = await Swal.fire({
        title: 'Zamknąć przyjęcie?',
        text: 'Towary zostaną dodane do magazynu.',
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#27ae60',
        confirmButtonText: 'Zamknij', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;
    const res  = await fetch(`/plac/delivery/${ORDER_ID}/close`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Przyjęcie zamknięte!', timer: 1800, showConfirmButton: false });
        window.location.href = '{{ route('plac.delivery.index') }}';
    }
}
</script>
@endsection