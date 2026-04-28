@extends('layouts.app')

@section('title', 'Raport migracji orders')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
    .stat-card {
        border-radius: 8px;
        padding: 16px;
        background: #fff;
        border: 1px solid var(--gray-2);
    }
    .stat-card .stat-value { font-size: 28px; font-weight: 700; line-height: 1.1; }
    .stat-card .stat-label { font-size: 12px; color: var(--gray-3); text-transform: uppercase; letter-spacing: .04em; }

    .filter-pill {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid var(--gray-2);
        color: var(--black);
        font-size: 12px;
        text-decoration: none;
        margin: 0 4px 6px 0;
        transition: all .12s;
    }
    .filter-pill:hover { background: var(--gray-1); }
    .filter-pill.active { background: #1f3a5f; color: #fff; border-color: #1f3a5f; }

    .badge-status-ok      { background: #d4edda; color: #155724; }
    .badge-status-skipped { background: #f8d7da; color: #721c24; }
    .badge-status-fk      { background: #fff3cd; color: #856404; }

    .table-report { font-size: 13px; }
    .table-report td, .table-report th { padding: 6px 8px; vertical-align: middle; }
    .table-report .fk-bad { color: #c0392b; font-weight: 600; }
    .table-report tr.row-skipped { background: #fff5f5; }
    .table-report tr.row-fk      { background: #fffbe6; }
</style>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="m-0">Raport migracji orders</h1>
    <small class="text-muted">Źródło: <code>mrowisko.planowanie</code> → analiza wg logiki <code>MigrateOrdersSeeder</code></small>
</div>

{{-- Statystyki --}}
<div class="row g-3 mt-1">
    <div class="col-md-2 col-6">
        <div class="stat-card">
            <div class="stat-label">Wszystkich</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="stat-card">
            <div class="stat-label">closed (komplet)</div>
            <div class="stat-value text-success">{{ $stats['ok'] }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="stat-card">
            <div class="stat-label">delivered (bez wazenia)</div>
            <div class="stat-value text-info">{{ $stats['kept_no_wazenia'] }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="stat-card">
            <div class="stat-label">planned (świeże)</div>
            <div class="stat-value text-primary">{{ $stats['kept_recent_no_exec'] }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="stat-card">
            <div class="stat-label">Pominięte</div>
            <div class="stat-value text-danger">{{ $stats['skip_no_plan_na_plac'] + $stats['skip_no_towary'] }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="stat-card">
            <div class="stat-label">Problemy FK</div>
            <div class="stat-value text-warning">{{ $stats['fk_problems'] }}</div>
        </div>
    </div>
</div>

{{-- Statystyki FK --}}
<div class="mt-3">
    <strong class="small text-muted">Niepoprawne FK (rozbicie):</strong>
    <div class="d-flex flex-wrap gap-3 mt-1 small">
        <span>client_id: <strong class="{{ $fkCounts['client_id'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $fkCounts['client_id'] }}</strong></span>
        <span>start_client_id: <strong class="{{ $fkCounts['start_client_id'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $fkCounts['start_client_id'] }}</strong></span>
        <span>driver_id: <strong class="{{ $fkCounts['driver_id'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $fkCounts['driver_id'] }}</strong></span>
        <span>tractor_id: <strong class="{{ $fkCounts['tractor_id'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $fkCounts['tractor_id'] }}</strong></span>
        <span>trailer_id: <strong class="{{ $fkCounts['trailer_id'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $fkCounts['trailer_id'] }}</strong></span>
        <span>lieferschein_id: <strong class="{{ $fkCounts['lieferschein_id'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $fkCounts['lieferschein_id'] }}</strong></span>
        <span>fraction_id: <strong class="{{ $fkCounts['fraction_id'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $fkCounts['fraction_id'] }}</strong></span>
    </div>
</div>

{{-- Filtry --}}
<div class="mt-3">
    @php
        $pills = [
            'all'              => 'Wszystkie',
            'ok'               => 'OK (bez problemów FK)',
            'closed'           => '└ closed',
            'delivered'        => '└ delivered (bez wazenia)',
            'planned'          => '└ planned (świeże, niewykonane)',
            'skipped'          => 'Pominięte (stare i niewykonane)',
            'no_plan_na_plac'  => '└ brak plan_na_plac',
            'no_towary'        => '└ brak towarów',
            'fk'               => 'Problemy FK (wszystkie)',
            'fk_client'        => '└ client_id',
            'fk_start'         => '└ start_client_id',
            'fk_driver'        => '└ driver_id',
            'fk_tractor'       => '└ tractor_id',
            'fk_trailer'       => '└ trailer_id',
            'fk_ls'            => '└ lieferschein_id',
            'fk_fraction'      => '└ fraction_id',
        ];
    @endphp
    @foreach($pills as $key => $label)
        <a href="?filter={{ $key }}" class="filter-pill {{ $filter === $key ? 'active' : '' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="mt-3 small text-muted">
    Wyświetlono: <strong>{{ count($rows) }}</strong> rekordów
</div>

<div class="table-responsive mt-2">
    <table class="table table-sm table-hover table-report">
        <thead class="table-light">
            <tr>
                <th>plan_id</th>
                <th>Status</th>
                <th>Powód / problem</th>
                <th>Data</th>
                <th>Godz</th>
                <th>Typ</th>
                <th>client_id</th>
                <th>start_id</th>
                <th>driver_id</th>
                <th>tractor_id</th>
                <th>trailer_id</th>
                <th>ls_id</th>
                <th>Towary</th>
                <th>Notatka</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr class="{{ $r['status'] === 'skipped' ? 'row-skipped' : ($r['status'] === 'fk_problem' ? 'row-fk' : '') }}">
                    <td><strong>{{ $r['planowanie_id'] }}</strong></td>
                    <td>
                        @if($r['status'] === 'ok')
                            <span class="badge badge-status-ok">OK</span>
                        @elseif($r['status'] === 'skipped')
                            <span class="badge badge-status-skipped">SKIP</span>
                        @else
                            <span class="badge badge-status-fk">FK</span>
                        @endif
                    </td>
                    <td>
                        {{ $r['reason'] }}
                        @if(!empty($r['towary_invalid_fraction_ids']))
                            <br><small class="text-danger">brakujące fraction_id: {{ implode(', ', $r['towary_invalid_fraction_ids']) }}</small>
                        @endif
                    </td>
                    <td>{{ $r['data'] }}</td>
                    <td>{{ $r['godzina'] }}</td>
                    <td>{{ $r['type'] }}</td>
                    <td class="{{ in_array('client_id', $r['fk_issues']) ? 'fk-bad' : '' }}">{{ $r['kontrahent_cel'] ?? '—' }}</td>
                    <td class="{{ in_array('start_client_id', $r['fk_issues']) ? 'fk-bad' : '' }}">{{ $r['start'] ?? '—' }}</td>
                    <td class="{{ in_array('driver_id', $r['fk_issues']) ? 'fk-bad' : '' }}">{{ $r['kierowca_id'] ?? '—' }}</td>
                    <td class="{{ in_array('tractor_id', $r['fk_issues']) ? 'fk-bad' : '' }}">{{ $r['ciagnik_id'] ?? '—' }}</td>
                    <td class="{{ in_array('trailer_id', $r['fk_issues']) ? 'fk-bad' : '' }}">{{ $r['naczepa_id'] ?? '—' }}</td>
                    <td class="{{ in_array('lieferschein_id', $r['fk_issues']) ? 'fk-bad' : '' }}">{{ $r['ls_id'] ?? '—' }}</td>
                    <td>{{ $r['towary_count'] ?: '—' }}</td>
                    <td><small>{{ \Illuminate\Support\Str::limit($r['towary_note'] ?? '', 40) }}</small></td>
                </tr>
            @empty
                <tr><td colspan="14" class="text-center text-muted py-4">Brak rekordów dla wybranego filtra.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
