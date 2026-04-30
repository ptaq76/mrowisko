@extends('layouts.app')

@section('title', 'Raport – Dostawy')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.report-wrap { padding: 20px; }
.report-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.report-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;display:flex;align-items:center;gap:8px; }
.badge-count { display:inline-block;background:#27ae60;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px; }

.filters { background:#fff;border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.07);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end; }
.filter-group { display:flex;flex-direction:column;gap:4px; }
.filter-group label { font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em; }
.filter-group select,
.filter-group input { padding:7px 10px;border:1px solid #dde0e5;border-radius:7px;font-size:13px;color:#1a1a1a;outline:none;min-width:160px; }
.filter-group select:focus,
.filter-group input:focus { border-color:#27ae60; }
.btn-copy-date { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;cursor:pointer;background:#eef8f2;color:#27ae60;font-size:14px;border:1px solid #dde0e5;transition:all .15s ease; }
.btn-copy-date:hover { background:#27ae60;color:#fff;border-color:#27ae60; }
.btn-filter { padding:8px 18px;background:#27ae60;color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer; }
.btn-filter:hover { background:#219a52; }
.btn-archived { padding:8px 16px;background:#f4f5f7;color:#555;border:1px solid #dde0e5;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;display:flex;align-items:center;gap:6px; }
.btn-archived:hover { background:#e8e9ec; }

.report-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden;width:80%;margin:0 auto; }
.report-table { width:100%;border-collapse:collapse;font-size:13px; }
.report-table thead tr { background:#27ae60;color:#fff; }
.report-table th { padding:10px 12px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left; }
.report-table td { padding:10px 12px;border-bottom:2px solid #d8dce3;vertical-align:top; }
.report-table tr:last-child td { border-bottom:none; }
.report-table tr:hover td { background:#f0fdf4; }
.th-right { text-align:right !important; }
.td-right { text-align:right; }

.cell-date { font-weight:700;white-space:nowrap;color:#1a1a1a;font-size:13px; }
.cell-client { font-weight:800;font-size:14px;color:#1a1a1a; }
.cell-driver { font-size:11px;color:#888;display:flex;align-items:center;gap:5px;flex-wrap:wrap;margin-top:3px; }
.cell-driver-plates { display:flex;gap:4px;margin-left:auto; }
.nr-rej { display:inline-block;background:#fff;border:2px solid #1a1a1a;padding:1px 5px;border-radius:4px;font-weight:800;font-size:11px; }

.goods-table { width:100%;border-collapse:collapse;border:1px solid #e8eaed; }
.goods-table tr { border-bottom:1px solid #e8eaed; }
.goods-table tr:last-child { border-bottom:none; }
.goods-table td { padding:3px 7px;font-size:12px;border-right:1px solid #e8eaed; }
.goods-table td:last-child { border-right:none; }
.g-name { color:#1a1a1a;font-weight:600;width:160px;min-width:160px;max-width:160px;white-space:nowrap; }
.g-name { display:flex;align-items:center;gap:4px; }
.g-name-text { overflow:hidden;text-overflow:ellipsis;flex:1;min-width:0; }
.g-bales { color:#1a1a1a;font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:800;text-align:right;white-space:nowrap;width:55px;min-width:55px; }
.g-weight { color:#1a1a1a;text-align:right;white-space:nowrap;width:80px;min-width:80px; }
.goods-sum { background:#f0fdf4; }
.goods-sum td { font-weight:800;font-size:12px; }

.weight-real { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#1a1a1a; }
.weight-none { color:#ddd;font-size:12px; }

.btn-revert-d { background:#fef9e7;border:1px solid #f9d38c;border-radius:6px;padding:6px 10px;color:#d68910;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:5px;white-space:nowrap; }
.btn-revert-d:hover { background:#f39c12;color:#fff; }
.btn-archive-d { background:#eaf8f0;border:1px solid #a9dfbf;border-radius:6px;padding:6px 10px;color:#219a52;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:5px;white-space:nowrap; }
.btn-archive-d:hover { background:#27ae60;color:#fff; }

.empty-state { text-align:center;padding:48px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }

.g-photos { width:36px;min-width:36px;text-align:center; }
.g-cam-btn {
    position: relative;
    background: transparent; border: none;
    color: #aaa; cursor: pointer; font-size: 13px;
    padding: 2px 4px;
}
.g-cam-btn.has-photos { color: #27ae60; }
.g-cam-btn:disabled { cursor: default; opacity: .35; }
.g-cam-badge {
    position: absolute; top: -4px; right: -2px;
    background: #27ae60; color: #fff;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 9px; font-weight: 900;
    min-width: 14px; height: 14px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    padding: 0 3px; line-height: 1;
    border: 1.5px solid #fff;
}

.report-gallery-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
.report-gallery-tile {
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    background: #f0f2f5;
    cursor: pointer;
}
.report-gallery-tile img { width:100%; height:100%; object-fit:cover; display:block; }
.report-lightbox-img { max-width:100%; max-height:70vh; border-radius:8px; object-fit:contain; }
.report-lightbox-counter { font-family:'Barlow Condensed',sans-serif; font-size:14px; color:#888; margin-bottom:8px; }
</style>
@endsection

@section('content')
<div class="report-wrap">

    <div class="report-header">
        <div class="report-title">
            <i class="fas fa-boxes" style="color:#27ae60"></i>
            Raport Dostaw
            <span class="badge-count">{{ $orders->count() }}</span>
        </div>
        <a href="{{ route('biuro.reports.deliveries.archived') }}" class="btn-archived">
            <i class="fas fa-archive"></i> Archiwum
        </a>
    </div>

    <form method="GET" action="{{ route('biuro.reports.deliveries') }}">
        <div class="filters">
            <div class="filter-group">
                <label>Data od</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from', now()->subMonths(3)->startOfMonth()->format('Y-m-d')) }}">
            </div>
            <div class="filter-group" style="justify-content:flex-end">
                <span class="btn-copy-date" onclick="copyDateTo()" title="Ustaw datę do = data od">
                    <i class="fas fa-angle-double-right"></i>
                </span>
            </div>
            <div class="filter-group">
                <label>Data do</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}">
            </div>
            <div class="filter-group">
                <label>Klient</label>
                <select name="client_id">
                    <option value="">– wszyscy –</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->short_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Towar</label>
                <select name="fraction_id">
                    <option value="">– wszystkie –</option>
                    @foreach($fractions as $f)
                        <option value="{{ $f->id }}" {{ request('fraction_id') == $f->id ? 'selected' : '' }}>
                            {{ $f->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Filtruj
            </button>
            @if(request()->hasAny(['client_id','fraction_id','date_from','date_to']))
            <a href="{{ route('biuro.reports.deliveries') }}" style="font-size:12px;color:#aaa;align-self:center">
                <i class="fas fa-times"></i> Wyczyść
            </a>
            @endif
        </div>
    </form>

    @if($orders->isEmpty())
    <div class="empty-state">
        <i class="fas fa-boxes"></i>
        <p style="font-size:15px;font-weight:600">Brak dostaw do wyświetlenia</p>
    </div>
    @else
    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Dostawca</th>
                    <th>Towary</th>
                    <th class="th-right">Waga kierowcy</th>
                    <th style="width:120px">Akcje</th>
                    <th>Operator</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                @php
                    $totalBales = $order->loadingItems->sum('bales');
                    $totalKg    = $order->loadingItems->sum('weight_kg');
                @endphp
                <tr id="row-{{ $order->id }}">
                    <td class="cell-date">{{ $order->planned_date->format('d.m.Y') }}</td>
                    <td>
                        <div class="cell-client">{{ $order->client?->short_name }}</div>
                        <div class="cell-driver">
                            <i class="fas fa-user" style="font-size:10px;color:#aaa"></i>
                            {{ $order->driver?->name }}
                            <span class="cell-driver-plates">
                                @if($order->tractor)<span class="nr-rej">{{ $order->tractor->plate }}</span>@endif
                                @if($order->trailer)<span class="nr-rej">{{ $order->trailer->plate }}</span>@endif
                            </span>
                        </div>
                    </td>
                    <td>
                        @if($order->loadingItems->isNotEmpty())
                        <table class="goods-table">
                            @foreach($order->loadingItems as $item)
                            @php $photoCount = $item->photos->count(); @endphp
                            <tr>
                                <td class="g-name">
                                    <span class="g-name-text">{{ $item->fraction?->name }}</span>
                                    @if($photoCount > 0)
                                    <button class="g-cam-btn has-photos"
                                            onclick='openReportGallery(@json($item->photos->map(fn($p) => ["url" => $p->url, "thumb_url" => $p->thumb_url])->values()), "{{ $item->fraction?->name }}")'
                                            title="{{ $photoCount }} zdjęć">
                                        <i class="fas fa-camera"></i>
                                        <span class="g-cam-badge">{{ $photoCount }}</span>
                                    </button>
                                    @endif
                                </td>
                                <td class="g-bales">{{ $item->bales }}</td>
                                <td class="g-weight">{{ number_format($item->weight_kg / 1000, 3, ',', ' ') }} t</td>
                            </tr>
                            @endforeach
                            @if($order->loadingItems->count() > 1)
                            <tr class="goods-sum">
                                <td style="color:#aaa;font-size:10px">&#8721;</td>
                                <td class="g-bales">{{ $totalBales }}</td>
                                <td class="g-weight" style="font-weight:800">{{ number_format($totalKg / 1000, 3, ',', ' ') }} t</td>
                            </tr>
                            @endif
                        </table>
                        @else
                        <span class="weight-none">brak pozycji</span>
                        @endif
                    </td>
                    <td class="td-right">
                        @if($order->weight_netto)
                            <div class="weight-real">{{ number_format($order->weight_netto, 3, ',', ' ') }} t</div>
                        @else
                            <span class="weight-none">–</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;flex-direction:row;gap:5px;flex-wrap:wrap">
                            <button class="btn-revert-d" onclick="revertDelivery({{ $order->id }})" title="Cofnij zamknięcie dostawy">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button class="btn-archive-d" onclick="archiveDelivery({{ $order->id }})" title="Przenieś do archiwum">
                                <i class="fas fa-archive"></i>
                            </button>
                        </div>
                    </td>
                    <td style="font-size:12px;color:#666">
                        {{ $order->loadingItems->pluck('operator.name')->filter()->unique()->implode(', ') ?: '–' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function copyDateTo() {
    const from = document.getElementById('date_from').value;
    if (from) document.getElementById('date_to').value = from;
}

async function archiveDelivery(id) {
    const result = await Swal.fire({
        title: 'Przenieść do archiwum?',
        text: 'Dostawa zniknie z listy aktywnych.',
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#27ae60',
        confirmButtonText: 'Tak, archiwizuj', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;
    const res  = await fetch(`/biuro/reports/deliveries/${id}/archive`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('row-' + id).remove();
        Swal.fire({ icon: 'success', title: 'Zarchiwizowano!', timer: 1500, showConfirmButton: false });
    }
}

async function openReportGallery(photos, label) {
    if (!photos || !photos.length) return;

    let html = `<div class="report-lightbox-counter">${label} — ${photos.length} ${photos.length === 1 ? 'zdjęcie' : 'zdjęć'}</div>`;
    html += '<div class="report-gallery-grid">';
    photos.forEach((p, idx) => {
        html += `<div class="report-gallery-tile" data-idx="${idx}"><img src="${p.thumb_url}" alt=""></div>`;
    });
    html += '</div>';

    let pickedIdx = null;
    await Swal.fire({
        title: 'Zdjęcia',
        html,
        showConfirmButton: true,
        confirmButtonText: 'Zamknij',
        confirmButtonColor: '#27ae60',
        width: 520,
        didOpen: () => {
            document.querySelectorAll('.report-gallery-tile').forEach(t => {
                t.addEventListener('click', () => {
                    pickedIdx = parseInt(t.dataset.idx);
                    Swal.close();
                });
            });
        },
    });

    if (pickedIdx !== null) {
        await openReportLightbox(photos, pickedIdx, label);
    }
}

async function openReportLightbox(photos, startIdx, label) {
    let idx = startIdx;
    while (true) {
        if (idx < 0) idx = photos.length - 1;
        if (idx >= photos.length) idx = 0;

        const p = photos[idx];
        const html = `
            <div class="report-lightbox-counter">${label} — ${idx + 1} / ${photos.length}</div>
            <img src="${p.url}" class="report-lightbox-img" alt="">`;
        const showNav = photos.length > 1;
        const result = await Swal.fire({
            html,
            showCancelButton: true,
            showDenyButton: showNav,
            showConfirmButton: showNav,
            confirmButtonText: '<i class="fas fa-chevron-right"></i>',
            denyButtonText:    '<i class="fas fa-chevron-left"></i>',
            cancelButtonText:  'Zamknij',
            confirmButtonColor: '#27ae60',
            denyButtonColor:    '#27ae60',
            cancelButtonColor:  '#888',
            reverseButtons: true,
            width: 700,
        });
        if (result.isConfirmed) { idx++; continue; }
        if (result.isDenied)    { idx--; continue; }
        break;
    }
}

async function revertDelivery(id) {
    const result = await Swal.fire({
        title: 'Cofnąć zamknięcie dostawy?',
        html: 'Towary zostaną usunięte z magazynu.<br>Status wróci do <b>Zważone</b>.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#27ae60',
        confirmButtonText: 'Tak, cofnij', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;
    const res  = await fetch(`/biuro/reports/deliveries/${id}/revert`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('row-' + id).remove();
        Swal.fire({ icon: 'success', title: 'Cofnięto!', timer: 1500, showConfirmButton: false });
    }
}
</script>
@endsection
