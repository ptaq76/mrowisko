@extends('layouts.app')

@section('title', 'Annex 7 – Operacje odzysku')
@section('module_name', 'ADMINISTRATOR')

@section('nav_menu')
    @include('admin.annex7._nav')
@endsection

@section('content')

<div class="page-header">
    <h1>Operacje odzysku (pole 8)</h1>
    <a href="{{ route('admin.annex7-recovery-operations.create') }}" class="btn btn-add">
        <i class="fa-solid fa-plus"></i> Nowa operacja
    </a>
</div>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({ icon: 'success', title: 'Sukces', text: '{{ session('success') }}', timer: 2500, showConfirmButton: false });
        });
    </script>
@endif
@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({ icon: 'error', title: 'Błąd', text: '{{ session('error') }}' });
        });
    </script>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Kod</th>
                    <th style="width:100px">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse($operations as $op)
                <tr>
                    <td><code>{{ $op->code }}</code></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.annex7-recovery-operations.edit', $op) }}"
                               class="btn btn-edit btn-sm" title="Edytuj">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.annex7-recovery-operations.destroy', $op) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-delete" title="Usuń">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center text-muted py-4">Brak operacji odzysku.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $operations->links() }}

@endsection

@section('scripts')
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const form = this.closest('form');
        Swal.fire({
            title: 'Usunąć operację?',
            text: 'Tej operacji nie można cofnąć.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Tak, usuń',
            cancelButtonText: 'Anuluj',
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endsection
