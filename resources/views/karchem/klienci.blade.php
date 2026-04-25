@extends('layouts.karchem')

@section('content')

<div class="container mt-4">
<!-- Nagłówek -->
<div class="container-fluid mb-4">
    <div class="p-3 rounded shadow-sm text-white d-flex justify-content-between align-items-center"
         style="background: linear-gradient(135deg, #2c3e50, #3a506b);">
        <div class="d-flex align-items-center">
            <i class="fas fa-users fa-2x me-3"></i>
            <h2 class="m-0 fw-bold">KLIENCI KARCHEM</h2>
        </div>
    </div>
</div>
<div class="container mt-4 w-75">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th class="align-middle">NIP</th>
                <th class="d-flex align-items-center justify-content-between">
                    Klient
                    <button id="btnAddNip" class="btn btn-sm btn-warning" title="Dodaj nowy NIP" data-bs-toggle="modal"
                        data-bs-target="#addNipModal">
                        <i class="fas fa-plus"></i> Dodaj nowego klienta
                    </button>
                </th>

                <th class="align-middle">Akcje</th>
            </tr>

        </thead>
        <tbody>
            @forelse ($klienci as $klient)
            <tr>
                <td>{{ $klient->nip }}</td>
                <td class="text-uppercase">{{ $klient->sender_name ?? '-' }}</td>
                <td>
                    <form action="{{ route('karchem.destroy', $klient->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger btn-delete">
                            <i class="fas fa-trash-alt"></i> Usuń
                        </button>

                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3">Brak klientów do wyświetlenia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>




@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    // złap wszystkie przyciski usuń z klasą btn-delete
    document.querySelectorAll('.btn-delete').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // zablokuj natychmiastowe wysłanie formularza

            const form = this.closest('form');

            Swal.fire({
                title: 'Na pewno usunąć?',
                text: "Tej akcji nie można cofnąć!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Tak, usuń!',
                cancelButtonText: 'Anuluj'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // wyślij formularz po potwierdzeniu
                }
            });
        });
    });
});




</script>
@endpush