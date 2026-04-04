@extends('layouts.app')

@section('title', 'Edycja użytkownika')
@section('module_name', 'ADMINISTRATOR')

@section('nav_menu')
    <div class="dropdown">
        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fa-solid fa-users"></i> Użytkownicy
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">
                <i class="fa-solid fa-list"></i> Lista użytkowników
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.users.create') }}">
                <i class="fa-solid fa-user-plus"></i> Nowy użytkownik
            </a></li>
        </ul>
    </div>
@endsection

@section('content')

<div class="page-header">
    <h1>Edycja: {{ $user->name }}</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Powrót
    </a>
</div>

<div class="card" style="max-width:520px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label" for="name">Imię i nazwisko</label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="login">Login</label>
                <input type="text" id="login" name="login"
                       class="form-control @error('login') is-invalid @enderror"
                       value="{{ old('login', $user->login) }}"
                       autocomplete="off">
                @error('login')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label" for="module">Moduł</label>
                <select id="module" name="module"
                        class="form-select @error('module') is-invalid @enderror"
                        {{ $user->module === 'admin' ? 'disabled' : '' }}>
                    @foreach($modules as $value => $label)
                        @if($value !== 'admin' || $user->module === 'admin')
                            <option value="{{ $value }}"
                                {{ old('module', $user->module) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @if($user->module === 'admin')
                    <div class="form-text">Modułu administratora nie można zmienić.</div>
                @endif
                @error('module')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-save">
                    <i class="fa-solid fa-floppy-disk"></i> Zapisz zmiany
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-xmark"></i> Anuluj
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
