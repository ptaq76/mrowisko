@extends('layouts.app')

@section('title', 'Raport – Ważenia')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.report-wrap { padding: 20px; }
.report-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.report-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
.badge-count { display:inline-block;background:#3498db;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px;margin-left:8px; }

.filters { background:#fff;border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.07);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end; }
.filter-group { display:flex;flex-direction:column;gap:4px; }
.filter-group label { font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em; }
.filter-group select,
.filter-group input { padding:7px 10px;border:1px solid #dde0e5;border-radius:7px;font-size:13px;color:#1a1a1a;outline:none;min-width:150px; }
.filter-group select:focus,
.filter-group input:focus { border-color:#3498db; }
.btn-copy-date {
    display:inline-flex;align-items:center;justify-content:center;
    width:32px;height:32px;border-radius:7px;cursor:pointer;
    background:#eef2f7;color:#3498db;font-size:14px;
    border:1px solid #dde0e5;transition:all .15s ease;
}
.btn-copy-date:hover { background:#3498db;color:#fff;border-color:#3498db; }

.btn-filter { padding:8px 18px;background:#3498db;color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer; }
.btn-filter:hover { background:#2980b9; }

.report-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden; }
.report-table { width:100%;border-collapse:collapse;font-size:13px; }
.report-table thead tr { background:#3498db;color:#fff; }
.report-table th { padding:10px 12px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left; }
.report-table td { padding:10px 12px;border-bottom:1px solid #f0f2f5;vertical-align:middle; }
.report-table tr:last-child td { border-bottom:none; }
.report-table tr:hover td { background:#f0f7fd; }

.cell-date  { font-weight:700;white-space:nowrap;font-size:13px; }
.cell-client { font-weight:700;font-size:13px;color:#1a1a1a; }
.cell-plate { font-size:12px;font-weight:700;color:#555; }
.nr-rej { display:inline-block;background:#fff;border:2px solid #1a1a1a;padding:1px 6px;border-radius:4px;font-weight:800;font-size:12px; }

.w-tare  { font-size:13px;color:#555; }
.w-brutto { font-size:13px;color:#555;font-weight:600; }
.w-netto  { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#1a1a1a; }
.w-netto-unit { font-size:11px;color:#888; }

.cell-driver { font-size:13px;font-weight:600;color:#1a1a1a; }

.btn-delete-w {
    background:#fdecea;border:1px solid #f5c6cb;border-radius:6px;
    padding:6px 10px;color:#e74c3c;cursor:pointer;font-size:12px;
    display:flex;align-items:center;gap:5px;white-space:nowrap;
}
.btn-delete-w:hover { background:#e74c3c;color:#fff; }

.empty-state { text-align:center;padding:48px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }
</style>
@endsection

@section('content')
<div class="report-wrap">

    <div class="report-header">
        <div class="report-title" style="display:flex;align-items:center;gap:8px">
            <i class="fas fa-weight" style="color:#3498db"></i>
            Raport Ważeń Kierowców
            <span class="badge-count" style="font-size:11px;vertical-align:middle">{{ $orders->count() }}</span>
        </div>
    </div>

    @php $defaultFrom = now()->subDays(7)->format('Y-m-d'); $defaultTo = now()->format('Y-m-d'); @endphp
    <form method="GET" action="{{ route('biuro.reports.weighings') }}">
        <div class="filters">
            <div class="filter-group">
                <label>Data od</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from', $defaultFrom) }}">
            </div>
            <div class="filter-group" style="justify-content:flex-end">
                <span class="btn-copy-date" onclick="copyDateTo()" title="Ustaw datę do = data od">
                    <i class="fas fa-angle-double-right"></i>
                </span>
            </div>
            <div class="filter-group">
                <label>Data do</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to', $defaultTo) }}">
            </div>
            <div class="filter-group">
                <label>Klient</label>
                <select name="client_id">
                    <option value="">– wszyscy –</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->short_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Kierowca</label>
                <select name="driver_id">
                    <option value="">– wszyscy –</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Filtruj
            </button>
            @if(request()->hasAny(['date_from','date_to','driver_id','client_id']))
            <a href="{{ route('biuro.reports.weighings') }}" style="font-size:12px;color:#aaa;align-self:center">
                <i class="fas fa-times"></i> Wyczyść
            </a>
            @endif
        </div>
    </form>

    @if($orders->isEmpty())
    <div class="empty-state">
        <i class="fas fa-weight"></i>
        <p style="font-size:15px;font-weight:600">Brak ważeń do wyświetlenia</p>
    </div>
    @else
    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Klient</th>
                    <th>Pojazdy</th>
                    <th>Tara</th>
                    <th>Brutto</th>
                    <th>Netto</th>
                    <th>Kierowca</th>
                    <th>Waga odbiorcy</th>
                    <th style="width:80px">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                @php
                    $tare = $order->weight_brutto && $order->weight_netto
                        ? round($order->weight_brutto - $order->weight_netto, 3)
                        : null;
                @endphp
                <tr id="row-{{ $order->id }}">
                    <td class="cell-date">{{ $order->planned_date->format('d.m.Y') }}</td>
                    <td>
                        <div class="cell-client" style="display:flex;align-items:center;gap:6px">
                            <i class="fas {{ $order->type === 'sale' ? 'fa-arrow-up' : 'fa-arrow-down' }}"
                               style="color:{{ $order->type === 'sale' ? '#f39c12' : '#27ae60' }};font-size:12px;flex-shrink:0"></i>
                            {{ $order->client?->short_name }}
                        </div>
                    </td>
                    <td>
                        @if($order->tractor)
                            <span class="nr-rej">{{ $order->tractor->plate }}</span>
                        @endif
                        @if($order->trailer)
                            <span style="color:#ccc;margin:0 3px">/</span>
                            <span class="nr-rej">{{ $order->trailer->plate }}</span>
                        @endif
                    </td>
                    <td class="w-tare">
                        {{ $tare ? number_format($tare, 3, ',', ' ') . ' t' : '–' }}
                    </td>
                    <td class="w-brutto">
                        {{ $order->weight_brutto ? number_format($order->weight_brutto, 3, ',', ' ') . ' t' : '–' }}
                    </td>
                    <td>
                        <div class="w-netto">{{ number_format($order->weight_netto, 3, ',', ' ') }}</div>
                    </td>
                    <td class="cell-driver">{{ $order->driver?->name ?? '–' }}</td>
                    <td>
                        @if($order->weight_receiver)
                            <div style="font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#2471a3">
                                {{ number_format($order->weight_receiver, 3, ',', ' ') }}
                            </div>
                        @else
                            <span style="color:#ddd;font-size:12px">–</span>
                        @endif
                    </td>
                    <td>
                        @if($order->weight_receiver)
                        <button class="btn-delete-w" disabled title="Zablokowane – waga odbiorcy jest wpisana"
                                style="opacity:.35;cursor:not-allowed">
                            <i class="fas fa-lock"></i> Usuń
                        </button>
                        @else
                        <button class="btn-delete-w" onclick="deleteWeighing({{ $order->id }})" title="Usuń zapis wagi">
                            <i class="fas fa-trash-alt"></i> Usuń
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function copyDateTo() {
    const from = document.getElementById('date_from').value;
    if (from) document.getElementById('date_to').value = from;
}

async function deleteWeighing(id) {
    const result = await Swal.fire({
        title: 'Usunąć zapis wagi?',
        html: '<b>Uwaga:</b> Waga brutto i netto zostaną usunięte z bazy.<br>Status wróci do poprzedniego.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        confirmButtonText: 'Tak, usuń',
        cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;

    const res  = await fetch(`/biuro/reports/weighings/${id}/delete`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('row-' + id).remove();
        Swal.fire({ icon: 'success', title: 'Usunięto!', timer: 1500, showConfirmButton: false });
    }
}
</script>
@endsection
