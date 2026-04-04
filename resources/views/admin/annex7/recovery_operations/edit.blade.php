@extends('layouts.app')

@section('title', 'Annex 7 – Edycja operacji odzysku')
@section('module_name', 'ADMINISTRATOR')

@section('nav_menu')
    @include('admin.annex7._nav')
@endsection

@section('content')

<div class="page-header">
    <h1>Edycja operacji – {{ $annex7RecoveryOperation->code }}</h1>
    <a href="{{ route('admin.annex7-recovery-operations.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Wróć
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.annex7-recovery-operations.update', $annex7RecoveryOperation) }}">
            @csrf @method('PUT')
            @include('admin.annex7.recovery_operations._form', ['operation' => $annex7RecoveryOperation])
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-save">
                    <i class="fa-solid fa-save"></i> Zapisz zmiany
                </button>
                <a href="{{ route('admin.annex7-recovery-operations.index') }}" class="btn btn-secondary">Anuluj</a>
            </div>
        </form>
    </div>
</div>

@endsection
