@extends('layouts.karchem')
@section('content')
<div class="container mt-4">

    <!-- Nagłówek -->
    <div class="container-fluid mb-4">
        <div class="p-3 rounded shadow-sm text-white d-flex justify-content-between align-items-center"
            style="background: linear-gradient(135deg, #2c3e50, #3a506b);">

            <div class="d-flex align-items-center">
                <i class="fas fa-truck fa-2x me-3"></i>
                <h2 class="m-0 fw-bold">WYSYŁKI</h2>
            </div>

            <button id="toggle-form-btn" class="btn btn-danger">
                <i class="fas fa-plus"></i> Dodaj
            </button>

        </div>
    </div>

    <!-- Filtry: Rok, Miesiące, Klient, Kod -->
    <div class="container-fluid mb-3">
        <form method="GET" id="filterForm">
            <div class="row g-3 mb-3">
                <!-- Select Rok -->
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Rok</label>
                    <select name="rok" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        @foreach($lata as $r)
                        <option value="{{ $r }}" {{ $rok==$r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Klient -->
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Klient</label>
                    <select name="klient" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Wszyscy klienci</option>
                        @foreach($klienci as $k)
                        <option value="{{ $k }}" {{ $selectedKlient == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Kod odpadu -->
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Kod odpadu</label>
                    <select name="kod_filter" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Wszystkie kody</option>
                        @foreach($kody as $kod)
                        <option value="{{ $kod->kod }}" {{ $selectedKod == $kod->kod ? 'selected' : '' }}>
                            {{ $kod->kod }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Przycisk Reset -->
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('karchem.wysylki', ['rok' => $rok]) }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>

            <!-- Przyciskowe miesiące -->
            <div class="d-flex justify-content-center flex-wrap">
                @php
                $miesiace = [
                    1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                    7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
                ];
                @endphp

                <!-- Przycisk "Wszystkie miesiące" -->
                <a href="{{ route('karchem.wysylki', array_merge(request()->except('miesiac'), ['miesiac' => null])) }}"
                    class="btn btn-sm {{ is_null($selectedMiesiac) ? 'btn-primary active' : 'btn-secondary' }} m-1"
                    style="min-width: 70px;">
                    Wszystkie
                </a>

                @foreach($miesiace as $num => $rzymska)
                <a href="{{ route('karchem.wysylki', array_merge(request()->except('miesiac'), ['miesiac' => $num])) }}"
                    class="btn btn-sm {{ $selectedMiesiac == $num ? 'btn-primary active' : 'btn-secondary' }} m-1"
                    style="min-width: 40px;">
                    {{ $rzymska }}
                </a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Ukryty formularz Dodaj -->
    <div id="dodaj-formularz" class="card shadow-sm mb-4 mx-auto" style="max-width: 500px; display: none;">
        <div class="card-header py-2 bg-light border-bottom d-flex justify-content-between align-items-center">
            <span class="fw-semibold text-secondary small">Dodaj wysyłkę</span>
            <button type="button" class="btn-close"
                onclick="document.getElementById('dodaj-formularz').style.display='none'"></button>
        </div>

        <div class="card-body">

            <form action="{{ route('karchem.wysylki.store') }}" method="POST">
                @csrf

                <!-- Data -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Data</label>
                    <input type="date" name="data" class="form-control" value="" required>
                </div>

                <!-- Kod odpadu -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Kod odpadu</label>
                    <select name="kod" class="form-select" required>
                        @foreach($kody as $kod)
                        <option value="{{ $kod->kod }}" {{ $kod->kod == '15 01 02' ? 'selected' : '' }}>
                            {{ $kod->kod }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Ilość -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Ilość</label>
                    <input type="number" name="ilosc" step="0.001" min="0.010" value="" class="form-control"
                        required>
                </div>

                <!-- Klient -->
                <div class="mb-3">
                    <label class="form-label fw-bold d-flex align-items-center gap-2">
                        Klient
                        <button type="button"
                                class="btn btn-sm btn-info"
                                onclick="document.querySelector('input[name=klient]').value='EWRANT'">
                            EWRANT
                        </button>
                    </label>

                    <input type="text"
                        name="klient"
                        class="form-control"
                        placeholder="Nazwa klienta"
                        required>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-save"></i> Zapisz
                </button>

            </form>

        </div>
    </div>

    <!-- Nazwa miesiąca -->
    <div class="text-center mb-3">
        <h4 class="fw-bold text-secondary">
            @if(is_null($selectedMiesiac))
            Wszystkie miesiące {{ $rok }}
            @else
            @php
            $miesiaceNazwy = [
                1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
                5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
                9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień'
            ];
            @endphp
            {{ $miesiaceNazwy[$selectedMiesiac] }} {{ $rok }}
            @endif
        </h4>

        @if($selectedKlient || $selectedKod)
        <div class="mt-2">
            @if($selectedKlient)
            <span class="badge bg-info me-2">Klient: {{ $selectedKlient }}</span>
            @endif
            @if($selectedKod)
            <span class="badge bg-warning">Kod: {{ $selectedKod }}</span>
            @endif
        </div>
        @endif
    </div>

    <!-- Tabela wysyłek -->
    <div class="table-responsive mx-auto" style="max-width: 800px;">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>Data</th>
                    <th style="min-width: 200px;">Klient</th>
                    <th>Kod</th>
                    <th>Ilość</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wysylki as $w)
                <tr>
                    <td class="text-center align-middle">{{ \Carbon\Carbon::parse($w->data)->format('Y-m-d') }}</td>
                    <td class="align-middle">{{ $w->klient }}</td>
                    <td class="text-center align-middle">{{ $w->kod }}</td>
                    <td class="text-end align-middle">{{ number_format($w->ilosc, 3, ',', '') }}</td>
                    <td class="text-center align-middle">
                        <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $w->id }}"
                            data-data="{{ \Carbon\Carbon::parse($w->data)->format('Y-m-d') }}" data-kod="{{ $w->kod }}"
                            data-ilosc="{{ $w->ilosc }}" data-klient="{{ $w->klient }}" data-bs-toggle="modal"
                            data-bs-target="#editModal">
                            <i class="fas fa-edit"></i> Edytuj
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Brak danych dla wybranego okresu</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Podsumowanie wg kodów -->
    <div class="container mt-4 d-flex flex-column align-items-center">
        <div class="w-100" style="max-width: 400px;">
            <h6 class="fw-bold">Podsumowanie wg kodu odpadu</h6>
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Kod odpadu</th>
                        <th class="text-end">Suma (Mg)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumaKod = 0; @endphp
                    @foreach ($podsumowanieKod as $kod => $ilosc)
                    @php $sumaKod += $ilosc; @endphp
                    <tr>
                        <td>{{ $kod }}</td>
                        <td class="text-end">{{ number_format($ilosc, 3, ',', ' ') }}</td>
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

<!-- Modal edycji -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="editFormWysylki" method="POST" class="mx-auto" style="max-width: 500px;">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Data -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">
                            <i class="fas fa-calendar me-1"></i> Data
                        </label>
                        <input type="date" id="modal-data" name="data" class="form-control" required>
                    </div>

                    <!-- Kod odpadu -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">
                            <i class="fas fa-barcode me-1"></i> Kod odpadu
                        </label>
                        <select id="modal-kod" name="kod" class="form-select" required>
                            @foreach($kody as $kod)
                            <option value="{{ $kod->kod }}">{{ $kod->kod }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ilość -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">
                            <i class="fas fa-weight me-1"></i> Ilość
                        </label>
                        <input type="number" step="0.001" id="modal-ilosc" name="ilosc" class="form-control" required>
                    </div>

                    <!-- Klient -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">
                            <i class="fas fa-user me-1"></i> Klient
                        </label>
                        <input type="text" id="modal-klient" name="klient" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light flex-fill" data-bs-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-check me-1"></i> Zapisz
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('toggle-form-btn').addEventListener('click', function () {
    const form = document.getElementById('dodaj-formularz');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
});

$(document).ready(function () {

    // === 1. Otwieranie modala i wypełnianie danymi ===
    $('.edit-btn').on('click', function () {
        let id = $(this).data('id');
        let data = $(this).data('data'); 
        let kod = $(this).data('kod');
        let ilosc = $(this).data('ilosc');
        let klient = $(this).data('klient');

        // Konwersja daty do formatu YYYY-MM-DD
        let dataFormatted = data;
        if (data) {
            let dateObj = new Date(data);
            if (!isNaN(dateObj.getTime())) {
                dataFormatted = dateObj.toISOString().split('T')[0];
            }
        }

        $('#modal-data').val(dataFormatted);

        // Konwersja kodu z liczby (np. 150102) na format z spacjami (np. "15 01 02")
        let kodString = String(kod);
        if (kodString.length === 6) {
            kodString = kodString.substring(0, 2) + ' ' + kodString.substring(2, 4) + ' ' + kodString.substring(4, 6);
        }
        $('#modal-kod').val(kodString);

        $('#modal-ilosc').val(ilosc);
        $('#modal-klient').val(klient);

        // Ustawienie dynamicznego action dla formularza
        $('#editFormWysylki').attr('data-id', id);
        $('#editFormWysylki').attr('action', '/karchem/wysylki/' + id);
    });

    // === 2. Obsługa wysyłki AJAX ===
    $('#editFormWysylki').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let id = form.attr('data-id');
        let url = form.attr('action');

        let data = $('#modal-data').val();
        let kod = $('#modal-kod').val();
        let ilosc = $('#modal-ilosc').val();
        let klient = $('#modal-klient').val();

        $.ajax({
            url: url,
            type: "PUT",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                data: data,
                kod: kod,
                ilosc: ilosc,
                klient: klient
            },
            success: function (res) {
                // Zaktualizuj wiersz w tabeli
                let row = $('button[data-id="' + id + '"]').closest('tr');
                row.find('td:nth-child(1)').text(data);
                row.find('td:nth-child(2)').text(klient);
                row.find('td:nth-child(3)').text(kod);
                row.find('td:nth-child(4)').text(parseFloat(ilosc).toFixed(3).replace('.', ','));

                // Zamknij modal
                $('#editModal').modal('hide');

                // Powiadomienie
                Swal.fire({
                    icon: 'success',
                    title: 'Wysyłka została zaktualizowana',
                    timer: 1500,
                    showConfirmButton: false,
                });
            },
            error: function (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Błąd podczas zapisu',
                    text: 'Sprawdź dane.',
                });
            }
        });
    });

});
</script>
@endpush

@push('styles')
<style>
    /* Modal styling */
    #editModal .modal-dialog {
        display: flex;
        align-items: center;
        min-height: calc(100% - 1rem);
    }

    #editModal .modal-content {
        border: none !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
    }

    #editModal .modal-header {
        border-bottom: none !important;
        padding-bottom: 0 !important;
        padding-top: 1rem !important;
        padding-right: 1rem !important;
    }

    #editModal .modal-body {
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
        padding-top: 0.5rem !important;
        padding-bottom: 1.5rem !important;
    }

    #editModal .modal-footer {
        border-top: none !important;
        padding-top: 0 !important;
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
        padding-bottom: 1.5rem !important;
        display: flex !important;
        gap: 0.5rem !important;
    }

    /* Close button */
    #editModal .btn-close {
        opacity: 0.5 !important;
        transition: opacity 0.2s ease !important;
    }

    #editModal .btn-close:hover {
        opacity: 1 !important;
    }

    /* Form labels */
    #editModal .form-label {
        font-weight: 600 !important;
        color: #6c757d !important;
        font-size: 0.875rem !important;
        margin-bottom: 0.5rem !important;
    }

    #editModal .form-label i {
        opacity: 0.7;
    }

    /* Form controls */
    #editModal .form-control,
    #editModal .form-select {
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        padding: 0.625rem 0.75rem !important;
        transition: all 0.2s ease !important;
    }

    #editModal .form-control:focus,
    #editModal .form-select:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15) !important;
    }

    /* Buttons */
    #editModal .btn {
        border-radius: 8px !important;
        padding: 0.625rem 1.25rem !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
    }

    #editModal .btn-secondary,
    #editModal .btn-light {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
        color: #6c757d !important;
    }

    #editModal .btn-secondary:hover,
    #editModal .btn-light:hover {
        background-color: #e9ecef !important;
        border-color: #ced4da !important;
    }

    #editModal .btn-success,
    #editModal .btn-primary {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7) !important;
        border: none !important;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2) !important;
        color: white !important;
    }

    #editModal .btn-success:hover,
    #editModal .btn-primary:hover {
        background: linear-gradient(135deg, #0b5ed7, #0a58ca) !important;
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3) !important;
        transform: translateY(-1px) !important;
    }

    /* Footer buttons equal width */
    #editModal .modal-footer .btn {
        flex: 1;
    }
</style>
@endpush