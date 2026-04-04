<a href="{{ route('admin.dashboard') }}" class="btn {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="fa-solid fa-gauge"></i> Dashboard
</a>

<div class="dropdown">
    <button class="btn dropdown-toggle {{ request()->routeIs('biuro.*') ? 'active' : '' }}" type="button" data-bs-toggle="dropdown">
        <i class="fa-solid fa-building"></i> Biuro
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ route('biuro.planning.index') }}">
            <i class="fa-solid fa-calendar-alt"></i> Planowanie
        </a></li>
        <li><a class="dropdown-item" href="{{ route('biuro.weighings.index') }}">
            <i class="fa-solid fa-weight"></i> Ważenia
        </a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="{{ route('biuro.reports.loadings') }}">
            <i class="fa-solid fa-truck-loading"></i> Raport załadunki
        </a></li>
        <li><a class="dropdown-item" href="{{ route('biuro.reports.deliveries') }}">
            <i class="fa-solid fa-boxes"></i> Raport dostawy
        </a></li>
        <li><a class="dropdown-item" href="{{ route('biuro.reports.weighings') }}">
            <i class="fa-solid fa-weight"></i> Raport ważenia
        </a></li>
    </ul>
</div>

<div class="dropdown">
    <button class="btn dropdown-toggle {{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}" type="button" data-bs-toggle="dropdown">
        <i class="fa-solid fa-truck-moving"></i> Kierowcy
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ route('admin.drivers.index') }}">
            <i class="fa-solid fa-list"></i> Wszystkie zlecenia
        </a></li>
    </ul>
</div>

<div class="dropdown">
    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="fa-solid fa-warehouse"></i> Plac
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ route('plac.orders') }}">
            <i class="fa-solid fa-calendar-day"></i> Plan dnia
        </a></li>
        <li><a class="dropdown-item" href="{{ route('plac.loading.index') }}">
            <i class="fa-solid fa-truck-loading"></i> Załadunki
        </a></li>
        <li><a class="dropdown-item" href="{{ route('plac.delivery.index') }}">
            <i class="fa-solid fa-boxes"></i> Dostawy
        </a></li>
        <li><a class="dropdown-item" href="{{ route('plac.warehouse.index') }}">
            <i class="fa-solid fa-warehouse"></i> Magazyn
        </a></li>
    </ul>
</div>

<div class="dropdown">
    <button class="btn dropdown-toggle {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" type="button" data-bs-toggle="dropdown">
        <i class="fa-solid fa-users-cog"></i> Użytkownicy
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

<div class="dropdown">
    <button class="btn dropdown-toggle {{ request()->routeIs('admin.annex7-contractors.*') || request()->routeIs('admin.annex7-recovery-operations.*') || request()->routeIs('admin.annex7-waste-descriptions.*') ? 'active' : '' }}"
            type="button" data-bs-toggle="dropdown">
        <i class="fa-solid fa-truck-ramp-box"></i> Annex 7
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item {{ request()->routeIs('admin.annex7-contractors.*') ? 'active' : '' }}"
               href="{{ route('admin.annex7-contractors.index') }}">
            <i class="fa-solid fa-building"></i> Kontrahenci
        </a></li>
        <li><a class="dropdown-item {{ request()->routeIs('admin.annex7-recovery-operations.*') ? 'active' : '' }}"
               href="{{ route('admin.annex7-recovery-operations.index') }}">
            <i class="fa-solid fa-recycle"></i> Operacje odzysku
        </a></li>
        <li><a class="dropdown-item {{ request()->routeIs('admin.annex7-waste-descriptions.*') ? 'active' : '' }}"
               href="{{ route('admin.annex7-waste-descriptions.index') }}">
            <i class="fa-solid fa-list"></i> Opisy odpadów
        </a></li>
    </ul>
</div>

<a href="{{ route('admin.agent') }}" class="btn {{ request()->routeIs('admin.agent') ? 'active' : '' }}" style="background:#6c3483;color:#fff;border-color:#6c3483">
    <i class="fa-solid fa-robot"></i> Agent AI
</a>
