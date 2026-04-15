<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Plac') – MrowiskoBIS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800;900&family=Barlow+Condensed:wght@700;800;900&display=swap" rel="stylesheet">

    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }

        body {
            font-family: 'Barlow', sans-serif;
            background: #f0f2f5;
            margin: 0; padding: 0;
            padding-bottom: 24px;
            min-height: 100vh;
        }

        .mobile-nav {
            position: sticky; top: 0; z-index: 200;
            background: #1a1a1a;
            padding: 10px 16px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,.3);
        }
        .nav-brand {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 18px; font-weight: 900; letter-spacing: .1em;
            color: #f39c12;
        }
        .nav-right { display: flex; align-items: center; gap: 10px; }
        .nav-user  { font-size: 13px; font-weight: 700; color: #fff; }

        .hamburger-btn {
            background: none; border: 1px solid #444; color: #aaa;
            padding: 5px 10px; border-radius: 6px; font-size: 14px;
            cursor: pointer; display: flex; align-items: center; gap: 6px;
            transition: border-color .15s;
        }
        .hamburger-btn:hover { border-color: #777; color: #fff; }

        .nav-dropdown {
            position: absolute; top: calc(100% + 4px); right: 16px;
            background: #fff; border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0,0,0,.22);
            min-width: 230px; overflow: hidden; z-index: 300;
            display: none; border: 1px solid #e2e5e9;
        }
        .nav-dropdown.open { display: block; }

        .nav-menu-item {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 17px; font-weight: 700; letter-spacing: .04em;
            color: #1a1a1a; text-decoration: none;
            transition: background .1s;
            border-bottom: 1px solid #f4f5f7;
        }
        .nav-menu-item:last-child { border-bottom: none; }
        .nav-menu-item:hover { background: #f4f5f7; }
        .nav-menu-item i { width: 20px; text-align: center; color: #aaa; font-size: 14px; }
        .nav-menu-item.active { color: #f39c12; }
        .nav-menu-item.active i { color: #f39c12; }

        .nav-menu-divider { height: 1px; background: #e2e5e9; }

        .nav-logout-btn {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 17px; font-weight: 700; letter-spacing: .04em;
            color: #e74c3c; background: none; border: none;
            width: 100%; cursor: pointer; transition: background .1s;
        }
        .nav-logout-btn:hover { background: #fdf2f2; }
        .nav-logout-btn i { width: 20px; text-align: center; }

        .date-bar {
            background: #fff; padding: 10px 16px;
            border-bottom: 1px solid #e2e5e9;
            display: flex; align-items: center; gap: 10px;
        }
        .date-bar input[type=date] {
            flex: 1; border: 1px solid #d5d8dc; border-radius: 8px;
            padding: 8px 12px; font-family: 'Barlow', sans-serif;
            font-size: 15px; font-weight: 600; color: #1a1a1a; outline: none;
        }
        .date-bar .day-name {
            font-size: 14px; font-weight: 700; color: #f39c12; white-space: nowrap;
        }

        .main-content { padding: 12px; }
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
document.addEventListener('click', function(e) {
    const btn  = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('navDropdown');
    if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.remove('open');
    }
});
</script>

@yield('scripts')
</body>
</html>