@extends('layouts.karchem')

@section('content')

<div class="container mt-4">
<!-- Nagłówek -->
<div class="container-fluid mb-4">
    <div class="p-3 rounded shadow-sm text-white d-flex justify-content-between align-items-center"
         style="background: linear-gradient(135deg, #2c3e50, #3a506b);">
        <div class="d-flex align-items-center">
            <i class="fas fa-warehouse fa-2x me-3"></i>
            <h2 class="m-0 fw-bold">PRZYJĘCIA NA PLACU</h2>
        </div>
    </div>
</div>

<!-- Filtry: Rok i Miesiące -->
<div class="container-fluid mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap">

        <!-- Select rok po lewej -->
        <form method="GET" id="filterForm" class="d-flex align-items-center">
            <input type="hidden" name="towar" value="{{ $selectedTowar }}">
            <input type="hidden" name="miesiac" value="{{ $selectedMiesiac }}">
            <select name="rok" class="form-select me-3" style="max-width: 120px;"
                    onchange="document.getElementById('filterForm').submit()">
                @foreach([2025, 2026] as $rok)
                    <option value="{{ $rok }}" {{ $selectedRok==$rok ? 'selected' : '' }}>{{ $rok }}</option>
                @endforeach
            </select>
        </form>

        <!-- Przyciskowe miesiące po prawej -->
        <div class="d-flex justify-content-end flex-wrap">
            @php
                $miesiace = [
                    1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                    7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
                ];
            @endphp

            <a href="{{ route('karchem.plac', array_merge(request()->except('miesiac'), ['miesiac' => null])) }}"
               class="btn btn-sm {{ is_null($selectedMiesiac) ? 'btn-primary active' : 'btn-secondary' }} m-1"
               style="min-width: 70px;">Wszystkie</a>

            @foreach($miesiace as $num => $rzymska)
                <a href="{{ route('karchem.plac', array_merge(request()->except('miesiac'), ['miesiac' => $num])) }}"
                   class="btn btn-sm {{ $selectedMiesiac == $num ? 'btn-primary active' : 'btn-secondary' }} m-1"
                   style="min-width: 40px;">{{ $rzymska }}</a>
            @endforeach
        </div>
    </div>
</div>

<!-- Nazwa wybranego miesiąca -->
<div class="d-flex align-items-center mb-3">
    <div class="flex-grow-1 text-center">
        <h4 class="fw-bold text-secondary mb-0">
            @php
                $miesiaceNazwy = [
                    1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
                    5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
                    9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień'
                ];
            @endphp

            @if(is_null($selectedMiesiac))
                Wszystkie miesiące {{ $selectedRok }}
            @else
                {{ $miesiaceNazwy[$selectedMiesiac] }} {{ $selectedRok }}
            @endif
        </h4>
    </div>

    <div class="ms-auto">
        <a href="{{ route('karchem.placDrukuj', [
                'rok' => $selectedRok,
                'miesiac' => $selectedMiesiac,
                'towar' => $selectedTowar,
            ]) }}"
        class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1"></i> Drukuj
        </a>


    </div>
</div>


<!-- Tabela główna -->
<div class="table-responsive mx-auto" style="max-width: 800px;">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>DATA</th>
                <th>KONTRAHENT</th>
                <th>KOD ODPADU</th>
                <th class="align-middle">
                    <form method="GET" action="{{ route('karchem.plac') }}">
                        <input type="hidden" name="rok" value="{{ $selectedRok }}">
                        <input type="hidden" name="miesiac" value="{{ $selectedMiesiac }}">
                        <select name="towar" id="towar" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Wszystkie towary --</option>
                            @foreach($select_towar as $id => $nazwa)
                                <option value="{{ $id }}" {{ $selectedTowar == $id ? 'selected' : '' }}>{{ $nazwa }}</option>
                            @endforeach
                        </select>
                    </form>
                </th>
                <th class="text-end">WAGA</th>
                <th class="text-end">OPERATOR</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produkcja as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                    <td>KARCHEM</td>
                    <td>{{ $kodyOdpadow[$item->fraction->name] ?? '' }}</td>
                    <td>{{ str_replace(['KARCHEM ', 'BELKA'], '', $item->fraction->name) }}</td>
                    <td class="text-end">{{ number_format($item->weight_kg, 3, ',', ' ') }}</td>
                    <td class="text-end">{{ $item->operator->name ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Brak danych z produkcji.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="fw-bold table-light">
                <td colspan="4" class="text-end">RAZEM:</td>
                <td class="text-end">{{ number_format($sumaWagaProdukcji, 3, ',', ' ') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Podsumowanie wg towaru -->
<div class="container mt-4 d-flex flex-column align-items-center">
    <div class="w-100" style="max-width: 400px;">
        <h6 class="fw-bold">Podsumowanie wg towaru</h6>
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Towar</th>
                    <th class="text-end">Suma (kg)</th>
                </tr>
            </thead>
            <tbody>
                @php $sumaTowar = 0; @endphp
                @foreach ($podsumowanieTowar as $nazwa => $waga)
                    @php $sumaTowar += $waga; @endphp
                    <tr>
                        <td>{{ str_replace(['KARCHEM ', 'BELKA'], '', $nazwa) }}</td>
                        <td class="text-end">{{ number_format($waga, 3, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td>RAZEM</td>
                    <td class="text-end">{{ number_format($sumaTowar, 3, ',', ' ') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Podsumowanie wg kodu odpadu -->
<div class="container mt-4 d-flex flex-column align-items-center">
    <div class="w-100" style="max-width: 400px;">
        <h6 class="fw-bold">Podsumowanie wg kodu odpadu</h6>
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Kod odpadu</th>
                    <th class="text-end">Suma (kg)</th>
                </tr>
            </thead>
            <tbody>
                @php $sumaKod = 0; @endphp
                @foreach ($podsumowanieKod as $kod => $waga)
                    @php $sumaKod += $waga; @endphp
                    <tr>
                        <td>{{ $kod }}</td>
                        <td class="text-end">{{ number_format($waga, 3, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td>RAZEM</td>
                    <td class="text-end">{{ number_format($sumaKod, 3, ',', ' ') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

</div>
@endsection

@push('scripts')

<script>
// Miejsce na dodatkowe skrypty JS
</script>

@endpush
