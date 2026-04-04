@extends('layouts.app')

@section('title', 'Annex 7 – Kontrahenci')
@section('module_name', 'ADMINISTRATOR')

@section('nav_menu')
    @include('admin.annex7._nav')
@endsection

@section('content')

<div class="page-header">
    <h1>Kontrahenci Annex 7</h1>
    <a href="{{ route('admin.annex7-contractors.create') }}" class="btn btn-add">
        <i class="fa-solid fa-plus"></i> Nowy kontrahent
    </a>
</div>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({ icon: 'success', title: 'Sukces', text: '{{ session('success') }}', timer: 2500, showConfirmButton: false });
        });
    </script>
@endif

{{-- Filtr roli --}}
<div class="mb-3 d-flex gap-2 flex-wrap align-items-center">
    <span class="text-muted me-1" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em">Rola:</span>
    <a href="{{ route('admin.annex7-contractors.index') }}"
       class="btn btn-sm {{ !$role ? 'btn-dark' : 'btn-outline-secondary' }}">
        Wszystkie <span class="badge bg-secondary ms-1">{{ $contractors->total() }}</span>
    </a>
    @foreach($roles as $key => $label)
    @php
        $activeClass = match($key) {
            'arranger'  => 'btn-primary',
            'importer'  => 'btn-success',
            'carrier'   => 'btn-warning text-dark',
            'generator' => 'btn-secondary',
            'recovery'  => 'btn-info text-dark',
            default     => 'btn-dark',
        };
    @endphp
    <a href="{{ route('admin.annex7-contractors.index', ['role' => $key]) }}"
       class="btn btn-sm {{ $role === $key ? $activeClass : 'btn-outline-secondary' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Skrót</th>
                    <th>Rola</th>
                    <th>Adres</th>
                    <th>Kontakt</th>
                    <th>Tel</th>
                    <th>Mail</th>
                    <th style="width:100px">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contractors as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>
                        @if($c->short_name)
                            <span class="badge bg-info text-dark">{{ $c->short_name }}</span>
                        @else
                            <span class="text-muted">–</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = match($c->role) {
                                'arranger'  => 'bg-primary',
                                'importer'  => 'bg-success',
                                'carrier'   => 'bg-warning text-dark',
                                'generator' => 'bg-secondary',
                                'recovery'  => 'bg-info text-dark',
                                default     => 'bg-light text-dark',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $c->roleName() }}</span>
                    </td>
                    <td>{{ $c->address ?? '–' }}</td>
                    <td>{{ $c->contact ?? '–' }}</td>
                    <td>{{ $c->tel ?? '–' }}</td>
                    <td>{{ $c->mail ?? '–' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.annex7-contractors.edit', $c) }}"
                               class="btn btn-edit btn-sm" title="Edytuj">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.annex7-contractors.destroy', $c) }}">
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
                    <td colspan="8" class="text-center text-muted py-4">Brak kontrahentów dla wybranej roli.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $contractors->links() }}

@endsection

@section('scripts')
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const form = this.closest('form');
        Swal.fire({
            title: 'Usunąć kontrahenta?',
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
