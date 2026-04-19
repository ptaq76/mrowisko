<a href="{{ route('biuro.planning.index') }}" class="btn {{ request()->routeIs('biuro.planning.*') ? 'active' : '' }}">
    <i class="fa-solid fa-calendar-alt"></i> Planowanie
</a>
<a href="{{ route('biuro.weighings.index') }}"  class="btn {{ request()->routeIs('biuro.weighings.*') ? 'active' : '' }}">
    <i class="fa-solid fa-weight"></i> Ważenia
</a>
<a href="{{ route('biuro.ls.index') }}"  class="btn {{ request()->routeIs('biuro.ls.*') ? 'active' : '' }}">
    <i class="fa-solid fa-file-alt"></i> Lieferscheiny
</a>
<div class="dropdown">
    <button class="btn dropdown-toggle {{ request()->routeIs('biuro.annex7.*') || request()->routeIs('biuro.reklamacje.*') ? 'active' : '' }}"
            type="button" data-bs-toggle="dropdown">
        <i class="fa-solid fa-file-contract"></i> Dokumenty
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item {{ request()->routeIs('biuro.annex7.*') ? 'active' : '' }}"
               href="{{ route('biuro.annex7.index') }}">
            <i class="fa-solid fa-file-signature"></i> Annex 7
        </a></li>
        <li><a class="dropdown-item {{ request()->input('typ')==='reklamacja' && request()->routeIs('biuro.reklamacje.*') ? 'active' : '' }}"
               href="{{ route('biuro.reklamacje.index', ['typ' => 'reklamacja']) }}">
            <i class="fa-solid fa-file-circle-exclamation"></i> Reklamacje
        </a></li>
        <li><a class="dropdown-item {{ request()->input('typ')==='gewichtsmeldung' && request()->routeIs('biuro.reklamacje.*') ? 'active' : '' }}"
               href="{{ route('biuro.reklamacje.index', ['typ' => 'gewichtsmeldung']) }}">
            <i class="fa-solid fa-file-circle-check"></i> Gewichtsmeldung
        </a></li>
    </ul>
</div>


<div class="dropdown">
    <button class="btn dropdown-toggle {{ request()->routeIs('biuro.bdo.*') ? 'active' : '' }}"
            type="button" data-bs-toggle="dropdown">
        <i class="fa-solid fa-recycle"></i> BDO
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item {{ request()->routeIs('biuro.bdo.karty') ? 'active' : '' }}"
               href="{{ route('biuro.bdo.karty') }}">
            <i class="fa-solid fa-arrow-left"></i> Przejmujący
        </a></li>
        <li><a class="dropdown-item {{ request()->routeIs('biuro.bdo.kartyPrzekazujacy') ? 'active' : '' }}"
               href="{{ route('biuro.bdo.kartyPrzekazujacy') }}">
            <i class="fa-solid fa-arrow-right"></i> Przekazujący
        </a></li>
        <li><hr class="dropdown-divider"></li>
        <li><h6 class="dropdown-header">Synchronizacja</h6></li>
        <li><a class="dropdown-item" href="#" onclick="bdoSync('przejmujacy'); return false;">
            <i class="fa-solid fa-sync"></i> Pobierz Przejmujący
        </a></li>
        <li><a class="dropdown-item" href="#" onclick="bdoSync('przekazujacy'); return false;">
            <i class="fa-solid fa-sync"></i> Pobierz Przekazujący
        </a></li>
    </ul>
</div>



<a href="{{ route('biuro.clients.index') }}" class="btn {{ request()->routeIs('biuro.clients.*') ? 'active' : '' }}">
    <i class="fa-solid fa-building"></i> Kontrahenci
</a>

<div class="dropdown">
    <button class="btn dropdown-toggle {{ request()->routeIs('biuro.reports.*') || request()->routeIs('biuro.raporty.*') || request()->routeIs('biuro.plan-na-plac') ? 'active' : '' }}" type="button" data-bs-toggle="dropdown">
        <i class="fa-solid fa-chart-bar"></i> Raporty
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item {{ request()->routeIs('biuro.plan-na-plac') ? 'active' : '' }}"
               href="{{ route('biuro.plan-na-plac') }}">
            <i class="fa-solid fa-industry"></i> Plan na plac
        </a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item {{ request()->routeIs('biuro.raporty.wysylki') ? 'active' : '' }}"
               href="{{ route('biuro.raporty.wysylki') }}">
            <i class="fa-solid fa-ship"></i> Wysyłki zagraniczne
        </a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a href="{{ route('biuro.reports.warehouse') }}" class="dropdown-item {{ request()->routeIs('biuro.reports.warehouse*') ? 'active' : '' }}">
            <i class="fa-solid fa-warehouse"></i> Magazyn
        </a></li>
        <li><a class="dropdown-item {{ request()->routeIs('biuro.reports.loadings') ? 'active' : '' }}"
               href="{{ route('biuro.reports.loadings') }}">
            <i class="fa-solid fa-truck-loading"></i> Załadunki
        </a></li>
        <li><a class="dropdown-item {{ request()->routeIs('biuro.reports.deliveries') ? 'active' : '' }}"
               href="{{ route('biuro.reports.deliveries') }}">
            <i class="fa-solid fa-boxes"></i> Dostawy
        </a></li>
        <li><a class="dropdown-item {{ request()->routeIs('biuro.reports.weighings') ? 'active' : '' }}"
               href="{{ route('biuro.reports.weighings') }}">
            <i class="fa-solid fa-weight"></i> Ważenia kierowców
        </a></li>
        <li><a class="dropdown-item {{ request()->routeIs('biuro.reports.pickup-requests') ? 'active' : '' }}"
               href="{{ route('biuro.reports.pickup-requests') }}">
            <i class="fa-solid fa-handshake"></i> Zlecenia handlowców
        </a></li>
        <li><a class="dropdown-item {{ request()->routeIs('biuro.reports.planning') ? 'active' : '' }}"
               href="{{ route('biuro.reports.planning') }}">
            <i class="fa-solid fa-calendar-check"></i> Raport planowania
        </a></li>
        <li><a class="dropdown-item {{ request()->routeIs('biuro.reports.foreign-shipments') ? 'active' : '' }}"
               href="{{ route('biuro.reports.foreign-shipments') }}">
            <i class="fa-solid fa-globe"></i> Wysyłki – wagi
        </a></li>
    </ul>
</div>
<a href="{{ route('biuro.ustawienia') }}"
   class="btn {{ request()->routeIs('biuro.fractions.*', 'biuro.haulers.*', 'biuro.vehicles.*', 'biuro.importers.*', 'biuro.waste-codes.*', 'biuro.fuel-vehicles.*', 'biuro.koszty-transportu.*') ? 'active' : '' }}">
    <i class="fa-solid fa-cog"></i> Ustawienia
</a>