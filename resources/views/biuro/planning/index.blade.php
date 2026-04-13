@extends('layouts.app')

@section('title', 'Planowanie')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
    :root {
        --green:       #6EBF58;
        --green-dark:  #4ea33e;
        --green-light: #e8f7e4;
        --black:       #1a1a1a;
        --gray-1:      #f4f5f7;
        --gray-2:      #dde0e5;
    }
    #main { padding: 0 !important; }

    .planning-layout {
        display: flex;
        height: calc(100vh - 58px);
        overflow: hidden;
    }

    /* ── LEWA KOLUMNA ── */
    .col-left {
        width: 380px;
        flex-shrink: 0;
        border-right: 1px solid var(--gray-2);
        overflow-y: auto;
        background: var(--gray-1);
        padding: 10px 8px;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .week-day-header {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #F9D38C;
        padding: 4px 8px;
        border-radius: 4px;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .week-day-header.today { background: var(--green); }
    .week-day-header:hover { filter: brightness(.95); }

    .week-order-mini {
        font-size: 11px;
        padding: 2px 6px;
        border-left: 3px solid #ddd;
        margin-bottom: 2px;
        color: var(--black);
    }

    /* ── ŚRODKOWA KOLUMNA ── */
    .col-middle {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        padding: 10px;
        background: var(--gray-1);
    }

    .driver-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        border-bottom: 1px solid rgba(0,0,0,.12);
        flex-shrink: 0;
        border-radius: 12px 12px 0 0;
        overflow: hidden;
    }

    .driver-header .driver-name {
        font-family: var(--font-display);
        font-size: 22px;
        font-weight: 700;
        letter-spacing: .04em;
    }

    .driver-avatar {
        width: 52px; height: 52px;
        border-radius: 50%;
        border: 3px solid rgba(0,0,0,.15);
        object-fit: cover;
    }

    .driver-avatar-placeholder {
        width: 52px; height: 52px;
        border-radius: 50%;
        border: 3px solid rgba(0,0,0,.15);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display);
        font-weight: 700; font-size: 18px;
        color: rgba(0,0,0,.5);
        background: rgba(0,0,0,.12);
    }

    .orders-list {
        flex: 1;
        padding: 10px 12px;
        overflow-y: auto;
    }

    /* Karta zlecenia */
    .order-card {
        border: 2px solid var(--black);
        border-radius: 6px;
        background: #fff;
        margin-bottom: 8px;
        overflow: hidden;
    }

    .order-card-body {
        display: flex;
        gap: 0;
        min-height: 80px;
    }

    .order-col-main {
        flex: 0 0 40%;
        padding: 8px 10px;
        border-right: 1px solid var(--gray-2);
    }

    .order-col-ls {
        flex: 0 0 35%;
        padding: 8px 10px;
        border-right: 1px solid var(--gray-2);
        font-size: 12px;
    }

    .order-col-actions {
        flex: 0 0 25%;
        padding: 8px 10px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: space-between;
    }

    .order-client {
        font-family: var(--font-display);
        font-size: 18px;
        font-weight: 700;
        letter-spacing: .02em;
    }

    .order-time { font-size: 13px; color: var(--gray-3); font-weight: 600; }

    .nr_rej {
        display: inline-block;
        background: white;
        border: 2px solid black;
        padding: 1px 5px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 11px;
    }

    .order-start {
        font-size: 11px;
        color: var(--gray-3);
        margin-top: 2px;
    }

    .order-type-badge {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 1px 6px;
        border-radius: 3px;
    }

    .type-pickup { background: #e3f0ff; color: #1a6fbe; }
    .type-sale   { background: var(--green-light); color: #2d7a2a; }

    /* Status buttony */
    .status-btns { display: flex; gap: 3px; flex-wrap: wrap; justify-content: flex-end; }
    .btn-status-sm {
        width: 28px; height: 28px;
        border-radius: 4px;
        border: 1px solid var(--gray-2);
        background: #fff;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 14px;
        transition: background .15s, border-color .15s;
        color: #aaa;
    }
    .btn-status-sm:hover { background: var(--gray-1); }
    .btn-status-sm.active { background: var(--green); border-color: var(--green-dark); color: #fff; }
    .btn-status-sm.in-progress { background: #fef9e7; border-color: #f39c12; color: #d68910; }

    /* ── PRAWA KOLUMNA ── */
    .col-right {
        width: 510px;
        flex-shrink: 0;
        border-left: 1px solid var(--gray-2);
        overflow-y: auto;
        background: var(--gray-1);
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    /* Avatary kierowców */
    .drivers-grid {
        display: flex;
        flex-wrap: nowrap;
        gap: 3px;
        padding: 6px 6px;
        border-bottom: 1px solid var(--gray-2);
        overflow-x: auto;
    }

    .driver-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        cursor: pointer;
        padding: 3px;
        border-radius: 3px;
        border: 2px solid transparent;
        background: none;
        transition: border-color .15s, background .15s;
        flex-shrink: 0;
    }

    .driver-btn:hover { background: var(--gray-2); }
    .driver-btn.active { border-color: var(--black); background: rgba(0,0,0,.06); }

    .driver-btn img {
        width: 58px; height: 58px;
        border-radius: 0;
        outline: 1px solid #000;
        object-fit: cover;
        display: block;
    }

    .avatar-init {
        width: 58px; height: 58px;
        border-radius: 0;
        outline: 1px solid #000;
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display);
        font-weight: 700; font-size: 15px;
        color: rgba(0,0,0,.7);
    }

    .driver-btn span { display: none; }

    /* Sekcje przycisków szybkich */
    .quick-section {
        padding: 8px;
    }

    .quick-section-title {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--gray-3);
        margin-bottom: 5px;
    }

    .quick-btns { display: flex; flex-wrap: wrap; gap: 3px; }

    .quick-btn-client {
        font-size: 10px;
        padding: 3px 6px;
        border-radius: 3px;
        border: 1px solid var(--gray-2);
        background: #fff;
        cursor: pointer;
        transition: background .1s;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--black);
    }

    .quick-btn-client:hover { background: var(--green-light); border-color: var(--green); }

    /* ── DATEPICKER – nowoczesny styl ── */
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

    /* ── ZLECENIA HANDLOWCÓW – karty ── */
    .pr-card {
        background: #fff;
        border-radius: 6px;
        border-left: 4px solid #f39c12;
        margin-bottom: 6px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }

    .pr-card-top {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 8px 4px;
        cursor: pointer;
    }

    .pr-card-top:hover { background: #fffbf4; }

    .pr-client {
        font-family: var(--font-display);
        font-size: 15px;
        font-weight: 800;
        color: var(--black);
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .pr-days-badge {
        font-family: var(--font-display);
        font-size: 13px;
        font-weight: 900;
        min-width: 32px;
        height: 24px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        padding: 0 4px;
    }

    .pr-salesman {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #2980b9;
        color: #fff;
        font-size: 8px;
        font-weight: 700;
        font-family: var(--font-display);
        flex-shrink: 0;
        cursor: default;
    }

    .pr-card-body {
        padding: 0 8px 6px 12px;
        border-top: 1px solid #f5f5f5;
    }

    .pr-date-row {
        font-size: 10px;
        color: #999;
        margin-bottom: 3px;
        padding-top: 4px;
    }

    .pr-items {
        display: flex;
        flex-direction: column;
        gap: 1px;
        margin-bottom: 3px;
    }

    .pr-item {
        font-size: 11px;
        color: #444;
        display: flex;
        justify-content: space-between;
    }

    .pr-item-nazwa { font-weight: 600; }
    .pr-item-meta  { color: #aaa; font-size: 10px; }

    .pr-notes {
        font-size: 10px;
        color: #aaa;
        font-style: italic;
        margin-top: 2px;
    }
</style>
@endsection

@section('content')
<div class="planning-layout">

    {{-- ══ LEWA KOLUMNA: tydzień ══ --}}
    <div class="col-left">
        <div id="datepicker" style="border:none;background:transparent;margin-top:0"></div>
        <input type="hidden" id="selectedDate" value="{{ $date->format('Y-m-d') }}">

        @foreach($weekDays as $dateStr => $dayData)
        @php
            $isToday   = $dateStr === now()->format('Y-m-d');
            $dayName = mb_convert_case($dayData['date']->locale('pl')->translatedFormat('l'), MB_CASE_TITLE);
        @endphp
        <div class="mb-2">
            <div class="week-day-header {{ $isToday ? 'today' : '' }}"
                 onclick="goToDate('{{ $dateStr }}')">
                <span>{{ $dayName }}</span>
                <span class="ms-auto text-muted" style="font-weight:400">{{ $dayData['date']->format('d.m') }}</span>
            </div>
            @foreach($dayData['orders'] as $o)
            <div class="week-order-mini" style="border-color: {{ $o->driver?->color ?? '#ddd' }}">
                {{ $o->client?->short_name ?? '?' }}
                @if($o->planned_time)
                    <span class="text-muted">{{ substr($o->planned_time, 0, 5) }}</span>
                @endif
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    {{-- ══ ŚRODKOWA KOLUMNA: zlecenia kierowcy ══ --}}
    <div class="col-middle">

        @if($driver)
        @php
            $dayFull  = mb_convert_case($date->locale('pl')->translatedFormat('l'), MB_CASE_TITLE);
        @endphp
        <div style="border: 3px solid {{ $driver?->color ?? '#ccc' }}; border-radius: 12px; overflow: hidden; flex: 1; display: flex; flex-direction: column">
        <div class="driver-header" style="background:{{ $driver->color }};border-bottom:3px solid rgba(0,0,0,.2)">

            @php
                $monthsPL = ['','STYCZEŃ','LUTY','MARZEC','KWIECIEŃ','MAJ','CZERWIEC','LIPIEC','SIERPIEŃ','WRZESIEŃ','PAŹDZIERNIK','LISTOPAD','GRUDZIEŃ'];
                $monthName = $monthsPL[(int)$date->format('n')];
            @endphp
            <div style="background:#fff;border:2px solid #000;border-radius:8px;min-width:68px;padding:4px 10px 6px;text-align:center;line-height:1;flex-shrink:0">
                <div style="font-family:var(--font-display);font-size:40px;font-weight:900;color:#000;letter-spacing:.01em">{{ $date->format('d') }}</div>
                <div style="font-size:10px;font-weight:700;color:#000;letter-spacing:.06em;margin-top:2px">{{ $monthName }}</div>
            </div>

            <div style="margin-left:10px">
                <div style="font-size:20px;font-family:var(--font-display);font-weight:800;color:#000;letter-spacing:.02em">{{ $dayFull }}</div>
                <div style="font-size:13px;color:#000;margin-top:1px">{{ $date->format('d.m.Y') }}</div>
            </div>

            <div style="flex:1;text-align:center">
                <div style="font-size:34px;font-family:var(--font-display);font-weight:900;color:#000;letter-spacing:.04em">
                    {{ $driver->name }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm" style="background:rgba(0,0,0,.15);border:none"
                        onclick="openOrderModal(null)" title="Nowe zlecenie">
                    <i class="mdi mdi-tooltip-plus" style="font-size:1.8em"></i>
                </button>
                @if($driver->avatar)
                    <img src="{{ asset('drivers/' . $driver->avatar) }}"
                         style="width:58px;height:58px;object-fit:cover;border-radius:0;border:none"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div style="display:none;width:58px;height:58px;background:rgba(0,0,0,.15);align-items:center;justify-content:center;font-family:var(--font-display);font-weight:700;font-size:22px;color:#000">
                        {{ $driver->initials() }}
                    </div>
                @else
                    <div style="width:58px;height:58px;background:rgba(0,0,0,.15);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-weight:700;font-size:22px;color:#000">
                        {{ $driver->initials() }}
                    </div>
                @endif
            </div>
        </div>
        @endif

        <div class="orders-list" style="border: 3px solid {{ $driver?->color ?? '#eee' }}; border-top: none; background: #fff; border-radius: 0 0 12px 12px; overflow: hidden">
            @forelse($orders as $order)
            <div class="order-card">
                <div class="order-card-body">

                    <div class="order-col-main">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            @if($order->planned_time)
                                <span class="order-time">{{ substr($order->planned_time, 0, 5) }}</span>
                            @endif
                        </div>
                        <div class="order-client">{{ $order->client?->short_name ?? '?' }}</div>
                        <div class="mt-1">
                            <span class="nr_rej" style="font-size:13px">{{ $order->tractor?->plate ?? '–' }}</span>
                            @if($order->trailer)
                                / <span class="nr_rej" style="font-size:13px">{{ $order->trailer->plate }}</span>
                            @endif
                        </div>
                        @if($order->startClient)
                            <div class="order-start">
                                <i class="fas fa-map-marker-alt"></i> {{ $order->startClient->short_name }}
                            </div>
                        @endif
                        @if($order->notes)
                            <div class="mt-1" style="font-size:12px">{{ $order->notes }}</div>
                        @endif
                    </div>

                    <div class="order-col-ls">
                        @if($order->lieferschein)
                        @php $ls = $order->lieferschein; @endphp
                        <div class="fw-bold" style="font-size:13px;color: {{ $order->planned_date->format('Y-m-d') !== $ls->date->format('Y-m-d') ? 'red' : 'inherit' }}">
                            LS: {{ $ls->number }}
                            <a href="{{ route('biuro.ls.pdf', $ls) }}" target="_blank"
                               class="ms-1 text-danger" title="PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </div>
                        <div class="fw-bold" style="font-size:13px">{{ $ls->importer?->name }}</div>
                        <div style="font-size:13px">{{ $order->fractions_note }}</div>
                        <div style="font-size:13px">Okienko: {{ $ls->time_window }}</div>
                        @else
                            @if($order->fractions_note)
                                <div style="font-size:13px"><span class="fw-bold">Towar:</span> {{ $order->fractions_note }}</div>
                            @endif
                        @endif
                    </div>

                    <div class="order-col-actions">
                        <div class="status-btns">
                            @php
                                $loadingWasClosed = $order->warehouseLoadingItems->isNotEmpty();

                                if ($order->type === 'sale') {
                                    $statuses = [
                                        'loaded'  => ['fas fa-truck-loading', 'Załadowane'],
                                        'weighed' => ['fas fa-weight',        'Zważone'],
                                        'closed'  => ['fas fa-check-double',  'Zamknięte'],
                                    ];
                                    $activeStatuses     = [];
                                    $inProgressStatuses = [];
                                    if ($loadingWasClosed)                              $activeStatuses[] = 'loaded';
                                    elseif ($order->loadingItems->isNotEmpty())         $inProgressStatuses[] = 'loaded';
                                    if ($order->weight_netto)                           $activeStatuses[] = 'weighed';
                                    if ($order->status === 'closed')                    $activeStatuses[] = 'closed';
                                } else {
                                    $statuses = [
                                        'weighed'   => ['fas fa-weight',       'Zważone'],
                                        'delivered' => ['fas fa-boxes',        'Dostarczone'],
                                        'closed'    => ['fas fa-check-double', 'Zamknięte'],
                                    ];
                                    $activeStatuses     = [];
                                    $inProgressStatuses = [];
                                    $deliveryWasClosed  = $order->warehouseDeliveryItems->isNotEmpty();
                                    if ($order->weight_netto)                           $activeStatuses[] = 'weighed';
                                    if ($deliveryWasClosed)                             $activeStatuses[] = 'delivered';
                                    elseif ($order->loadingItems->isNotEmpty())         $inProgressStatuses[] = 'delivered';
                                    if ($order->status === 'closed')                    $activeStatuses[] = 'closed';
                                }
                            @endphp
                            @foreach($statuses as $statusKey => $statusInfo)
                            @php
                                $isActive     = in_array($statusKey, $activeStatuses);
                                $isInProgress = in_array($statusKey, $inProgressStatuses);
                            @endphp
                            <button class="btn-status-sm {{ $isActive ? 'active' : ($isInProgress ? 'in-progress' : '') }}"
                                    data-status="{{ $statusKey }}"
                                    title="{{ $statusInfo[1] }}{{ $isInProgress ? ' (w trakcie)' : '' }}"
                                    onclick="setStatus({{ $order->id }}, '{{ $statusKey }}', '{{ $statusInfo[1] }}', this)">
                                <i class="{{ $statusInfo[0] }}"></i>
                            </button>
                            @endforeach
                        </div>
                        @php $isLocked = !empty($activeStatuses) || !empty($inProgressStatuses); @endphp
                        @php
                            $placDiff = null;
                            if ($order->plac_date && $order->planned_date) {
                                $placDiff = $order->plac_date->diffInDays($order->planned_date, false);
                            }
                        @endphp
                        @if($order->plac_date)
                            <div style="margin-top:5px;text-align:center">
                                <span title="Widoczne na placu od {{ $order->plac_date->format('d.m') }}"
                                      style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#1a1a1a;color:#fff;font-size:11px;font-weight:900;font-family:'Barlow Condensed',sans-serif">
                                    {{ $placDiff === 0 ? '0' : $placDiff }}
                                </span>
                            </div>
                        @else
                            <div style="margin-top:5px;text-align:center">
                                <span title="Nie wysłano na plac"
                                      style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#e2e5e9;color:#aaa;font-size:11px;font-weight:900;cursor:pointer"
                                      onclick="quickSetPlacDate({{ $order->id }}, '{{ $order->planned_date->format('Y-m-d') }}')">
                                    <i class="fas fa-industry" style="font-size:9px"></i>
                                </span>
                            </div>
                        @endif
                        @if(!$isLocked)
                        <button class="btn btn-edit btn-sm mt-2"
                                onclick="openEditOrderModal({{ $order->id }})" title="Edytuj">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        @else
                        <button class="btn btn-sm mt-2"
                                style="opacity:.35;cursor:not-allowed;background:#f4f5f7;border:1px solid #dde0e5;color:#aaa"
                                title="Zlecenie w realizacji – edycja zablokowana" disabled>
                            <i class="fa-solid fa-lock"></i>
                        </button>
                        @endif
                    </div>

                </div>
            </div>
            @empty
            <div class="text-center text-muted py-5" style="font-size:14px">
                <i class="fas fa-calendar-day fa-2x mb-2 d-block opacity-25"></i>
                Brak zleceń na ten dzień
            </div>
            @endforelse
        </div>
        </div>
    </div>

    {{-- ══ PRAWA KOLUMNA ══ --}}
    <div class="col-right">

        {{-- Avatary kierowców --}}
        <div class="drivers-grid">
            @foreach($drivers as $d)
            <button class="driver-btn {{ $driver?->id === $d->id ? 'active' : '' }}"
                    onclick="selectDriver({{ $d->id }})"
                    title="{{ $d->full_name }}"
                    style="{{ $driver?->id === $d->id ? 'border-color:' . $d->color . ';background:' . $d->color . '20' : '' }}">
                @if($d->avatar)
                    <img src="{{ asset('drivers/' . $d->avatar) }}"
                         style="border: 2px solid {{ $d->color }}"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="avatar-init" style="display:none;background:{{ $d->color }}40;border:2px solid {{ $d->color }}">
                        {{ $d->initials() }}
                    </div>
                @else
                    <div class="avatar-init" style="background:{{ $d->color }}40;border:2px solid {{ $d->color }}">
                        {{ $d->initials() }}
                    </div>
                @endif
                <span>{{ $d->name }}</span>
            </button>
            @endforeach
        </div>

        {{-- Wolne LS --}}
        @if($freeLs->isNotEmpty())
        <div class="quick-section">
            <div class="quick-section-title">LS na dziś</div>
            @foreach($freeLs as $ls)
            <div style="font-size:11px;padding:2px 0;border-bottom:1px solid var(--gray-2)">
                <span class="fw-bold">{{ $ls->number }}</span>
                <span class="text-muted ms-1">{{ $ls->client?->short_name }}</span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ══ ZLECENIA HANDLOWCÓW ══ --}}
        @if($pickupRequests->isNotEmpty())
        <div class="quick-section" style="border-bottom: 1px solid var(--gray-2); padding-bottom: 10px;">
            <div class="quick-section-title d-flex align-items-center justify-content-between">
                <span>Zlecenia handlowców</span>
                <span style="background:#f39c12;color:#fff;font-size:9px;font-weight:700;padding:1px 6px;border-radius:10px;">
                    {{ $pickupRequests->count() }}
                </span>
            </div>

            @foreach($pickupRequests as $pr)
            @php
                $goodsForModal = $pr->items->pluck('nazwa')->implode(', ');
                $diffDays = (int) now()->startOfDay()->diffInDays($pr->requested_date->startOfDay(), false);
                if ($diffDays > 0)      { $dayColor = '#27ae60'; $dayPrefix = '-'; }
                elseif ($diffDays < 0)  { $dayColor = '#e74c3c'; $dayPrefix = '+'; }
                else                    { $dayColor = '#f39c12'; $dayPrefix = ''; }
                $dayLabel = $dayPrefix . abs($diffDays);
                $salesmanInitials = $pr->salesman
                    ? collect(explode(' ', $pr->salesman->name))->map(fn($w) => mb_substr($w,0,1))->take(2)->implode('')
                    : '?';
            @endphp
            <div class="pr-card">
                {{-- Górny wiersz: klient + termin + licznik dni + handlowiec --}}
                <div class="pr-card-top"
                     onclick="openOrderModal(null, 'pickup', {{ $pr->client_id }}, {{ $pr->id }}, {{ json_encode($goodsForModal) }})">
                    <div class="pr-client">{{ $pr->client?->short_name ?? '?' }}</div>

                    {{-- Termin --}}
                    <div style="font-size:10px;color:#999;white-space:nowrap;margin-right:2px">
                        Termin: {{ $pr->requested_date?->format('d.m') }}
                    </div>

                    {{-- Licznik dni --}}
                    <div class="pr-days-badge" style="background:{{ $dayColor }}18;color:{{ $dayColor }};border:1px solid {{ $dayColor }}44"
                         title="{{ $diffDays > 0 ? 'Za ' . abs($diffDays) . ' dni' : ($diffDays < 0 ? abs($diffDays) . ' dni po terminie' : 'Dziś') }}">
                        {{ $dayLabel === '0' ? 'dziś' : $dayLabel . 'd' }}
                    </div>

                    {{-- Inicjały handlowca --}}
                    @if($pr->salesman)
                    <span class="pr-salesman" title="{{ $pr->salesman->name }}">{{ $salesmanInitials }}</span>
                    @endif
                </div>

                {{-- Dolna część: towary w tabelce + uwagi + przycisk odrzuć --}}
                <div class="pr-card-body">
                    <table style="width:100%;border-collapse:collapse;margin-top:4px;margin-bottom:3px;margin-left:auto">
                        @foreach($pr->items as $item)
                        <tr>
                            <td style="font-size:11px;font-weight:600;color:#333;padding:2px 6px 2px 0;width:100%">{{ $item->nazwa }}</td>
                            <td style="font-size:11px;color:#666;text-align:right;white-space:nowrap;padding:2px 6px;border-left:1px solid #eee">@if($item->ilosc) {{ $item->ilosc }} @endif</td>
                            <td style="font-size:11px;color:#444;font-weight:600;text-align:right;white-space:nowrap;padding:2px 0 2px 6px;border-left:1px solid #eee">@if($item->cena) {{ number_format($item->cena, 2, ',', ' ') }} zł/t @endif</td>
                        </tr>
                        @endforeach
                    </table>
                    @if($pr->notes)
                    <div class="pr-notes">{{ $pr->notes }}</div>
                    @endif

                    {{-- Przycisk odrzuć --}}
                    <div style="margin-top:6px;text-align:right">
                        <button type="button"
                                onclick="event.stopPropagation(); odrzucZlecenie({{ $pr->id }}, '{{ addslashes($pr->client?->short_name ?? '?') }}')"
                                style="font-size:10px;padding:2px 8px;border-radius:3px;border:1px solid #e74c3c;background:#fff;color:#e74c3c;cursor:pointer;font-weight:600;transition:background .1s"
                                onmouseover="this.style.background='#fdf2f2'"
                                onmouseout="this.style.background='#fff'">
                            <i class="fas fa-ban" style="font-size:9px"></i> Odrzuć
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Wysyłki --}}
        <div class="quick-section">
            <div class="quick-section-title">Wysyłki</div>
            <div class="quick-btns">
                <button class="quick-btn-client" onclick="openOrderModal(null, 'sale', null)"
                        style="width:100%;background:var(--gray-1)">
                    Wybierz...
                </button>
                @foreach($topSale as $c)
                <button class="quick-btn-client" onclick="openOrderModal(null, 'sale', {{ $c->id }})">
                    {{ Str::limit($c->short_name, 12) }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Odbiory --}}
        <div class="quick-section">
            <div class="quick-section-title">Odbiory</div>
            <div class="quick-btns">
                <button class="quick-btn-client" onclick="openOrderModal(null, 'pickup', null)"
                        style="width:100%;background:var(--gray-1)">
                    Wybierz...
                </button>
                @foreach($topPickup as $c)
                <button class="quick-btn-client" onclick="openOrderModal(null, 'pickup', {{ $c->id }})">
                    {{ Str::limit($c->short_name, 12) }}
                </button>
                @endforeach
            </div>
        </div>

    </div>

</div>

@include('biuro.planning.order_modal')
@include('biuro.planning.order_modal_edit')

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>

<script>
const currentDate  = '{{ $date->format("Y-m-d") }}';
const currentDriver = {{ $driver?->id ?? 'null' }};
const today = '{{ now()->format("Y-m-d") }}';

function goToDate(date) {
    const url = new URL(window.location.href);
    url.searchParams.set('data', date);
    if (currentDriver) url.searchParams.set('kierowca', currentDriver);
    window.location.href = url.toString();
}

function selectDriver(driverId) {
    const url = new URL(window.location.href);
    url.searchParams.set('kierowca', driverId);
    url.searchParams.set('data', currentDate);
    window.location.href = url.toString();
}

async function setStatus(orderId, statusKey, statusLabel, btn) {
    const isActive = btn.classList.contains('active');

    const question = isActive
        ? `Cofnąć status "<b>${statusLabel}</b>"?`
        : `Zmienić status na "<b>${statusLabel}</b>"?`;

    const result = await Swal.fire({
        title: 'Zmiana statusu',
        html: question,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6EBF58',
        confirmButtonText: 'Tak',
        cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;

    const order = btn.closest('.order-card');
    const allBtns = [...order.querySelectorAll('.btn-status-sm')];
    const idx = allBtns.indexOf(btn);

    const newStatus = isActive
        ? (idx === 0 ? 'planned' : allBtns[idx - 1].dataset.status)
        : statusKey;

    const res = await fetch(`/biuro/orders/${orderId}/status`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: newStatus }),
    });
    const data = await res.json();

    if (data.success) {
        const statusOrder = allBtns.map(b => b.dataset.status);
        const newIdx = statusOrder.indexOf(newStatus);
        allBtns.forEach((b, i) => {
            if (i <= newIdx) b.classList.add('active');
            else b.classList.remove('active');
        });
        if (newStatus === 'planned') {
            allBtns.forEach(b => b.classList.remove('active'));
        }
    }
}

$.getJSON('/swieta.php', function(holidays) {
    const holidaysMap = {};
    holidays.forEach(h => { holidaysMap[h.date] = h.name; });

    $('#datepicker').datepicker({
        dayNames:       ['Niedziela','Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota'],
        dayNamesMin:    ['Nd','Pn','Wt','Śr','Cz','Pt','Sb'],
        monthNames:     ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
        monthNamesShort:['Sty','Lut','Mar','Kwi','Maj','Cze','Lip','Sie','Wrz','Paź','Lis','Gru'],
        firstDay:       1,
        dateFormat:     'yy-mm-dd',
        showOtherMonths: true,
        selectOtherMonths: true,
        beforeShowDay: function(date) {
            const ds = $.datepicker.formatDate('yy-mm-dd', date);
            if (holidaysMap[ds]) return [true, 'highlight-red', holidaysMap[ds]];
            if (ds === currentDate || ds === today) return [true, 'selected-day', ''];
            return [true, '', ''];
        },
        onSelect: function(dateText) {
            goToDate(dateText);
        }
    });
    $('#datepicker').datepicker('setDate', currentDate);
}).fail(function() {
    $('#datepicker').html('<input type="date" class="form-control form-control-sm" value="' + currentDate + '" onchange="goToDate(this.value)">');
});

async function quickSetPlacDate(orderId, plannedDate) {
    const pd = new Date(plannedDate);

    const prevWorkday = new Date(pd);
    prevWorkday.setDate(prevWorkday.getDate() - 1);
    while (prevWorkday.getDay() === 0 || prevWorkday.getDay() === 6) {
        prevWorkday.setDate(prevWorkday.getDate() - 1);
    }

    const fmt   = d => d.toISOString().split('T')[0];
    const fmtPl = d => d.toLocaleDateString('pl-PL', { weekday:'short', day:'numeric', month:'short' });

    const result = await Swal.fire({
        title: 'Wyślij na plac',
        icon: 'question',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: `0 – ${fmtPl(pd)}`,
        denyButtonText: `-1 – ${fmtPl(prevWorkday)}`,
        cancelButtonText: 'Anuluj',
        confirmButtonColor: '#27ae60',
        denyButtonColor: '#f39c12',
    });

    let placDate = null;
    if (result.isConfirmed)   placDate = fmt(pd);
    else if (result.isDenied) placDate = fmt(prevWorkday);
    else return;

    await fetch(`/biuro/orders/${orderId}/plac-date`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ plac_date: placDate }),
    });

    Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1000, showConfirmButton: false });
    setTimeout(() => location.reload(), 1100);
}
</script>
<script>
async function odrzucZlecenie(id, klient) {
    const result = await Swal.fire({
        title: 'Odrzucić zlecenie?',
        html: `Zlecenie od <b>${klient}</b> zostanie oznaczone jako odrzucone przez biuro.<br>Handlowiec zobaczy tę informację.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        confirmButtonText: 'Tak, odrzuć',
        cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;

    const res = await fetch(`/biuro/pickup-requests/${id}/odrzuc`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    });
    const data = await res.json();
    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Odrzucono', timer: 1200, showConfirmButton: false });
        location.reload();
    }
}
</script>
@include('biuro.planning.order_modal_js')
@endsection