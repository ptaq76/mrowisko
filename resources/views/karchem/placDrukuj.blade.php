@extends('layouts.karchem')

@section('content')

@php
    $miesiaceNazwy = [
        1 => 'STYCZEŃ', 2 => 'LUTY', 3 => 'MARZEC', 4 => 'KWIECIEŃ',
        5 => 'MAJ', 6 => 'CZERWIEC', 7 => 'LIPIEC', 8 => 'SIERPIEŃ',
        9 => 'WRZESIEŃ', 10 => 'PAŹDZIERNIK', 11 => 'LISTOPAD', 12 => 'GRUDZIEŃ'
    ];
@endphp


<div class="container mt-4">
<!-- Nagłówek -->
<div class="mb-3">
    <h3 class="text-center fw-bold print-title">
        PRZYJĘCIA NA PLACU –
        {{ $selectedMiesiac ? $miesiaceNazwy[$selectedMiesiac] : 'WSZYSTKIE MIESIĄCE' }}
        {{ $selectedRok }}
    </h3>
</div>


<!-- Tabela główna -->
<div class="row print-layout">
    <!-- LEWA KOLUMNA: PODSUMOWANIA -->
    <div class="col-4">
        {{-- Podsumowanie wg towaru --}}
        <h6 class="fw-bold mb-2">PODSUMOWANIE WG TOWARU</h6>
        <table class="table table-sm table-bordered print-table">
            <tbody>
                @php $sumaTowar = 0; @endphp
                @foreach ($podsumowanieTowar as $nazwa => $waga)
                    @php $sumaTowar += $waga; @endphp
                    <tr>
                        <td>{{ str_replace(['KARCHEM ', 'BELKA'], '', $nazwa) }}</td>
                        <td class="text-end">{{ number_format($waga, 3, ',', ' ') }}</td>
                    </tr>
                @endforeach
                <tr class="fw-bold">
                    <td>RAZEM</td>
                    <td class="text-end">{{ number_format($sumaTowar, 3, ',', ' ') }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Podsumowanie wg kodu --}}
        <h6 class="fw-bold mt-4 mb-2">PODSUMOWANIE WG KODU ODPADU</h6>
        <table class="table table-sm table-bordered print-table">
            <tbody>
                @php $sumaKod = 0; @endphp
                @foreach ($podsumowanieKod as $kod => $waga)
                    @php $sumaKod += $waga; @endphp
                    <tr>
                        <td>{{ $kod }}</td>
                        <td class="text-end">{{ number_format($waga, 3, ',', ' ') }}</td>
                    </tr>
                @endforeach
                <tr class="fw-bold">
                    <td>RAZEM</td>
                    <td class="text-end">{{ number_format($sumaKod, 3, ',', ' ') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- PRAWA KOLUMNA: TABELA GŁÓWNA -->
    <div class="col-8">
        <table class="table table-sm table-bordered print-table">
            <thead>
                <tr>
                    <th style="width: 16%">DATA</th>
                    <th style="width: 12%">KOD</th>
                    <th style="width: 45%">TOWAR</th>
                    <th style="width: 12%" class="text-end">WAGA (t)</th>
                    <th style="width: 15%">OPERATOR</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produkcja as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                        <td>{{ $kodyOdpadow[$item->fraction->name] ?? '' }}</td>
                        <td>{{ str_replace(['KARCHEM ', 'BELKA'], '', $item->fraction->name) }}</td>
                        <td class="text-end">{{ number_format($item->weight_kg, 3, ',', ' ') }}</td>
                        <td>{{ $item->operator->name ?? '—' }}</td>
                    </tr>
                @endforeach
                <tr class="fw-bold">
                    <td colspan="3" class="text-end">RAZEM</td>
                    <td class="text-end">{{ number_format($sumaWagaProdukcji, 3, ',', ' ') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('styles')
<style>
@page {
    size: A4 portrait;
    margin: 10mm;
}

@media print {
    body {
        font-size: 11px;
        color: #000;
    }

    .container,
    .container-fluid {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .print-title {
        font-size: 15px;
        margin-bottom: 6px;
    }

    .print-layout {
        gap: 0 !important;
    }

    table {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }

    .print-table {
        font-size: 10px;
    }

    .print-table th,
    .print-table td {
        padding: 2px 4px !important;
    }

    th, td {
        word-wrap: break-word;
        white-space: normal;
    }
}
</style>
@endpush



@push('scripts')
<script>
    window.addEventListener('load', () => {
        window.print();
    });

    window.addEventListener('afterprint', () => {
        history.back();
    });
</script>
@endpush


