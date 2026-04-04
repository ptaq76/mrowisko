@extends('layouts.app')
@section('title', 'Plan na plac')
@section('module_name', 'BIURO')
@section('nav_menu') @include('biuro._nav') @endsection

@section('styles')
<style>
.page-title {
    font-family:'Barlow Condensed',sans-serif;
    font-size:22px;font-weight:900;letter-spacing:.06em;
    text-transform:uppercase;margin-bottom:14px;
}

/* ── Layout ── */
.pnp-layout {
    display: flex;
    gap: 24px;
    max-width: 75%;
    align-items: flex-start;
}

.pnp-left  { flex: 0 0 260px; }
.pnp-right { flex: 1; min-width: 0; }

/* ── Datepicker – identyczny z planning/index ── */
#datepicker { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.10); }

.ui-datepicker {
    display: block !important;
    font-family: 'Barlow', sans-serif !important;
    font-size: 15px !important;
    width: 100% !important;
    border: none !important;
    box-shadow: none !important;
    background: #fff !important;
    padding: 0 !important;
}

.ui-datepicker-header {
    background: linear-gradient(135deg, #6EBF58, #4da83e) !important;
    color: #fff !important;
    border: none !important;
    border-radius: 0 !important;
    padding: 10px 8px !important;
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 700 !important;
    letter-spacing: .04em !important;
}

.ui-datepicker-title {
    color: #fff !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    letter-spacing: .06em !important;
    text-transform: uppercase !important;
}

.ui-datepicker-prev, .ui-datepicker-next {
    top: 8px !important;
    cursor: pointer !important;
}

.ui-datepicker-prev span, .ui-datepicker-next span {
    border-color: transparent #fff transparent transparent !important;
}

.ui-datepicker-header .ui-state-hover {
    background: rgba(255,255,255,.25) !important;
    border: none !important;
    border-radius: 4px !important;
}

.ui-datepicker th {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    letter-spacing: .08em !important;
    color: #6EBF58 !important;
    padding: 6px 0 4px !important;
    text-transform: uppercase !important;
}

.ui-datepicker td { padding: 2px 3px !important; }

.ui-datepicker td a, .ui-datepicker td span {
    text-align: center !important;
    border-radius: 6px !important;
    border: none !important;
    padding: 5px 2px !important;
    font-size: 14px !important;
    transition: background .15s, color .15s !important;
    color: #1a1a1a !important;
    background: transparent !important;
}

.ui-datepicker td a:hover {
    background: #e8f7e4 !important;
    color: #2d7a1a !important;
}

.ui-datepicker .ui-datepicker-today a {
    background: #e8f7e4 !important;
    color: #2d7a1a !important;
    font-weight: 700 !important;
    border: 2px solid #6EBF58 !important;
}

.ui-datepicker .selected-day a {
    background: #ff9900 !important;
    color: #fff !important;
    font-weight: 700 !important;
    box-shadow: 0 2px 6px rgba(255,153,0,.4) !important;
}

.ui-datepicker .highlight-red a {
    color: #e74c3c !important;
    font-weight: 700 !important;
}

.ui-datepicker .ui-datepicker-other-month a {
    color: #bdc3c7 !important;
}

/* ── Nagłówek daty ── */
.pnp-date-heading {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 26px;
    font-weight: 900;
    letter-spacing: .05em;
    color: #1a1a1a;
    margin-bottom: 10px;
    text-transform: uppercase;
}

/* ── Podsumowanie ── */
.pnp-summary { font-size:13px;color:#888;margin-bottom:12px; }

/* ── Karty zleceń ── */
.order-card { background:#fff;border-radius:10px;margin-bottom:10px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.08); }
.order-header { padding:8px 14px;display:flex;align-items:center;justify-content:space-between; }
.order-header.type-sale   { background:#fff3e0;border-left:4px solid #e67e22; }
.order-header.type-pickup { background:#e8f4fb;border-left:4px solid #2980b9; }
.order-client { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;letter-spacing:.03em;color:#1a1a1a; }
.order-time   { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;color:#1a1a1a; }
.order-body   { padding:8px 14px;font-size:13px;color:#555; }
.order-body .row-item { display:flex;gap:6px;margin-bottom:3px; }
.row-label { font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.06em;min-width:70px; }
.nr-rej { display:inline-block;border:2px solid #1a1a1a;border-radius:4px;padding:1px 7px;font-weight:800;font-size:13px;letter-spacing:.04em; }
.type-badge { font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px; }
.type-sale-badge   { background:#fff3e0;color:#e67e22; }
.type-pickup-badge { background:#e8f4fb;color:#2980b9; }
.plac-diff { font-size:11px;font-weight:700;color:#888; }
.empty-state { text-align:center;padding:48px 20px;color:#aaa; }
.empty-state i { font-size:40px;margin-bottom:10px;display:block; }
</style>
@endsection

@section('content')

<div class="page-title"><i class="fas fa-industry" style="color:#e67e22"></i> Plan na plac</div>

<div class="pnp-layout">

    {{-- ── LEWA: datepicker ── --}}
    <div class="pnp-left">
        <div id="datepicker" style="border:none;background:transparent;margin-top:0"></div>
    </div>

    {{-- ── PRAWA: zlecenia ── --}}
    <div class="pnp-right">

        <div class="pnp-date-heading">
            {{ mb_strtoupper($date->locale('pl')->translatedFormat('l, d F Y')) }}
        </div>

        @if($orders->isEmpty())
        <div class="empty-state">
            <i class="fas fa-calendar-check"></i>
            <p>Brak zleceń zaplanowanych na placu w tym dniu</p>
        </div>
        @else

        <div class="pnp-summary">
            Łącznie: <strong style="color:#1a1a1a">{{ $orders->count() }}</strong> zleceń
            (<strong style="color:#e67e22">{{ $orders->where('type','sale')->count() }}</strong> załadunki,
            <strong style="color:#2980b9">{{ $orders->where('type','pickup')->count() }}</strong> odbiory)
        </div>

        @foreach($orders as $order)
        @php
            $diff = $order->plac_date && $order->planned_date
                ? $order->plac_date->diffInDays($order->planned_date, false)
                : null;
        @endphp
        <div class="order-card">
            <div class="order-header type-{{ $order->type }}">
                <div class="d-flex align-items-center gap-2">
                    <span class="type-badge {{ $order->type === 'sale' ? 'type-sale-badge' : 'type-pickup-badge' }}">
                        {{ $order->type === 'sale' ? 'Załadunek' : 'Odbiór' }}
                    </span>
                    <span class="order-client">{{ $order->client?->short_name ?? '?' }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($diff !== null && $diff < 0)
                        <span class="plac-diff" title="Zlecenie {{ abs($diff) }} dzień(dni) przed datą zlecenia">
                            {{ $diff }}d
                        </span>
                    @endif
                    @if($order->planned_time)
                        <span class="order-time">{{ substr($order->planned_time, 0, 5) }}</span>
                    @endif
                    <span style="font-size:12px;color:#888">{{ $order->planned_date?->format('d.m.Y') }}</span>
                </div>
            </div>
            <div class="order-body">
                <div class="row-item">
                    <span class="row-label">Pojazd</span>
                    <span>
                        <span class="nr-rej">{{ $order->tractor?->plate ?? '–' }}</span>
                        @if($order->trailer) / <span class="nr-rej">{{ $order->trailer->plate }}</span> @endif
                    </span>
                </div>
                @if($order->startClient)
                <div class="row-item">
                    <span class="row-label">Start</span>
                    <span>{{ $order->startClient->short_name }}</span>
                </div>
                @endif
                <div class="row-item">
                    <span class="row-label">Towary</span>
                    <span>{{ $order->fractions_note ?? '–' }}</span>
                </div>
                @if($order->lieferschein)
                <div class="row-item">
                    <span class="row-label">LS</span>
                    <span>
                        <strong>{{ $order->lieferschein->number }}</strong>
                        — {{ $order->lieferschein->importer?->name }}
                        — okienko: {{ $order->lieferschein->time_window }}
                    </span>
                </div>
                @endif
                @if($order->notes)
                <div class="row-item">
                    <span class="row-label">Uwagi</span>
                    <span style="color:#888">{{ $order->notes }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach

        @endif
    </div>{{-- /.pnp-right --}}

</div>{{-- /.pnp-layout --}}

@endsection

@section('scripts')
{{-- jQuery UI – CSS i JS razem, tak jak w planning/index --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>

<script>
const currentDate = '{{ $date->format("Y-m-d") }}';
const today       = '{{ now()->format("Y-m-d") }}';

function goToDate(date) {
    window.location.href = '{{ route('biuro.plan-na-plac') }}?data=' + date;
}

$.getJSON('/swieta.php', function(holidays) {
    const holidaysMap = {};
    holidays.forEach(h => { holidaysMap[h.date] = h.name; });

    $('#datepicker').datepicker({
        dayNames:        ['Niedziela','Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota'],
        dayNamesMin:     ['Nd','Pn','Wt','Śr','Cz','Pt','Sb'],
        monthNames:      ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
        monthNamesShort: ['Sty','Lut','Mar','Kwi','Maj','Cze','Lip','Sie','Wrz','Paź','Lis','Gru'],
        firstDay:        1,
        dateFormat:      'yy-mm-dd',
        showOtherMonths: true,
        selectOtherMonths: true,
        beforeShowDay: function(date) {
            const ds = $.datepicker.formatDate('yy-mm-dd', date);
            if (holidaysMap[ds])    return [true, 'highlight-red', holidaysMap[ds]];
            if (ds === currentDate) return [true, 'selected-day', ''];
            if (ds === today)       return [true, 'ui-datepicker-today', ''];
            return [true, '', ''];
        },
        onSelect: function(dateText) {
            goToDate(dateText);
        }
    });
    $('#datepicker').datepicker('setDate', currentDate);

}).fail(function() {
    $('#datepicker').html(
        '<input type="date" class="form-control form-control-sm" value="' + currentDate + '" onchange="goToDate(this.value)">'
    );
});
</script>
@endsection
