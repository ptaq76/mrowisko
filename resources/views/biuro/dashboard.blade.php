@extends('layouts.app')

@section('title', 'Dashboard')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.dashboard-tile {
    transition: transform .15s, box-shadow .15s;
    cursor: pointer;
    color: var(--black);
}
.dashboard-tile:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,.1);
    border-color: var(--green);
}
</style>
@endsection

@section('content')
<div class="page-header">
    <h1>Panel biura</h1>
</div>

<div class="row g-3" style="max-width:900px">
    <div class="col-md-4">
        <a href="{{ route('biuro.planning.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-calendar-alt fa-2x mb-2" style="color:#6EBF58"></i>
                <div class="fw-bold">Planowanie</div>
                <div class="text-muted small mt-1">Kalendarz zleceń kierowców</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('biuro.ls.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-id-badge fa-2x mb-2" style="color:#3498db"></i>
                <div class="fw-bold">Lieferschein</div>
                <div class="text-muted small mt-1">Lista LS, dodawanie, edycja</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('biuro.clients.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-building fa-2x mb-2" style="color:#9b59b6"></i>
                <div class="fw-bold">Kontrahenci</div>
                <div class="text-muted small mt-1">Baza klientów, adresy, kontakty</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('biuro.vehicles.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-truck fa-2x mb-2" style="color:#e67e22"></i>
                <div class="fw-bold">Pojazdy</div>
                <div class="text-muted small mt-1">Ciągniki, naczepy, solo</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('biuro.fractions.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-recycle fa-2x mb-2" style="color:#27ae60"></i>
                <div class="fw-bold">Frakcje odpadów</div>
                <div class="text-muted small mt-1">Rodzaje i formy towarów</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('biuro.importers.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-ship fa-2x mb-2" style="color:#2980b9"></i>
                <div class="fw-bold">Importerzy</div>
                <div class="text-muted small mt-1">Lista importerów LS</div>
            </div>
        </a>
    </div>
</div>
@endsection
