@extends('layouts.app')

@section('title', 'Edycja: ' . $client->short_name)
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('content')

<div class="page-header">
    <h1>Edycja: {{ $client->short_name }}</h1>
    <a href="{{ route('biuro.clients.show', $client) }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Powrót
    </a>
</div>

<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ route('biuro.clients.update', $client) }}">
            @csrf @method('PUT')
            @include('biuro.clients._form')
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-save">
                    <i class="fa-solid fa-floppy-disk"></i> Zapisz zmiany
                </button>
                <a href="{{ route('biuro.clients.show', $client) }}" class="btn btn-secondary">
                    <i class="fa-solid fa-xmark"></i> Anuluj
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
