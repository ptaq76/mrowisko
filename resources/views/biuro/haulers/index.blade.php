@extends('layouts.ustawienia')
@section('title', 'Woźacy')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
    .wrap { padding: 20px; }
    .page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;margin-bottom:6px; }
    .page-sub { font-size:13px;color:#888;margin-bottom:16px; }
    .search-input { padding:8px 12px;border:1px solid #dde0e5;border-radius:8px;font-size:13px;outline:none;min-width:250px;margin-bottom:14px; }
    .search-input:focus { border-color:#6EBF58; }

    .table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden;max-width:50%; }
    table { width:100%;border-collapse:collapse;font-size:13px; }
    thead tr { background:#1a1a1a;color:#fff;position:sticky;top:0;z-index:10; }
    th { padding:10px 14px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-align:left; }
    td { padding:10px 14px;border-bottom:1px solid #f0f2f5;vertical-align:middle; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#f8f9fa; }

    .toggle-btn { border:none;background:none;cursor:pointer;padding:4px;border-radius:4px;font-size:20px;line-height:1;transition:transform .1s; }
    .toggle-btn:active { transform:scale(.88); }
    .icon-on  { color:#6EBF58; }
    .icon-off { color:#e74c3c; }

    .haulers-active-wrap { margin-bottom:14px; }
    .haulers-active-label { font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9aa3ad;margin-bottom:6px; }
    .haulers-chips { display:flex;flex-wrap:wrap;gap:6px; }
    .hauler-chip { display:inline-flex;align-items:center;gap:5px;background:#e8f7e4;border:1px solid #b8e0ad;border-radius:20px;padding:3px 10px;font-size:12px;font-weight:600;color:#1a1a1a;position:relative; }
    .hauler-chip i { font-size:10px;color:#58a545; }
    .hauler-chip .chip-remove { display:none;background:none;border:none;color:#e74c3c;cursor:pointer;font-size:13px;padding:0 0 0 2px;line-height:1; }
    .hauler-chip:hover .chip-remove { display:inline-flex; }
    .hauler-chip:hover { border-color:#f5c6cb;background:#fdecea; }
</style>
@endsection

@section('settings_content')
<div class="wrap">
    <div class="page-title"><i class="mdi mdi-car"></i> WOZACY</div>
    <div class="page-sub">Zaznaczeni klienci pojawiają się jako szybkie przyciski w module ważeń.</div>

    <div class="haulers-active-wrap">
        <div class="haulers-active-label">Zaznaczeni woźacy</div>
        <div class="haulers-chips" id="activeChips">
            @forelse($clients->whereIn('id', $haulerIds) as $client)
                <span class="hauler-chip" id="chip-{{ $client->id }}">
                    <i class="mdi mdi-car"></i> {{ $client->short_name }}
                    <button type="button" class="chip-remove" onclick="removeHauler({{ $client->id }})" title="Usuń">&times;</button>
                </span>
            @empty
                <span class="empty-msg" style="font-size:12px;color:#ccc;font-style:italic">Brak zaznaczonych</span>
            @endforelse
        </div>
    </div>

    <input type="text" class="search-input" placeholder="Szukaj klienta..." id="searchInput" oninput="filterTable()">

    <div class="table-wrap">
        <table id="haulTable">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Nazwa klienta</th>
                    <th style="width:80px;text-align:center">Woźak</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $i => $client)
                <tr id="cr-{{ $client->id }}">
                    <td style="color:#aaa;font-size:12px">{{ $i + 1 }}</td>
                    <td class="client-name" style="font-weight:700;font-size:13px;color:#1a1a1a">{{ $client->short_name }}</td>
                    <td style="text-align:center">
                        <button type="button" class="toggle-btn" id="hbtn-{{ $client->id }}" onclick="toggle({{ $client->id }})">
                            @if(in_array($client->id, $haulerIds))
                                <span class="icon-on">✔</span>
                            @else
                                <span class="icon-off">✖</span>
                            @endif
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Pobranie tokena CSRF z meta tagu
const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');

async function toggle(clientId) {
    const btn = document.getElementById('hbtn-' + clientId);
    const row = document.getElementById('cr-' + clientId);
    const clientName = row.querySelector('.client-name').textContent.trim();
    
    btn.style.opacity = '.4';
    btn.disabled = true;

    try {
        const res = await fetch(`/biuro/haulers/${clientId}/toggle`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': getCsrfToken(), 
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        });

        if (!res.ok) throw new Error('Błąd serwera');

        const data = await res.json();
        
        if (data.success) {
            btn.innerHTML = data.is_hauler
                ? '<span class="icon-on">✔</span>'
                : '<span class="icon-off">✖</span>';
            updateChip(clientId, data.is_hauler, clientName);
        }
    } catch (error) {
        console.error(error);
        alert('Nie udało się zmienić statusu. Spróbuj ponownie.');
    } finally {
        btn.style.opacity = '1';
        btn.disabled = false;
    }
}

function updateChip(clientId, isHauler, name) {
    const chipsContainer = document.getElementById('activeChips');
    const existingChip = document.getElementById('chip-' + clientId);
    const emptyMsg = chipsContainer.querySelector('.empty-msg');

    if (isHauler) {
        if (emptyMsg) emptyMsg.remove();
        
        if (!existingChip) {
            const chip = document.createElement('span');
            chip.className = 'hauler-chip';
            chip.id = 'chip-' + clientId;
            chip.innerHTML = `
                <i class="mdi mdi-car"></i> ${name} 
                <button type="button" class="chip-remove" onclick="removeHauler(${clientId})" title="Usuń">&times;</button>
            `;
            chipsContainer.appendChild(chip);
        }
    } else {
        if (existingChip) existingChip.remove();
        
        if (chipsContainer.querySelectorAll('.hauler-chip').length === 0) {
            chipsContainer.innerHTML = '<span class="empty-msg" style="font-size:12px;color:#ccc;font-style:italic">Brak zaznaczonych</span>';
        }
    }
}

async function removeHauler(clientId) {
    // Funkcja removeHauler robi to samo co toggle, 
    // więc po prostu ją wywołujemy dla spójności
    await toggle(clientId);
}

function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#haulTable tbody tr').forEach(row => {
        const name = row.querySelector('.client-name').textContent.toLowerCase();
        row.style.display = name.includes(q) ? '' : 'none';
    });
}
</script>
@endsection