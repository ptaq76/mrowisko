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

.table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden; }
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
</style>
@endsection

@section('settings_content')
<div class="wrap">
    <div class="page-title"><i class="fas fa-truck"></i> WOZACY</div>
    <div class="page-sub">Zaznaczeni klienci pojawiają się jako szybkie przyciski w module ważeń.</div>

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
                    <td style="font-weight:600">{{ $client->short_name }}</td>
                    <td style="text-align:center">
                        <button class="toggle-btn" id="hbtn-{{ $client->id }}"
                                onclick="toggle({{ $client->id }})">
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
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function toggle(clientId) {
    const btn = document.getElementById('hbtn-' + clientId);
    btn.style.opacity = '.4';
    const res  = await fetch(`/biuro/haulers/${clientId}/toggle`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    btn.style.opacity = '1';
    if (data.success) {
        btn.innerHTML = data.is_hauler
            ? '<span class="icon-on">✔</span>'
            : '<span class="icon-off">✖</span>';
    }
}

function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#haulTable tbody tr').forEach(row => {
        row.style.display = row.cells[1].textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
@endsection
