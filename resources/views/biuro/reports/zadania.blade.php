@extends('layouts.app')

@section('title', 'Raport – Zadania')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.report-wrap { padding: 20px; }
.report-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.report-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;display:flex;align-items:center;gap:8px; }
.badge-count { display:inline-block;background:#1a1a1a;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px; }

.filters { background:#fff;border-radius:10px;padding:14px 16px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.07);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end; }
.filter-group { display:flex;flex-direction:column;gap:4px; }
.filter-group label { font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em; }
.filter-group input, .filter-group select { padding:7px 10px;border:1px solid #dde0e5;border-radius:7px;font-size:13px;color:#1a1a1a;outline:none;min-width:140px; }
.filter-group input:focus, .filter-group select:focus { border-color:#1a1a1a; }
.btn-filter { padding:8px 18px;background:#1a1a1a;color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer; }
.btn-filter:hover { background:#333; }
.btn-clear { padding:8px 14px;background:#fff;color:#666;border:1px solid #dde0e5;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none; }
.btn-clear:hover { background:#f4f5f7;color:#1a1a1a; }

.report-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden; }
.report-table { width:100%;border-collapse:collapse;font-size:13px; }
.report-table thead tr { background:#1a1a1a;color:#fff; }
.report-table th { padding:10px 12px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left; }
.report-table td { padding:10px 12px;border-bottom:1px solid #e8eaed;vertical-align:top; }
.report-table tr:last-child td { border-bottom:none; }
.report-table tr:hover td { background:#f9f9f9; }

.status-pill { display:inline-block;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:700;color:#fff;white-space:nowrap; }
.status-pending { background:#f39c12; }
.status-done    { background:#27ae60; }

.scope-pill { display:inline-block;padding:2px 8px;border-radius:8px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em; }
.scope-driver { background:#eaf4fb;color:#1a6fbe; }
.scope-plac   { background:#fef9e7;color:#d68910; }
.scope-all    { background:#f4ecf7;color:#7d3c98; }

.empty-state { text-align:center;padding:48px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }
</style>
@endsection

@section('content')
<div id="poll-area" class="report-wrap">

    <div class="report-header">
        <div class="report-title">
            <i class="fas fa-tasks"></i>
            Zadania
            <span class="badge-count">{{ $zadania->total() }}</span>
        </div>
    </div>

    <form method="GET" class="filters">
        <div class="filter-group">
            <label>Treść zadania</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Szukaj...">
        </div>

        <div class="filter-group">
            <label>Zakres</label>
            <select name="scope" onchange="document.getElementById('drv').style.display = this.value === 'driver' ? '' : 'none'">
                <option value="all" {{ request('scope', 'all') === 'all' ? 'selected' : '' }}>Wszystkie</option>
                <option value="plac" {{ request('scope') === 'plac' ? 'selected' : '' }}>Plac</option>
                <option value="driver" {{ request('scope') === 'driver' ? 'selected' : '' }}>Konkretny kierowca</option>
            </select>
        </div>

        <div class="filter-group" id="drv" style="display: {{ request('scope') === 'driver' ? '' : 'none' }}">
            <label>Kierowca</label>
            <select name="driver_id">
                <option value="">— wybierz —</option>
                @foreach($drivers as $d)
                    <option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label>Status</label>
            <select name="status">
                <option value="">Wszystkie</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Oczekujące</option>
                <option value="done"    {{ request('status') === 'done'    ? 'selected' : '' }}>Wykonane</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Data od</label>
            <input type="date" name="data_from" value="{{ request('data_from') }}">
        </div>

        <div class="filter-group">
            <label>Data do</label>
            <input type="date" name="data_to" value="{{ request('data_to') }}">
        </div>

        <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filtruj</button>
        <a href="{{ route('biuro.raporty.zadania') }}" class="btn-clear">Wyczyść</a>
    </form>

    @if($zadania->isEmpty())
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <p>Brak zadań spełniających kryteria.</p>
        </div>
    @else
    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Treść</th>
                    <th>Komu</th>
                    <th>Status</th>
                    <th>Utworzył</th>
                    <th>Wykonał</th>
                </tr>
            </thead>
            <tbody>
                @foreach($zadania as $z)
                <tr>
                    <td style="white-space:nowrap;font-weight:700">{{ $z->data->format('d.m.Y') }}</td>
                    <td>{{ $z->tresc }}</td>
                    <td>
                        @if($z->target === 'driver')
                            <span class="scope-pill scope-driver">{{ $z->driver?->name ?? '?' }}</span>
                        @elseif($z->target === 'plac')
                            <span class="scope-pill scope-plac">Plac</span>
                        @else
                            <span class="scope-pill scope-all">{{ $z->driver?->name ?? '?' }} <small>(wszyscy)</small></span>
                        @endif
                    </td>
                    <td>
                        @if($z->status === 'done')
                            <span class="status-pill status-done">Wykonane</span>
                        @else
                            <span class="status-pill status-pending">Oczekuje</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#666">{{ $z->creator?->name ?? '–' }}</td>
                    <td style="font-size:12px;color:#666">
                        @if($z->status === 'done')
                            {{ $z->completer?->name ?? '–' }}
                            <div style="font-size:10px;color:#aaa">{{ $z->completed_at?->format('d.m.Y H:i') }}</div>
                        @else
                            –
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px">
        {{ $zadania->links() }}
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
// POLLING: lista zadań odświeża się sama co 5s
if (window.pollPageFragment) {
    window.pollPageFragment('poll-area', 5000);
}
</script>
@endsection
