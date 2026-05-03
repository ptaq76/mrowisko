{{-- Wspólny zestaw <meta>/<link> do wszystkich layoutów. --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#1a1a1a">
<meta name="robots" content="noindex, nofollow">
<meta name="description" content="Mrowisko — system zarządzania pracą firmy">

<link rel="icon" type="image/png" href="{{ asset('ant_min.png') }}">
<link rel="apple-touch-icon" href="{{ asset('ant_min.png') }}">
<link rel="manifest" href="{{ asset('manifest.json') }}">

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Mrowisko">
