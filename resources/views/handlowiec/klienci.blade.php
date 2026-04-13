@extends('layouts.handlowiec')
@section('title', 'Klienci')
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
    text-decoration:none;color:#555;transition:background .12s;
}
.h-back-btn:hover { background:#e2e5e9;color:#1a1a1a; }
.h-page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px; }
.klient-card { background:#fff;border-radius:12px;margin-bottom:10px;box-shadow:0 1px 4px rgba(0,0,0,.08);display:flex;align-items:center;justify-content:space-between;padding:14px 16px; }
.k-name { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#1a1a1a; }
.k-city { font-size:12px;color:#888;margin-top:2px; }
.k-edit { padding:8px 14px;background:#f4f5f7;border:1.5px solid #dde0e5;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;color:#555; }
.empty-state { text-align:center;padding:48px 20px;color:#aaa; }
</style>
@endsection

@section('content')
<div class="h-wrap">
    <a href="{{ route('handlowiec.dashboard') }}" class="h-back-btn">
        <i class="fas fa-home"></i> Powrót
    </a>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <div class="h-page-title" style="margin-bottom:0"><i class="fas fa-building"></i> Moi klienci</div>
        <a href="{{ route('handlowiec.nowy-klient') }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:#1a1a1a;color:#fff;border-radius:12px;font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:900;letter-spacing:.04em;text-transform:uppercase;text-decoration:none;">
            <i class="fas fa-plus-circle"></i> Nowy klient
        </a>
    </div>

    @forelse($klienci as $k)
    <div class="klient-card">
        <div>
            <div class="k-name">{{ $k->short_name ?? $k->name }}</div>
            <div class="k-city">
                {{ $k->city }}{{ $k->city && $k->phone ? ' · ' : '' }}{{ $k->phone }}
            </div>
        </div>
        <a href="{{ route('handlowiec.klient-edit', $k) }}" class="k-edit">
            <i class="fas fa-pen"></i> Edytuj
        </a>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-building" style="font-size:40px;display:block;margin-bottom:10px"></i>
        <p>Brak przypisanych klientów</p>
    </div>
    @endforelse
</div>
@endsection
