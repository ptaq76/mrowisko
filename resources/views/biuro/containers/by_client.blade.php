@extends('layouts.ustawienia')

@section('title', 'Kontenery — według klientów')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
.stats-row { display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap }
.stat-pill { background:#fff;border:1px solid #e2e5e9;border-radius:10px;padding:8px 14px;font-size:13px;display:inline-flex;align-items:center;gap:8px }
.stat-pill .stat-num { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;color:#1a1a1a }
.stat-pill.plac .stat-num { color:#27ae60 }
.stat-pill.client .stat-num { color:#d68910 }
.tabs-row { display:flex;gap:8px;margin-bottom:14px;border-bottom:2px solid #e2e5e9 }
.tab-link { padding:10px 18px;font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:15px;letter-spacing:.04em;text-transform:uppercase;text-decoration:none;color:#888;border-bottom:3px solid transparent;margin-bottom:-2px }
.tab-link.active { color:#1a1a1a;border-bottom-color:#d68910 }
.group-card { background:#fff;border:1px solid #e2e5e9;border-radius:10px;margin-bottom:12px;overflow:hidden }
.group-header { background:#f9f7f3;padding:10px 14px;display:flex;align-items:center;gap:10px;border-bottom:1px solid #e2e5e9 }
.group-header.plac-header { background:#eafaf1;border-bottom-color:#d4f0de }
.group-name { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;letter-spacing:.04em;text-transform:uppercase;color:#1a1a1a;flex:1 }
.group-count { background:#fff;border:1px solid #e2e5e9;border-radius:20px;padding:2px 10px;font-size:12px;font-weight:700;color:#555 }
.group-count.plac-count { background:#27ae60;color:#fff;border-color:#27ae60 }
.group-count.client-count { background:#d68910;color:#fff;border-color:#d68910 }
.containers-list { padding:10px 14px;display:flex;flex-wrap:wrap;gap:8px }
.cont-pill { background:#f4f6f9;border:1px solid #d5d8dc;border-radius:6px;padding:5px 10px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px }
.cont-pill .type-icon { font-size:10px;color:#888 }
.cont-pill .qty { background:#1a1a1a;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;font-weight:800;margin-left:4px }
.cont-pill.prasokontener { background:#e3f2fd;border-color:#90caf9 }
.cont-pill.prasokontener .type-icon { color:#1976d2 }
</style>
@endsection

@section('settings_content')

<div class="page-header">
    <div class="page-title"><i class="fa-solid fa-dumpster"></i> Kontenery — według klientów</div>
</div>

<div class="stats-row">
    <div class="stat-pill plac">
        <i class="fa-solid fa-warehouse" style="color:#27ae60"></i>
        Na placu: <span class="stat-num">{{ $stats['plac'] }}</span>
    </div>
    <div class="stat-pill client">
        <i class="fa-solid fa-handshake" style="color:#d68910"></i>
        U klientów: <span class="stat-num">{{ $stats['client'] }}</span>
    </div>
    <div class="stat-pill">
        <i class="fa-solid fa-layer-group" style="color:#888"></i>
        Razem: <span class="stat-num">{{ $stats['total'] }}</span>
    </div>
</div>

<div class="tabs-row">
    <a href="{{ route('biuro.containers.index') }}" class="tab-link">
        <i class="fa-solid fa-list"></i> Lista typów
    </a>
    <a href="{{ route('biuro.containers.byClient') }}" class="tab-link active">
        <i class="fa-solid fa-people-group"></i> Według klientów
    </a>
</div>

{{-- Plac --}}
<div class="group-card">
    <div class="group-header plac-header">
        <i class="fa-solid fa-warehouse" style="color:#27ae60"></i>
        <div class="group-name">Plac</div>
        <span class="group-count plac-count">{{ $placStocks->sum('quantity') }} szt.</span>
    </div>
    @if($placStocks->isEmpty())
        <div class="containers-list" style="color:#888;font-size:13px">Brak kontenerów na placu.</div>
    @else
        <div class="containers-list">
            @foreach($placStocks as $s)
                <span class="cont-pill {{ $s->container->type === 'prasokontener' ? 'prasokontener' : '' }}">
                    @if($s->container->type === 'prasokontener')
                        <i class="fa-solid fa-compress-alt type-icon" title="Prasokontener"></i>
                    @else
                        <i class="fa-solid fa-dumpster type-icon"></i>
                    @endif
                    {{ $s->container->name }}
                    <span class="qty">{{ $s->quantity }}</span>
                </span>
            @endforeach
        </div>
    @endif
</div>

{{-- Klienci --}}
@forelse($clients as $client)
    @php $stocksForClient = $clientStocks->get($client->id, collect())->sortBy(fn($s) => $s->container->name); @endphp
    <div class="group-card">
        <div class="group-header">
            <i class="fa-solid fa-handshake" style="color:#d68910"></i>
            <div class="group-name">{{ $client->short_name }}</div>
            <span class="group-count client-count">{{ $stocksForClient->sum('quantity') }} szt.</span>
        </div>
        <div class="containers-list">
            @foreach($stocksForClient as $s)
                <span class="cont-pill {{ $s->container->type === 'prasokontener' ? 'prasokontener' : '' }}">
                    @if($s->container->type === 'prasokontener')
                        <i class="fa-solid fa-compress-alt type-icon" title="Prasokontener"></i>
                    @else
                        <i class="fa-solid fa-dumpster type-icon"></i>
                    @endif
                    {{ $s->container->name }}
                    <span class="qty">{{ $s->quantity }}</span>
                </span>
            @endforeach
        </div>
    </div>
@empty
    <div class="text-center text-muted py-3">Żaden klient nie ma obecnie kontenerów.</div>
@endforelse

@endsection
