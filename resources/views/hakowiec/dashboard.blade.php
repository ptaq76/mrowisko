@extends('layouts.app')
@section('module_name', 'HAKOWIEC')

@section('content')
<div class="container py-3">

    <h2 class="mb-3">
        <i class="fas fa-truck"></i> Hakowiec - {{ $date->format('d.m.Y') }}
        @if($driver)
            <small class="text-muted">{{ $driver->name }}</small>
        @endif
    </h2>

    {{-- ══ ZADANIA ══ --}}
    @if($zadania->isNotEmpty())
    <div class="card mb-3" style="background:#fff8e1;border:2px solid #f9d38c">
        <div class="card-header" style="background:#f9d38c;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6d4c00">
            <i class="fas fa-tasks"></i> Zadania na dziś
        </div>
        <div class="card-body p-0">
            @foreach($zadania as $z)
            <div class="d-flex align-items-center gap-2 p-2" style="border-bottom:1px solid #f0e0a0;{{ $z->status === 'done' ? 'opacity:.5' : '' }}">
                @if($z->status === 'done')
                    <i class="fas fa-check-circle text-success fa-lg"></i>
                @else
                    <i class="far fa-circle text-muted fa-lg"></i>
                @endif
                <span style="flex:1;font-size:14px;font-weight:600;{{ $z->status === 'done' ? 'text-decoration:line-through;color:#888' : '' }}">
                    {{ $z->tresc }}
                </span>
                @if($z->status === 'pending')
                <button class="btn btn-success btn-sm" onclick="wykonajZadanie({{ $z->id }})">
                    <i class="fas fa-check"></i> Wykonaj
                </button>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(!$driver)
        <div class="alert alert-warning">Brak przypisanego kierowcy dla tego konta.</div>
    @endif

    <div class="card">
        <div class="card-body text-muted">Moduł w budowie.</div>
    </div>

</div>
@endsection

@section('scripts')
<script>
async function wykonajZadanie(id) {
    const result = await Swal.fire({
        title: 'Czy zapisać wykonanie zadania?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Tak',
        cancelButtonText: 'Nie',
        confirmButtonColor: '#27ae60',
    });
    if (!result.isConfirmed) return;
    const fd = new FormData();
    fd.append('_token', '{{ csrf_token() }}');
    await fetch(`/hakowiec/zadania/${id}/wykonaj`, { method: 'POST', body: fd });
    location.reload();
}
</script>
@endsection
