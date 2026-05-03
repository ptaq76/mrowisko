<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @include('layouts._meta')
    <title>Mrowisko</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800;900&family=Barlow+Condensed:wght@700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --yellow: #F5C842;
            --yellow-dark: #d4a800;
            --bg-page: #eef0f3;
            --bg-card: #ffffff;
            --text-primary: #111111;
            --text-muted: #888888;
            --border: #e2e5e9;
            --radius-card: 14px;
            --radius-btn: 12px;
        }

        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }

        body {
            font-family: 'Barlow', sans-serif;
            background: var(--bg-page);
            margin: 0; padding: 0;
            padding-bottom: 32px;
            min-height: 100vh;
        }

        /* ── NAV ── */
        .mobile-nav {
            position: sticky; top: 0; z-index: 200;
            background: #111;
            padding: 11px 16px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,.35);
        }
        .nav-brand {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 20px; font-weight: 900; letter-spacing: .14em;
            color: var(--yellow);
        }
        .nav-right { display: flex; align-items: center; gap: 10px; }
        .nav-user  { font-size: 13px; font-weight: 600; color: #ccc; }

        .hamburger-btn {
            background: none; border: 1px solid #3a3a3a; color: #aaa;
            padding: 6px 10px; border-radius: 7px; font-size: 14px;
            cursor: pointer; display: flex; align-items: center; gap: 6px;
        }
        .hamburger-btn:active { border-color: #666; color: #fff; }

        .nav-dropdown {
            position: absolute; top: calc(100% + 4px); right: 16px;
            background: #fff; border-radius: 16px;
            box-shadow: 0 10px 36px rgba(0,0,0,.22);
            min-width: 240px; overflow: hidden; z-index: 300;
            display: none; border: 1px solid var(--border);
        }
        .nav-dropdown.open { display: block; }

        .nav-menu-item {
            display: flex; align-items: center; gap: 13px;
            padding: 15px 18px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 18px; font-weight: 700; letter-spacing: .04em;
            color: var(--text-primary); text-decoration: none;
            border-bottom: 1px solid #f4f5f7;
            transition: background .1s;
        }
        .nav-menu-item:last-child { border-bottom: none; }
        .nav-menu-item:hover, .nav-menu-item:active { background: #f4f5f7; }
        .nav-menu-item i { width: 20px; text-align: center; color: #bbb; font-size: 15px; }
        .nav-menu-item.active { color: var(--yellow-dark); }
        .nav-menu-item.active i { color: var(--yellow); }
        .nav-menu-divider { height: 1px; background: var(--border); }

        /* Submenu */
        .nav-submenu-toggle { cursor: pointer; justify-content: space-between; }
        .nav-submenu-toggle .chev { font-size: 12px; color: #bbb; transition: transform .15s; margin-left:auto; }
        .nav-submenu-toggle.open .chev { transform: rotate(180deg); }
        .nav-submenu { display: none; background: #fafbfc; }
        .nav-submenu.open { display: block; }
        .nav-submenu .nav-menu-item {
            padding-left: 46px;
            font-size: 16px;
            background: #fafbfc;
        }
        .nav-submenu .nav-menu-item:hover { background: #f0f1f3; }

        .nav-logout-btn {
            display: flex; align-items: center; gap: 13px;
            padding: 15px 18px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 18px; font-weight: 700; letter-spacing: .04em;
            color: #e74c3c; background: none; border: none;
            width: 100%; cursor: pointer;
        }
        .nav-logout-btn:hover { background: #fdf2f2; }
        .nav-logout-btn i { width: 20px; text-align: center; }

        /* ── DATE BAR ── */
        .date-bar {
            background: #fff; padding: 10px 16px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 10px;
        }
        .date-bar input[type=date] {
            flex: 1; border: 1px solid #d5d8dc; border-radius: 8px;
            padding: 8px 12px; font-family: 'Barlow', sans-serif;
            font-size: 15px; font-weight: 600; color: var(--text-primary); outline: none;
        }
        .date-bar input[type=date]:focus { border-color: var(--yellow); }
        .date-bar .day-name {
            font-size: 15px; font-weight: 700; color: var(--yellow-dark); white-space: nowrap;
        }

        /* ── CONTENT ── */
        .main-content { padding: 12px; }

        /* ── SHARED BUTTONS ── */
        .btn-back {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            background: #111; color: #fff;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 19px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;
            width: 80%; margin: 0 auto 14px;
            padding: 15px; border-radius: var(--radius-btn);
            border: none; cursor: pointer; text-decoration: none;
        }
        .btn-back:hover, .btn-back:active { background: #2a2a2a; color: #fff; }

        .btn-yellow {
            width: 100%; padding: 20px;
            background: var(--yellow); color: var(--text-primary);
            border: none; border-radius: var(--radius-btn);
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 22px; font-weight: 900;
            letter-spacing: .06em; text-transform: uppercase;
            cursor: pointer; margin-bottom: 10px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            text-decoration: none;
        }
        .btn-yellow:hover { background: #eabd3b; color: var(--text-primary); }
        .btn-yellow:active { filter: brightness(.93); }

        .btn-red {
            width: 100%; padding: 20px;
            background: #e74c3c; color: #fff;
            border: none; border-radius: var(--radius-btn);
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 22px; font-weight: 900;
            letter-spacing: .06em; text-transform: uppercase;
            cursor: pointer; margin-bottom: 10px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-red:active { filter: brightness(.9); }

        .btn-gray {
            width: 100%; padding: 15px;
            background: #7f8c8d; color: #fff;
            border: none; border-radius: var(--radius-btn);
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 18px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;
            cursor: pointer;
        }
        .btn-gray:active { filter: brightness(.9); }

        /* ── SHARED CARDS ── */
        .card-white {
            background: var(--bg-card);
            border-radius: var(--radius-card);
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 10px;
        }

        .plate-badge {
            display: inline-block; border: 1.5px solid #111;
            padding: 1px 7px; border-radius: 4px;
            font-weight: 800; font-size: 12px; color: #111; letter-spacing: .04em;
        }
    </style>

    @yield('styles')
</head>
<body>

<nav class="mobile-nav" style="position:relative">
    <div class="nav-brand">PLAC</div>
    <div class="nav-right">
        <span class="nav-user">{{ auth()->user()->name }}</span>
        <button class="hamburger-btn" id="hamburgerBtn" onclick="toggleNavMenu()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="nav-dropdown" id="navDropdown">
        <a href="{{ route('plac.dashboard') }}"
           class="nav-menu-item {{ request()->routeIs('plac.dashboard','plac.index') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <div class="nav-menu-divider"></div>
        <a href="{{ route('plac.orders') }}"
           class="nav-menu-item {{ request()->routeIs('plac.orders*') ? 'active' : '' }}">
            <i class="fas fa-industry"></i> Plan placu
        </a>
        <a href="{{ route('plac.loading.index') }}"
           class="nav-menu-item {{ request()->routeIs('plac.loading*') ? 'active' : '' }}">
            <i class="fas fa-truck-loading"></i> Załadunki
        </a>
        <a href="{{ route('plac.delivery.index') }}"
           class="nav-menu-item {{ request()->routeIs('plac.delivery*') ? 'active' : '' }}">
            <i class="fas fa-boxes"></i> Dostawy
        </a>
        <a href="{{ route('plac.production.index') }}"
           class="nav-menu-item {{ request()->routeIs('plac.production*') ? 'active' : '' }}">
            <i class="fas fa-cogs"></i> Produkcja
        </a>
        <a href="{{ route('plac.warehouse.index') }}"
           class="nav-menu-item {{ request()->routeIs('plac.warehouse*') ? 'active' : '' }}">
            <i class="fas fa-warehouse"></i> Magazyn
        </a>
        <div class="nav-menu-divider"></div>
        <a href="{{ route('plac.fuel.index') }}"
           class="nav-menu-item {{ request()->routeIs('plac.fuel*') ? 'active' : '' }}">
            <i class="fas fa-gas-pump"></i> Paliwo
        </a>
        <a href="{{ route('plac.inventory.index') }}"
           class="nav-menu-item {{ request()->routeIs('plac.inventory*') ? 'active' : '' }}">
            <i class="fas fa-balance-scale"></i> Inwentaryzacja
        </a>

        @php $reportsOpen = request()->routeIs('plac.reports*'); @endphp
        <div class="nav-menu-item nav-submenu-toggle {{ $reportsOpen ? 'open active' : '' }}"
             onclick="toggleReportsSubmenu(event)">
            <i class="fas fa-chart-bar"></i>
            <span>Raporty</span>
            <i class="fas fa-chevron-down chev"></i>
        </div>
        <div class="nav-submenu {{ $reportsOpen ? 'open' : '' }}" id="reportsSubmenu">
            <a href="{{ route('plac.reports.deliveries') }}"
               class="nav-menu-item {{ request()->routeIs('plac.reports.deliveries') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> Dostawy
            </a>
            <a href="{{ route('plac.reports.loadings') }}"
               class="nav-menu-item {{ request()->routeIs('plac.reports.loadings') ? 'active' : '' }}">
                <i class="fas fa-truck-loading"></i> Załadunki
            </a>
        </div>

        <div class="nav-menu-divider"></div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="nav-logout-btn">
                <i class="fas fa-sign-out-alt"></i> Wyloguj
            </button>
        </form>
    </div>
</nav>

@if(trim($__env->yieldContent('hide_datebar')) !== '1')
<div class="date-bar">
    @php
        $dayNames = ['Niedziela','Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota'];
        $currentDate = isset($date) ? $date : \Carbon\Carbon::today();
        $currentDayName = $dayNames[$currentDate->dayOfWeek];
    @endphp
    <span class="day-name">{{ $currentDayName }}</span>
    <input type="date" id="datePicker" value="{{ $currentDate->format('Y-m-d') }}"
           onchange="window.location.href='{{ url()->current() }}?data='+this.value">
</div>
@endif

<div class="main-content">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
function toggleNavMenu() {
    document.getElementById('navDropdown').classList.toggle('open');
}
function toggleReportsSubmenu(e) {
    e.stopPropagation();
    e.currentTarget.classList.toggle('open');
    document.getElementById('reportsSubmenu').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const btn  = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('navDropdown');
    if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.remove('open');
    }
});
</script>

@include('partials._keypad')
@include('partials._session_guard')
@include('partials._polling')
@yield('scripts')
</body>
</html>