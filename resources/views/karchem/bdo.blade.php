@extends('layouts.karchem')

@section('content')

<div class="container mt-4">
<!-- Nagłówek -->
<div class="container-fluid mb-4">
    <div class="p-3 rounded shadow-sm text-white d-flex justify-content-between align-items-center"
         style="background: linear-gradient(135deg, #2c3e50, #3a506b);">
        <div class="d-flex align-items-center">
            <i class="fas fa-recycle fa-2x me-3"></i>
            <h2 class="m-0 fw-bold">KARTY BDO</h2>
        </div>
    </div>
</div>
</div>

<!-- Filtry -->
<div class="container-fluid mb-3">
    <form method="GET" id="filterForm">
        <div class="row g-3">
            <!-- Select Nazwa przekazującego -->
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Nazwa przekazującego</label>
                <select name="przekazujacy" class="form-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Wszyscy przekazujący</option>
                    @foreach($przekazujacy as $p)
                    <option value="{{ $p }}" {{ request('przekazujacy') == $p ? 'selected' : '' }}>
                        {{ mb_strimwidth($p, 0, 60, '...') }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Select Kod odpadu -->
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Kod odpadu</label>
                <select name="kod_odpadu" class="form-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Wszystkie kody</option>
                    @foreach($kodyOdpadow as $kod)
                    <option value="{{ $kod }}" {{ request('kod_odpadu') == $kod ? 'selected' : '' }}>
                        {{ $kod }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Select Status -->
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Wszystkie statusy</option>
                    @foreach($statusy as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                        {{ $s }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Przycisk Reset -->
            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('karchem.bdo') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Separator -->
<div class="mb-3"></div>

<div class="container-fluid px-3">
    <table class="table table-bordered table-hover table-sm align-middle" style="font-size: 1rem;">
        <thead class="table-dark text-center">
            <tr>
                <th>Numer karty</th>
                <th>Data transp.</th>
                <th>Nazwa przekazującego</th>
                <th>Transportujący</th>
                <th>Numer Rej.</th>
                <th>Kod odpadu</th>
                <th>Masa odpadów</th>
                <th>Odrzucenie</th>
                <th style="width:130px;">Akcja</th>
                <th style="width:20px;">Status</th>
                <th><i class="mdi mdi-printer"></i></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($karty as $karta)
            <tr style="height:50px;">
                <td class="text-center"> <i class="fa-solid fa-cloud-arrow-down icon"
                    onclick="aktualizujJednaKarte({{ $karta->id }}, '{{ $karta->kpo_id }}', {{ $karta->calendar_year }})">
                </i>
                    {{ substr($karta->card_number, 0, 5) }}
                </td>
                <td class="text-center text-nowrap">
                    {{ $karta->real_transport_date ? date('Y-m-d', strtotime($karta->real_transport_date)) : '—' }}

                </td>

                @php
                $nazwaPrzekazujacego = $karta->sender_name_or_first_name_and_last_name ?? '';
                $nazwaPrzekazujacego = preg_replace('/\x{00a0}/u', ' ', $nazwaPrzekazujacego);
                $nazwaPrzekazujacego = preg_replace('/\s+/u', ' ', $nazwaPrzekazujacego);
                $nazwaPrzekazujacego = trim($nazwaPrzekazujacego);

                if (class_exists('Normalizer')) {
                    $nazwaPrzekazujacego = Normalizer::normalize($nazwaPrzekazujacego, Normalizer::FORM_C);
                }

                $nazwaPrzekazujacego = preg_replace('/["\x{0027}\x{2018}\x{2019}\x{201A}\x{201B}\x{201C}\x{201D}\x{201E}\x{201F}]/u', '', $nazwaPrzekazujacego);
                $nazwaPrzekazujacego = mb_strtoupper($nazwaPrzekazujacego, 'UTF-8');

                $search = [
                    'SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ',
                    'SPÓŁKA KOMANDYTOWA',
                    'SPÓŁKA JAWNA',
                ];
                $replace = [
                    'SP. Z O.O.',
                    'SP. K.',
                    'SP. J.',
                ];
                $nazwaPrzekazujacego = str_ireplace($search, $replace, $nazwaPrzekazujacego);
                @endphp

                <td><strong>{{ $nazwaPrzekazujacego }}</strong></td>

                @php
                $transportujacy = $karta->carrier_name_or_first_name_and_last_name ?? '';
                $transportujacy = str_replace(
                    [
                        'PRZEDSIĘBIORSTWO HANDLOWE KARCHEM',
                        'EWRANT SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ SPÓŁKA KOMANDYTOWA'
                    ],
                    [
                        'P.H. KARCHEM',
                        'EWRANT'
                    ],
                    $transportujacy
                );
                @endphp
                <td>{{ $transportujacy }}</td>

                <td class="text-center text-nowrap">{{ $karta->vehicle_reg_number }}</td>
                <td class="text-center text-nowrap">{{ mb_substr($karta->waste_code_and_description, 0, 8, 'UTF-8') }}</td>
                <td class="text-end fw-bold" style="background-color: #fff3cd;">{{ number_format($karta->waste_mass, 3, ',', ' ') }}</td>

                <td class="text-center">
@php
$odrzucenie = null;

if (!is_null($karta->remarks)) {
    $remarks = strip_tags($karta->remarks);
    $odrzucenie = str_replace(': ', '<br>', $remarks);
    $odrzucenie = str_replace(';', '', $odrzucenie);
    
    // Formatowanie tekstu
    $odrzucenie = '
        <div class="text-center text-nowrap">
            <small class="d-block text-uppercase" style="font-size: 0.55rem;">' . explode('<br>', $odrzucenie)[0] . '</small>
            <strong style="font-size: 0.75rem;">' . (explode('<br>', $odrzucenie)[1] ?? '') . '</strong>
        </div>
    ';
}
@endphp
{!! $odrzucenie ?? '—' !!}
                </td>

<td class="text-center">
    @if ($karta->card_status == "Potwierdzenie wygenerowane")
        @if (!is_null($karta->remarks))
<button class="btn btn-success btn-sm text-white border border-white" 
        type="button" 
        style="width: 90%;font-size: 0.65rem;" 
        onclick="potwierdzMase({{ $karta->id }}, {{ $karta->waste_mass }}, '{{ addslashes($nazwaPrzekazujacego) }}', '{{ addslashes(mb_substr($karta->waste_code_and_description, 0, 8, 'UTF-8')) }}', '{{ $karta->kpo_id }}')">
    <i class="fas fa-check-circle fa-sm"></i> Potwierdź
</button>
        @else
<button class="btn btn-danger btn-sm text-white border border-white" 
        type="button" 
        style="width: 90%;font-size: 0.65rem;"
        onclick="odrzucKarte({{ $karta->id }}, '{{ addslashes($nazwaPrzekazujacego) }}', '{{ addslashes(mb_substr($karta->waste_code_and_description, 0, 8, 'UTF-8')) }}', '{{ $karta->kpo_id }}')">
    <i class="fas fa-times-circle fa-sm"></i> Odrzuć
</button>
<button class="btn btn-success btn-sm text-white border border-white" 
        type="button" 
        style="width: 90%;font-size: 0.65rem;" 
        onclick="potwierdzMase({{ $karta->id }}, {{ $karta->waste_mass }}, '{{ addslashes($nazwaPrzekazujacego) }}', '{{ addslashes(mb_substr($karta->waste_code_and_description, 0, 8, 'UTF-8')) }}', '{{ $karta->kpo_id }}')">
    <i class="fas fa-check-circle fa-sm"></i> Potwierdź
</button>
        @endif
    @else
        —
    @endif
</td>
                
                <td class="text-center">
                    <span class="badge bg-secondary">{{ $karta->card_status }}</span>
                </td>
                <td>
                    <form action="{{ route('karchem.generujPdf') }}" method="POST" style="display: inline;" onsubmit="pokazLoader(event)">
                    @csrf
                    <input type="hidden" name="kpoId" value="{{ $karta->kpo_id }}">
                    {{-- Zakładam, że masz kolumnę kpo_id w tabeli kart, która przechowuje GUID z BDO --}}
                    <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                        <i class="mdi mdi-printer icon"></i>
                    </button>
                </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center text-muted">Brak danych do wyświetlenia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
// Miejsce na skrypty
</script>
@endpush

@push('styles')
<style>
     @import url('https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap');
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    
    .table {
        font-family: 'Source Sans 3', sans-serif;
    }
.icon {
    display: inline-block;
    cursor: pointer;
    color: #6c757d;
    transition: color 0.3s ease, transform 0.3s ease;
}

.icon:hover {
    color: #007bff;
    transform: translateY(-2px) scale(1.08);
}
</style>
@endpush
