@extends('layouts.app')

@section('title', 'Annex 7 – Edycja kontrahenta')
@section('module_name', 'ADMINISTRATOR')

@section('nav_menu')
    @include('admin.annex7._nav')
@endsection

@section('content')

<div class="page-header">
    <h1>Edycja kontrahenta – {{ $annex7Contractor->name }}</h1>
    <a href="{{ route('admin.annex7-contractors.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Wróć
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.annex7-contractors.update', $annex7Contractor) }}">
            @csrf @method('PUT')
            @include('admin.annex7.contractors._form', ['contractor' => $annex7Contractor])
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-save">
                    <i class="fa-solid fa-save"></i> Zapisz zmiany
                </button>
                <a href="{{ route('admin.annex7-contractors.index') }}" class="btn btn-secondary">
                    Anuluj
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
