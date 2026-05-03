<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts._meta')
    <title>Mrowisko</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-ui-dist/jquery-ui.min.css">
    <link href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet"/>
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    @stack('styles')

    <style>
        body {
            padding-top: 60px;
            font-family: 'Roboto', sans-serif;
        }

        /* Navbar */
        .navbar {
            background-color: #6EBF58 !important;
        }

        .navbar .nav-link {
            font-size: 1.05rem;
            font-weight: 600;
            color: #ffffff !important;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link:focus {
            color: #f0e68c !important;
        }

        /* Dropdown menu - tylko w navbarze */
        .navbar-nav .dropdown-menu .dropdown-item {
            font-size: 0.95rem;
            color: #333333;
        }

        .navbar-nav .dropdown-menu .dropdown-item:hover {
            color: gray !important;
            background-color: #f8f9fa;
        }

        .btn-menu:hover,
        .dropdown-toggle:hover {
            color: gray !important;   
        }

        /* Główny kontener */
        .container {
            max-width: 1200px;
            padding: 0 15px;
        }

        /* Nagłówek */
        .header-row {
            background-color: #add8e6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .thead-custom th {
            background-color: #6495ed !important;
            color: white !important;
            padding-top: 10px !important;
            padding-bottom: 10px !important;
        }

        .table-custom td {
            padding: 10px 10px !important;
        }

        /* Loading spinner */
        .loading-spinner {
            border: 4px solid #ddd;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top shadow-sm">
        <div class="container-fluid">
            <!-- Logo i tytuł -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="{{ asset('logo.png') }}" alt="Logo" height="32">
                <span class="fw-bold fs-5">KARCHEM</span>
            </a>

            <!-- Toggle dla mobilnych -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Linki główne -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0" style="gap: 20px;">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karchem.bdo') }}">
                            <i class="fas fa-recycle me-1"></i> BDO
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karchem.plac') }}">
                            <i class="fas fa-truck-loading me-1"></i> Plac
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karchem.wysylki') }}"> 
                            <i class="fas fa-paper-plane me-1"></i> Wysyłki </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="magazynDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-warehouse me-1"></i> Magazyn
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="magazynDropdown">
                            <li><a class="dropdown-item" href="{{ route('karchem.magazyn') }}">Magazyn</a></li>
                            <li><a class="dropdown-item" href="{{ route('karchem.stanyPoczatkowe') }}">Stany początkowe</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karchem.klienci') }}">
                            <i class="fas fa-users me-1"></i> Klienci
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('karchem.archiwum') }}">
                            <i class="fas fa-archive me-1"></i> Archiwum
                        </a>
                    </li>
                </ul>

                <!-- Prawa strona: Tato + Logout -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0" style="gap: 15px;">
                    @if (Auth::user()?->name === 'Tato')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('biuro.index') }}">
                            <i class="fas fa-user-cog me-1"></i> Tato
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="mb-0">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link text-white p-0">
                                <i class="fas fa-sign-out-alt me-1"></i> Wyloguj
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="p-2">
        @yield('content')
    </div>

    <!-- Modal dodawania NIP -->
    <div class="modal fade" id="addNipModal" tabindex="-1" aria-labelledby="addNipModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('karchem.addNip') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNipModalLabel">Dodaj nowy NIP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tylko cyfry, bez kresek</p>
                        <input type="text" name="nip" class="form-control" placeholder="Wprowadź NIP" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-warning">Dodaj</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
    @vite('resources/js/bdo/akcje.js')

    <script>
        // Obsługa błędów i komunikatów
        @if ($errors->any())
            let errorMessage = '{!! implode("<br>", $errors->all()) !!}';
            Swal.fire({
                icon: 'error',
                title: 'BŁĄD!',
                html: errorMessage,
            });
        @endif

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukces!',
                text: '{{ session("success") }}',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'BŁĄD!',
                text: '{{ session("error") }}',
            });
        @endif

        // Document ready
        $(document).ready(function() {
            // Twój kod inicjalizacyjny
        });
    </script>

    @include('partials._session_guard')
</body>
</html>