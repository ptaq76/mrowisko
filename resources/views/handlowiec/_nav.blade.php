<a href="{{ route('handlowiec.dashboard') }}"
   class="btn {{ request()->routeIs('handlowiec.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home"></i>
</a>
<div class="dropdown">
    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="fas fa-bars"></i> Menu
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item {{ request()->routeIs('handlowiec.nowe-zlecenie') ? 'active' : '' }}"
               href="{{ route('handlowiec.nowe-zlecenie') }}">
            <i class="fas fa-plus-circle"></i> Nowe zlecenie
        </a></li>
        <li><a class="dropdown-item {{ request()->routeIs('handlowiec.zlecenia') ? 'active' : '' }}"
               href="{{ route('handlowiec.zlecenia') }}">
            <i class="fas fa-list-alt"></i> Moje zlecenia
        </a></li>
        <li><a class="dropdown-item {{ request()->routeIs('handlowiec.klienci*') ? 'active' : '' }}"
               href="{{ route('handlowiec.klienci') }}">
            <i class="fas fa-building"></i> Klienci
        </a></li>
    </ul>
</div>
