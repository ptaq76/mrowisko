@extends('layouts.handlowiec')
@section('title', 'Moje zlecenia')
@section('module_name', 'HANDLOWIEC')
@section('nav_menu') @include('handlowiec._nav') @endsection

@section('styles')
<style>
.h-wrap { padding:14px;max-width:600px;margin:0 auto; }
.h-back-btn {
    display:flex;align-items:center;justify-content:center;gap:10px;
    width:100%;padding:14px;margin-bottom:18px;
    background:#f4f5f7;border:1.5px solid #dde0e5;border-radius:12px;
    font-family:'Barlow Condensed',sans-serif;font-size:17px;font-weight:900;
    letter-spacing:.04em;text-transform:uppercase;
    text-decoration:none;color:#555;
    transition:background .12s;
}
.h-back-btn:hover { background:#e2e5e9;color:#1a1a1a; }
.h-page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px; }
.h-filters { display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap; }
.h-filters select {
    flex:1;min-width:140px;padding:10px 12px;
    border:1.5px solid #dde0e5;border-radius:10px;
    font-size:14px;outline:none;-webkit-appearance:none;background:#fff;
}
.h-filters select:focus { border-color:#1a1a1a; }
.zlecenie-card { background:#fff;border-radius:12px;margin-bottom:12px;box-shadow:0 1px 4px rgba(0,0,0,.08);overflow:hidden; }
.zlecenie-card.odrzucone { opacity:.75;border-left:4px solid #8e44ad; }
.z-header { padding:12px 14px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f0f2f5; }
.z-klient { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#1a1a1a; }
.z-data { font-size:12px;color:#888;font-weight:700; }
.z-body { padding:12px 14px; }
.status-pill { display:inline-block;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:700;color:#fff;white-space:nowrap; }
.z-uwagi { font-size:12px;color:#aaa;margin-top:6px;font-style:italic; }
.z-footer { display:flex;align-items:center;justify-content:space-between;margin-top:8px;padding-top:8px;border-top:1px solid #f0f2f5; }
.z-odrzucone-info { font-size:11px;color:#8e44ad;margin-top:6px;font-weight:600; }
.z-items-table { width:100%;border-collapse:collapse;margin-bottom:6px; }
.z-items-table td { padding:3px 0;font-size:13px; }
.z-items-table .td-nazwa { font-weight:600;color:#1a1a1a; }
.z-items-table .td-ilosc { color:#666;text-align:right;white-space:nowrap;padding-left:10px; }
.z-items-table .td-cena  { color:#1a1a1a;font-weight:700;text-align:right;white-space:nowrap;padding-left:10px; }
.empty-state { text-align:center;padding:48px 20px;color:#aaa; }
.empty-state i { font-size:40px;margin-bottom:10px;display:block; }
</style>
@endsection

@section('content')
<div class="h-wrap">

    <a href="{{ route('handlowiec.dashboard') }}" class="h-back-btn">
        <i class="fas fa-home"></i> Powrót
    </a>

    <div class="h-page-title"><i class="fas fa-list-alt"></i> Moje zlecenia</div>

    <form method="GET" class="h-filters">
        <select name="client_id" onchange="this.form.submit()">
            <option value="">– Wszyscy klienci –</option>
            @foreach($klienci as $k)
            <option value="{{ $k->id }}" {{ request('client_id') == $k->id ? 'selected' : '' }}>
                {{ $k->short_name ?? $k->name }}
            </option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">– Wszystkie statusy –</option>
            <option value="nowe"            {{ request('status') === 'nowe'            ? 'selected' : '' }}>Oczekuje (bez zlecenia)</option>
            <option value="przyjete"        {{ request('status') === 'przyjete'        ? 'selected' : '' }}>W realizacji</option>
            <option value="zrealizowane"    {{ request('status') === 'zrealizowane'    ? 'selected' : '' }}>Zrealizowane</option>
            <option value="odrzucone_biuro" {{ request('status') === 'odrzucone_biuro' ? 'selected' : '' }}>Odrzucone przez biuro</option>
            <option value="anulowane"       {{ request('status') === 'anulowane'       ? 'selected' : '' }}>Anulowane</option>
        </select>
    </form>

    @forelse($zlecenia as $z)
    @php
        $isOdrzucone = $z->status === 'odrzucone_biuro';

        $orderStatusLabels = [
            'planned'  => 'Przyjęte przez biuro',
            'weighed'  => 'Zważone',
            'loaded'   => 'Załadowane',
            'delivered'=> 'Dostarczone',
            'closed'   => 'Zrealizowane',
        ];
        $orderStatusColors = [
            'planned'  => '#2980b9',
            'weighed'  => '#3498db',
            'loaded'   => '#f39c12',
            'delivered'=> '#27ae60',
            'closed'   => '#27ae60',
        ];

        if ($z->status === 'odrzucone_biuro') {
            $statusLabel = 'Odrzucone przez biuro';
            $statusColor = '#8e44ad';
        } elseif ($z->order) {
            $statusLabel = $orderStatusLabels[$z->order->status] ?? $z->order->status;
            $statusColor = $orderStatusColors[$z->order->status] ?? '#aaa';
        } else {
            $statusLabel = 'Oczekuje';
            $statusColor = '#f39c12';
        }
    @endphp
    <div class="zlecenie-card {{ $isOdrzucone ? 'odrzucone' : '' }}">
        <div class="z-header">
            <div>
                <div class="z-klient">{{ $z->client?->short_name ?? $z->client?->name }}</div>
                <div class="z-data"><i class="fas fa-calendar-alt"></i> {{ $z->requested_date?->format('d.m.Y') }}</div>
            </div>
            <span class="status-pill" style="background:{{ $statusColor }}">{{ $statusLabel }}</span>
        </div>
        <div class="z-body">
            @if($z->items->isNotEmpty())
            <table class="z-items-table">
                @foreach($z->items as $item)
                <tr>
                    <td class="td-nazwa">{{ $item->nazwa }}</td>
                    <td class="td-ilosc">{{ $item->ilosc ?? '' }}</td>
                    <td class="td-cena">@if($item->cena){{ number_format($item->cena, 2, ',', ' ') }} zł/t @endif</td>
                </tr>
                @endforeach
            </table>
            @endif

            @if($isOdrzucone)
                <div class="z-odrzucone-info">
                    <i class="fas fa-ban"></i> To zlecenie zostało odrzucone przez biuro i nie będzie realizowane.
                </div>
            @endif

            @if($z->notes)
                <div class="z-uwagi">{{ $z->notes }}</div>
            @endif

            <div class="z-footer">
                <span style="font-size:11px;color:#ccc">{{ $z->created_at->format('d.m.Y H:i') }}</span>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p>Brak zleceń</p>
        <a href="{{ route('handlowiec.nowe-zlecenie') }}" style="color:#1a1a1a;font-weight:700">
            + Dodaj pierwsze zlecenie
        </a>
    </div>
    @endforelse
</div>
@endsection