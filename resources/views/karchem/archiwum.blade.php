@extends('layouts.karchem')

@section('content')

<div class="container mt-4">
<!-- Nagłówek -->
<div class="container-fluid mb-4">
<div class="p-3 rounded shadow-sm text-white d-flex justify-content-between align-items-center"
     style="background: linear-gradient(135deg, #2c3e50, #3a506b);">
    
    <div class="d-flex align-items-center w-100 flex-column text-center">
        <div class="d-flex align-items-center">
            <i class="fas fa-archive fa-2x me-3"></i>
            <h2 class="m-0 fw-bold">ARCHIWUM</h2>
        </div>

        <!-- Druga linia pod ARCHIWUM -->
        <div class="mt-1" style="font-size: 14px; opacity: 0.9;">
            karty ze statusem: <strong>POTWIERDZENIE TRANSPORTU</strong> oraz <strong>POTWIERDZENIE PRZEJĘCIA</strong>
        </div>
    </div>

</div>

</div>
</div>

@php
    // pomocnicze tablice
    $miesiacSlownie = [
        'all' => 'Wszystkie miesiące',
        1=>'Styczeń',2=>'Luty',3=>'Marzec',4=>'Kwiecień',5=>'Maj',6=>'Czerwiec',
        7=>'Lipiec',8=>'Sierpień',9=>'Wrzesień',10=>'Październik',11=>'Listopad',12=>'Grudzień'
    ];
    $roman = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
@endphp

<div class="w-100 py-3 mb-3" style="background: #f7f7f7;">
    {{-- Formularz z selectami (dla wygody przy bezpośredniego submitu) --}}
    <form id="filterForm" method="GET" class="container">

        <div class="d-flex justify-content-between align-items-center flex-wrap">

            {{-- PRAWA STRONA: rok + przyciski miesięcy (linki zachowujące GET params) --}}
            <div class="d-flex gap-2 align-items-center flex-wrap">

                {{-- ROK: tu zostawiamy select który submituje formularz --}}
                <select id="rokSelect" name="rok" class="form-select form-select-sm" style="width:110px">
                    @foreach ([2024,2025,2026] as $r)
                        <option value="{{ $r }}" @selected((string)$rok === (string)$r)>{{ $r }}</option>
                    @endforeach
                </select>

                {{-- WSZYSTKIE — link zachowujący pozostałe parametry --}}
                <a href="{{ request()->fullUrlWithQuery(['miesiac' => 'all']) }}"
                    class="btn btn-sm {{ (string)$miesiac === 'all' ? 'btn-primary' : 'btn-secondary' }}">
                    Wszystkie
                </a>

                {{-- MIESIĄCE — linki rzymskie --}}
                @for ($i = 1; $i <= 12; $i++)
                    <a href="{{ request()->fullUrlWithQuery(['miesiac' => $i]) }}"
                        class="btn btn-sm {{ (string)$miesiac === (string)$i ? 'btn-primary' : 'btn-secondary' }}">
                        {{ $roman[$i-1] }}
                    </a>
                @endfor
            </div>

        </div>

        {{-- 3 SELECTY POD TABELĘ, z labelami --}}
        <div class="row mt-3 g-2">

            <div class="col-md-4">
                <label for="sender" class="form-label small mb-1">Przekazujący</label>
                <select id="sender" name="sender" class="form-select form-select-sm">
                    <option value="">— wszystkie —</option>
                    @foreach ($senders as $p)
                        <option value="{{ $p }}" @selected(request('sender') == $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="carrier" class="form-label small mb-1">Transportujący</label>
                <select id="carrier" name="carrier" class="form-select form-select-sm">
                    <option value="">— wszystkie —</option>
                    @foreach ($carriers as $p)
                        <option value="{{ $p }}" @selected(request('carrier') == $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="kod" class="form-label small mb-1">Kod odpadu (pierwsze 8 znaków)</label>
                <select id="kod" name="kod" class="form-select form-select-sm">
                    <option value="">— wszystkie —</option>
                    @foreach ($kody as $p)
                        <option value="{{ $p }}" @selected(request('kod') == $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        {{-- Przyciski formularza: zastosuj / zresetuj --}}
        <div class="d-flex justify-content-end gap-2 mt-2">
            <button type="submit" class="btn btn-sm btn-success">Zastosuj filtry</button>
            <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">Wyczyść filtry</a>
        </div>

    </form>
</div>

{{-- BANER AKTYWNYCH FILTRÓW --}}
<div class="container mb-2">
    @php
        $active = [];
        if ($miesiac !== 'all') $active[] = 'Miesiąc: '.$miesiacSlownie[$miesiac] ?? $miesiac;
        if (!empty($rok)) $active[] = 'Rok: '.$rok;
        if (request('sender')) $active[] = 'Przekazujący: '.request('sender');
        if (request('carrier')) $active[] = 'Transportujący: '.request('carrier');
        if (request('kod')) $active[] = 'Kod: '.request('kod');
    @endphp

    <div class="mb-2 d-flex justify-content-between align-items-center">
        <div>
            <span class="me-2 text-muted">Filtry aktywne:</span>
            @if(count($active) > 0)
                @foreach($active as $a)
                    <span class="badge bg-primary me-1">{{ $a }}</span>
                @endforeach
            @else
                <span class="text-muted">Brak aktywnych filtrów</span>
            @endif
        </div>
        
        <span class="badge bg-dark text-white">{{ $kartyCount }}</span>
    </div>
</div>



{{-- TABELA --}}


<table class="table table-bordered table-striped table-sm align-middle fn-sm table-custom" style="font-size: 0.9rem;">
    <thead class="text-center thead-custom">
        <tr>
            <th style="width:50px;">Lp.</th>
            <th>Numer karty</th>
            <th>Data transp.</th>
            <th>
                Nazwa przekazującego
                @if(request('sender'))
                    <span class="badge bg-secondary ms-1 small">filtrowane</span>
                @endif
            </th>
            <th>
                Transportujący
                @if(request('carrier'))
                    <span class="badge bg-secondary ms-1 small">filtrowane</span>
                @endif
            </th>
            <th>Numer Rej.</th>
            <th>
                Kod odpadu
                @if(request('kod'))
                    <span class="badge bg-secondary ms-1 small">filtrowane</span>
                @endif
            </th>
            <th class="text-end">Masa odpadów</th>
            <th>Status</th>
            <th><i class="mdi mdi-printer"></i></th>
        </tr>
    </thead>

    <tbody>
        @forelse ($karty as $karta)
            <tr class="p-1">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-nowrap">{{ substr($karta->card_number, 0, 5) }}</td>

                <td class="text-nowrap">
                    {{ $karta->real_transport_date ? date('Y-m-d', strtotime($karta->real_transport_date)) : '—' }}
                    <br>
                    <small class="d-block text-center text-muted">
                        {{ $karta->real_transport_time ? date('H:i', strtotime($karta->real_transport_time)) : '—' }}
                    </small>
                </td>

                {{-- PRZEKAZUJĄCY --}}
                @php
    $nazwaPrzekazujacego = $karta->sender_name_or_first_name_and_last_name ?? '';

    // Usunięcie złamanych spacji, podwójnych odstępów itp.
    $nazwaPrzekazujacego = preg_replace('/\x{00a0}/u', ' ', $nazwaPrzekazujacego);
    $nazwaPrzekazujacego = preg_replace('/\s+/u', ' ', $nazwaPrzekazujacego);
    $nazwaPrzekazujacego = trim($nazwaPrzekazujacego);

    // Normalizacja unicode
    if (class_exists('Normalizer')) {
        $nazwaPrzekazujacego = Normalizer::normalize($nazwaPrzekazujacego, Normalizer::FORM_C);
    }

    // Usuń wszystkie rodzaje cudzysłowów
    $nazwaPrzekazujacego = preg_replace('/["\'‘’‚‛“”„‟]/u', '', $nazwaPrzekazujacego);

    // Zamiana na duże litery
    $nazwaPrzekazujacego = mb_strtoupper($nazwaPrzekazujacego, 'UTF-8');

    // Mapowanie form prawnych
    $search = [
        'SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ',
        'SPÓŁKA KOMANDYTOWA',
        'SPÓŁKA JAWNA',
        'SPÓŁKA AKCYJNA',
        'SPÓŁKA CYWILNA',
        'ZAKŁAD USŁUGOWO HANDLOWY',
    ];

    $replace = [
        'SP. Z O.O.',
        'SP. K.',
        'SP. J.',
        'S.A.',
        'S.C.',
        'Z.U.H.',
    ];

    $nazwaPrzekazujacego = str_ireplace($search, $replace, $nazwaPrzekazujacego);

@endphp

<td>{{ $nazwaPrzekazujacego }}</td>

                {{-- TRANSPORTUJĄCY (po skróceniu nazw) --}}
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


                <td class="text-nowrap">{{ $karta->vehicle_reg_number }}</td>

                <td>{{ mb_substr($karta->waste_code_and_description, 0, 8, 'UTF-8') }}</td>

                <td class="text-center fw-bold" style="background-color:#f8eb73;font-size: 1.1rem;">
                    {{ number_format($karta->waste_mass ?? 0, 3, ',', ' ') }}
                </td>

                <td class="text-nowrap">{{ $karta->card_status }}</td>

<td>
    <div class="dropdown">
        <button class="btn btn-link p-0" type="button" id="dropdownMenu{{ $karta->id }}" data-bs-toggle="dropdown" aria-expanded="false" style="background: none; border: none; cursor: pointer;">
            <i class="fa fa-chevron-down"></i>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu{{ $karta->id }}">
            <li>
                <form action="{{ route('karchem.generujPdf') }}" method="POST" style="display: inline;" onsubmit="pokazLoader(event)">
                    @csrf
                    <input type="hidden" name="kpoId" value="{{ $karta->kpo_id }}">
                    <button type="submit" class="dropdown-item">
                        <i class="mdi mdi-file-pdf-box me-2"></i> Generuj PDF
                    </button>
                </form>
            </li>
            <li>
                <form action="{{ route('karchem.doEwrant') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="kpoIdE" value="{{ $karta->kpo_id  }}">
                    <button type="submit" class="dropdown-item">
                        <i class="mdi mdi-update me-2"></i> Do Ewrant
                    </button>
                </form>
            </li>
        </ul>
    </div>
</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center">Brak danych do wyświetlenia.</td>
            </tr>
        @endforelse
        <tfoot>
    <tr class="fw-bold">
        <td colspan="7" class="text-end">Suma masy:</td>
        <td class="text-center bg-warning fw-bold" style="font-size: 1.2rem;">
            {{ number_format($karty->sum('waste_mass'), 3, ',', ' ') }}
        </td>
        <td></td>
    </tr>
</tfoot>
    </tbody>
</table>

{{-- PODSUMOWANIE MAS PO KODACH ODPADU --}}
<div class="container mt-5 mb-4 w-25">
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #3a506b, #5b7a9e);">
            <h5 class="m-0">
                <i class="fas fa-chart-bar me-2"></i>
                Podsumowanie mas odpadów według kodów
            </h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-sm align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width:200px;">Kod odpadu</th>
                        <th class="text-end">Suma masy [Mg]</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($podsumowanieKodow as $item)
                        <tr>
                            <td class="text-center fw-bold" style="font-size: 1.05rem;">{{ $item['kod'] }}</td>
                            <td class="text-end fw-bold" style="background-color:#f8f9fa; font-size: 1.05rem;">
                                {{ number_format($item['suma'], 3, ',', ' ') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">Brak danych do podsumowania</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-warning">
                    <tr class="fw-bold">
                        <td class="text-end">SUMA CAŁKOWITA:</td>
                        <td class="text-end" style="font-size: 1.2rem;">
                            {{ number_format($podsumowanieKodow->sum('suma'), 3, ',', ' ') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // 1) Obsługa selectu roku: po zmianie przesyłamy formularz,
    //    ale najpierw ustawiamy wartość hidden 'miesiac' jeśli nie ma (żeby nie utracić)
    document.getElementById('rokSelect').addEventListener('change', function() {
        // submit form -> zachowa wartości sender/carrier/kod bo są w formularzu
        document.getElementById('filterForm').submit();
    });

    // 2) Przy submit formularza: nic specjalnego — GET będzie zawierał wszystkie pola.
    //    (Dodatkowo: możesz dodać debounce lub walidację.)
</script>
@endpush
