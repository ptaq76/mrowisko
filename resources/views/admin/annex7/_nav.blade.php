{{-- resources/views/admin/annex7/_nav.blade.php --}}
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
