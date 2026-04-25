@extends('layouts.karchem')
@section('content')

<div class="container mt-4">
    <!-- Header -->
    <div class="container-fluid mb-4">
        <div class="p-3 rounded shadow-sm text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #2c3e50, #3a506b);">
            <div class="d-flex align-items-center">
                <i class="far fa-calendar-alt fa-2x me-3"></i>
                <h2 class="m-0 fw-bold">STANY POCZĄTKOWE</h2>
            </div>
            <button id="toggle-form-btn" class="btn btn-danger">
                <i class="fas fa-plus"></i> Dodaj
            </button>
        </div>
    </div>

    <!-- Ukryty formularz -->
    <div id="dodaj-formularz" class="card shadow-sm mb-4 mx-auto" 
         style="max-width: 400px; display: none;">
         <div class="card-header py-2 bg-light border-bottom d-flex justify-content-between align-items-center">
    <span class="fw-semibold text-secondary small">Stany poczatkowe</span>
    <button type="button" class="btn-close" onclick="document.getElementById('dodaj-formularz').style.display='none'"></button>
</div>

        <div class="card-body">
            <form action="{{ route('karchem.stanyPoczatkowe.store') }}" method="POST">
                @csrf

                <!-- Rok -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Rok</label>
                    <select name="rok" class="form-select">
                        @for ($y = 2024; $y <= 2027; $y++)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Kody odpadów -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Kody odpadów</label>
                    @foreach($kody as $kod)
                        <div class="row mb-2">
                            <div class="col-7">
                                <input type="text" class="form-control" value="{{ $kod->kod }}" disabled>
                                <input type="hidden" name="kody[]" value="{{ $kod->kod }}">
                            </div>
                            <div class="col-5">
                                <input type="number" 
                                    name="wartosci[]" 
                                    step="0.001"
                                    value="0"
                                    class="form-control"
                                    placeholder="0.000">
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-save"></i> Zapisz
                </button>
            </form>
        </div>
    </div>

    <!-- Wycentrowana sekcja z rokiem i tabelą -->
    <div class="d-flex flex-column align-items-center">
        
        <!-- Wybrany rok -->
        <div class="mb-3 text-center">
            <h3 class="fw-bold text-dark mb-2">
                <i class="fas fa-calendar-check me-2"></i>Rok: {{ $rok }}
            </h3>
            
            <!-- Select lat -->
            <form method="GET">
                <select name="rok" id="rok" class="form-select w-auto d-inline-block" onchange="this.form.submit()" style="min-width: 120px;">
                    @for($r=2024; $r<=2027; $r++)
                        <option value="{{ $r }}" {{ $rok == $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endfor
                </select>
            </form>
        </div>

        <!-- Tabela -->
        <div class="table-responsive" style="max-width: 600px;">
    <table class="table table-bordered table-striped">
        <thead class="table-success">
            <tr>
                <th class="text-center align-middle" style="width:150px;">Kod</th>
                <th class="text-center align-middle" style="width:150px;">Ilość</th>
                <th class="text-center align-middle" style="width:150px;">Akcja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stany as $stan)
                <tr>
                    <td class="text-center align-middle">{{ $stan->kod }}</td>
                    <td class="text-center align-middle">{{ number_format($stan->ilosc, 3, ',', '') }}</td>
                    <td class="text-center align-middle">
                        <button 
                            class="btn btn-sm btn-primary edit-btn"
                            data-id="{{ $stan->id }}"
                            data-rok="{{ $stan->rok }}"
                            data-kod="{{ $stan->kod }}"
                            data-ilosc="{{ $stan->ilosc }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal">
                            <i class="fas fa-edit"></i> Edytuj
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center align-middle">Brak danych dla wybranego roku</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


    </div>

</div>

<!-- Modal edycji -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editFormStany" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edytuj stan początkowy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rok</label>
                        <input type="text" id="modal-rok" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kod odpadu</label>
                        <input type="text" id="modal-kod" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ilość</label>
                        <input type="number" step="0.001" id="modal-ilosc" name="ilosc" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                    <button type="submit" class="btn btn-success">Zapisz</button>
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
    // Otwieranie modala i wypełnianie danymi
    $('.edit-btn').on('click', function () {
        let id = $(this).data('id');
        let rok = $(this).data('rok');
        let kod = $(this).data('kod');
        let ilosc = $(this).data('ilosc');

        $('#modal-rok').val(rok);
        $('#modal-kod').val(kod);
        $('#modal-ilosc').val(ilosc);
        $('#editFormStany').attr('data-id', id);
    });

    // Obsługa wysyłki AJAX
    $('#editFormStany').on('submit', function (e) {
        e.preventDefault();

        let id = $(this).attr('data-id');
        let ilosc = $('#modal-ilosc').val();

        $.ajax({
            url: "/karchem/stany-poczatkowe/" + id,
            type: "PUT",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                ilosc: ilosc
            },
            success: function (res) {
                let row = $('button[data-id="' + id + '"]').closest('tr');
                row.find('td:nth-child(2)').text(parseFloat(ilosc).toFixed(3).replace('.', ','));
                $('#editModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Zaktualizowano poprawnie',
                    timer: 1500,
                    showConfirmButton: false,
                });
            },
            error: function (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Błąd zapisu',
                });
            }
        });
    });
});
</script>
@endpush