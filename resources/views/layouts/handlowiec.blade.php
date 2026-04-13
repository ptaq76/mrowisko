<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Handlowiec') – MrowiskoBIS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800;900&family=Barlow+Condensed:wght@700;800;900&display=swap" rel="stylesheet">

    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }

        :root {
            --accent:      #6EBF58;
            --accent-dark: #58a545;
            --black:       #1a1a1a;
            --gray-1:      #f4f5f7;
            --gray-2:      #e2e5e9;
            --font-display: 'Barlow Condensed', sans-serif;
        }

        body {
            font-family: 'Barlow', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
            padding-bottom: 32px;
            min-height: 100vh;
        }

        /* ── NAV ── */
        .h-nav {
            position: sticky;
            top: 0;
            z-index: 200;
            background: var(--accent);
            padding: 6px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,.3);
            min-height: 48px;
        }

        .h-nav-brand {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 900;
            letter-spacing: .1em;
            color: var(--black);
        }

        .h-nav-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Hamburger toggle */
        .h-hamburger {
            background: none;
            border: 1px solid rgba(0,0,0,.25);
            color: var(--black);
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            font-family: var(--font-display);
            font-weight: 700;
            letter-spacing: .04em;
            transition: border-color .15s;
        }
        .h-hamburger:hover { border-color: #888; }

        .h-hamburger .h-username {
            font-size: 14px;
            color: var(--black);
        }

        /* Dropdown menu */
        .h-dropdown-menu {
            position: absolute;
            top: calc(100% + 4px);
            right: 14px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0,0,0,.18);
            min-width: 220px;
            overflow: hidden;
            z-index: 300;
            display: none;
            border: 1px solid var(--gray-2);
        }
        .h-dropdown-menu.open { display: block; }

        .h-menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 18px;
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
            letter-spacing: .04em;
            color: var(--black);
            text-decoration: none;
            transition: background .1s;
            border-bottom: 1px solid #f4f5f7;
        }
        .h-menu-item:last-child { border-bottom: none; }
        .h-menu-item:hover { background: var(--gray-1); }
        .h-menu-item.active { color: var(--accent-dark); background: #e8f7e4; }
        .h-menu-item i { width: 20px; text-align: center; font-size: 15px; color: #888; }
        .h-menu-item.active i { color: var(--accent-dark); }

        .h-menu-divider { height: 1px; background: var(--gray-2); margin: 4px 0; }

        .h-logout-btn {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 18px;
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
            letter-spacing: .04em;
            color: #e74c3c;
            cursor: pointer;
            transition: background .1s;
        }
        .h-logout-btn:hover { background: #fdf2f2; }
        .h-logout-btn i { width: 20px; text-align: center; }

        /* ── BACK BUTTON ── */
        .h-back-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 13px;
            margin: 14px 0 18px;
            background: #fff;
            border: 1.5px solid var(--gray-2);
            border-radius: 14px;
            font-family: var(--font-display);
            font-size: 17px;
            font-weight: 900;
            letter-spacing: .04em;
            text-transform: uppercase;
            text-decoration: none;
            color: #555;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            transition: background .12s, box-shadow .12s;
        }
        .h-back-btn:hover { background: var(--gray-1); color: var(--black); box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .h-back-btn i { color: var(--black); }

        /* ── CONTENT ── */
        #h-main {
            padding: 0 14px;
        }

        @yield('styles')
    </style>
</head>
<body>

<nav class="h-nav">
    <a href="{{ url('/') }}" class="h-nav-brand" style="text-decoration:none;display:flex;align-items:center;gap:8px">
        <img src="{{ asset('logo.png') }}" alt="Logo" style="height:28px;width:auto">
    </a>
    <div class="h-nav-right">
        <button class="h-hamburger" onclick="toggleMenu()" id="h-hamburger-btn">
            <i class="fas fa-bars"></i>
            <span class="h-username">{{ auth()->user()->name }}</span>
        </button>
    </div>

    {{-- Dropdown --}}
    <div class="h-dropdown-menu" id="h-dropdown">
        <a href="{{ route('handlowiec.dashboard') }}"
           class="h-menu-item {{ request()->routeIs('handlowiec.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Strona główna
        </a>
        <div class="h-menu-divider"></div>
        <a href="{{ route('handlowiec.nowe-zlecenie') }}"
           class="h-menu-item {{ request()->routeIs('handlowiec.nowe-zlecenie') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i> Nowe zlecenie
        </a>
        <a href="{{ route('handlowiec.zlecenia') }}"
           class="h-menu-item {{ request()->routeIs('handlowiec.zlecenia') ? 'active' : '' }}">
            <i class="fas fa-list-alt"></i> Moje zlecenia
        </a>
        <a href="{{ route('handlowiec.klienci') }}"
           class="h-menu-item {{ request()->routeIs('handlowiec.klienci*') ? 'active' : '' }}">
            <i class="fas fa-building"></i> Klienci
        </a>
        <div class="h-menu-divider"></div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="h-logout-btn">
                <i class="fas fa-sign-out-alt"></i> Wyloguj
            </button>
        </form>
    </div>
</nav>

<div id="h-main">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
function toggleMenu() {
    document.getElementById('h-dropdown').classList.toggle('open');
}
// Zamknij po kliknięciu poza menu
document.addEventListener('click', function(e) {
    const btn  = document.getElementById('h-hamburger-btn');
    const menu = document.getElementById('h-dropdown');
    if (!btn.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.remove('open');
    }
});
</script>

@yield('scripts')
</body>
</html>
