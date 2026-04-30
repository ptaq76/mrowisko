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
.cam-btn {
    position: relative;
    background: #f0f2f5; border: none; border-radius: 7px;
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    color: #888; cursor: pointer; font-size: 15px;
}
.cam-btn.has-photos { background: #e8f7e4; color: #1e8449; }
.cam-btn:active { filter: brightness(.92); }
.cam-badge {
    position: absolute; top: -5px; right: -5px;
    background: #27ae60; color: #fff;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 900;
    min-width: 17px; height: 17px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    padding: 0 4px; line-height: 1;
    border: 2px solid #fff;
}
.it-actions { display: flex; gap: 6px; align-items: center; justify-content: flex-end; }

/* ── Galeria zdjęć ── */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-top: 4px;
}
.gallery-tile {
    position: relative;
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    background: #f0f2f5;
    cursor: pointer;
    border: 2px solid transparent;
}
.gallery-tile img {
    width: 100%; height: 100%; object-fit: cover;
    display: block;
}
.gallery-del {
    position: absolute; top: 4px; right: 4px;
    background: rgba(231, 76, 60, .9);
    color: #fff; border: none; border-radius: 50%;
    width: 26px; height: 26px;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; cursor: pointer;
}
.gallery-add {
    aspect-ratio: 1;
    border-radius: 8px;
    background: #f0f8ff;
    border: 2px dashed #3498db;
    color: #3498db;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; cursor: pointer;
}
.gallery-add.disabled {
    background: #f4f5f7; border-color: #ddd; color: #ccc; cursor: not-allowed;
}
.gallery-empty {
    text-align: center; padding: 28px 12px;
    color: #aaa; font-size: 13px;
}
.lightbox-img {
    max-width: 100%; max-height: 70vh;
    border-radius: 8px;
    object-fit: contain;
}
.lightbox-counter {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 14px; color: #888; margin-bottom: 8px;
}
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

/* ── SweetAlert formularz opakowań ── */
.sw-pkg-input {
    width: 72px; padding: 8px 6px;
    border: 2px solid #e2e5e9; border-radius: 8px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 28px; font-weight: 900; text-align: center; color: #111;
    outline: none; -moz-appearance: textfield; flex-shrink: 0;
}
.sw-pkg-input::-webkit-outer-spin-button,
.sw-pkg-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.sw-pkg-input:focus { border-color: #3498db; }
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
    @php
        $pkgItems       = $order->packaging;
        $hasPkg         = $pkgItems->isNotEmpty();
        $allConfirmed   = $hasPkg && $pkgItems->every(fn($p) => $p->confirmed_at !== null);
        $placConfirmed  = $hasPkg && $pkgItems->some(fn($p)  => $p->confirmed_at !== null);
        $onlyDriver     = $hasPkg && !$placConfirmed;
        $totalPkgSzt    = $allConfirmed ? $pkgItems->sum(fn($p) => $p->qty_plac ?? $p->quantity ?? 0) : 0;
        $totalPkgKg     = $allConfirmed ? $pkgItems->sum(fn($p) => ($p->qty_plac ?? $p->quantity ?? 0) * (float)($p->opakowanie?->waga ?? 0)) : 0;
        $totalItemsT    = $order->loadingItems->sum('weight_kg') / 1000;
        $weightNetto    = $order->weight_netto ?? 0;
        $weightNettoPkg = $allConfirmed && $totalPkgKg > 0 ? round($weightNetto - $totalPkgKg / 1000, 3) : null;
        $weightDisplay  = $weightNettoPkg ?? $weightNetto;
        $diff           = $weightDisplay - $totalItemsT;
    @endphp

    @if($order->weight_netto)
    <div class="lh-weight">
        <div class="lhw-block">
            <span class="lhw-label">Waga kierowcy</span>
            <span class="lhw-val">{{ number_format($weightDisplay, 3, ',', ' ') }} t</span>
            @if($weightNettoPkg !== null)
            <span style="font-size:10px;color:rgba(255,255,255,.55);margin-top:1px">
                brutto {{ number_format($weightNetto, 3, ',', ' ') }} t
            </span>
            @endif
        </div>
        <div class="lhw-block" style="align-items:flex-end">
            <span class="lhw-label">Pozostało</span>
            <span class="lhw-val {{ $diff < 0 ? 'negative' : '' }}">{{ number_format($diff, 3, ',', ' ') }} t</span>
        </div>
    </div>
    @endif
</div>

{{-- ── OPAKOWANIA – z edycją ── --}}
<div class="pkg-card" onclick="openPackagingForm()" style="cursor:pointer">
    <div class="pkg-head">
        <span class="pkg-head-title"><i class="fas fa-box"></i> Opakowania</span>
        <span style="display:flex;align-items:center;gap:8px">
            @if(!$hasPkg)
                <span class="pkg-source">Brak informacji</span>
            @elseif($onlyDriver)
                <span class="pkg-source driver">Od kierowcy – wymaga potwierdzenia</span>
            @endif
            <i class="fas fa-edit" style="color:#bbb;font-size:13px"></i>
        </span>
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
        @if($totalPkgKg > 0)
        <div class="pkg-row" style="background:#f8f9fa">
            <span class="pkg-name" style="color:#888;font-size:11px;letter-spacing:.06em;text-transform:uppercase">Razem</span>
            <div class="pkg-right">
                <span class="pkg-qty" style="font-size:16px">{{ $totalPkgSzt }} szt.</span>
                <span class="pkg-kg">{{ number_format($totalPkgKg, 0, ',', ' ') }} kg</span>
            </div>
        </div>
        @endif
    @else
        <div class="pkg-empty">– kliknij aby dodać –</div>
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
                    <div class="it-actions">
                        <button id="cam-{{ $item->id }}"
                                class="cam-btn {{ ($item->photos_count ?? 0) > 0 ? 'has-photos' : '' }}"
                                onclick="event.stopPropagation(); openPhotoGallery({{ $item->id }})"
                                title="Zdjęcia">
                            <i class="fas fa-camera"></i>
                            @if(($item->photos_count ?? 0) > 0)
                                <span class="cam-badge">{{ $item->photos_count }}</span>
                            @endif
                        </button>
                        <button class="del-btn" onclick="event.stopPropagation(); deleteItem({{ $item->id }})" title="Usuń">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
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

@php
$pkgDataForJs = $allOpakowania->map(function($o) use ($pkgItems) {
    $fromDriver = $pkgItems->firstWhere('opakowanie_id', $o->id);
    return [
        'id'       => $o->id,
        'name'     => $o->name,
        'waga'     => (float)$o->waga,
        'driver'   => $fromDriver?->quantity,
        'qty_plac' => $fromDriver?->qty_plac,
    ];
})->values();
@endphp

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
const PKG_DATA = @json($pkgDataForJs);
let _pressTimer = null;

/* ── Formularz opakowań w SweetAlert ── */
async function openPackagingForm() {
    if (!PKG_DATA.length) {
        Swal.fire({ icon: 'info', title: 'Brak opakowań', text: 'Nie zdefiniowano żadnych opakowań zwrotnych.' });
        return;
    }

    const rows = PKG_DATA.map(p => {
        const defaultQty = p.qty_plac ?? p.driver ?? 0;
        const driverInfo = (p.driver !== null && p.driver !== undefined)
            ? `<span style="font-size:10px;color:#d68910;font-weight:700">kier: ${p.driver}</span>`
            : '';
        return `
        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:10px 0;border-bottom:1px solid #f0f0f0">
            <div style="text-align:left">
                <div style="font-family:'Barlow Condensed',sans-serif;font-size:18px;
                            font-weight:800;color:#1a1a1a">${p.name}</div>
                <div style="font-size:11px;color:#aaa;display:flex;gap:6px;align-items:center">
                    ${p.waga > 0 ? Math.round(p.waga) + ' kg/szt.' : ''}
                    ${driverInfo}
                </div>
            </div>
            <input type="text" id="spkg_${p.id}"
                   data-id="${p.id}" data-waga="${p.waga}"
                   class="sw-pkg-input js-numkey"
                   value="${defaultQty}"
                   data-keypad-label="${p.name} [szt.]"
                   data-decimal="false"
                   data-min="0" data-max="9999"
                   oninput="swUpdateTotal()">
        </div>`;
    }).join('');

    const html = `
        <div style="text-align:left">
            ${rows}
            <div style="display:flex;justify-content:space-between;align-items:center;
                        padding:10px 0;margin-top:2px">
                <span style="font-size:11px;font-weight:700;color:#aaa;
                             text-transform:uppercase;letter-spacing:.06em">Łączna waga</span>
                <span id="swTotalKg" style="font-family:'Barlow Condensed',sans-serif;
                                            font-size:20px;font-weight:900;color:#3498db">0 kg</span>
            </div>
        </div>`;

    const result = await Swal.fire({
        title: 'Palety / BigBoxy',
        html,
        showCancelButton: true,
        confirmButtonText: 'Zapisz',
        cancelButtonText: 'Anuluj',
        confirmButtonColor: '#3498db',
        cancelButtonColor: '#aaa',
        reverseButtons: true,
        didOpen: () => setTimeout(() => swUpdateTotal(), 50),
        preConfirm: () => {
            return PKG_DATA.map(p => ({
                opakowanie_id: p.id,
                qty_plac: parseInt(document.getElementById(`spkg_${p.id}`)?.value) || 0,
            }));
        },
    });

    if (!result.isConfirmed) return;

    const res = await fetch(`/plac/delivery/${ORDER_ID}/packaging`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ packaging: result.value }),
    });
    const data = await res.json();

    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1200, showConfirmButton: false });
        location.reload();
    } else {
        Swal.fire({ icon: 'error', title: 'Błąd', text: data.message ?? 'Spróbuj ponownie.' });
    }
}

function swUpdateTotal() {
    let total = 0;
    PKG_DATA.forEach(p => {
        const qty = parseInt(document.getElementById(`spkg_${p.id}`)?.value) || 0;
        total += qty * p.waga;
    });
    const el = document.getElementById('swTotalKg');
    if (el) el.textContent = Math.round(total) + ' kg';
}

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

/* ─────────── ZDJĘCIA TOWARU ─────────── */
const PHOTO_LIMIT = 5;
const PHOTO_MAX_PX = 1600;
const PHOTO_QUALITY = 0.85;
const THUMB_MAX_PX = 300;
const THUMB_QUALITY = 0.7;

let _photoState = { itemId: null, photos: [] };

async function openPhotoGallery(itemId) {
    _photoState.itemId = itemId;

    const res = await fetch(`/plac/delivery/${ORDER_ID}/items/${itemId}/photos`, {
        headers: { 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (!data.success) {
        Swal.fire({ icon: 'error', title: 'Błąd', text: 'Nie udało się pobrać zdjęć.' });
        return;
    }
    _photoState.photos = data.photos;
    await showGallery();
}

async function showGallery() {
    const photos = _photoState.photos;
    const canAdd = photos.length < PHOTO_LIMIT;

    let html = '<div class="lightbox-counter">Zdjęć: '+photos.length+' / '+PHOTO_LIMIT+'</div>';

    if (photos.length === 0 && !canAdd) {
        html += '<div class="gallery-empty">Brak zdjęć</div>';
    } else {
        html += '<div class="gallery-grid">';
        photos.forEach((p, idx) => {
            html += `
                <div class="gallery-tile" data-idx="${idx}">
                    <img src="${p.thumb_url}" alt="">
                    <button class="gallery-del" data-photo-id="${p.id}" title="Usuń">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>`;
        });
        if (canAdd) {
            html += `<div class="gallery-add" id="galAddBtn"><i class="fas fa-plus"></i></div>`;
        } else {
            html += `<div class="gallery-add disabled" title="Limit zdjęć"><i class="fas fa-ban"></i></div>`;
        }
        html += '</div>';
    }

    const result = await Swal.fire({
        title: 'Zdjęcia towaru',
        html,
        showCancelButton: false,
        showConfirmButton: true,
        confirmButtonText: 'Zamknij',
        confirmButtonColor: '#27ae60',
        width: 480,
        didOpen: () => {
            document.querySelectorAll('.gallery-tile').forEach(tile => {
                tile.addEventListener('click', (e) => {
                    if (e.target.closest('.gallery-del')) return;
                    const idx = parseInt(tile.dataset.idx);
                    Swal.close();
                    openLightbox(idx);
                });
            });
            document.querySelectorAll('.gallery-del').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const id = parseInt(btn.dataset.photoId);
                    Swal.close();
                    await deletePhoto(id);
                });
            });
            const addBtn = document.getElementById('galAddBtn');
            if (addBtn) {
                addBtn.addEventListener('click', () => {
                    Swal.close();
                    triggerCamera();
                });
            }
        },
    });

    // Po zamknięciu — odśwież badge w głównym widoku
    refreshCameraBadge();
}

async function openLightbox(startIdx) {
    let idx = startIdx;
    const photos = _photoState.photos;

    while (true) {
        if (photos.length === 0) break;
        if (idx < 0) idx = photos.length - 1;
        if (idx >= photos.length) idx = 0;

        const p = photos[idx];
        const counter = `Zdjęcie ${idx + 1} / ${photos.length}`;

        const html = `
            <div class="lightbox-counter">${counter}</div>
            <img src="${p.url}" class="lightbox-img" alt="">
        `;

        const showPrev = photos.length > 1;
        const result = await Swal.fire({
            html,
            showCancelButton: true,
            showDenyButton: showPrev,
            showConfirmButton: showPrev,
            confirmButtonText: '<i class="fas fa-chevron-right"></i>',
            denyButtonText:    '<i class="fas fa-chevron-left"></i>',
            cancelButtonText:  '<i class="fas fa-trash-alt"></i> Usuń',
            confirmButtonColor: '#27ae60',
            denyButtonColor:    '#27ae60',
            cancelButtonColor:  '#e74c3c',
            reverseButtons: true,
            width: 600,
            footer: '<button id="lbBack" class="btn-gray" style="padding:8px 16px;border:none;border-radius:7px;background:#f0f2f5;cursor:pointer">← Wróć do listy</button>',
            didOpen: () => {
                document.getElementById('lbBack')?.addEventListener('click', () => {
                    Swal.close();
                    Swal.getPopup()?.dataset && (Swal.getPopup().dataset.action = 'back');
                });
            },
        });

        if (result.isConfirmed) { idx++; continue; }
        if (result.isDenied) { idx--; continue; }
        if (result.dismiss === Swal.DismissReason.cancel) {
            // Usuń zdjęcie
            const photoId = p.id;
            const confirm = await Swal.fire({
                title: 'Usunąć zdjęcie?', icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#e74c3c',
                confirmButtonText: 'Usuń', cancelButtonText: 'Anuluj',
            });
            if (!confirm.isConfirmed) continue;
            await deletePhoto(photoId, false);
            // photos zostały zmodyfikowane — pokaż listę
            break;
        }
        // Backdrop / Esc
        break;
    }

    await showGallery();
}

async function deletePhoto(photoId, reopenGallery = true) {
    const res = await fetch(`/plac/delivery/${ORDER_ID}/items/${_photoState.itemId}/photos/${photoId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (!data.success) {
        await Swal.fire({ icon: 'error', title: 'Błąd', text: 'Nie udało się usunąć.' });
        if (reopenGallery) await openPhotoGallery(_photoState.itemId);
        return;
    }
    _photoState.photos = _photoState.photos.filter(p => p.id !== photoId);
    await Swal.fire({ icon: 'success', title: 'Usunięto', timer: 800, showConfirmButton: false });
    if (reopenGallery) await showGallery();
}

function triggerCamera() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.capture = 'environment';
    input.multiple = true;
    input.style.display = 'none';
    document.body.appendChild(input);
    input.addEventListener('change', async () => {
        const files = Array.from(input.files || []);
        document.body.removeChild(input);
        if (files.length === 0) {
            await openPhotoGallery(_photoState.itemId);
            return;
        }
        await uploadFiles(files);
    });
    input.click();
}

async function uploadFiles(files) {
    const free = PHOTO_LIMIT - _photoState.photos.length;
    if (free <= 0) {
        await Swal.fire({ icon: 'warning', title: 'Limit', text: 'Osiągnięto limit '+PHOTO_LIMIT+' zdjęć.' });
        await openPhotoGallery(_photoState.itemId);
        return;
    }
    const toUpload = files.slice(0, free);
    if (files.length > free) {
        await Swal.fire({
            icon: 'info',
            title: 'Limit zdjęć',
            text: `Wyślę tylko ${free} z ${files.length} (limit ${PHOTO_LIMIT}).`,
            timer: 1800, showConfirmButton: false,
        });
    }

    Swal.fire({
        title: 'Wysyłam zdjęcia...',
        html: `<div id="upProg" style="font-family:'Barlow Condensed',sans-serif;font-size:24px;font-weight:900;color:#27ae60">0 / ${toUpload.length}</div>`,
        allowOutsideClick: false, allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading(),
    });

    let success = 0;
    let lastError = null;

    for (let i = 0; i < toUpload.length; i++) {
        try {
            const file = toUpload[i];
            const photo = await compressImage(file, PHOTO_MAX_PX, PHOTO_QUALITY);
            const thumb = await compressImage(file, THUMB_MAX_PX, THUMB_QUALITY);

            const fd = new FormData();
            fd.append('photo', photo, 'photo.jpg');
            fd.append('thumb', thumb, 'thumb.jpg');

            const res = await fetch(`/plac/delivery/${ORDER_ID}/items/${_photoState.itemId}/photos`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: fd,
            });
            const data = await res.json();
            if (data.success) {
                _photoState.photos.push(data.photo);
                success++;
                const prog = document.getElementById('upProg');
                if (prog) prog.textContent = `${success} / ${toUpload.length}`;
            } else {
                lastError = data.message || 'Błąd uploadu.';
                break;
            }
        } catch (e) {
            lastError = e.message || 'Błąd.';
            break;
        }
    }

    Swal.close();

    if (lastError) {
        await Swal.fire({ icon: 'error', title: 'Błąd', text: lastError });
    } else if (success > 0) {
        await Swal.fire({ icon: 'success', title: `Dodano ${success}`, timer: 1000, showConfirmButton: false });
    }

    await showGallery();
}

function compressImage(file, maxPx, quality) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const reader = new FileReader();
        reader.onload = (e) => { img.src = e.target.result; };
        reader.onerror = () => reject(new Error('Nie można odczytać pliku.'));
        img.onload = () => {
            let { width, height } = img;
            if (width > maxPx || height > maxPx) {
                if (width > height) { height = Math.round(height * maxPx / width); width = maxPx; }
                else                { width  = Math.round(width  * maxPx / height); height = maxPx; }
            }
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);
            canvas.toBlob(blob => blob ? resolve(blob) : reject(new Error('Kompresja nie powiodła się.')), 'image/jpeg', quality);
        };
        img.onerror = () => reject(new Error('Niepoprawny obraz.'));
        reader.readAsDataURL(file);
    });
}

function refreshCameraBadge() {
    // Nie mamy danych z innych itemów — przeładowujemy tylko stan przycisku aktualnego
    const btn = document.getElementById('cam-' + _photoState.itemId);
    if (!btn) return;
    const count = _photoState.photos.length;
    btn.classList.toggle('has-photos', count > 0);
    let badge = btn.querySelector('.cam-badge');
    if (count > 0) {
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'cam-badge';
            btn.appendChild(badge);
        }
        badge.textContent = count;
    } else if (badge) {
        badge.remove();
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