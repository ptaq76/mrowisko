<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Zlecenia') – MrowiskoBIS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800;900&family=Barlow+Condensed:wght@700;800;900&display=swap" rel="stylesheet">

    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }

        body {
            font-family: 'Barlow', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
            padding-bottom: 20px;
            min-height: 100vh;
        }

        /* Navbar mobilny */
        .mobile-nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #1a1a1a;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,.3);
        }

        .mobile-nav .app-name {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 18px;
            font-weight: 900;
            letter-spacing: .1em;
            color: #6EBF58;
        }

        .mobile-nav .driver-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mobile-nav .driver-name {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }

        .mobile-nav .logout-btn {
            background: none;
            border: 1px solid #444;
            color: #aaa;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
        }

        /* Pasek daty */
        .date-bar {
            background: #fff;
            padding: 10px 16px;
            border-bottom: 1px solid #e2e5e9;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-bar input[type=date] {
            flex: 1;
            border: 1px solid #d5d8dc;
            border-radius: 8px;
            padding: 8px 12px;
            font-family: 'Barlow', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: #1a1a1a;
            outline: none;
        }

        .date-bar .day-name {
            font-size: 14px;
            font-weight: 700;
            color: #6EBF58;
            white-space: nowrap;
        }

        /* Główna treść */
        .main-content {
            padding: 12px;
        }

        @yield('styles')
    </style>
</head>
<body>

<nav class="mobile-nav">
    <div class="app-name">MROWISKO</div>
    <div class="driver-info">
        @if(isset($driver))
            <div class="driver-name">{{ $driver->name }}</div>
        @endif
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </form>
    </div>
</nav>

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

<div class="main-content">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@yield('scripts')
</body>
</html>
