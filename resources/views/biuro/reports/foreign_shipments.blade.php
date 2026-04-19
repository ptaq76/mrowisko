@extends('layouts.app')

@section('title', 'Raport wysyłek zagranicznych')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.report-wrap { padding: 20px; }
.report-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px; }
.report-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;display:flex;align-items:center;gap:8px; }
.badge-count { display:inline-block;background:#1a1a1a;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px; }

/* Filtry */
.filters { background:#fff;border-radius:10px;padding:14px 16px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,.07);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end; }
.filter-group { display:flex;flex-direction:column;gap:4px; }
.filter-group label { font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em; }
.filter-group select,
.filter-group input { padding:7px 10px;border:1px solid #dde0e5;border-radius:7px;font-size:13px;color:#1a1a1a;outline:none;min-width:160px; }
.filter-group select:focus,
.filter-group input:focus { border-color:#1a1a1a; }
.btn-copy-date { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;cursor:pointer;background:#f4f5f7;color:#555;font-size:14px;border:1px solid #dde0e5;transition:all .15s ease; }
.btn-copy-date:hover { background:#1a1a1a;color:#fff;border-color:#1a1a1a; }
.btn-filter { padding:8px 18px;background:#1a1a1a;color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer; }
.btn-filter:hover { background:#333; }

/* Podsumowanie */
.summary-bar { background:#fff;border-radius:10px;padding:12px 16px;margin-bottom:12px;box-shadow:0 1px 4px rgba(0,0,0,.07);display:flex;gap:24px;flex-wrap:wrap;align-items:center; }
.summary-item { display:flex;flex-direction:column;gap:2px; }
.summary-label { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#aaa; }
.summary-value { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;color:#1a1a1a; }
.summary-divider { width:1px;height:36px;background:#e8eaed; }

/* Tabela */
.report-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow-x:auto; }
.report-table { width:100%;border-collapse:collapse;font-size:12px;white-space:nowrap; }
.report-table thead tr { background:#1a1a1a;color:#fff; }
.report-table th { padding:9px 12px;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left;border-right:1px solid rgba(255,255,255,.07); }
.report-table th:last-child { border-right:none; }
.report-table th.th-right { text-align:right; }
.report-table td { padding:9px 12px;border-bottom:1px solid #f0f2f5;border-right:1px solid #f0f2f5;vertical-align:middle; }
.report-table td:last-child { border-right:none; }
.report-table tr:last-child td { border-bottom:none; }
.report-table tr:hover td { background:#f8f9fa; }

/* Komórki ogólne */
.ls-nr { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;color:#1a1a1a; }
.cell-main { font-weight:700;color:#1a1a1a;font-size:13px; }
.waste-code { display:inline-block;font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:900;background:#1a1a1a;color:#fff;padding:2px 7px;border-radius:4px; }
.nr-rej { display:inline-block;background:#fff;border:2px solid #1a1a1a;padding:1px 5px;border-radius:4px;font-weight:800;font-size:10px; }

/* Status */
.status-badge { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700; }
.s-planned   { background:#f0f4ff;color:#3b5bdb; }
.s-loaded    { background:#fff3e0;color:#f39c12; }
.s-weighed   { background:#e3f2fd;color:#3498db; }
.s-delivered { background:#f3e5f5;color:#9c27b0; }
.s-closed    { background:#e8f5e9;color:#27ae60; }

/* Waga plac */
.plac-weight { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;color:#f39c12; }
.plac-bales  { font-size:11px;font-weight:700;color:#aaa;margin-top:1px; }
.weight-none { color:#ddd;font-size:11px; }

/* Wagi kierowca/odbiorca */
.weight-pair  { display:flex;flex-direction:column;gap:4px; }
.weight-row   { display:flex;align-items:center;gap:6px; }
.weight-label { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#aaa;width:54px;flex-shrink:0; }
.w-kierowca   { font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:900;color:#3498db; }
.w-odbiorca   { font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:900;color:#27ae60; }
.weight-diff  { font-size:10px;font-weight:700;padding:1px 5px;border-radius:4px; }
.diff-pos { background:#fdecea;color:#e74c3c; }
.diff-neg { background:#e8f5e9;color:#27ae60; }

/* Stopka */
.sum-row td { font-weight:800;background:#f4f5f7;font-family:'Barlow Condensed',sans-serif;font-size:13px; }
.empty-state { text-align:center;padding:48px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }
</style>
@endsection

@section('content')
<div class="report-wrap">

    <div class="report-header">
        <div class="report-title">
            <i class="fas fa-ship" style="color:#1a1a1a"></i>
            Raport Wysyłek Zagranicznych
            <span class="badge-count">{{ $wysylki->count() }}</span>
        </div>
    </div>

    {{-- Filtry --}}
    <form method="GET" action="{{ route('biuro.reports.foreign-shipments') }}">
        <div class="filters">
            <div class="filter-group">
                <label>Data od</label>
                <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="filter-group" style="justify-content:flex-end">
                <span class="btn-copy-date" onclick="copyDateTo()" title="Ustaw datę do = data od">
                    <i class="fas fa-angle-double-right"></i>
                </span>
            </div>
            <div class="filter-group">
                <label>Data do</label>
                <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}">
            </div>
            <div class="filter-group">
                <label>Importer (odbiorca)</label>
                <select name="importer_id">
                    <option value="">– wszyscy –</option>
                    @foreach($importerzy as $imp)
                        <option value="{{ $imp->id }}" {{ request('importer_id') == $imp->id ? 'selected' : '' }}>
                            {{ $imp->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Klient (z LS)</label>
                <select name="client_id">
                    <option value="">– wszyscy –</option>
                    @foreach($klienci as $k)
                        <option value="{{ $k->id }}" {{ request('client_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->short_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Towar</label>
                <select name="goods_id">
                    <option value="">– wszystkie –</option>
                    @foreach($towary as $t)
                        <option value="{{ $t->id }}" {{ request('goods_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Kod odpadu</label>
                <select name="waste_code_id">
                    <option value="">– wszystkie –</option>
                    @foreach($kodyOdpadow as $kd)
                        <option value="{{ $kd->id }}" {{ request('waste_code_id') == $kd->id ? 'selected' : '' }}>
                            {{ $kd->code }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select name="status">
                    <option value="">– wszystkie –</option>
                    <option value="planned"   {{ request('status') === 'planned'   ? 'selected' : '' }}>Zaplanowane</option>
                    <option value="loaded"    {{ request('status') === 'loaded'    ? 'selected' : '' }}>Załadowane</option>
                    <option value="weighed"   {{ request('status') === 'weighed'   ? 'selected' : '' }}>Zważone</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Dostarczone</option>
                    <option value="closed"    {{ request('status') === 'closed'    ? 'selected' : '' }}>Zamknięte</option>
                </select>
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Filtruj
            </button>
            @if(request()->hasAny(['date_from','date_to','importer_id','client_id','goods_id','waste_code_id','status']))
                <a href="{{ route('biuro.reports.foreign-shipments') }}" style="font-size:12px;color:#aaa;align-self:center">
                    <i class="fas fa-times"></i> Wyczyść
                </a>
            @endif
        </div>
    </form>

    {{-- Pasek podsumowania --}}
    @if($wysylki->isNotEmpty())
    @php
        $sumaPlacKg   = $wysylki->sum(fn($w) => $w->warehouseLoadingItems->sum('weight_kg'));
        $sumaBales    = $wysylki->sum(fn($w) => $w->warehouseLoadingItems->sum('bales'));
        $sumaKierowca = $wysylki->sum(fn($w) => (float) $w->weight_netto);
        $sumaOdbiorca = $wysylki->sum(fn($w) => (float) $w->weight_receiver);
        $cntOdbiorca  = $wysylki->filter(fn($w) => (float) $w->weight_receiver > 0)->count();
    @endphp
    <div class="summary-bar">
        <div class="summary-item">
            <span class="summary-label">Wysyłek</span>
            <span class="summary-value">{{ $wysylki->count() }}</span>
        </div>
        <div class="summary-divider"></div>
        <div class="summary-item">
            <span class="summary-label">Plac – belki</span>
            <span class="summary-value" style="color:#f39c12">{{ $sumaBales }} szt.</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Plac – waga</span>
            <span class="summary-value" style="color:#f39c12">{{ number_format($sumaPlacKg / 1000, 3, ',', ' ') }} t</span>
        </div>
        <div class="summary-divider"></div>
        <div class="summary-item">
            <span class="summary-label">Kierowca ∑</span>
            <span class="summary-value" style="color:#3498db">{{ number_format($sumaKierowca, 3, ',', ' ') }} t</span>
        </div>
        @if($cntOdbiorca > 0)
        <div class="summary-item">
            <span class="summary-label">Odbiorca ∑</span>
            <span class="summary-value" style="color:#27ae60">{{ number_format($sumaOdbiorca, 3, ',', ' ') }} t</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Z wagą odbiorcy</span>
            <span class="summary-value" style="font-size:16px">{{ $cntOdbiorca }}/{{ $wysylki->count() }}</span>
        </div>
        @endif
    </div>
    @endif

    {{-- Tabela --}}
    @if($wysylki->isEmpty())
    <div class="empty-state">
        <i class="fas fa-ship"></i>
        <p style="font-size:15px;font-weight:600">Brak wysyłek zagranicznych w wybranym zakresie</p>
    </div>
    @else
    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Nr LS</th>
                    <th>Data</th>
                    <th>Importer</th>
                    <th>Klient (LS)</th>
                    <th>Towar</th>
                    <th>Kod odpadu</th>
                    <th>Kierowca / Pojazd</th>
                    <th>Status</th>
                    <th class="th-right">Waga plac</th>
                    <th>Waga kier. / odbiorcy</th>
                </tr>
            </thead>
            <tbody>
            @php
                $totalPlacKg   = 0;
                $totalBales    = 0;
                $totalKierowca = 0;
                $totalOdbiorca = 0;
            @endphp
            @foreach($wysylki as $w)
            @php
                $ls           = $w->lieferschein;
                $placKg       = $w->warehouseLoadingItems->sum('weight_kg');
                $placBales    = $w->warehouseLoadingItems->sum('bales');
                $wagaKierowca = (float) $w->weight_netto;
                $wagaOdbiorca = (float) $w->weight_receiver;
                $totalPlacKg   += $placKg;
                $totalBales    += $placBales;
                $totalKierowca += $wagaKierowca;
                $totalOdbiorca += $wagaOdbiorca;

                $diffVal   = ($wagaOdbiorca > 0 && $wagaKierowca > 0)
                    ? round($wagaOdbiorca - $wagaKierowca, 3) : null;
                $diffClass = $diffVal !== null ? ($diffVal < 0 ? 'diff-neg' : 'diff-pos') : '';

                $statusMap = [
                    'planned'   => ['label' => 'Zaplanowane',  'class' => 's-planned'],
                    'loaded'    => ['label' => 'Załadowane',   'class' => 's-loaded'],
                    'weighed'   => ['label' => 'Zważone',      'class' => 's-weighed'],
                    'delivered' => ['label' => 'Dostarczone',  'class' => 's-delivered'],
                    'closed'    => ['label' => 'Zamknięte',    'class' => 's-closed'],
                ];
                $st = $statusMap[$w->status] ?? ['label' => $w->status, 'class' => 's-planned'];
            @endphp
            <tr>
                {{-- Nr LS --}}
                <td><span class="ls-nr">{{ $ls?->number ?? '–' }}</span></td>

                {{-- Data bez godziny --}}
                <td class="cell-main">{{ $w->planned_date?->format('d.m.Y') ?? '–' }}</td>

                {{-- Importer --}}
                <td><div class="cell-main">{{ $ls?->importer?->name ?? '–' }}</div></td>

                {{-- Klient z LS --}}
                <td><div class="cell-main">{{ $ls?->client?->short_name ?? '–' }}</div></td>

                {{-- Towar --}}
                <td>{{ $ls?->goods?->name ?? '–' }}</td>

                {{-- Kod odpadu --}}
                <td>
                    @if($ls?->wasteCode)
                        <span class="waste-code">{{ $ls->wasteCode->code }}</span>
                    @else
                        <span style="color:#ddd">–</span>
                    @endif
                </td>

                {{-- Kierowca / Pojazd --}}
                <td>
                    @if($w->driver)
                        <div style="font-size:12px;font-weight:600">{{ $w->driver->name }}</div>
                    @endif
                    <div style="display:flex;gap:3px;margin-top:2px">
                        @if($w->tractor)<span class="nr-rej">{{ $w->tractor->plate }}</span>@endif
                        @if($w->trailer)<span class="nr-rej">{{ $w->trailer->plate }}</span>@endif
                    </div>
                </td>

                {{-- Status --}}
                <td><span class="status-badge {{ $st['class'] }}">{{ $st['label'] }}</span></td>

                {{-- Waga plac: waga + belki --}}
                <td style="text-align:right">
                    @if($placKg > 0)
                        <div class="plac-weight">{{ number_format($placKg / 1000, 3, ',', ' ') }} t</div>
                        <div class="plac-bales">{{ $placBales }} szt.</div>
                    @else
                        <span class="weight-none">–</span>
                    @endif
                </td>

                {{-- Waga kierowcy i odbiorcy w jednej kolumnie --}}
                <td>
                    <div class="weight-pair">
                        <div class="weight-row">
                            <span class="weight-label">Kierowca</span>
                            @if($wagaKierowca > 0)
                                <span class="w-kierowca">{{ number_format($wagaKierowca, 3, ',', ' ') }} t</span>
                            @else
                                <span class="weight-none">–</span>
                            @endif
                        </div>
                        <div class="weight-row">
                            <span class="weight-label">Odbiorca</span>
                            @if($wagaOdbiorca > 0)
                                <span class="w-odbiorca">{{ number_format($wagaOdbiorca, 3, ',', ' ') }} t</span>
                                @if($diffVal !== null)
                                    <span class="weight-diff {{ $diffClass }}"
                                          title="Różnica odbiorca vs kierowca">
                                        {{ $diffVal > 0 ? '+' : '' }}{{ number_format($diffVal, 3, ',', ' ') }} t
                                    </span>
                                @endif
                            @else
                                <span class="weight-none">–</span>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach

            {{-- Wiersz sumy --}}
            <tr class="sum-row">
                <td colspan="8">Razem: {{ $wysylki->count() }} wysyłek</td>
                <td style="text-align:right">
                    {{ number_format($totalPlacKg / 1000, 3, ',', ' ') }} t
                    <div style="font-size:11px;color:#888;font-weight:600">{{ $totalBales }} szt.</div>
                </td>
                <td>
                    <div class="weight-pair" style="gap:3px">
                        <div class="weight-row">
                            <span class="weight-label">Kierowca</span>
                            <span>{{ number_format($totalKierowca, 3, ',', ' ') }} t</span>
                        </div>
                        @if($totalOdbiorca > 0)
                        <div class="weight-row">
                            <span class="weight-label">Odbiorca</span>
                            <span>{{ number_format($totalOdbiorca, 3, ',', ' ') }} t</span>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
function copyDateTo() {
    const from = document.getElementById('date_from').value;
    if (from) document.getElementById('date_to').value = from;
}
</script>
@endsection