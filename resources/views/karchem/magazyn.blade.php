@extends('layouts.karchem')
@section('content')
<div class="container mt-4">

    <!-- Nagłówek -->
    <div class="container-fluid mb-4">
        <div class="p-3 rounded shadow-sm text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #2c3e50, #3a506b);">

            <div class="d-flex align-items-center">
                <i class="fas fa-warehouse fa-2x me-3"></i>
                <h2 class="m-0 fw-bold">MAGAZYN</h2>
            </div>

        </div>
    </div>

    <!-- Filtry: Rok i Miesiące -->
    <div class="container-fluid mb-3">
        <div class="d-flex justify-content-between align-items-center">
            
            <!-- Select lat po lewej -->
            <form method="GET" id="filterForm" class="d-flex align-items-center">
                <input type="hidden" name="miesiac" value="{{ $selectedMiesiac }}">

                <select name="rok" class="form-select me-3" style="max-width: 120px;" onchange="document.getElementById('filterForm').submit()">
                    @foreach($lata as $r)
                        <option value="{{ $r }}" {{ $rok == $r ? 'selected' : '' }}>{{ $r }}</option>
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

                <!-- Przycisk "Wszystkie miesiące" -->
                <a href="{{ route('karchem.magazyn', array_merge(request()->except('miesiac'), ['miesiac' => null])) }}"
                    class="btn btn-sm {{ is_null($selectedMiesiac) ? 'btn-primary active' : 'btn-secondary' }} m-1"
                    style="min-width: 70px;">
                    Wszystkie
                </a>

                @foreach($miesiace as $num => $rzymska)
                    <a href="{{ route('karchem.magazyn', array_merge(request()->except('miesiac'), ['miesiac' => $num])) }}"
                        class="btn btn-sm {{ $selectedMiesiac == $num ? 'btn-primary active' : 'btn-secondary' }} m-1"
                        style="min-width: 40px;">
                        {{ $rzymska }}
                    </a>
                @endforeach
            </div>

        </div>
    </div>

    <!-- Nazwa miesiąca -->
    <div class="text-center mb-3">
        <h4 class="fw-bold text-secondary">
            @if(is_null($selectedMiesiac))
                Aktualny stan {{ $rok }}
            @else
                @php
                    $miesiaceNazwy = [
                        1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
                        5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
                        9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień'
                    ];
                @endphp
                Tylko {{ $miesiaceNazwy[$selectedMiesiac] }} {{ $rok }}
            @endif
        </h4>
    </div>

    <!-- Tabela magazynu -->
    <div class="table-responsive mx-auto" style="max-width: 700px;">
        <table class="table table-bordered table-striped">
            <thead class="table-info">
                <tr>
                    <th>Kod odpadu</th>
                    <th class="text-end">Ilość (kg)</th>
                    <th class="text-center">Szczegóły</th>
                </tr>
            </thead>
            <tbody>
                @php $sumaCalkowita = 0; @endphp
                @forelse($stanMagazynu as $kod => $ilosc)
                    @php $sumaCalkowita += $ilosc; @endphp
                    <tr>
                        <td>{{ $kod }}</td>
                        <td class="text-end">{{ number_format($ilosc, 3, ',', ' ') }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-success btn-szczegoly" 
                                    data-kod="{{ $kod }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#szczegolyModal">
                                <i class="fas fa-info-circle"></i> Pokaż
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Brak danych dla wybranego okresu</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="fw-bold table-light">
                    <td>RAZEM</td>
                    <td class="text-end">{{ number_format($sumaCalkowita, 3, ',', ' ') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

<!-- Modal szczegółów -->
<div class="modal fade" id="szczegolyModal" tabindex="-1" aria-labelledby="szczegolyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="szczegolyModalLabel">
                    <i class="fas fa-clipboard-list me-2"></i>Szczegóły dla kodu: <span id="modal-kod"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-content">
                    <!-- Treść będzie wstawiana dynamicznie -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Przygotowanie danych ze strony serwera
    const szczegoly = @json($szczegolyDane);
    const selectedMiesiac = {{ $selectedMiesiac ?? 'null' }};
    
    $('.btn-szczegoly').on('click', function() {
        const kod = $(this).data('kod');
        const dane = szczegoly[kod];
        
        $('#modal-kod').text(kod);
        
        let content = '<div class="list-group">';
        
        if (selectedMiesiac === null) {
            // Brak wybranego miesiąca - pokazujemy wszystko
            content += `
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 text-primary"><i class="fas fa-calendar-check me-2"></i>Stan początkowy:</h6>
                        <strong>${dane.stanPoczatkowy} kg</strong>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 text-success"><i class="fas fa-plus-circle me-2"></i>Suma BDO:</h6>
                        <strong class="text-success">+${dane.sumaBdo} kg</strong>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 text-danger"><i class="fas fa-minus-circle me-2"></i>Suma wysyłek:</h6>
                        <strong class="text-danger">-${dane.sumaWysylek} kg</strong>
                    </div>
                </div>
                <div class="list-group-item bg-light">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 fw-bold"><i class="fas fa-equals me-2"></i>Stan końcowy:</h6>
                        <strong class="fs-5">${dane.stanKoncowy} kg</strong>
                    </div>
                    <small class="text-muted">Stan początkowy + BDO - Wysyłki</small>
                </div>
            `;
        } else {
            // Wybrany miesiąc - pokazujemy tylko BDO i wysyłki
            content += `
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 text-success"><i class="fas fa-plus-circle me-2"></i>Suma BDO:</h6>
                        <strong class="text-success">+${dane.sumaBdo} kg</strong>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 text-danger"><i class="fas fa-minus-circle me-2"></i>Suma wysyłek:</h6>
                        <strong class="text-danger">-${dane.sumaWysylek} kg</strong>
                    </div>
                </div>
                <div class="list-group-item bg-light">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 fw-bold"><i class="fas fa-equals me-2"></i>Bilans miesiąca:</h6>
                        <strong class="fs-5">${dane.stanKoncowy} kg</strong>
                    </div>
                    <small class="text-muted">BDO - Wysyłki</small>
                </div>
            `;
        }
        
        content += '</div>';
        
        $('#modal-content').html(content);
    });
});
</script>
@endpush

@push('styles')
<style>
.btn-szczegoly {
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
}

.list-group-item h6 {
    margin-bottom: 0.25rem;
}

.modal-body {
    padding: 1.5rem;
}
</style>
@endpush