<style>
    .settings-sidebar {
        background: #fff;
        border-right: 1px solid var(--gray-2);
        min-height: calc(100vh - 58px);
        padding: 16px 0;
    }

    .settings-sidebar .sidebar-header {
        font-family: var(--font-display);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--gray-3);
        padding: 4px 20px 10px;
    }

    .settings-sidebar .sidebar-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 20px;
        font-size: 13px;
        font-weight: 500;
        color: var(--black);
        text-decoration: none;
        transition: background .15s;
        border-left: 3px solid transparent;
    }

    .settings-sidebar .sidebar-link i {
        width: 16px;
        text-align: center;
        font-size: 13px;
        color: var(--gray-3);
        flex-shrink: 0;
    }

    .settings-sidebar .sidebar-link:hover {
        background: var(--green-light);
        color: var(--black);
    }

    .settings-sidebar .sidebar-link:hover i {
        color: var(--green-dark);
    }

    .settings-sidebar .sidebar-link.active {
        background: var(--green-light);
        border-left-color: var(--green);
        color: var(--black);
        font-weight: 600;
    }

    .settings-sidebar .sidebar-link.active i {
        color: var(--green-dark);
    }

    /* ── Ujednolicony tytuł strony ustawień ─────────────────── */
    .page-title {
        font-family: 'Barlow Condensed', sans-serif !important;
        font-size: 22px !important;
        font-weight: 900 !important;
        letter-spacing: .06em !important;
        text-transform: uppercase !important;
        color: #1a1a1a !important;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<div class="settings-sidebar">
    <div class="sidebar-header">Ustawienia</div>

    <a href="{{ route('biuro.importers.index') }}"
       class="sidebar-link {{ request()->routeIs('biuro.importers.*') ? 'active' : '' }}">
        <i class="fa-solid fa-industry"></i> Importerzy
    </a>
    <a href="{{ route('biuro.waste-codes.index') }}"
       class="sidebar-link {{ request()->routeIs('biuro.waste-codes.*') ? 'active' : '' }}">
        <i class="fa-solid fa-recycle"></i> Kody odpadów
    </a>
    <a href="{{ route('biuro.koszty-transportu.index') }}"
       class="sidebar-link {{ request()->routeIs('biuro.koszty-transportu.*') ? 'active' : '' }}">
        <i class="fa-solid fa-route"></i> Koszty transportu
    </a>
    <a href="{{ route('biuro.vehicles.index') }}"
       class="sidebar-link {{ request()->routeIs('biuro.vehicles.*') ? 'active' : '' }}">
        <i class="fa-solid fa-truck-moving"></i> Pojazdy
    </a>
    <a href="{{ route('biuro.vehicle-sets.index') }}"
       class="sidebar-link {{ request()->routeIs('biuro.vehicle-sets.*') ? 'active' : '' }}">
        <i class="fa-solid fa-weight-hanging"></i> Tary zestawów
    </a>
    <a href="{{ route('biuro.pojazdy-terminy.index') }}"
   class="sidebar-link {{ request()->routeIs('biuro.pojazdy-terminy.*') ? 'active' : '' }}">
    <i class="fa-solid fa-calendar-check"></i> Pojazdy – Terminy
    </a>
    <a href="{{ route('biuro.fuel-vehicles.index') }}"
       class="sidebar-link {{ request()->routeIs('biuro.fuel-vehicles.*') ? 'active' : '' }}">
        <i class="fa-solid fa-gas-pump"></i> Pojazdy – Paliwo
    </a>
    <a href="{{ route('biuro.fractions.index') }}"
       class="sidebar-link {{ request()->routeIs('biuro.fractions.*') ? 'active' : '' }}">
        <i class="fa-solid fa-boxes"></i> Towary
    </a>
    <a href="{{ route('biuro.haulers.index') }}"
       class="sidebar-link {{ request()->routeIs('biuro.haulers.*') ? 'active' : '' }}">
        <i class="fa-solid fa-truck"></i> Woźacy
    </a>
</div>