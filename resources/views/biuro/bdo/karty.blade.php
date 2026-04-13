@extends('layouts.app')

@section('title', 'BDO - Przejmujący')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
    :root {
        --bdo-black: #1a1a1a;
        --bdo-dark: #2c2c2c;
        --bdo-gray: #6c757d;
        --bdo-light: #f5f5f5;
    }

    /* Zakładki statusów */
    .nav-tabs {
        background: linear-gradient(135deg, var(--bdo-dark) 0%, var(--bdo-black) 100%);
        padding: 10px 10px 0 10px;
        border-radius: 8px 8px 0 0;
        border-bottom: none;
        margin-bottom: 0 !important;
    }

    .nav-tabs .nav-link {
        color: #ffffff;
        font-weight: 500;
        font-size: 0.85rem;
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px 8px 0 0;
        margin-right: 4px;
        transition: all 0.3s ease;
        padding: 8px 12px;
    }

    .nav-tabs .nav-link:hover:not(.active) {
        color: #ffffff;
        background-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .nav-tabs .nav-link.active {
        color: #000000;
        font-weight: 700;
        background-color: #ffffff;
        border: 2px solid #ffffff;
        border-bottom-color: #ffffff;
        box-shadow: 0 -3px 12px rgba(0, 0, 0, 0.3);
        z-index: 2;
    }

    .nav-link:not(.active) .badge.bg-secondary {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff;
    }

    .nav-link.active .badge.bg-secondary { 
        background-color: var(--bdo-dark) !important; 
        color: #ffffff;
    }

    /* Filtry */
    .filtry-container {
        background: linear-gradient(135deg, #e8e8e8 0%, #d4d4d4 100%);
        padding: 15px 20px;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-top: none;
        margin-bottom: 20px;
    }

    .vr {
        width: 2px;
        background: linear-gradient(to bottom, transparent, var(--bdo-dark), transparent);
        align-self: stretch;
        margin: 0 12px;
        opacity: 0.3;
    }

    .filtry-container .form-select {
        border: 1px solid #999999;
        background-color: #ffffff;
    }

    .filtry-container .form-select:focus {
        border-color: var(--bdo-dark);
        box-shadow: 0 0 0 0.2rem rgba(44, 44, 44, 0.25);
    }

    /* Przyciski */
    .btn-czarny {
        background: linear-gradient(135deg, var(--bdo-dark) 0%, var(--bdo-black) 100%);
        color: #ffffff;
        border: 2px solid var(--bdo-dark);
        font-weight: 600;
    }

    .btn-czarny:hover {
        background: linear-gradient(135deg, var(--bdo-black) 0%, #000000 100%);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
    }

    .btn-outline-czarny {
        background-color: #ffffff;
        color: var(--bdo-dark);
        border: 2px solid var(--bdo-dark);
        font-weight: 600;
    }

    .btn-outline-czarny:hover {
        background: linear-gradient(135deg, var(--bdo-dark) 0%, var(--bdo-black) 100%);
        color: #ffffff;
    }

    /* Badge tytułu */
    .view-title-badge {
        background: linear-gradient(135deg, var(--bdo-dark) 0%, var(--bdo-black) 100%);
        color: #ffffff;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: 1px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
    }

    /* Tabela */
    .table-wrapper {
        background: linear-gradient(135deg, #f5f5f5 0%, #eeeeee 100%);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .table-wrapper .table {
        margin-bottom: 0;
        background-color: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    .table-dark {
        background: linear-gradient(135deg, var(--bdo-dark) 0%, var(--bdo-black) 100%);
    }

    .table-dark th {
        border-color: rgba(255, 255, 255, 0.15);
        font-size: 0.85rem;
    }

    .table-wrapper tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.05) !important;
    }

    table {
        width: 100%;
        table-layout: fixed;
    }

    th, td {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-3">
    {{-- Zakładki statusów --}}
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'wszystkie' ? 'active' : '' }}" 
               href="{{ route('biuro.bdo.karty', ['status' => 'wszystkie', 'nowe' => $nowe ? '1' : '0']) }}">
                Wszystkie
                <span class="badge bg-secondary ms-1">{{ array_sum($statusCounts) }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'wygenerowane' ? 'active' : '' }}" 
               href="{{ route('biuro.bdo.karty', ['status' => 'wygenerowane', 'nowe' => $nowe ? '1' : '0']) }}">
                Potw. wygenerowane
                <span class="badge bg-secondary ms-1">{{ $statusCounts['Potwierdzenie wygenerowane'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'przejecie' ? 'active' : '' }}" 
               href="{{ route('biuro.bdo.karty', ['status' => 'przejecie', 'nowe' => $nowe ? '1' : '0']) }}">
                Potw. przejęcia
                <span class="badge bg-secondary ms-1">{{ $statusCounts['Potwierdzenie przejęcia'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'transport' ? 'active' : '' }}" 
               href="{{ route('biuro.bdo.karty', ['status' => 'transport', 'nowe' => $nowe ? '1' : '0']) }}">
                Potw. transportu
                <span class="badge bg-secondary ms-1">{{ $statusCounts['Potwierdzenie transportu'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'zatwierdzone' ? 'active' : '' }}" 
               href="{{ route('biuro.bdo.karty', ['status' => 'zatwierdzone', 'nowe' => $nowe ? '1' : '0']) }}">
                Zatwierdzone
                <span class="badge bg-secondary ms-1">{{ $statusCounts['Zatwierdzona'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'planowane' ? 'active' : '' }}" 
               href="{{ route('biuro.bdo.karty', ['status' => 'planowane', 'nowe' => $nowe ? '1' : '0']) }}">
                Planowane
                <span class="badge bg-secondary ms-1">{{ $statusCounts['Planowana'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'odrzucone' ? 'active' : '' }}" 
               href="{{ route('biuro.bdo.karty', ['status' => 'odrzucone', 'nowe' => $nowe ? '1' : '0']) }}">
                Odrzucone
                <span class="badge bg-secondary ms-1">{{ $statusCounts['Odrzucona'] ?? 0 }}</span>
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
                <label class="form-label mb-1 small text-muted">&nbsp;</label>
                <button type="submit" name="nowe" value="{{ $nowe ? '0' : '1' }}" 
                        class="btn {{ $nowe ? 'btn-czarny' : 'btn-outline-czarny' }} btn-sm d-flex align-items-center gap-1">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    NOWE
                </button>
            </div>

            <div class="vr"></div>

            {{-- Select Przekazujący --}}
            <div class="d-flex flex-column">
                <label class="form-label mb-1 small text-muted">Przekazujący</label>
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

            {{-- Select Transportujący --}}
            <div class="d-flex flex-column">
                <label class="form-label mb-1 small text-muted">Transportujący</label>
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

            {{-- Select Kod odpadu --}}
            <div class="d-flex flex-column">
                <label class="form-label mb-1 small text-muted">Kod odpadu</label>
                <select name="kod_odpadu" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                    <option value="">Wszystkie</option>
                    @foreach($kodyOdpadow as $kod)
                        <option value="{{ $kod }}" {{ $kodOdpadu == $kod ? 'selected' : '' }}>{{ $kod }}</option>
                    @endforeach
                </select>
            </div>

            @if($nowe || $przekazujacy || $transportujacy || $kodOdpadu)
                <div class="d-flex flex-column">
                    <label class="form-label mb-1 small text-muted">&nbsp;</label>
                    <a href="{{ route('biuro.bdo.karty', ['status' => $status]) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-filter-circle-xmark"></i> Wyczyść
                    </a>
                </div>
            @endif

            <div class="ms-auto d-flex align-items-center">
                <div class="view-title-badge">
                    <i class="fa-solid fa-arrow-left me-2"></i>
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
