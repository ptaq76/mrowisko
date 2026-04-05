@extends('layouts.ustawienia')

@section('title', 'Towary')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.fractions-wrap { padding: 20px; }

.page-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 16px;
}
.page-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase; color: #1a1a1a;
}

.table-wrap {
    background: #fff; border-radius: 10px;
    box-shadow: 0 1px 4px rgba(0,0,0,.08);
    overflow: hidden;
}

/* Sticky header */
.fractions-table { width: 100%; border-collapse: collapse; font-size: 13px; }

.fractions-table thead tr {
    background: #1a1a1a; color: #fff;
    position: sticky; top: 0; z-index: 10;
}
.fractions-table th {
    padding: 11px 14px;
    font-size: 11px; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    text-align: center; white-space: nowrap;
}
.fractions-table th.col-name { text-align: left; }
.fractions-table th.col-group { text-align: left; }

.fractions-table td {
    padding: 9px 14px;
    border-bottom: 1px solid #f0f2f5;
    text-align: center; vertical-align: middle;
}
.fractions-table tr:last-child td { border-bottom: none; }
.fractions-table tr:hover td { background: #f8f9fa; }

.fractions-table td.col-num   { width: 40px; color: #aaa; font-size: 12px; text-align:left; }
.fractions-table td.col-name  { text-align: left; font-weight: 700; color: #1a1a1a; min-width: 180px; font-size: 13px; font-family: var(--font-body); }
.fractions-table td.col-group { text-align: left; color: #888; font-size: 12px; min-width: 100px; }
.fractions-table th.col-num   { text-align: left; }
.fractions-table th.col-name  { text-align: left; }

/* Toggle przycisk */
.toggle-btn {
    border: none; background: none; cursor: pointer;
    padding: 4px; border-radius: 4px;
    font-size: 20px; line-height: 1;
    transition: transform .1s;
}
.toggle-btn:active { transform: scale(.88); }
.toggle-btn .icon-on  { color: #6EBF58; }
.toggle-btn .icon-off { color: #e74c3c; }

/* Filtr */
.search-wrap {
    display: flex; gap: 10px; margin-bottom: 14px; align-items: center;
}
.search-input {
    padding: 8px 12px; border: 1px solid #dde0e5;
    border-radius: 8px; font-size: 13px; outline: none;
    min-width: 250px;
}
.search-input:focus { border-color: #6EBF58; }

.badge-total {
    background: #f4f5f7; color: #555; font-size: 12px;
    font-weight: 700; padding: 4px 10px; border-radius: 10px;
}

.btn-add-fraction {
    padding: 9px 18px; background: #6EBF58; color: #fff;
    border: none; border-radius: 8px; font-size: 13px;
    font-weight: 700; cursor: pointer; display: flex;
    align-items: center; gap: 6px;
}
.btn-add-fraction:hover { background: #5aab48; }

/* Modal */
.modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.5); z-index: 1000;
    align-items: center; justify-content: center;
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: #fff; border-radius: 12px;
    width: 100%; max-width: 520px; padding: 28px;
    box-shadow: 0 8px 32px rgba(0,0,0,.2);
    animation: fadeIn .2s ease;
}
@keyframes fadeIn { from { opacity: 0; transform: scale(.96); } to { opacity: 1; transform: scale(1); } }

.modal-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 20px; font-weight: 900; color: #1a1a1a;
    margin-bottom: 20px;
    display: flex; justify-content: space-between; align-items: center;
}
.modal-close {
    background: #f0f2f5; border: none; border-radius: 50%;
    width: 38px; height: 38px; cursor: pointer; font-size: 22px;
    display: flex; align-items: center; justify-content: center;
}

.m-label { display: block; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #888; margin-bottom: 6px; }
.m-input, .m-select {
    width: 100%; padding: 10px 12px; border: 1.5px solid #dde0e5;
    border-radius: 8px; font-size: 14px; color: #1a1a1a; outline: none;
    margin-bottom: 14px;
}
.m-input:focus, .m-select:focus { border-color: #6EBF58; }

.visibility-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 8px; margin-bottom: 14px;
}
.vis-item {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 10px; border: 1.5px solid #e2e5e9;
    border-radius: 8px; cursor: pointer; font-size: 13px;
    font-weight: 600; color: #555; transition: all .15s;
}
.vis-item input[type=checkbox] { display: none; }
.vis-item.checked { border-color: #6EBF58; background: #e8f7e4; color: #2d7a1a; }
.vis-item .vi-icon { font-size: 16px; }

.belka-toggle {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border: 1.5px solid #e2e5e9;
    border-radius: 8px; margin-bottom: 16px; cursor: pointer;
    font-size: 13px; font-weight: 600; color: #555;
    transition: all .15s;
}
.belka-toggle.checked { border-color: #f39c12; background: #fef9e7; color: #d68910; }
.belka-toggle input { display: none; }

.btn-modal-save {
    width: 100%; padding: 13px; background: #6EBF58; color: #fff;
    border: none; border-radius: 8px; font-size: 15px; font-weight: 700;
    cursor: pointer;
}
.btn-modal-save:hover { background: #5aab48; }

.btn-edit-fraction {
    background: none; border: 1px solid #dde0e5; border-radius: 6px;
    padding: 5px 8px; color: #888; cursor: pointer; font-size: 12px;
    transition: all .15s;
}
.btn-edit-fraction:hover { background: #1a1a1a; color: #fff; border-color: #1a1a1a; }
</style>
@endsection

@section('settings_content')
<div class="fractions-wrap" style="max-width:75%">

    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-boxes"></i>
            Towary
            <span style="font-size:14px;color:#aaa;font-weight:400;margin-left:8px">{{ $fractions->count() }} pozycji</span>
        </div>
        <button class="btn-add-fraction" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Dodaj towar
        </button>
    </div>

    <div class="search-wrap">
        <input type="text" id="searchInput" class="search-input"
               placeholder="Szukaj towaru..." oninput="filterTable()">
    </div>

    <div class="table-wrap">
        <table class="fractions-table" id="fracTable">
            <thead>
                <tr>
                    <th class="col-num">#</th>
                    <th class="col-name">Nazwa towaru</th>
                    <th class="col-group">Grupa</th>
                    <th title="Widoczny u handlowców">Handlowcy</th>
                    <th title="Widoczny przy dostawach">Dostawy</th>
                    <th title="Widoczny przy załadunkach">Załadunki</th>
                    <th title="Widoczny w produkcji">Produkcja</th>
                    <th title="Aktywny">Aktywny</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($fractions as $i => $f)
                <tr id="fr-{{ $f->id }}">
                    <td class="col-num">{{ $i + 1 }}</td>
                    <td class="col-name">{{ $f->name }}</td>
                    <td class="col-group">{{ $f->group?->name ?? 'Brak' }}</td>

                    @foreach([
                        'show_in_sales'      => 'Handlowcy',
                        'show_in_deliveries' => 'Dostawy',
                        'show_in_loadings'   => 'Załadunki',
                        'show_in_production' => 'Produkcja',
                        'is_active'          => 'Aktywny',
                    ] as $field => $label)
                    <td>
                        <button class="toggle-btn"
                                id="btn-{{ $f->id }}-{{ $field }}"
                                onclick="toggle({{ $f->id }}, '{{ $field }}')"
                                title="{{ $label }}">
                            @if($f->$field)
                                <span class="icon-on">✔</span>
                            @else
                                <span class="icon-off">✖</span>
                            @endif
                        </button>
                    </td>
                    @endforeach
                    <td>
                        <button class="btn-edit-fraction"
                                onclick="openEditModal({{ $f->id }}, '{{ addslashes($f->name) }}', {{ $f->group_id ?? 'null' }})"
                                title="Edytuj">
                            <i class="fas fa-pen"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

{{-- Modal dodawania --}}
<div class="modal-overlay" id="addModal">
    <div class="modal-box">
        <div class="modal-title">
            <span>Nowy towar</span>
            <button class="modal-close" onclick="closeAddModal()">×</button>
        </div>

        <label class="m-label">Nazwa towaru *</label>
        <input type="text" id="mName" class="m-input" placeholder="np. Karton Czysty">

        <label class="m-label">Grupa</label>
        <select id="mGroup" class="m-select">
            <option value="">– brak –</option>
            @foreach($groups as $g)
                <option value="{{ $g->id }}">{{ $g->name }}</option>
            @endforeach
        </select>

        <label class="m-label">Widoczność</label>
        <div class="visibility-grid">
            <div class="vis-item checked" id="vi-sales" onclick="toggleVis('sales')">
                <input type="checkbox" id="cb-sales" checked>
                <span class="vi-icon">🤝</span> Handlowcy
            </div>
            <div class="vis-item checked" id="vi-deliveries" onclick="toggleVis('deliveries')">
                <input type="checkbox" id="cb-deliveries" checked>
                <span class="vi-icon">🚚</span> Dostawy
            </div>
            <div class="vis-item checked" id="vi-loadings" onclick="toggleVis('loadings')">
                <input type="checkbox" id="cb-loadings" checked>
                <span class="vi-icon">📦</span> Załadunki
            </div>
            <div class="vis-item checked" id="vi-production" onclick="toggleVis('production')">
                <input type="checkbox" id="cb-production" checked>
                <span class="vi-icon">⚙️</span> Produkcja
            </div>
        </div>

        <div class="belka-toggle" id="belkaToggle" onclick="toggleBelka()">
            <input type="checkbox" id="cb-belka">
            <span style="font-size:18px">🔠</span>
            <div>
                <div>Dodaj wariant <strong>BELKA</strong></div>
                <div style="font-size:11px;color:#aaa;margin-top:1px">Utworzy też: <span id="belkaPreview">NAZWA BELKA</span></div>
            </div>
        </div>

        <button class="btn-modal-save" onclick="saveNewFraction()">
            <i class="fas fa-check"></i> Zapisz
        </button>
    </div>
</div>

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function toggle(fractionId, field) {
    const btn = document.getElementById(`btn-${fractionId}-${field}`);
    btn.style.opacity = '.4';

    const res  = await fetch(`/biuro/fractions/${fractionId}/toggle`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ field }),
    });
    const data = await res.json();

    btn.style.opacity = '1';

    if (data.success) {
        btn.innerHTML = data.value
            ? '<span class="icon-on">✔</span>'
            : '<span class="icon-off">✖</span>';
    }
}

function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#fracTable tbody tr').forEach(row => {
        const name = row.querySelector('.col-name').textContent.toLowerCase();
        row.style.display = name.includes(q) ? '' : 'none';
    });
}

function openAddModal() {
    document.getElementById('mName').value = '';
    document.getElementById('mGroup').value = '';
    document.getElementById('belkaPreview').textContent = 'NAZWA BELKA';
    document.getElementById('cb-belka').checked = false;
    document.getElementById('belkaToggle').classList.remove('checked');
    document.getElementById('addModal').classList.add('open');
    setTimeout(() => document.getElementById('mName').focus(), 200);
}

function closeAddModal() {
    document.getElementById('addModal').classList.remove('open');
}

document.getElementById('addModal').addEventListener('click', e => {
    if (e.target === document.getElementById('addModal')) closeAddModal();
});

document.getElementById('mName').addEventListener('input', function() {
    const v = this.value.trim();
    document.getElementById('belkaPreview').textContent = v ? v + ' BELKA' : 'NAZWA BELKA';
});

function toggleVis(key) {
    const el = document.getElementById('vi-' + key);
    const cb = document.getElementById('cb-' + key);
    cb.checked = !cb.checked;
    el.classList.toggle('checked', cb.checked);
}

function toggleBelka() {
    const cb = document.getElementById('cb-belka');
    cb.checked = !cb.checked;
    document.getElementById('belkaToggle').classList.toggle('checked', cb.checked);
}

async function saveNewFraction() {
    const name = document.getElementById('mName').value.trim();
    if (!name) {
        Swal.fire({ icon: 'warning', title: 'Podaj nazwę towaru', timer: 1800, showConfirmButton: false });
        return;
    }

    const payload = {
        name,
        group_id:           document.getElementById('mGroup').value || null,
        show_in_sales:      document.getElementById('cb-sales').checked      ? 1 : 0,
        show_in_deliveries: document.getElementById('cb-deliveries').checked ? 1 : 0,
        show_in_loadings:   document.getElementById('cb-loadings').checked   ? 1 : 0,
        show_in_production: document.getElementById('cb-production').checked ? 1 : 0,
        add_belka:          document.getElementById('cb-belka').checked      ? 1 : 0,
    };

    const res  = await fetch('{{ route('biuro.fractions.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
        closeAddModal();
        const count = data.belka ? 2 : 1;
        await Swal.fire({
            icon: 'success',
            title: 'Dodano!',
            html: `<strong>${data.fraction.name}</strong>${data.belka ? '<br><strong>' + data.belka.name + '</strong>' : ''}`,
            timer: 2000, showConfirmButton: false,
        });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}

function openEditModal(id, name, groupId) {
    document.getElementById('editId').value   = id;
    document.getElementById('editName').value = name;
    document.getElementById('editGroup').value = groupId ?? '';
    document.getElementById('editModal').classList.add('open');
    setTimeout(() => document.getElementById('editName').focus(), 200);
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}

document.getElementById('editModal')?.addEventListener('click', e => {
    if (e.target === document.getElementById('editModal')) closeEditModal();
});

async function saveEditFraction() {
    const id   = document.getElementById('editId').value;
    const name = document.getElementById('editName').value.trim();
    if (!name) {
        Swal.fire({ icon: 'warning', title: 'Podaj nazwę towaru', timer: 1800, showConfirmButton: false });
        return;
    }

    const payload = {
        name,
        group_id: document.getElementById('editGroup').value || null,
        _method: 'PUT',
    };

    const res  = await fetch(`/biuro/fractions/${id}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
        closeEditModal();
        await Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1500, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}
</script>
@endsection

{{-- Modal edycji --}}
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <div class="modal-title">
            <span>Edycja towaru</span>
            <button class="modal-close" onclick="closeEditModal()">×</button>
        </div>
        <input type="hidden" id="editId">

        <label class="m-label">Nazwa towaru *</label>
        <input type="text" id="editName" class="m-input" placeholder="np. Karton Czysty">

        <label class="m-label">Grupa</label>
        <select id="editGroup" class="m-select">
            <option value="">– brak –</option>
            @foreach($groups as $g)
                <option value="{{ $g->id }}">{{ $g->name }}</option>
            @endforeach
        </select>

        <button class="btn-modal-save" onclick="saveEditFraction()">
            <i class="fas fa-check"></i> Zapisz zmiany
        </button>
    </div>
</div>
