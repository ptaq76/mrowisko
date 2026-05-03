@extends('layouts.plac')

@section('title', 'Plac')

@section('styles')
<style>
.tile-menu {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 20px 0;
}

.tile {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 22px 16px;
    border-radius: 10px;
    text-decoration: none;
    color: #fff;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 20px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    box-shadow: 0 3px 8px rgba(0,0,0,.15);
    transition: filter .15s;
}
.tile:active { filter: brightness(.88); }
.tile i { font-size: 22px; }

.tile-plan        { background: #7f8c8d; }
.tile-production  { background: #2980b9; }
.tile-delivery    { background: #27ae60; }
.tile-loading     { background: #f39c12; }
.tile-weighing    { background: #34495e; }
.tile-warehouse   { background: #16a085; }
.tile-inventory   { background: #c0392b; }
.tile-fuel        { background: #f39c12; }
</style>
@endsection

@section('content')

@php $date = \Carbon\Carbon::today(); @endphp

<div class="tile-menu">

    <a href="{{ route('plac.orders') }}" class="tile tile-plan">
        <i class="fas fa-calendar-day"></i> Plan dnia
    </a>

    <a href="{{ route('plac.production.index') }}" class="tile tile-production">
        <i class="fas fa-cogs"></i> Produkcja belek
    </a>

    <a href="{{ route('plac.delivery.index') }}" class="tile tile-delivery">        <i class="fas fa-truck-loading"></i> Przyjęcie towaru
    </a>

    <a href="{{ route('plac.loading.index') }}" class="tile tile-loading">
        <i class="fas fa-truck-moving"></i> Załadunki
    </a>

    <a href="{{ route('plac.weighing.form') }}" class="tile tile-weighing">
        <i class="fas fa-weight"></i> Waga
    </a>

    <a href="{{ route('plac.warehouse.index') }}" class="tile tile-warehouse">
        <i class="fas fa-warehouse"></i> Magazyn
    </a>

    <a href="{{ route('plac.inventory.index') }}" class="tile tile-inventory">
        <i class="fas fa-clipboard-list"></i> Inwentaryzacja
    </a>
    <a href="{{ route('plac.fuel.index') }}" class="tile tile-fuel">
        <i class="fas fa-gas-pump"></i> Paliwo
    </a>

</div>

@endsection
