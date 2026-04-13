<div class="dropdown">
    <button class="btn dropdown-toggle d-flex align-items-center gap-2"
            type="button" data-bs-toggle="dropdown">
        <i class="fas fa-bars"></i>
        <span style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:15px;letter-spacing:.04em">
            {{ auth()->user()->name }}
        </span>
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item {{ request()->routeIs('handlowiec.dashboard') ? 'active' : '' }}"
               href="{{ route('handlowiec.dashboard') }}">
                <i class="fas fa-home"></i> Strona główna
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item {{ request()->routeIs('handlowiec.nowe-zlecenie') ? 'active' : '' }}"
               href="{{ route('handlowiec.nowe-zlecenie') }}">
                <i class="fas fa-plus-circle"></i> Nowe zlecenie
            </a>
        </li>
        <li>
            <a class="dropdown-item {{ request()->routeIs('handlowiec.zlecenia') ? 'active' : '' }}"
               href="{{ route('handlowiec.zlecenia') }}">
                <i class="fas fa-list-alt"></i> Moje zlecenia
            </a>
        </li>
        <li>
            <a class="dropdown-item {{ request()->routeIs('handlowiec.klienci*') ? 'active' : '' }}"
               href="{{ route('handlowiec.klienci') }}">
                <i class="fas fa-building"></i> Klienci
            </a>
        </li>
    </ul>
</div>