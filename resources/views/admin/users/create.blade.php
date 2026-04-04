@extends('layouts.app')

@section('title', 'Nowy użytkownik')
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
    <h1>Nowy użytkownik</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Powrót
    </a>
</div>

<div class="card" style="max-width:520px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="name">Imię i nazwisko</label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="Jan Kowalski">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="login">Login</label>
                <input type="text" id="login" name="login"
                       class="form-control @error('login') is-invalid @enderror"
                       value="{{ old('login') }}" placeholder="jan.kowalski"
                       autocomplete="off">
                @error('login')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Hasło</label>
                <input type="text" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       value="{{ old('password') }}" placeholder="Min. 6 znaków"
                       autocomplete="off">
                <div class="form-text">Hasło będzie widoczne – zanotuj je i przekaż użytkownikowi.</div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label" for="module">Moduł</label>
                <select id="module" name="module"
                        class="form-select @error('module') is-invalid @enderror">
                    <option value="">– wybierz moduł –</option>
                    @foreach($modules as $value => $label)
                        @if($value !== 'admin')
                            <option value="{{ $value }}" {{ old('module') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('module')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-add">
                    <i class="fa-solid fa-user-plus"></i> Utwórz użytkownika
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-xmark"></i> Anuluj
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
