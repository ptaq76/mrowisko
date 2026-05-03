<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts._meta')
    <title>Mrowisko</title>

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Material Design Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.2.96/css/materialdesignicons.min.css">

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.5/sweetalert2.min.css">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --green:       #6EBF58;
            --green-dark:  #58a545;
            --green-light: #e8f7e4;
            --black:       #1a1a1a;
            --gray-1:      #f4f5f7;
            --gray-2:      #e2e5e9;
            --gray-3:      #9aa3ad;
            --font-display: 'Barlow Condensed', sans-serif;
            --font-body:    'Barlow', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background: var(--gray-1);
            padding-top: 58px;
        }

        /* ── NAVBAR ─────────────────────────────────────────── */
        #navbar .nav-divider {
            width: 1px;
            height: 28px;
            background: rgba(0,0,0,.18);
            flex-shrink: 0;
        }

        #navbar .nav-module {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--black);
        }

        .nav-user-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--black);
        }
        #navbar {
            background: var(--green) !important;
            min-height: 52px;
            padding: 6px 0;
        }

        #navbar .navbar-brand img {
            height: 30px;
            width: auto;
        }

        #navbar .navbar-brand .module-name {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--black);
            margin-left: 10px;
        }

        #navbar .nav-link {
            color: var(--black) !important;
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            padding: 5px 10px !important;
            border-radius: 4px;
            transition: background .15s;
        }

        #navbar .nav-link:hover,
        #navbar .nav-link.show {
            background: rgba(0,0,0,.12) !important;
            color: var(--black) !important;
        }

        #navbar .dropdown-toggle::after {
            margin-left: 4px;
        }

        #navbar .dropdown-menu {
            margin-top: 4px !important;
            top: 100% !important;
            bottom: auto !important;
            border: 1px solid var(--gray-2);
            box-shadow: 0 4px 12px rgba(0,0,0,.10);
            border-radius: 6px;
            padding: 4px 0;
            min-width: 190px;
        }

        #navbar .dropdown-item {
            font-size: 13px;
            font-weight: 500;
            color: var(--black);
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #navbar .dropdown-item i {
            width: 14px;
            text-align: center;
            color: var(--gray-3);
            font-size: 12px;
        }

        #navbar .dropdown-item:hover {
            background: var(--green-light);
            color: var(--black);
        }

        #navbar .dropdown-item:hover i { color: var(--green-dark); }

        #navbar .dropdown-header {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--gray-3);
            padding: 6px 16px 3px;
        }

        #navbar .dropdown-divider { margin: 4px 0; }

        /* Wyloguj */
        .btn-logout {
            color: var(--black) !important;
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 4px;
            border: none;
            background: none;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-logout:hover { background: rgba(0,0,0,.12); }

        /* Avatar */
        .nav-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(0,0,0,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 13px;
            color: var(--black);
            flex-shrink: 0;
        }

        /* ── MAIN ───────────────────────────────────────────── */
        #main { padding: 28px; }
        #main > .form-wrap { display: block; margin-left: auto; margin-right: auto; }

        /* ── PAGE HEADER ────────────────────────────────────── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            gap: 12px;
        }

        .page-header h1 {
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 700;
            letter-spacing: .02em;
            color: var(--black);
            margin: 0;
        }

        /* ── CARD ───────────────────────────────────────────── */
        .card {
            border: 1px solid var(--gray-2);
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }

        .card-header {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            background: var(--gray-1);
            border-bottom: 2px solid var(--green);
            color: var(--black);
        }

        /* ── TABLE ──────────────────────────────────────────── */
        .table thead tr {
            border-bottom: 2px solid var(--green);
            background: var(--gray-1);
        }

        .table thead th {
            font-family: var(--font-display);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--gray-3);
            border-bottom: none;
            padding: 10px 14px;
        }

        .table tbody td {
            padding: 10px 14px;
            vertical-align: middle;
            font-size: 14px;
        }

        .table tbody tr:hover { background: var(--green-light); }

        /* ── BUTTONS ────────────────────────────────────────── */
        /* Dodaj / Nowy */
        .btn-add {
            background: #1a1a1a;
            color: #fff;
            border: none;
        }
        .btn-add:hover { background: #333; color: #fff; }

        /* Edytuj */
        .btn-edit {
            background: #3498db;
            color: #fff;
            border: none;
        }
        .btn-edit:hover { background: #2980b9; color: #fff; }

        /* Zapisz */
        .btn-save {
            background: var(--green);
            color: var(--black);
            border: none;
        }
        .btn-save:hover { background: var(--green-dark); color: var(--black); }

        /* Zmień hasło */
        .btn-password {
            background: #f39c12;
            color: #fff;
            border: none;
        }
        .btn-password:hover { background: #e67e22; color: #fff; }

        /* Zatwierdź */
        .btn-confirm {
            background: #2c3e50;
            color: #fff;
            border: none;
        }
        .btn-confirm:hover { background: #1a252f; color: #fff; }

        /* ── FORMS ──────────────────────────────────────────── */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 0.2rem rgba(110,191,88,.25);
        }

        .form-label {
            font-weight: 600;
            font-size: 13px;
        }

        /* ── UTILITIES ──────────────────────────────────────── */
        .text-muted { color: var(--gray-3) !important; font-size: 13px; }

        code {
            background: var(--gray-1);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 13px;
            color: var(--black);
        }

        @yield('styles')
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav id="navbar" class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid px-3">

        {{-- Logo --}}
        <a class="navbar-brand me-2" href="{{ url('/') }}">
            <img src="{{ asset('logo.png') }}" alt="Logo">
        </a>

        <div class="nav-divider me-3"></div>

        {{-- Nazwa modułu --}}
        <span class="nav-module me-3">@yield('module_name', 'Panel')</span>

        {{-- Menu nawigacyjne modułu --}}
        <div class="d-flex align-items-center gap-1 flex-grow-1">
            @yield('nav_menu')
        </div>

        {{-- Prawa strona: user + wyloguj --}}
        <div class="d-flex align-items-center gap-3 ms-auto">
            <div class="d-flex align-items-center gap-2">
                <div class="nav-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <span class="nav-user-name d-none d-md-inline">{{ auth()->user()->name }}</span>
            </div>
            <div class="nav-divider"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="d-none d-md-inline">Wyloguj</span>
                </button>
            </form>
        </div>

    </div>
</nav>

{{-- MAIN --}}
<main id="main">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 fade show" role="alert">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible d-flex align-items-center gap-2 fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

{{-- jQuery --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

{{-- Bootstrap 5 JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

{{-- SweetAlert2 --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.5/sweetalert2.all.min.js"></script>

<script>
    // CSRF token dla AJAX
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Auto-hide flash messages po 4s
    setTimeout(() => {
        $('.alert').alert('close');
    }, 4000);

    // Globalna konfiguracja SweetAlert
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    // Helper: potwierdzenie usunięcia
    function confirmDelete(form) {
        Swal.fire({
            title: 'Czy na pewno?',
            text: 'Tej operacji nie można cofnąć.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#9aa3ad',
            confirmButtonText: 'Tak, usuń',
            cancelButtonText: 'Anuluj'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
        return false;
    }
</script>
 <script src="{{ asset('js/bdo/akcje.js') }}"></script>

@include('partials._session_guard')
@include('partials._polling')

@yield('scripts')

</body>
</html>