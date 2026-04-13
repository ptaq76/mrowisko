@extends('layouts.handlowiec')
@section('title', 'Handlowiec')
@section('module_name', 'HANDLOWIEC')
@section('nav_menu') @include('handlowiec._nav') @endsection

@section('styles')
<style>
.h-dashboard {
    padding: 16px;
    max-width: 480px;
    margin: 0 auto;
}
.h-btn-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
    margin-top: 8px;
}
.h-btn {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 22px 24px;
    border-radius: 20px;
    border: none;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 24px;
    font-weight: 900;
    letter-spacing: .06em;
    text-transform: uppercase;
    text-decoration: none;
    cursor: pointer;
    transition: transform .12s, box-shadow .15s;
    box-shadow: 0 4px 14px rgba(0,0,0,.12);
    position: relative;
    overflow: hidden;
}
.h-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,.08);
    opacity: 0;
    transition: opacity .15s;
}
.h-btn:hover::after { opacity: 1; }
.h-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.16); }
.h-btn:active { transform: scale(.98); }

.h-btn i {
    font-size: 26px;
    width: 38px;
    text-align: center;
    flex-shrink: 0;
}
.h-btn-sub {
    font-size: 12px;
    font-weight: 500;
    letter-spacing: .04em;
    opacity: .7;
    text-transform: none;
    font-family: 'Barlow', sans-serif;
    display: block;
    margin-top: 1px;
}
.h-btn-primary { background: linear-gradient(135deg, #1a1a1a, #3a3a3a); color: #fff; }
.h-btn-blue    { background: linear-gradient(135deg, #2471a3, #2980b9); color: #fff; }
.h-btn-green   { background: linear-gradient(135deg, #1e8449, #27ae60); color: #fff; }
</style>
@endsection

@section('content')
<div class="h-dashboard">
    <div class="h-btn-grid">
        <a href="{{ route('handlowiec.nowe-zlecenie') }}" class="h-btn h-btn-primary">
            <i class="fas fa-plus-circle"></i>
            <div>
                Nowe zlecenie
                <span class="h-btn-sub">Wyślij zamówienie odbioru</span>
            </div>
        </a>
        <a href="{{ route('handlowiec.zlecenia') }}" class="h-btn h-btn-blue">
            <i class="fas fa-list-alt"></i>
            <div>
                Moje zlecenia
                <span class="h-btn-sub">Historia i statusy</span>
            </div>
        </a>
        <a href="{{ route('handlowiec.klienci') }}" class="h-btn h-btn-green">
            <i class="fas fa-building"></i>
            <div>
                Klienci
                <span class="h-btn-sub">Zarządzaj kontrahentami</span>
            </div>
        </a>
    </div>
</div>
@endsection