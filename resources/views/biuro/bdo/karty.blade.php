@extends('layouts.app')

@section('title', 'BDO - Przejmujący')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
</style>
<style>
    :root {
        --bdo-green:       #1e8449;
        --bdo-green-dark:  #145a32;
        --bdo-green-light: #d5f5e3;
        --bdo-bg-dark:     #145a32;
    }

    /* Pasek zakładek */
    #main .nav-tabs {
        background: var(--bdo-green) !important;
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
        font-size: 0.85rem !important;
        background-color: rgba(255, 255, 255, 0.15) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        border-radius: 6px 6px 0 0 !important;
        margin-right: 4px !important;
        padding: 10px 15px !important;
    }

    #main .nav-tabs .nav-link:hover:not(.active) {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    /* Aktywna zakładka */
    #main .nav-tabs .nav-link.active {
        color: #ffffff !important;
        font-weight: 800 !important;
        font-size: 0.85rem !important;
        background-color: var(--bdo-green-dark) !important;
        border: 1px solid var(--bdo-green-dark) !important;
        border-bottom: 3px solid #00d8ff !important;
    }

    /* Badge nieaktywny */
    #main .nav-tabs .nav-link:not(.active) .badge {
        background-color: rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
        font-size: 0.75rem !important;
        padding: 4px 8px !important;
        margin-left: 8px !important;
    }

    /* Badge aktywny */
    #main .nav-tabs .nav-link.active .badge {
        background-color: var(--bdo-green-dark) !important;
        color: #ffffff !important;
        font-weight: 900 !important;
        font-size: 0.75rem !important;
        padding: 4px 8px !important;
        margin-left: 8px !important;
        border: 1px solid rgba(255,255,255,.3) !important;
    }

    /* Filtry */
    .filtry-container {
        background-color: var(--bdo-green) !important;
        padding: 15px 20px;
        border-radius: 0 0 8px 8px;
        border: 1px solid var(--bdo-green-dark);
        border-top: none;
        margin-bottom: 20px;
    }

    .filtry-container .form-label {
        color: rgba(255,255,255,.85) !important;
    }

    .vr {
        width: 1px;
        background-color: rgba(255,255,255,.3);
        align-self: stretch;
        margin: 0 12px;
        opacity: 0.8;
    }

    .filtry-container .form-select {
        border: 1px solid #999;
        background-color: #ffffff;
    }

    .filtry-container .form-select:focus {
        border-color: var(--bdo-green-dark);
        box-shadow: 0 0 0 0.2rem rgba(20,90,50,.25);
    }

    /* Przyciski */
    .btn-czarny {
        background: var(--bdo-green-dark);
        color: #ffffff;
        border: 2px solid var(--bdo-green-dark);
        font-weight: 600;
    }

    .btn-czarny:hover {
        background: #0e3d22;
        color: #ffffff;
    }

    .btn-outline-czarny {
        background-color: #ffffff;
        color: var(--bdo-green-dark);
        border: 2px solid var(--bdo-green-dark);
        font-weight: 600;
    }

    .btn-outline-czarny:hover {
        background: var(--bdo-green-dark);
        color: #ffffff;
    }

    /* Badge tytułu PRZEJMUJĄCY */
    .view-title-badge {
        background: var(--bdo-green-dark);
        color: #ffffff;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 900;
        font-size: 1.2rem;
        letter-spacing: .04em;
        box-shadow: 0 4px 12px rgba(0,0,0,.3);
        display: flex;
        align-items: center;
    }

    /* Tabela */
    .table-wrapper {
        background-color: #ffffff;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e1e8ed;
    }

    .table-dark { background-color: var(--bdo-bg-dark) !important; }

    .table-wrapper tbody tr:hover {
        background-color: var(--bdo-green-light) !important;
    }

    table { width: 100%; table-layout: fixed; }
    th, td { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>
@endsection

@section('content')
<div class="container-fluid px-3">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'wszystkie' ? 'active' : '' }}"
               href="{{ route('biuro.bdo.karty', ['status' => 'wszystkie', 'nowe' => $nowe ? '1' : '0']) }}">
                Wszystkie
                <span class="badge ms-1">{{ array_sum($statusCounts) }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'wygenerowane' ? 'active' : '' }}"
               href="{{ route('biuro.bdo.karty', ['status' => 'wygenerowane', 'nowe' => $nowe ? '1' : '0']) }}">
                Potw. wygenerowane
                <span class="badge ms-1">{{ $statusCounts['Potwierdzenie wygenerowane'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'przejecie' ? 'active' : '' }}"
               href="{{ route('biuro.bdo.karty', ['status' => 'przejecie', 'nowe' => $nowe ? '1' : '0']) }}">
                Potw. przejęcia
                <span class="badge ms-1">{{ $statusCounts['Potwierdzenie przejęcia'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'transport' ? 'active' : '' }}"
               href="{{ route('biuro.bdo.karty', ['status' => 'transport', 'nowe' => $nowe ? '1' : '0']) }}">
                Potw. transportu
                <span class="badge ms-1">{{ $statusCounts['Potwierdzenie transportu'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'zatwierdzone' ? 'active' : '' }}"
               href="{{ route('biuro.bdo.karty', ['status' => 'zatwierdzone', 'nowe' => $nowe ? '1' : '0']) }}">
                Zatwierdzone
                <span class="badge ms-1">{{ $statusCounts['Zatwierdzona'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'planowane' ? 'active' : '' }}"
               href="{{ route('biuro.bdo.karty', ['status' => 'planowane', 'nowe' => $nowe ? '1' : '0']) }}">
                Planowane
                <span class="badge ms-1">{{ $statusCounts['Planowana'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'odrzucone' ? 'active' : '' }}"
               href="{{ route('biuro.bdo.karty', ['status' => 'odrzucone', 'nowe' => $nowe ? '1' : '0']) }}">
                Odrzucone
                <span class="badge ms-1">{{ $statusCounts['Odrzucona'] ?? 0 }}</span>
            </a>
        </li>
    </ul>

    {{-- Filtry --}}
    <div class="filtry-container">
        <form method="GET" action="{{ route('biuro.bdo.karty') }}" class="d-flex align-items-end gap-2">
            <input type="hidden" name="status" value="{{ $status }}">

            @if($nowe)
                <input type="hidden" name="nowe" value="1">
            @endif

            <div class="d-flex flex-column">
                <label class="form-label mb-1 small fw-bold">&nbsp;</label>
                <button type="submit" name="nowe" value="{{ $nowe ? '0' : '1' }}"
                        class="btn {{ $nowe ? 'btn-czarny' : 'btn-outline-light' }} btn-sm d-flex align-items-center gap-1">
                    <i class="fa-solid fa-clock-rotate-left" style="color:#000000"></i>
                    NOWE
                </button>
            </div>

            <div class="vr"></div>

            <div class="d-flex flex-column">
                <label class="form-label mb-1 small fw-bold">Przekazujący</label>
                <select name="przekazujacy" class="form-select form-select-sm" style="width: 250px;" onchange="this.form.submit()">
                    <option value="">Wszystkie</option>
                    @foreach($przekazujacyList as $nadawca)
                        @php
                            $nazwaPrzekazujacegoOryg = $nadawca;
                            $nazwaPrzekazujacegoFormat = preg_replace('/\s+/u', ' ', trim($nadawca));
                            $nazwaPrzekazujacegoFormat = mb_strtoupper($nazwaPrzekazujacegoFormat, 'UTF-8');
                            $nazwaPrzekazujacegoFormat = str_ireplace(
                                ['SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ', 'SPÓŁKA KOMANDYTOWA', 'SPÓŁKA JAWNA'],
                                ['SP. Z O.O.', 'SP. K.', 'SP. J.'],
                                $nazwaPrzekazujacegoFormat
                            );
                        @endphp
                        <option value="{{ $nazwaPrzekazujacegoOryg }}" {{ $przekazujacy == $nazwaPrzekazujacegoOryg ? 'selected' : '' }}>
                            {{ Str::limit($nazwaPrzekazujacegoFormat, 40) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex flex-column">
                <label class="form-label mb-1 small fw-bold">Transportujący</label>
                <select name="transportujacy" class="form-select form-select-sm" style="width: 180px;" onchange="this.form.submit()">
                    <option value="">Wszystkie</option>
                    @foreach($transportujacyList as $przewoznik)
                        @php
                            $transportujacyOryg = $przewoznik;
                            $transportujacyFormat = ($przewoznik === 'BEZ KARCHEM') ? 'BEZ KARCHEM' : str_replace(
                                ['PRZEDSIĘBIORSTWO HANDLOWE KARCHEM', 'EWRANT SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ SPÓŁKA KOMANDYTOWA'],
                                ['P.H. KARCHEM', 'EWRANT'],
                                $przewoznik
                            );
                        @endphp
                        <option value="{{ $transportujacyOryg }}" {{ $transportujacy == $transportujacyOryg ? 'selected' : '' }}>
                            {{ Str::limit($transportujacyFormat, 25) }}
                        </option>
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

            @if($nowe || $przekazujacy || $transportujacy || $kodOdpadu)
                <div class="d-flex flex-column">
                    <label class="form-label mb-1 small fw-bold">&nbsp;</label>
                    <a href="{{ route('biuro.bdo.karty', ['status' => $status]) }}" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-filter-circle-xmark"></i> Wyczyść
                    </a>
                </div>
            @endif

            <div class="ms-auto d-flex align-items-center">
                <div class="view-title-badge">
                    <i class="fa-solid fa-recycle me-2"></i>
                    PRZEJMUJĄCY
                </div>
            </div>
        </form>
    </div>

    {{-- Tabela --}}
    <div class="table-wrapper">
        <table class="table table-bordered table-hover table-sm align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 5%;">Nr karty</th>
                    @if($status === 'zatwierdzone')
                        <th style="width: 7%;">Plan.Data</th>
                    @else
                        <th style="width: 7%;">Data trans.</th>
                    @endif
                    <th style="width: 22%;">Przekazujący</th>
                    <th style="width: 6%;">Kod</th>
                    <th style="width: 7%;">Masa</th>
                    <th style="width: 8%;">Korekta</th>
                    @if($status === 'wygenerowane' || $status === 'zatwierdzone')
                        <th style="width: 7%;">Akcje</th>
                    @endif
                    <th style="width: 10%;">Transportujący</th>
                    <th style="width: 8%;">Nr rej.</th>
                    <th style="width: 10%;">Ost. zmiana</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karty as $karta)
                    @php
                        $nazwaPrzekazujacego = $karta->sender_name_or_first_name_and_last_name ?? '';
                        $nazwaPrzekazujacego = preg_replace('/\s+/u', ' ', trim($nazwaPrzekazujacego));
                        $nazwaPrzekazujacego = mb_strtoupper($nazwaPrzekazujacego, 'UTF-8');
                        $nazwaPrzekazujacego = str_ireplace(
                            ['SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ', 'SPÓŁKA KOMANDYTOWA', 'SPÓŁKA JAWNA'],
                            ['SP. Z O.O.', 'SP. K.', 'SP. J.'],
                            $nazwaPrzekazujacego
                        );

                        $odrzucenie = null;
                        if (!is_null($karta->remarks)) {
                            $remarks = strip_tags($karta->remarks);
                            $odrzucenie = str_replace(': ', '<br>', $remarks);
                            $odrzucenie = str_replace(';', '', $odrzucenie);
                            $odrzucenie = preg_replace('/(\d+[.,]\d+)/', '<b>$1</b>', $odrzucenie);
                        }

                        $transportujacy = $karta->carrier_name_or_first_name_and_last_name ?? '';
                        $transportujacy = str_replace(
                            ['PRZEDSIĘBIORSTWO HANDLOWE KARCHEM', 'EWRANT SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ SPÓŁKA KOMANDYTOWA'],
                            ['P.H. KARCHEM', 'EWRANT'],
                            $transportujacy
                        );
                    @endphp

                    <tr style="height: 50px;">
                        <td style="white-space: normal; word-wrap: break-word; font-size: 0.8rem;">{{ $karta->card_status }}</td>
                        <td class="text-center fw-bold">{{ substr($karta->card_number, 0, 5) }}</td>
                        @if($status === 'zatwierdzone')
                            <td class="text-center">
                                {{ $karta->planned_transport_date ? $karta->planned_transport_date->format('Y-m-d') : '—' }}
                                @if($karta->planned_transport_time)
                                    <div class="text-muted small">{{ date('H:i', strtotime($karta->planned_transport_time)) }}</div>
                                @endif
                            </td>
                        @else
                            <td class="text-center">
                                {{ $karta->real_transport_date ? $karta->real_transport_date->format('Y-m-d') : '—' }}
                            </td>
                        @endif

                        <td class="fw-bold" style="font-size: 0.85rem;">{{ Str::limit($nazwaPrzekazujacego, 35) }}</td>
                        <td class="text-center text-nowrap">{{ mb_substr($karta->waste_code_and_description, 0, 8, 'UTF-8') }}</td>
                        <td class="text-end fw-bold">{{ number_format($karta->waste_mass, 3, ',', ' ') }}</td>
                        <td class="text-center" style="font-size: 0.8rem;">{!! $odrzucenie ?? '—' !!}</td>

                        @if($status === 'wygenerowane')
                            <td class="text-center">
                                @if($odrzucenie)
                                    <button class="btn btn-success btn-sm text-white" style="font-size: 0.7rem;"
                                            onclick="potwierdzMase({{ $karta->id }}, {{ $karta->waste_mass }}, '{{ addslashes($nazwaPrzekazujacego) }}', '{{ addslashes(mb_substr($karta->waste_code_and_description, 0, 8, 'UTF-8')) }}', '{{ $karta->kpo_id }}')">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                @else
                                    <button class="btn btn-danger btn-sm text-white mb-1" style="font-size: 0.7rem;"
                                            onclick="odrzucKarte({{ $karta->id }}, '{{ addslashes($nazwaPrzekazujacego) }}', '{{ addslashes(mb_substr($karta->waste_code_and_description, 0, 8, 'UTF-8')) }}', '{{ $karta->kpo_id }}')">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <button class="btn btn-success btn-sm text-white" style="font-size: 0.7rem;"
                                            onclick="potwierdzMase({{ $karta->id }}, {{ $karta->waste_mass }}, '{{ addslashes($nazwaPrzekazujacego) }}', '{{ addslashes(mb_substr($karta->waste_code_and_description, 0, 8, 'UTF-8')) }}', '{{ $karta->kpo_id }}')">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                @endif
                            </td>
                        @endif

                        @if($status === 'zatwierdzone')
                            <td class="text-center">
                                <button class="btn btn-success btn-sm text-white" style="font-size: 0.7rem;"
                                        onclick="wygenerujPotwierdzenie({{ $karta->id }}, '{{ $karta->kpo_id }}', '{{ $karta->planned_transport_time }}')">
                                    <i class="fa-solid fa-truck"></i> Wyg.
                                </button>
                            </td>
                        @endif

                        <td style="font-size: 0.8rem;">{{ Str::limit($transportujacy, 20) }}</td>
                        <td class="text-center text-nowrap" style="font-size: 0.85rem;">
                            {!! preg_replace('/[\/\\\\]/', '<br>', $karta->vehicle_reg_number) !!}
                        </td>
                        <td class="text-center">
                            @if ($karta->kpo_last_modified_at)
                                <div style="font-size: 0.85rem;">{{ $karta->kpo_last_modified_at->format('Y-m-d') }}</div>
                                <div style="font-size: 0.8rem; color: #555;">
                                    {{ $karta->kpo_last_modified_at->format('H:i') }}
                                    <span onclick="aktualizujJednaKarte({{ $karta->id }}, '{{ $karta->kpo_id }}', {{ $karta->calendar_year }})"
                                          style="cursor: pointer;" title="Aktualizuj kartę">🔄</span>
                                </div>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ ($status === 'wygenerowane' || $status === 'zatwierdzone') ? 11 : 10 }}" class="text-center py-4">
                            <i class="fa-solid fa-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="mb-0 mt-2">Brak kart do wyświetlenia</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/bdo/akcje.js') }}"></script>
@endsection