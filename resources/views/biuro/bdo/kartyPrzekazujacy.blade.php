@extends('layouts.app')

@section('title', 'BDO - Przekazujący')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
</style>
<style>
    :root {
        --bdo-blue: #4a69bd;
        --bdo-blue-dark: #1e3799;
        --bdo-bg-dark: #2c3e50;
        --filter-bg: #4a69bd;
    }

    /* Pasek zakładek - NIEBIESKI */
    #main .nav-tabs {
        background: var(--bdo-blue) !important;
        padding: 12px 15px 0 15px !important;
        border-radius: 8px 8px 0 0 !important;
        border-bottom: none !important;
        margin-bottom: 0 !important;
        display: flex !important;
        align-items: center !important;
    }

    /* Nieaktywne zakładki */
    #main .nav-tabs .nav-link {
        color: #ffffff !important;
        font-weight: 500 !important;
        background-color: rgba(255, 255, 255, 0.15) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        margin-right: 5px !important;
        padding: 10px 15px !important;
    }

    /* AKTYWNA ZAKŁADKA */
    #main .nav-tabs .nav-link.active {
        color: #ffffff !important;
        background-color: var(--bdo-blue-dark) !important;
        border: 1px solid var(--bdo-blue-dark) !important;
        border-bottom: 3px solid #00d8ff !important;
        font-weight: 800 !important;
    }

    /* Hover zakładki */
    #main .nav-tabs .nav-link:hover:not(.active) {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    /* BADGE */
    #main .nav-tabs .nav-link .badge {
        font-size: 0.75rem !important;
        padding: 4px 8px !important;
        margin-left: 8px !important;
    }

    /* Badge nieaktywny */
    #main .nav-tabs .nav-link:not(.active) .badge {
        background-color: rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
    }

    /* Badge aktywny */
    #main .nav-tabs .nav-link.active .badge {
        background-color: var(--bdo-blue-dark) !important;
        color: #ffffff !important;
        font-weight: 900 !important;
        border: 1px solid rgba(255,255,255,.3) !important;
    }

    /* Tytuł PRZEKAZUJĄCY */
    .view-title-nav {
        margin-left: auto !important;
        color: #000000 !important;
        font-weight: 900 !important;
        font-size: 1.35rem !important;
        letter-spacing: .04em !important;
        display: flex !important;
        align-items: center !important;
        padding-bottom: 8px;
    }

    .view-title-nav i {
        color: #000000 !important;
        margin-right: 8px !important;
    }

    /* Filtry - NIEBIESKI */
    .filtry-container {
        background-color: var(--bdo-blue) !important;
        padding: 15px 20px;
        border-radius: 0 0 8px 8px;
        border: 1px solid var(--bdo-blue-dark);
        border-top: none;
        margin-bottom: 20px;
    }

    /* Etykiety filtrów - białe na niebieskim tle */
    .filtry-container .form-label {
        color: rgba(255,255,255,.8) !important;
    }

    .vr {
        width: 1px;
        background-color: rgba(255,255,255,.3);
        align-self: stretch;
        margin: 0 15px;
        opacity: 0.8;
    }

    /* Tabela */
    .table-wrapper {
        background-color: #ffffff;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e1e8ed;
    }

    .table-dark { background-color: var(--bdo-bg-dark); }
    table { table-layout: fixed; width: 100%; }
    th, td { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
@endsection

@section('content')
<div id="poll-area" class="container-fluid px-3">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'wszystkie' ? 'active' : '' }}"
               href="{{ route('biuro.bdo.kartyPrzekazujacy', ['status' => 'wszystkie', 'nowe' => $nowe ? '1' : '0']) }}">
                Wszystkie <span class="badge">{{ array_sum($statusCounts) }}</span>
            </a>
        </li>
        @php
            $tabs = [
                'wygenerowane' => ['label' => 'Potw. wygenerowane', 'key' => 'Potwierdzenie wygenerowane'],
                'przejecie'    => ['label' => 'Potw. przejęcia',    'key' => 'Potwierdzenie przejęcia'],
                'transport'    => ['label' => 'Potw. transportu',   'key' => 'Potwierdzenie transportu'],
                'zatwierdzone' => ['label' => 'Zatwierdzone',       'key' => 'Zatwierdzona'],
                'planowane'    => ['label' => 'Planowane',          'key' => 'Planowana'],
                'odrzucone'    => ['label' => 'Odrzucone',          'key' => 'Odrzucona'],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
            <li class="nav-item">
                <a class="nav-link {{ $status === $key ? 'active' : '' }}"
                   href="{{ route('biuro.bdo.kartyPrzekazujacy', ['status' => $key, 'nowe' => $nowe ? '1' : '0']) }}">
                    {{ $tab['label'] }} <span class="badge">{{ $statusCounts[$tab['key']] ?? 0 }}</span>
                </a>
            </li>
        @endforeach

        <div class="view-title-nav">
            <i class="fa-solid fa-recycle"></i> PRZEKAZUJĄCY
        </div>
    </ul>

    <div class="filtry-container">
        <form method="GET" action="{{ route('biuro.bdo.kartyPrzekazujacy') }}" class="d-flex align-items-end gap-2">
            <input type="hidden" name="status" value="{{ $status }}">

            <div class="d-flex flex-column">
                <label class="form-label mb-1 small fw-bold">Status</label>
                <button type="submit" name="nowe" value="{{ $nowe ? '0' : '1' }}"
                        class="btn {{ $nowe ? 'btn-dark' : 'btn-outline-light' }} btn-sm">
                    <i class="fa-solid fa-clock-rotate-left" style="color:#000000"></i> NOWE
                </button>
            </div>

            <div class="vr"></div>

            <div class="d-flex flex-column">
                <label class="form-label mb-1 small fw-bold">Przejmujący</label>
                <select name="przejmujacy" class="form-select form-select-sm" style="width: 250px;" onchange="this.form.submit()">
                    <option value="">Wszystkie</option>
                    @foreach($przejmujacyList as $odbiorca)
                        <option value="{{ $odbiorca }}" {{ $przejmujacy == $odbiorca ? 'selected' : '' }}>{{ Str::limit($odbiorca, 40) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex flex-column">
                <label class="form-label mb-1 small fw-bold">Transportujący</label>
                <select name="transportujacy" class="form-select form-select-sm" style="width: 180px;" onchange="this.form.submit()">
                    <option value="">Wszystkie</option>
                    @foreach($transportujacyList as $przewoznik)
                        <option value="{{ $przewoznik }}" {{ $transportujacy == $przewoznik ? 'selected' : '' }}>{{ Str::limit($przewoznik, 25) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex flex-column">
                <label class="form-label mb-1 small fw-bold">Kod odpadu</label>
                <select name="kod_odpadu" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                    <option value="">Wszystkie</option>
                    @foreach($kodyOdpadow as $kod)
                        <option value="{{ $kod }}" {{ $kodOdpadu == $kod ? 'selected' : '' }}>{{ $kod }}</option>
                    @endforeach
                </select>
            </div>

            @if($nowe || $przejmujacy || $transportujacy || $kodOdpadu)
                <div class="d-flex flex-column ms-2">
                    <a href="{{ route('biuro.bdo.kartyPrzekazujacy', ['status' => $status]) }}" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-filter-circle-xmark"></i> Wyczyść
                    </a>
                </div>
            @endif
        </form>
    </div>

    <div class="table-wrapper">
        <table class="table table-bordered table-hover table-sm align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 5%;">Nr karty</th>
                    <th style="width: 7%;">Data trans.</th>
                    <th style="width: 22%;">Przejmujący</th>
                    <th style="width: 6%;">Kod</th>
                    <th style="width: 7%;">Masa</th>
                    <th style="width: 8%;">Korekta</th>
                    <th style="width: 12%;">Transportujący</th>
                    <th style="width: 8%;">Nr rej.</th>
                    <th style="width: 10%;">Ost. zmiana</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karty as $karta)
                    <tr style="height: 50px;">
                        <td style="white-space: normal; font-size: 0.8rem;">{{ $karta->card_status }}</td>
                        <td class="text-center fw-bold">{{ substr($karta->card_number, 0, 5) }}</td>
                        <td class="text-center">{{ $karta->real_transport_date ? $karta->real_transport_date->format('Y-m-d') : '—' }}</td>
                        <td class="fw-bold" style="font-size: 0.85rem;">{{ Str::limit($karta->receiver_name_or_first_name_and_last_name, 35) }}</td>
                        <td class="text-center">{{ mb_substr($karta->waste_code_and_description, 0, 8, 'UTF-8') }}</td>
                        <td class="text-end fw-bold">{{ number_format($karta->waste_mass, 3, ',', ' ') }}</td>
                        <td class="text-center" style="font-size: 0.8rem;">—</td>
                        <td style="font-size: 0.8rem;">{{ Str::limit($karta->carrier_name_or_first_name_and_last_name, 20) }}</td>
                        <td class="text-center">{{ $karta->vehicle_reg_number }}</td>
                        <td class="text-center small">{{ $karta->kpo_last_modified_at ? $karta->kpo_last_modified_at->format('Y-m-d H:i') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center py-5 text-muted">Brak kart do wyświetlenia</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
// POLLING: lista kart przekazujących odświeża się sama co 5s
if (window.pollPageFragment) {
    window.pollPageFragment('poll-area', 5000);
}
</script>
@endsection