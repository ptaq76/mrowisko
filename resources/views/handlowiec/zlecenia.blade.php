@extends('layouts.app')
@section('title', 'Moje zlecenia')
@section('module_name', 'HANDLOWIEC')
@section('nav_menu') @include('handlowiec._nav') @endsection

@section('styles')
<style>
.h-wrap { padding:14px;max-width:600px;margin:0 auto; }
.h-page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px; }
.h-filter { display:flex;gap:8px;margin-bottom:14px; }
.h-filter select { flex:1;padding:10px 12px;border:1.5px solid #dde0e5;border-radius:10px;font-size:15px;outline:none;-webkit-appearance:none; }
.h-filter select:focus { border-color:#1a1a1a; }
.zlecenie-card { background:#fff;border-radius:12px;margin-bottom:12px;box-shadow:0 1px 4px rgba(0,0,0,.08);overflow:hidden; }
.zlecenie-card.odrzucone { opacity:.75; border-left: 4px solid #8e44ad; }
.z-header { padding:12px 14px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f0f2f5; }
.z-klient { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#1a1a1a; }
.z-data { font-size:12px;color:#888;font-weight:700; }
.z-body { padding:12px 14px; }
.z-towary { margin-bottom:8px; }
.z-towar-row { display:flex;justify-content:space-between;align-items:center;font-size:13px;padding:4px 0;border-bottom:1px solid #f8f9fa; }
.z-towar-row:last-child { border-bottom:none; }
.z-towar-nazwa { font-weight:600;color:#1a1a1a; }
.z-towar-meta { color:#888;font-size:12px; }
.z-status-row { display:flex;align-items:center;justify-content:space-between;margin-top:8px;padding-top:8px;border-top:1px solid #f0f2f5; }
.status-pill { display:inline-block;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:700;color:#fff; }
.z-order-status { font-size:11px;color:#888; }
.z-uwagi { font-size:12px;color:#aaa;margin-top:6px;font-style:italic; }
.empty-state { text-align:center;padding:48px 20px;color:#aaa; }
.empty-state i { font-size:40px;margin-bottom:10px;display:block; }
.z-odrzucone-info { font-size:11px;color:#8e44ad;margin-top:6px;font-weight:600; }
</style>
@endsection

@section('content')
<div class="h-wrap">
    <div class="h-page-title"><i class="fas fa-list-alt"></i> Moje zlecenia</div>

    <form method="GET" class="h-filter">
        <select name="client_id" onchange="this.form.submit()">
            <option value="">– Wszyscy klienci –</option>
            @foreach($klienci as $k)
            <option value="{{ $k->id }}" {{ request('client_id') == $k->id ? 'selected' : '' }}>
                {{ $k->short_name ?? $k->name }}
            </option>
            @endforeach
        </select>
    </form>

    @forelse($zlecenia as $z)
    @php
        $kolor = $z->statusColor();
        $isOdrzucone = $z->status === 'odrzucone_biuro';
    @endphp
    <div class="zlecenie-card {{ $isOdrzucone ? 'odrzucone' : '' }}">
        <div class="z-header">
            <div>
                <div class="z-klient">{{ $z->client?->short_name ?? $z->client?->name }}</div>
                <div class="z-data"><i class="fas fa-calendar-alt"></i> {{ $z->requested_date?->format('d.m.Y') }}</div>
            </div>
            <span class="status-pill" style="background:{{ $kolor }}">{{ $z->statusLabel() }}</span>
        </div>
        <div class="z-body">
            <div class="z-towary">
                @foreach($z->items as $item)
                <div class="z-towar-row">
                    <span class="z-towar-nazwa">{{ $item->nazwa }}</span>
                    <span class="z-towar-meta">
                        @if($item->ilosc) {{ $item->ilosc }} @endif
                        @if($item->cena) · {{ number_format($item->cena, 2, ',', ' ') }} €/t @endif
                    </span>
                </div>
                @endforeach
            </div>

            @if($isOdrzucone)
                <div class="z-odrzucone-info">
                    <i class="fas fa-ban"></i> To zlecenie zostało odrzucone przez biuro i nie będzie realizowane.
                </div>
            @endif

            @if($z->notes)
                <div class="z-uwagi">{{ $z->notes }}</div>
            @endif

            <div class="z-status-row">
                <span style="font-size:11px;color:#ccc">{{ $z->created_at->format('d.m.Y H:i') }}</span>
                @if($z->order)
                    <span class="z-order-status">
                        <i class="fas fa-link"></i> Zlecenie: <strong>{{ $z->order->status }}</strong>
                    </span>
                @endif
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