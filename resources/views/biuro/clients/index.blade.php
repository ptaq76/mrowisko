@extends('layouts.app')

@section('title', 'Kontrahenci')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
</style>
@endsection

@section('content')

<div class="page-header">
    <div class="page-title"><i class="fa-solid fa-building"></i> Kontrahenci</div>
    <a href="{{ route('biuro.clients.create') }}" class="btn btn-add">
        <i class="fa-solid fa-plus"></i> Nowy kontrahent
    </a>
</div>

{{-- Filtry --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">

            {{-- Autocomplete: nazwa / NIP --}}
            <div class="col-md-4 position-relative">
                <input type="text" id="filterSearch" class="form-control form-control-sm"
                       placeholder="Szukaj po nazwie, skrócie, NIP...">
                <div id="autocompleteDropdown"
                     class="position-absolute bg-white border rounded shadow-sm w-100"
                     style="display:none;z-index:1000;top:100%;left:0;max-height:280px;overflow-y:auto">
                </div>
            </div>

            {{-- Typ --}}
            <div class="col-md-2">
                <select id="filterType" class="form-select form-select-sm">
                    <option value="">Wszystkie typy</option>
                    <option value="pickup">Dostawca</option>
                    <option value="sale">Odbiorca</option>
                    <option value="both">Dostawca i odbiorca</option>
                </select>
            </div>

            {{-- Handlowiec --}}
            <div class="col-md-3">
                <select id="filterSalesman" class="form-select form-select-sm">
                    <option value="">Wszyscy handlowcy</option>
                    @foreach(\App\Models\User::where('module','handlowiec')->orderBy('name')->get() as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div class="col-md-2">
                <select id="filterActive" class="form-select form-select-sm">
                    <option value="1">Aktywni</option>
                    <option value="0">Nieaktywni</option>
                    <option value="">Wszyscy</option>
                </select>
            </div>

            {{-- Reset --}}
            <div class="col-md-1">
                <button id="resetFilters" class="btn btn-secondary btn-sm w-100" title="Wyczyść filtry">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Tabela --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="clientsTable">
            <thead>
                <tr>
                    <th>Skrót</th>
                    <th>NIP</th>
                    <th>Miasto</th>
                    <th>Adres</th>
                    <th>Typ</th>
                    <th>Handlowiec</th>
                    <th style="width:100px">Akcje</th>
                </tr>
            </thead>
            <tbody id="clientsBody">
                <tr><td colspan="7" class="text-center text-muted py-4">
                    <div class="spinner-border spinner-border-sm text-secondary me-2"></div> Ładowanie...
                </td></tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span class="text-muted small" id="clientsCount"></span>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Wszystkie dane załadowane raz
let allClients = [];

const TYPE_LABELS = {
    pickup: '<span class="badge bg-primary" style="min-width:140px;display:inline-block">Dostawca</span>',
    sale:   '<span class="badge bg-success" style="min-width:140px;display:inline-block">Odbiorca</span>',
    both:   '<span class="badge bg-warning text-dark" style="min-width:140px;display:inline-block">Dostawca i odbiorca</span>',
};

// Załaduj dane jednorazowo
async function loadClients() {
    const res  = await fetch('{{ route("biuro.clients.data") }}');
    allClients = await res.json();
    renderTable();
}

function renderTable() {
    const search   = document.getElementById('filterSearch').value.toLowerCase().trim();
    const type     = document.getElementById('filterType').value;
    const salesman = document.getElementById('filterSalesman').value;
    const active   = document.getElementById('filterActive').value;

    let filtered = allClients.filter(c => {
        if (search && !c.short_name.toLowerCase().includes(search)
                   && !c.name.toLowerCase().includes(search)
                   && !(c.nip || '').toLowerCase().includes(search)) return false;
        if (type     && c.type !== type) return false;
        if (salesman && String(c.salesman_id) !== salesman) return false;
        if (active !== '' && String(c.is_active ? '1' : '0') !== active) return false;
        return true;
    });

    const tbody = document.getElementById('clientsBody');
    document.getElementById('clientsCount').textContent = filtered.length + ' kontrahentów';

    if (!filtered.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Brak wyników.</td></tr>';
        return;
    }

    tbody.innerHTML = filtered.map(c => `
        <tr>
            <td><strong>${escHtml(c.short_name)}</strong></td>
            <td style="font-size:13px">${escHtml(c.nip || '')}</td>
            <td>${escHtml(c.city || '')}</td>
            <td><span style="font-size:13px;color:#9aa3ad">${escHtml(c.street || '')}</span></td>
            <td>${TYPE_LABELS[c.type] || c.type}</td>
            <td style="font-size:13px;color:#9aa3ad">${escHtml(c.salesman_name || '–')}</td>
            <td>
                <div class="d-flex gap-1">
                    <a href="/biuro/clients/${c.id}" class="btn btn-secondary btn-sm" title="Podgląd">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <a href="/biuro/clients/${c.id}/edit" class="btn btn-edit btn-sm" title="Edytuj">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                </div>
            </td>
        </tr>
    `).join('');
}

function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}

// Autocomplete
const searchInput = document.getElementById('filterSearch');
const acDropdown  = document.getElementById('autocompleteDropdown');

searchInput.addEventListener('input', function() {
    renderTable();
    const q = this.value.toLowerCase().trim();
    if (q.length < 2) { acDropdown.style.display = 'none'; return; }

    const matches = allClients
        .filter(c => c.short_name.toLowerCase().includes(q)
                  || c.name.toLowerCase().includes(q)
                  || (c.nip || '').toLowerCase().includes(q))
        .slice(0, 8);

    if (!matches.length) { acDropdown.style.display = 'none'; return; }

    acDropdown.innerHTML = matches.map(c => `
        <div class="px-3 py-2 border-bottom ac-item" style="cursor:pointer;font-size:13px"
             data-value="${escHtml(c.short_name)}">
            <strong>${escHtml(c.short_name)}</strong>
            <span style="color:#9aa3ad;margin-left:8px">${escHtml(c.city || '')}</span>
        </div>
    `).join('');
    acDropdown.style.display = 'block';

    acDropdown.querySelectorAll('.ac-item').forEach(item => {
        item.addEventListener('mousedown', function(e) {
            e.preventDefault();
            searchInput.value = this.dataset.value;
            acDropdown.style.display = 'none';
            renderTable();
        });
        item.addEventListener('mouseover', function() {
            this.style.background = '#e8f7e4';
        });
        item.addEventListener('mouseout', function() {
            this.style.background = '';
        });
    });
});

searchInput.addEventListener('blur', () => {
    setTimeout(() => acDropdown.style.display = 'none', 150);
});

// Filtry dynamiczne
['filterType','filterSalesman','filterActive'].forEach(id => {
    document.getElementById(id).addEventListener('change', renderTable);
});

// Reset
document.getElementById('resetFilters').addEventListener('click', () => {
    document.getElementById('filterSearch').value   = '';
    document.getElementById('filterType').value     = '';
    document.getElementById('filterSalesman').value = '';
    document.getElementById('filterActive').value   = '1';
    acDropdown.style.display = 'none';
    renderTable();
});

loadClients();
</script>
@endsection