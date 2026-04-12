@extends('layouts.app')
@section('title', 'Handlowiec')
@section('module_name', 'HANDLOWIEC')
@section('nav_menu') @include('handlowiec._nav') @endsection

@section('styles')
<style>
.h-dashboard { padding:24px 16px;max-width:480px;margin:0 auto; }
.h-greeting { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;letter-spacing:.04em;margin-bottom:24px;color:#1a1a1a; }
.h-btn-grid { display:grid;grid-template-columns:1fr;gap:14px; }
.h-btn {
    display:flex;align-items:center;gap:16px;
    padding:20px 22px;border-radius:14px;border:none;
    font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;
    letter-spacing:.04em;text-transform:uppercase;
    text-decoration:none;cursor:pointer;
    transition:transform .12s,box-shadow .12s;
    box-shadow:0 2px 8px rgba(0,0,0,.10);
}
.h-btn:active { transform:scale(.97); }
.h-btn i { font-size:28px;width:36px;text-align:center; }
.h-btn-primary { background:#1a1a1a;color:#fff; }
.h-btn-blue    { background:#2980b9;color:#fff; }
.h-btn-green   { background:#27ae60;color:#fff; }
</style>
@endsection

@section('content')
<div class="h-dashboard">
    <div class="h-greeting">
        Cześć, {{ auth()->user()->name }}!
    </div>
    <div class="h-btn-grid">
        <a href="{{ route('handlowiec.nowe-zlecenie') }}" class="h-btn h-btn-primary">
            <i class="fas fa-plus-circle"></i>
            Nowe zlecenie
        </a>
        <a href="{{ route('handlowiec.zlecenia') }}" class="h-btn h-btn-blue">
            <i class="fas fa-list-alt"></i>
            Moje zlecenia
        </a>
        <a href="{{ route('handlowiec.klienci') }}" class="h-btn h-btn-green">
            <i class="fas fa-building"></i>
            Klienci
        </a>
    </div>
</div>
@endsection
