@extends('layouts.app')

@section('title', 'Ważenia')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.weighings-wrap { padding: 20px; }
.page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
.btn-add-w { padding:9px 18px;background:#3498db;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px; }
.btn-add-w:hover { background:#2980b9; }

.w-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden; }
.w-table { width:100%;border-collapse:collapse;font-size:13px; }
.w-table thead tr { background:#3498db;color:#fff; }
.w-table th { padding:10px 12px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left;white-space:nowrap; }
.w-table td { padding:10px 12px;border-bottom:1px solid #c8cdd3;vertical-align:middle; }
.w-table tr:last-child td { border-bottom:none; }
.w-table tbody tr { cursor:pointer;transition:background .1s; }
.w-table tbody tr:hover td { background:#e8f4fd; }

.cell-dt   { font-weight:700;font-size:13px;white-space:nowrap; }
.cell-time { font-size:11px;color:#aaa; }
.cell-client { font-weight:700;font-size:13px; }
.plates { font-size:12px;font-weight:700;color:#555;white-space:nowrap; }
.nr-rej { display:inline-block;background:#fff;border:2px solid #1a1a1a;padding:1px 5px;border-radius:4px;font-weight:800;font-size:11px; }
.w-val    { font-family:'Barlow Condensed',sans-serif;font-size:17px;font-weight:800; }
.w-val.muted { color:#888; }
.w-result { font-family:'Barlow Condensed',sans-serif;font-size:19px;font-weight:900;color:#2d7a1a; }
.w-result.negative { color:#e74c3c; }
.w-empty { color:#ccc; font-size:12px; }
.row-source-order .cell-client::before {
    content:''; display:inline-block; width:5px; height:5px; border-radius:50%;
    background:#27ae60; margin-right:6px; vertical-align:middle;
}
.row-locked { cursor:default; }
.row-locked:hover td { background:#fff !important; }

/* Statusy — ikony kierowca/plac */
.status-cell {
    border-left: 1px solid #e2e5e9;
    border-right: 1px solid #e2e5e9;
    background: #fafbfc;
    text-align: center;
    white-space: nowrap;
    width: 80px;
}
.status-icons { display:flex; gap:8px; justify-content:center; align-items:center; }
.s-icon {
    display:inline-flex; align-items:center; justify-content:center;
    width:26px; height:26px; border-radius:50%;
    font-size:13px; color:#fff;
}
.s-icon.green { background:#27ae60; }
.s-icon.gray { background:#c8cdd3; }
.s-icon.partial { background:#f39c12; }

.btn-del-w { background:#fdecea;border:1px solid #f5c6cb;border-radius:5px;padding:5px 9px;color:#e74c3c;cursor:pointer;font-size:12px; }
.btn-del-w:hover { background:#e74c3c;color:#fff; }
.btn-arch-w {
    background:#f4f5f7;border:1px solid #dde0e5;border-radius:5px;padding:5px 9px;
    color:#7f8c8d;cursor:pointer;font-size:12px;transition:all .15s;
}
.btn-arch-w:hover { background:#7f8c8d;color:#fff;border-color:#7f8c8d; }
.btn-arch-w:disabled, .btn-del-w:disabled {
    background:#f4f5f7 !important; color:#dde0e5 !important; border-color:#eaecef !important;
    cursor:not-allowed; opacity:.6;
}
.btn-arch-w:disabled:hover, .btn-del-w:disabled:hover {
    background:#f4f5f7 !important; color:#dde0e5 !important; border-color:#eaecef !important;
}
.empty-state { text-align:center;padding:40px;color:#ccc; }
.empty-state i { font-size:36px;margin-bottom:8px;display:block; }

/* Modal – 2x szerszy */
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:12px;width:100%;max-width:1200px;max-height:92vh;overflow-y:auto;padding:28px;box-shadow:0 8px 32px rgba(0,0,0,.2); }
.modal-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:#1a1a1a;margin-bottom:18px;display:flex;justify-content:space-between;align-items:center; }
.modal-close { background:#f0f2f5;border:none;border-radius:50%;width:32px;height:32px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center; }

.m-label { display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:5px; }
.m-input, .m-select { width:100%;padding:9px 11px;border:1.5px solid #dde0e5;border-radius:8px;font-size:15px;font-weight:600;color:#1a1a1a;outline:none;margin-bottom:0; }
.m-input:focus, .m-select:focus { border-color:#3498db; }
.m-input:disabled, .m-select:disabled { background:#f4f5f7;color:#888; }

.m-row-2 { display:grid;grid-template-columns:1fr 1fr;gap:12px; }

.m-result { background:#e8f7e4;border-radius:8px;padding:7px 12px;display:flex;justify-content:space-between;align-items:center;border:1.5px solid #a8d8a8; }
.mr-label { font-size:11px;font-weight:700;color:#2d7a1a;text-transform:uppercase;letter-spacing:.06em; }
.mr-val   { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;color:#2d7a1a; }
.mr-val.neg { color:#e74c3c; }
#wW1.needs-fill, #wGoods.needs-fill { border-color:#e74c3c; box-shadow:0 0 0 2px rgba(231,76,60,.2); }
#wW2.needs-fill { border-color:#e74c3c; box-shadow:0 0 0 2px rgba(231,76,60,.2); }

/* Hakowiec — toggle + 4 pola */
.hakowiec-toggle-row {
    display:flex; align-items:center; justify-content:space-between;
    padding:8px 12px; background:#fff7e6; border:1.5px dashed #e67e22;
    border-radius:8px; margin-bottom:10px;
}
.hakowiec-toggle-row.active { background:#fef5ec; border-style:solid; }
.hakowiec-toggle-label { font-size:12px; font-weight:700; color:#c0392b; display:flex; align-items:center; gap:8px; }
.hakowiec-toggle-label i { font-size:15px; }
.hakowiec-switch { position:relative; width:44px; height:22px; }
.hakowiec-switch input { opacity:0; width:0; height:0; }
.hakowiec-switch .slider {
    position:absolute; cursor:pointer; inset:0; background:#bcc4ce;
    border-radius:22px; transition:.2s;
}
.hakowiec-switch .slider::before {
    position:absolute; content:''; height:18px; width:18px; left:2px; bottom:2px;
    background:#fff; border-radius:50%; transition:.2s;
}
.hakowiec-switch input:checked + .slider { background:#e67e22; }
.hakowiec-switch input:checked + .slider::before { transform:translateX(22px); }

.hakowiec-fields { margin-top:10px; padding-top:10px; border-top:1.5px solid #e2e5e9; }
.hakowiec-row {
    display:grid; grid-template-columns:1fr 1fr auto; gap:10px;
    align-items:center; margin-bottom:8px;
}
.hakowiec-row .h-label-row {
    grid-column:1 / -1; font-size:11px; font-weight:700;
    color:#888; text-transform:uppercase; letter-spacing:.06em;
    display:flex; align-items:center; gap:8px; margin-bottom:2px;
}
.h-input {
    width:100%; padding:8px 10px;
    border:1.5px solid #dde0e5; border-radius:6px;
    font-family:'Barlow Condensed',sans-serif;
    font-size:18px; font-weight:900; text-align:center;
    background:#fff; outline:none;
}
.h-input:focus { border-color:#e67e22; }
.h-input.tara { background:#f4f5f7; color:#666; }
.h-netto {
    font-family:'Barlow Condensed',sans-serif; font-size:14px; font-weight:800;
    color:#2d7a1a; min-width:80px; text-align:right;
}
.h-netto.empty { color:#ccc; }
.h-sum-row {
    display:flex; justify-content:space-between; align-items:center;
    background:#fef5ec; border-radius:6px; padding:6px 12px;
    font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em;
    color:#c0392b; margin-top:6px;
}
.h-sum-val { font-family:'Barlow Condensed',sans-serif; font-size:16px; font-weight:900; color:#1a1a1a; }
#wW1:disabled, #wW2:disabled { background:#f4f5f7; color:#1a1a1a; }

/* Hakowiec — lista tar w kolumnach po pojeździe */
.btn-hak-tary {
    background:#e67e22; border:none; border-radius:6px; padding:5px 12px; color:#fff;
    font-family:'Barlow Condensed',sans-serif; font-size:13px; font-weight:900;
    letter-spacing:.06em; text-transform:uppercase; cursor:pointer;
}
.btn-hak-tary:hover { background:#d35400; }
/* Lista tar — floating panel z prawej strony viewportu, niezależnie od modala */
#hakTaraList {
    position: fixed;
    top: 50%;
    right: 24px;
    transform: translateY(-50%);
    width: 360px;
    max-height: 88vh;
    overflow-y: auto;
    background: #fff;
    border: 2px solid #e67e22;
    border-radius: 12px;
    padding: 14px;
    box-shadow: 0 8px 32px rgba(0,0,0,.3);
    z-index: 1100;
}
.hak-tara-cols {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}
.hak-tara-target {
    text-align: center; font-size: 12px; font-weight: 700;
    color: #e67e22; margin-bottom: 8px; padding-bottom: 8px;
    border-bottom: 1.5px dashed #fcd9b8;
}
.hak-tara-col {
    background:#f8f9fa; border:1px solid #e2e5e9; border-radius:6px; padding:6px;
    display:flex; flex-direction:column;
}
.hak-tara-section + .hak-tara-section {
    margin-top:8px; padding-top:8px; border-top:1.5px dashed #bcc4ce;
}
.hak-tara-col-head {
    text-align:center; font-size:11px; font-weight:900; letter-spacing:.04em;
    background:#fff; border:1.5px solid #1a1a1a; border-radius:4px;
    padding:2px 4px; margin-bottom:6px;
}
.hak-tara-item {
    display:flex; align-items:center; justify-content:space-between;
    width:100%; padding:4px 8px; margin-bottom:2px;
    background:#fff; border:1px solid #dde0e5; border-radius:5px;
    font-family:'Barlow Condensed',sans-serif;
    color:#1a1a1a; cursor:pointer; transition:all .12s;
}
.hak-tara-item:hover { background:#fef5ec; border-color:#e67e22; }
.hak-tara-item .pair { font-size:12px; font-weight:700; color:#444; letter-spacing:.02em; }
.hak-tara-item .weight { font-size:14px; font-weight:900; color:#1a1a1a; text-align:right; white-space:nowrap; }
.hak-tara-item:hover .weight { color:#c0392b; }

.active-orders-label { font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:6px; }
.active-orders { display:flex;flex-wrap:wrap;gap:5px;margin-bottom:0; }
.ao-btn { padding:5px 11px;border:1.5px solid #3498db;border-radius:20px;background:#eaf4fb;color:#2471a3;font-size:12px;font-weight:700;cursor:pointer;transition:all .15s;white-space:nowrap; }
.ao-btn:hover { background:#3498db;color:#fff; }
.ao-btn.selected { background:#3498db;color:#fff; }
.linked-badge { display:inline-flex;align-items:center;gap:6px;background:#e8f7e4;border:1.5px solid #27ae60;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:700;color:#1a7a3a;margin-top:6px; }

.modal-footer { display:flex;gap:10px;justify-content:flex-end;margin-top:18px;padding-top:14px;border-top:1px solid #e2e5e9; }
.btn-cancel { padding:10px 20px;background:#f4f5f7;color:#555;border:1px solid #dde0e5;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer; }
.btn-cancel:hover { background:#e8e9ec; }
.btn-save { padding:10px 24px;background:#3498db;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer; }
.btn-save:hover { background:#2980b9; }
</style>
@endsection

@section('content')
<div id="poll-area" class="weighings-wrap">

    <div class="page-header">
        <div style="display:flex;align-items:center;gap:12px">
            <div class="page-title"><i class="fas fa-weight" style="color:#3498db"></i> Ważenia</div>
            <button class="btn-add-w" onclick="openAddModal()" style="background:#e74c3c">
                <i class="fas fa-plus"></i> Dodaj ważenie
            </button>
        </div>
        <a href="{{ route('biuro.weighings.archived') }}" class="btn-archived-link">
            <i class="fas fa-archive"></i> Archiwum
        </a>
    </div>

    @if($rows->isEmpty())
    <div class="empty-state"><i class="fas fa-weight"></i><p>Brak ważeń</p></div>
    @else
    <div class="w-table-wrap">
        <table class="w-table">
            <thead><tr>
                <th>Data</th>
                <th>Klient</th>
                <th>Pojazdy</th>
                <th>Brutto</th>
                <th>Tara</th>
                <th>Netto</th>
                <th>Towar</th>
                <th>Uwagi</th>
                <th class="status-cell" style="background:#2980b9;border-color:#2980b9">Status</th>
                <th style="width:90px">Akcje</th>
            </tr></thead>
            <tbody>
            @foreach($rows as $r)
            @php
                $rowKey = $r->source.'-'.$r->id;
                $bothGreen = $r->source === 'order' && $r->has_weight && $r->plac_closed;
                $hasAnyWeight = $r->has_weight || ($r->has_partial ?? false);
                $deleteEnabled = $r->source === 'weighing'
                    ? true
                    : ($hasAnyWeight && ! $r->plac_closed);
                $archiveEnabled = $r->source === 'weighing'
                    ? true
                    : ($r->has_weight && $r->plac_closed);
                $driverIconClass = $r->has_weight ? 'green' : (($r->has_partial ?? false) ? 'partial' : 'gray');
                $driverIconTitle = $r->has_weight
                    ? 'Zważone'
                    : (($r->has_partial ?? false) ? 'Ważenie częściowe' : 'Brak wagi');
                $placIcon = $r->type === 'sale' ? 'fa-truck-loading' : 'fa-boxes';
                $placTitleDone = $r->type === 'sale' ? 'Załadowane' : 'Dostarczone';
                $placTitleOpen = $r->type === 'sale' ? 'Załadunek otwarty' : 'Dostawa otwarta';
            @endphp
            <tr id="row-{{ $rowKey }}"
                class="row-source-{{ $r->source }} {{ $bothGreen ? 'row-locked' : '' }}"
                data-source="{{ $r->source }}" data-id="{{ $r->id }}" data-locked="{{ $bothGreen ? '1' : '0' }}"
                onclick="rowClick(this)">
                <td>
                    <div class="cell-dt">{{ $r->date?->format('d.m.Y') ?? '–' }}</div>
                    <div class="cell-time">{{ $r->time_at?->format('H:i') }}</div>
                </td>
                <td class="cell-client">
                    @if($r->type)
                        <span style="color:{{ $r->type==='sale' ? '#f39c12' : '#27ae60' }};margin-right:4px">
                            {{ $r->type==='sale' ? '↑' : '↓' }}
                        </span>
                    @endif
                    {{ $r->client?->short_name ?? '–' }}
                </td>
                <td class="plates">
                    @if($r->plate1)<span class="nr-rej">{{ $r->plate1 }}</span>@endif
                    @if($r->plate2) <span class="nr-rej">{{ $r->plate2 }}</span>@endif
                </td>
                <td>
                    @if($r->brutto !== null)
                        <span class="w-val">{{ number_format($r->brutto, 3, ',', '') }}</span>
                    @else<span class="w-empty">–</span>@endif
                </td>
                <td>
                    @if($r->tara !== null)
                        <span class="w-val muted">{{ number_format($r->tara, 3, ',', '') }}</span>
                    @else<span class="w-empty">–</span>@endif
                </td>
                <td>
                    @if($r->netto !== null)
                        <span class="w-result {{ $r->netto < 0 ? 'negative' : '' }}">{{ number_format($r->netto, 3, ',', '') }}</span>
                    @else<span class="w-empty">–</span>@endif
                </td>
                <td style="font-size:12px;color:#555;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $r->goods }}">
                    {{ $r->goods ?? '–' }}
                </td>
                <td style="font-size:12px;color:#888;max-width:120px" title="{{ $r->notes }}">
                    {{ Str::limit($r->notes, 40) }}
                </td>

                {{-- Statusy --}}
                <td class="status-cell">
                    @if($r->source === 'order')
                    <div class="status-icons">
                        <span class="s-icon {{ $driverIconClass }}" title="{{ $driverIconTitle }}">
                            <i class="fas fa-weight"></i>
                        </span>
                        <span class="s-icon {{ $r->plac_closed ? 'green' : 'gray' }}" title="{{ $r->plac_closed ? $placTitleDone : $placTitleOpen }}">
                            <i class="fas {{ $placIcon }}"></i>
                        </span>
                    </div>
                    @endif
                </td>

                {{-- Akcje --}}
                <td onclick="event.stopPropagation()">
                    <div style="display:flex;gap:4px">
                        @if($deleteEnabled)
                        <button class="btn-del-w" onclick="deleteRow('{{ $r->source }}', {{ $r->id }})" title="Usuń wagę">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        @else
                        <button class="btn-del-w" disabled title="Usuń (niedostępne)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        @endif

                        <button class="btn-arch-w"
                                onclick="archiveRow('{{ $r->source }}', {{ $r->id }})"
                                {{ $archiveEnabled ? '' : 'disabled' }}
                                title="{{ $archiveEnabled ? 'Archiwizuj' : 'Archiwum dostępne po zamknięciu placu' }}">
                            <i class="fas fa-archive"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Modal --}}
<div class="modal-overlay" id="weighModal">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-title">
            <span id="modalTitle">Dodaj ważenie</span>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>

        <input type="hidden" id="wId">
        <input type="hidden" id="wOrderId">
        <input type="hidden" id="wSource" value="weighing">

        <div style="display:flex;flex-direction:column;gap:12px">
            <div>

                {{-- Aktywne zlecenia --}}
                <div style="margin-bottom:8px" id="activeOrdersWrap">
                    <div class="active-orders-label">Aktywne zlecenia (bez wagi)</div>
                    <div class="active-orders" style="margin-bottom:20px">
                        @foreach($activeOrders as $ao)
                        <button type="button" class="ao-btn"
                                data-client="{{ $ao->client_id }}"
                                data-order="{{ $ao->id }}"
                                data-plate1="{{ $ao->tractor?->plate }}"
                                data-plate2="{{ $ao->trailer?->plate }}"
                                data-tractor-tara="{{ $ao->tractor?->tare_kg }}"
                                data-trailer-tara="{{ $ao->trailer?->tare_kg }}"
                                data-tractor-subtype="{{ $ao->tractor?->subtype }}"
                                data-trailer-subtype="{{ $ao->trailer?->subtype }}"
                                data-type="{{ $ao->type }}"
                                data-client-name="{{ $ao->client?->short_name }}"
                                data-date="{{ $ao->planned_date->format('d.m') }}"
                                data-goods="{{ $ao->loadingItems->pluck('fraction.name')->filter()->unique()->implode(', ') ?: $ao->fractions_note }}"
                                onclick="selectActiveOrder(this)">
                            <span style="color:{{ $ao->type==='sale'?'#f39c12':'#27ae60' }}">{{ $ao->type==='sale'?'↑':'↓' }}</span>
                            {{ $ao->client?->short_name ?? '?' }}
                            <span style="opacity:.6;font-size:10px"> {{ $ao->planned_date->format('d.m') }}</span>
                        </button>
                        @endforeach
                    </div>
                    <div id="linkedBadge" style="display:none;align-items:center;gap:6px;background:#e8f7e4;border:1.5px solid #27ae60;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:700;color:#1a7a3a;margin-top:6px">
                        <i class="fas fa-link"></i>
                        <span id="linkedText"></span>
                    </div>
                </div>

                {{-- Wozacy + Skróty --}}
                <div style="display:flex;gap:12px;margin-bottom:8px;align-items:flex-start" id="haulerShortcutsWrap">
                    @if($haulers->isNotEmpty())
                    <div style="flex:1;min-width:0">
                        <div class="active-orders-label">Woźacy</div>
                        <div style="display:flex;flex-wrap:wrap;gap:5px">
                            @foreach($haulers as $h)
                            <button type="button"
                                    style="padding:5px 14px;border:1.5px solid #e67e22;border-radius:20px;background:#fef5ec;color:#c0392b;font-size:12px;font-weight:700;cursor:pointer;transition:all .15s"
                                    onmouseover="this.style.background='#e67e22';this.style.color='#fff'"
                                    onmouseout="this.style.background='#fef5ec';this.style.color='#c0392b'"
                                    onclick="document.getElementById('wClient').value='{{ $h->client_id }}'">
                                {{ $h->client?->short_name }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div style="flex:1;min-width:0">
                        <div class="active-orders-label">Skróty</div>
                        <div style="display:flex;flex-wrap:wrap;gap:5px">
                            <button type="button"
                                    style="padding:5px 14px;border:1.5px solid #27ae60;border-radius:20px;background:#e8f7e4;color:#1a7a3a;font-size:12px;font-weight:700;cursor:pointer;transition:all .15s"
                                    onmouseover="this.style.background='#27ae60';this.style.color='#fff'"
                                    onmouseout="this.style.background='#e8f7e4';this.style.color='#1a7a3a'"
                                    onclick="WeighingShortcuts.recykler()">
                                Recykler
                            </button>
                        </div>
                    </div>
                </div>

                <div class="m-row-2" style="margin-bottom:8px">
                    <div>
                        <label class="m-label">Klient</label>
                        <select id="wClient" class="m-select">
                            <option value="">– wybierz –</option>
                            @foreach($clients as $c)
                            <option value="{{ $c->id }}">{{ $c->short_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="m-label">Data i godzina</label>
                        <input type="datetime-local" id="wDate" class="m-input">
                    </div>
                </div>

                {{-- Pasek z przyciskiem TARY (dostępny zawsze) --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;padding:8px 12px;background:#f8f9fa;border:1px solid #e2e5e9;border-radius:8px">
                    <span style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#888">Tary z bazy zestawów</span>
                    <button type="button" onclick="toggleHakTaraList()" class="btn-hak-tary">
                        <i class="fas fa-weight-hanging"></i> TARY
                    </button>
                </div>
                <div id="hakTaraList" style="display:none">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;padding-bottom:8px;border-bottom:1.5px dashed #fcd9b8">
                        <span style="font-size:12px;font-weight:900;color:#e67e22;text-transform:uppercase;letter-spacing:.06em">
                            <i class="fas fa-weight-hanging"></i> Tary
                        </span>
                        <button type="button" onclick="toggleHakTaraList()"
                                style="background:#f4f5f7;border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;color:#888"
                                title="Zamknij">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="hak-tara-target" id="hakTaraTarget">Klik tary → ciągnik</div>
                    <div class="hak-tara-cols" id="hakTaraCols"></div>
                </div>

                {{-- Toggle hakowca --}}
                <div class="hakowiec-toggle-row" id="hakowiecRow">
                    <span class="hakowiec-toggle-label">
                        <i class="fas fa-truck-pickup"></i> Hakowiec — 2 ważenia (ciągnik + naczepa)
                    </span>
                    <label class="hakowiec-switch">
                        <input type="checkbox" id="hakowiecToggle" onchange="onHakowiecToggle()">
                        <span class="slider"></span>
                    </label>
                </div>

                {{-- Sekcja 4 pól (ukryta domyślnie) --}}
                <div id="hakowiecFields" class="hakowiec-fields" style="display:none;background:#fff;border:1.5px solid #fcd9b8;border-radius:10px;padding:12px 14px;margin-bottom:10px">
                    {{-- Wiersz 1: ciągnik --}}
                    <div class="h-label-row">
                        <i class="fas fa-truck" style="color:#e67e22"></i>
                        <span>Ciągnik</span>
                        <span id="hCPlateLabel" style="color:#888;font-weight:600;letter-spacing:0;text-transform:none"></span>
                    </div>
                    <div class="hakowiec-row">
                        <div>
                            <label class="m-label" style="margin-bottom:3px">Brutto [t]</label>
                            <input type="number" id="hCBrutto" class="h-input" step="0.001" min="0" oninput="onHakowiecCalc()">
                        </div>
                        <div>
                            <label class="m-label" style="margin-bottom:3px">Tara [t]</label>
                            <input type="number" id="hCTara" class="h-input tara" step="0.001" min="0" oninput="onHakowiecCalc()">
                        </div>
                        <div style="text-align:center">
                            <div class="m-label" style="margin-bottom:3px">Netto</div>
                            <span class="h-netto empty" id="hCNetto">–</span>
                        </div>
                    </div>

                    {{-- Wiersz 2: naczepa --}}
                    <div class="h-label-row" style="margin-top:8px">
                        <i class="fas fa-trailer" style="color:#e67e22"></i>
                        <span>Naczepa</span>
                        <span id="hNPlateLabel" style="color:#888;font-weight:600;letter-spacing:0;text-transform:none"></span>
                    </div>
                    <div class="hakowiec-row">
                        <div>
                            <label class="m-label" style="margin-bottom:3px">Brutto [t]</label>
                            <input type="number" id="hNBrutto" class="h-input" step="0.001" min="0" oninput="onHakowiecCalc()">
                        </div>
                        <div>
                            <label class="m-label" style="margin-bottom:3px">Tara [t]</label>
                            <input type="number" id="hNTara" class="h-input tara" step="0.001" min="0" oninput="onHakowiecCalc()">
                        </div>
                        <div style="text-align:center">
                            <div class="m-label" style="margin-bottom:3px">Netto</div>
                            <span class="h-netto empty" id="hNNetto">–</span>
                        </div>
                    </div>

                    {{-- Sumy --}}
                    <div class="h-sum-row">
                        <span>Razem brutto / tara → Wagi</span>
                        <span><span class="h-sum-val" id="hSumBrutto">–</span> / <span class="h-sum-val" id="hSumTara">–</span> t</span>
                    </div>
                </div>

                <div style="background:#dde3ea;border-radius:10px;padding:14px 16px;margin-bottom:8px;border:2px solid #bcc4ce">
                    <div style="margin-bottom:10px">
                        <span style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#555">Wskazania wagi</span>
                    </div>
                    <div class="m-row-2">
                        <div>
                            <label class="m-label" id="wW1Label" style="color:#3498db;font-size:12px">Waga 1 [t]</label>
                            <input type="number" id="wW1" class="m-input" step="0.001" min="0" oninput="calcResult()"
                                   style="font-size:22px;font-weight:900;font-family:'Barlow Condensed',sans-serif;padding:12px 14px;border-width:2px">
                        </div>
                        <div>
                            <label class="m-label" id="wW2Label" style="color:#3498db;font-size:12px">Waga 2 [t]</label>
                            <input type="number" id="wW2" class="m-input" step="0.001" min="0" oninput="calcResult()"
                                   style="font-size:22px;font-weight:900;font-family:'Barlow Condensed',sans-serif;padding:12px 14px;border-width:2px">
                        </div>
                    </div>
                </div>

                <div class="m-result" id="resultBox">
                    <span class="mr-label" id="resultLabel">Wynik (Waga 1 – Waga 2)</span>
                    <span class="mr-val" id="resultVal">–</span>
                </div>

                <div>
                    <label class="m-label">Towar <span id="goodsHint" style="color:#e74c3c;font-weight:700;display:none">(wymagany przy obu wagach)</span></label>
                    <input type="text" id="wGoods" class="m-input">
                </div>

                <div class="m-row-2">
                    <div>
                        <label class="m-label">Nr rej. 1</label>
                        <input type="text" id="wPlate1" class="m-input" style="text-transform:uppercase">
                    </div>
                    <div>
                        <label class="m-label">Nr rej. 2 (naczepa)</label>
                        <input type="text" id="wPlate2" class="m-input" style="text-transform:uppercase">
                    </div>
                </div>

                <div>
                    <label class="m-label">Uwagi</label>
                    <textarea id="wNotes" class="m-input" rows="2" style="resize:none"></textarea>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal()">Anuluj</button>
            <button class="btn-save" onclick="saveWeighing()">
                <i class="fas fa-check"></i> Zapisz
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/weighings_shortcuts.js') }}?v={{ filemtime(public_path('js/weighings_shortcuts.js')) }}"></script>
<script>
const CSRF = '{{ csrf_token() }}';
let _editId = null;
let _editSource = null; // 'weighing' | 'order'
let _orderType = null;  // 'pickup' | 'sale' | null

/* ─── Hakowiec — lista tar w kolumnach po pojeździe ─── */
async function toggleHakTaraList() {
    const list = document.getElementById('hakTaraList');
    if (list.style.display !== 'none') {
        list.style.display = 'none';
        return;
    }
    if (_tareCache.length === 0) {
        try {
            const res  = await fetch('/biuro/weighings/all-tares', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            data.sets.forEach(s => _tareCache.push(s));
        } catch(e) { return; }
    }
    renderHakTaraColumns();
    updateHakTaraTarget();
    list.style.display = 'block';
}

// Stałe przypisanie sekcji do kolumn — grupowanie po pierwszej części labela (przed " / ").
// Sekcje nie wymienione tutaj trafiają do kolumny ZS992RM (REST_COLUMN_INDEX).
const HAK_COLUMNS = [
    ['WGM3595C', 'WGM2125P'],
    ['PNT81294', 'ZS438MG'],
    ['WGM0958F', 'Nissan', 'Toyota'],
    ['WGM2624C'],
    ['ZS992RM'],
];
const REST_COLUMN_INDEX = 4; // indeks kolumny ZS992RM, do której idą nieprzypisane

function renderHakTaraColumns() {
    // Grupuj wpisy po pierwszej części labela (przed " / "); wpisy bez "/" → cały label
    const byHead = {};
    _tareCache.forEach(s => {
        const head = s.label.split(' / ')[0].trim();
        if (!byHead[head]) byHead[head] = [];
        byHead[head].push({ label: s.label, tare_kg: s.tare_kg });
    });

    // Sekcje nieprzewidziane w HAK_COLUMNS → do kolumny ZS992RM
    const fixedSet = new Set(HAK_COLUMNS.flat());
    const restHeads = Object.keys(byHead).filter(h => !fixedSet.has(h)).sort();
    const columns = HAK_COLUMNS.map(c => [...c]);
    if (restHeads.length > 0) {
        columns[REST_COLUMN_INDEX].push(...restHeads);
    }

    const container = document.getElementById('hakTaraCols');
    container.innerHTML = '';

    columns.forEach(heads => {
        const col = document.createElement('div');
        col.className = 'hak-tara-col';

        heads.forEach(head => {
            const items = byHead[head];
            if (!items || items.length === 0) return;
            const section = document.createElement('div');
            section.className = 'hak-tara-section';
            const headEl = document.createElement('div');
            headEl.className = 'hak-tara-col-head';
            headEl.textContent = head;
            section.appendChild(headEl);
            items.forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'hak-tara-item';
                const tareTons = parseFloat(item.tare_kg).toFixed(3).replace('.', ',');
                btn.innerHTML = `<span class="pair">${item.label}</span><span class="weight">${tareTons} t</span>`;
                btn.onclick = () => selectHakTara(item.tare_kg);
                section.appendChild(btn);
            });
            col.appendChild(section);
        });

        if (col.children.length === 0) {
            col.style.opacity = '.4';
            col.innerHTML = '<div style="color:#ccc;text-align:center;font-size:11px;padding:8px">brak</div>';
        }
        container.appendChild(col);
    });
}

// Jawny target ustawiany gdy operator kliknie/sfokusuje pole tary.
// Konsumowany po jednym kliku TARY. Bez tego — domyślnie pierwsze puste pole, fallback do ciągnika.
let _hakTaraExplicitTarget = null;

function selectHakTara(tareKg) {
    const tareTons = parseFloat(tareKg).toFixed(3);
    const isHakowiec = document.getElementById('hakowiecToggle').checked;

    if (isHakowiec) {
        const cTara = document.getElementById('hCTara');
        const nTara = document.getElementById('hNTara');
        let target = _hakTaraExplicitTarget;
        if (!target) {
            // Auto: pierwsze puste pole; jeśli oba wypełnione — nadpisuje ciągnik
            if (!cTara.value.trim()) target = 'cTara';
            else if (!nTara.value.trim()) target = 'nTara';
            else target = 'cTara';
        }
        const el = target === 'nTara' ? nTara : cTara;
        el.value = tareTons;
        _hakTaraExplicitTarget = null; // konsumujemy
        onHakowiecCalc();
    } else {
        // Bez hakowca — tara idzie do właściwego pola wagowego zależnie od typu
        if (_orderType === 'sale') {
            document.getElementById('wW1').value = tareTons;
        } else {
            document.getElementById('wW2').value = tareTons;
        }
        document.getElementById('hakTaraList').style.display = 'none';
        calcResult();
    }
    updateHakTaraTarget();
}

function updateHakTaraTarget() {
    const target = document.getElementById('hakTaraTarget');
    if (!target) return;
    const isHakowiec = document.getElementById('hakowiecToggle').checked;

    if (!isHakowiec) {
        const fieldName = _orderType === 'sale' ? 'Waga 1 (Tara)' : 'Waga 2 (Tara)';
        target.textContent = 'Klik tary → ' + fieldName;
        target.style.color = '#888';
        return;
    }
    // Jawny target wybrany przez operatora (focus na polu) ma priorytet
    if (_hakTaraExplicitTarget === 'cTara') {
        target.textContent = '→ ciągnik (wybrane ręcznie)';
        target.style.color = '#e67e22';
        return;
    }
    if (_hakTaraExplicitTarget === 'nTara') {
        target.textContent = '→ naczepa (wybrane ręcznie)';
        target.style.color = '#e67e22';
        return;
    }
    const cFilled = document.getElementById('hCTara').value.trim() !== '';
    const nFilled = document.getElementById('hNTara').value.trim() !== '';
    if (!cFilled) {
        target.textContent = 'Klik tary → ciągnik';
        target.style.color = '#888';
    } else if (!nFilled) {
        target.textContent = 'Klik tary → naczepa';
        target.style.color = '#888';
    } else {
        target.textContent = 'Obie tary uzupełnione — klik nadpisze ciągnik (lub kliknij pole aby wybrać)';
        target.style.color = '#27ae60';
    }
}

// Focus na polu tary = jawny target (priorytet dla najbliższego klika TARY)
document.addEventListener('DOMContentLoaded', () => {
    const c = document.getElementById('hCTara');
    const n = document.getElementById('hNTara');
    if (c) c.addEventListener('focus', () => { _hakTaraExplicitTarget = 'cTara'; updateHakTaraTarget(); });
    if (n) n.addEventListener('focus', () => { _hakTaraExplicitTarget = 'nTara'; updateHakTaraTarget(); });
});

/* ─── Hakowiec — toggle + 4 pola ─── */
function onHakowiecToggle() {
    const on = document.getElementById('hakowiecToggle').checked;
    document.getElementById('hakowiecFields').style.display = on ? '' : 'none';
    document.getElementById('hakowiecRow').classList.toggle('active', on);
    document.getElementById('wW1').disabled = on;
    document.getElementById('wW2').disabled = on;
    if (!on) {
        // Wyczyść i przelicz Waga1/Waga2 jako wolne pola
        ['hCBrutto','hCTara','hNBrutto','hNTara'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('hCNetto').textContent = '–';
        document.getElementById('hNNetto').textContent = '–';
        document.getElementById('hSumBrutto').textContent = '–';
        document.getElementById('hSumTara').textContent = '–';
    } else {
        onHakowiecCalc();
    }
    calcResult();
}

function onHakowiecCalc() {
    const cb = parseFloat(document.getElementById('hCBrutto').value);
    const ct = parseFloat(document.getElementById('hCTara').value);
    const nb = parseFloat(document.getElementById('hNBrutto').value);
    const nt = parseFloat(document.getElementById('hNTara').value);

    // Netto per element
    const cNetto = (!isNaN(cb) && !isNaN(ct)) ? Math.round((cb - ct) * 1000) / 1000 : null;
    const nNetto = (!isNaN(nb) && !isNaN(nt)) ? Math.round((nb - nt) * 1000) / 1000 : null;
    const cN = document.getElementById('hCNetto');
    const nN = document.getElementById('hNNetto');
    cN.textContent = cNetto !== null ? cNetto.toFixed(3).replace('.', ',') : '–';
    nN.textContent = nNetto !== null ? nNetto.toFixed(3).replace('.', ',') : '–';
    cN.classList.toggle('empty', cNetto === null);
    nN.classList.toggle('empty', nNetto === null);

    // Sumy
    const allFilled = !isNaN(cb) && !isNaN(ct) && !isNaN(nb) && !isNaN(nt);
    const sumBrutto = allFilled ? Math.round((cb + nb) * 1000) / 1000 : null;
    const sumTara = allFilled ? Math.round((ct + nt) * 1000) / 1000 : null;
    document.getElementById('hSumBrutto').textContent = sumBrutto !== null ? sumBrutto.toFixed(3).replace('.', ',') : '–';
    document.getElementById('hSumTara').textContent = sumTara !== null ? sumTara.toFixed(3).replace('.', ',') : '–';

    // Wpisz do Waga1/Waga2 zgodnie z typem zlecenia
    if (allFilled) {
        const w1El = document.getElementById('wW1');
        const w2El = document.getElementById('wW2');
        if (_orderType === 'sale') {
            w1El.value = sumTara.toFixed(3);
            w2El.value = sumBrutto.toFixed(3);
        } else {
            // pickup lub bez zlecenia: brutto=W1, tara=W2
            w1El.value = sumBrutto.toFixed(3);
            w2El.value = sumTara.toFixed(3);
        }
        calcResult();
    }

    if (typeof updateHakTaraTarget === 'function') updateHakTaraTarget();
}

function maybeAutoEnableHakowiec(btn) {
    const isHakowiec = btn.dataset.tractorSubtype === 'hakowiec' || btn.dataset.trailerSubtype === 'hakowiec';
    const toggle = document.getElementById('hakowiecToggle');
    toggle.checked = isHakowiec;
    onHakowiecToggle();

    if (isHakowiec) {
        // Pola tara zostają puste — operator wpisuje świadomie (kontener może być różny dla hakowca)
        document.getElementById('hCTara').value = '';
        document.getElementById('hNTara').value = '';
        document.getElementById('hCPlateLabel').textContent = btn.dataset.plate1 ? '['+btn.dataset.plate1+']' : '';
        document.getElementById('hNPlateLabel').textContent = btn.dataset.plate2 ? '['+btn.dataset.plate2+']' : '';
        onHakowiecCalc();
    }
}

function rowClick(tr) {
    if (tr.dataset.locked === '1') {
        rowLockedAlert();
    } else {
        openEditModal(tr.dataset.source, parseInt(tr.dataset.id, 10));
    }
}

function rowLockedAlert() {
    Swal.fire({
        icon: 'info',
        title: 'Ważenie zamknięte',
        html: 'Zlecenie ma wagę przekazaną i plac zamknięty.<br><small style="color:#888">Aby edytować — najpierw cofnij wagę przyciskiem 🗑 (gdy plac jeszcze otwarty) lub przywróć dostawę z raportu.</small>',
        timer: 3500,
        showConfirmButton: false,
    });
}

function openAddModal() {
    _editId = null;
    _editSource = null;
    _orderType = null;
    document.getElementById('modalTitle').textContent = 'Dodaj ważenie';
    document.getElementById('wId').value      = '';
    document.getElementById('wOrderId').value = '';
    document.getElementById('wSource').value  = 'weighing';
    document.getElementById('wClient').value  = '';
    document.getElementById('wDate').value    = new Date().toISOString().slice(0,16);
    document.getElementById('wPlate1').value  = '';
    document.getElementById('wPlate2').value  = '';
    document.getElementById('wW1').value      = '';
    document.getElementById('wW2').value      = '';
    document.getElementById('wGoods').value   = '';
    document.getElementById('wNotes').value   = '';
    document.getElementById('resultVal').textContent = '–';
    document.getElementById('resultVal').className   = 'mr-val';
    document.querySelectorAll('.ao-btn').forEach(b => b.classList.remove('selected'));
    document.getElementById('linkedBadge').style.display = 'none';
    document.getElementById('activeOrdersWrap').style.display = '';
    document.getElementById('haulerShortcutsWrap').style.display = '';
    setWeightLabels(null);
    setOrderEditMode(false);
    document.getElementById('hakowiecToggle').checked = false;
    onHakowiecToggle();
    _hakTaraExplicitTarget = null;
    document.getElementById('weighModal').classList.add('open');
}

function openEditModal(source, id) {
    _editId = id;
    _editSource = source;
    document.getElementById('modalTitle').textContent = 'Edytuj ważenie';
    const url = source === 'order'
        ? `/biuro/weighings/orders/${id}/edit`
        : `/biuro/weighings/${id}/edit`;
    fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(d => {
            document.getElementById('wId').value      = d.id;
            document.getElementById('wSource').value  = d.source;
            document.getElementById('wClient').value  = d.client_id ?? '';
            document.getElementById('wDate').value    = d.weighed_at_input ?? new Date().toISOString().slice(0,16);
            document.getElementById('wPlate1').value  = d.plate1 ?? '';
            document.getElementById('wPlate2').value  = d.plate2 ?? '';
            document.getElementById('wW1').value      = d.weight1 ?? '';
            document.getElementById('wW2').value      = d.weight2 ?? '';
            document.getElementById('wGoods').value   = d.goods ?? '';
            document.getElementById('wNotes').value   = d.notes ?? '';
            document.getElementById('wOrderId').value = d.order_id ?? '';
            _orderType = d.order_type ?? null;

            const badge = document.getElementById('linkedBadge');
            const text  = document.getElementById('linkedText');
            if (d.order_id) {
                const arrow = d.order_type === 'sale' ? '↑' : '↓';
                text.textContent = 'POWIĄZANE: ' + arrow + ' ' + (d.order_label ?? '#' + d.order_id);
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }

            setWeightLabels(d.order_type);
            setOrderEditMode(d.source === 'order');
            // Edycja zawsze startuje z toggle off — w bazie mamy tylko sumy, brak rozbicia na hakowca
            document.getElementById('hakowiecToggle').checked = false;
            onHakowiecToggle();
            _hakTaraExplicitTarget = null;
            calcResult();
            document.getElementById('weighModal').classList.add('open');
        });
}

/**
 * Tryb edycji wpisu z orders: ukrywa listę aktywnych zleceń i blokuje pola
 * niezwiązane z wagą (klient, data, pojazdy, towar — pochodzą ze zlecenia).
 */
function setOrderEditMode(isOrder) {
    document.getElementById('activeOrdersWrap').style.display = isOrder ? 'none' : '';
    document.getElementById('haulerShortcutsWrap').style.display = isOrder ? 'none' : '';
    const lockFields = ['wClient', 'wDate', 'wPlate1', 'wPlate2', 'wGoods', 'wNotes'];
    lockFields.forEach(id => { document.getElementById(id).disabled = isOrder; });
}

function setWeightLabels(orderType) {
    const w1Label = document.getElementById('wW1Label');
    const w2Label = document.getElementById('wW2Label');
    const resultLabel = document.getElementById('resultLabel');
    if (orderType === 'pickup') {
        w1Label.textContent = 'Brutto [t]';
        w2Label.textContent = 'Tara [t]';
        resultLabel.textContent = 'Netto (Brutto – Tara)';
    } else if (orderType === 'sale') {
        w1Label.textContent = 'Tara [t]';
        w2Label.textContent = 'Brutto [t]';
        resultLabel.textContent = 'Netto (Brutto – Tara)';
    } else {
        w1Label.textContent = 'Waga 1 [t]';
        w2Label.textContent = 'Waga 2 [t]';
        resultLabel.textContent = 'Wynik (Waga 1 – Waga 2)';
    }
}

function closeModal() {
    document.getElementById('weighModal').classList.remove('open');
}

async function selectActiveOrder(btn) {
    document.querySelectorAll('.ao-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    document.getElementById('wClient').value  = btn.dataset.client;
    document.getElementById('wOrderId').value = btn.dataset.order;
    _orderType = btn.dataset.type;

    const plate1 = btn.dataset.plate1 ? btn.dataset.plate1.toUpperCase() : '';
    const plate2 = btn.dataset.plate2 ? btn.dataset.plate2.toUpperCase() : '';
    if (plate1) document.getElementById('wPlate1').value = plate1;
    if (plate2) document.getElementById('wPlate2').value = plate2;

    if (btn.dataset.goods) document.getElementById('wGoods').value = btn.dataset.goods;

    setWeightLabels(btn.dataset.type);
    maybeAutoEnableHakowiec(btn);

    const badge = document.getElementById('linkedBadge');
    const text  = document.getElementById('linkedText');
    const arrow = btn.dataset.type === 'sale' ? '↑' : '↓';
    text.textContent = 'POWIĄZANE: ' + arrow + ' ' + btn.dataset.clientName + ' · ' + btn.dataset.date;
    badge.style.display = 'inline-flex';

    // Tara z vehicle_set tylko gdy NIE hakowiec (hakowiec ma własne tary per pojazd)
    if (plate1 && !document.getElementById('hakowiecToggle').checked) {
        try {
            const params = new URLSearchParams({ plate1, plate2 });
            const res  = await fetch('/biuro/weighings/tare-for-vehicles?' + params, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.found && data.tare) {
                const tareVal = parseFloat(data.tare).toFixed(3);
                if (btn.dataset.type === 'sale') {
                    document.getElementById('wW1').value = tareVal;
                } else {
                    document.getElementById('wW2').value = tareVal;
                }
                calcResult();
            }
        } catch(e) { /* brak tary */ }
    }
}

const _tareCache = [];

function calcResult() {
    const w1El = document.getElementById('wW1');
    const w2El = document.getElementById('wW2');
    const w1   = parseFloat(w1El.value);
    const w2   = parseFloat(w2El.value);
    const val  = document.getElementById('resultVal');
    const goodsHint = document.getElementById('goodsHint');
    const goodsEl = document.getElementById('wGoods');

    const w1filled = !isNaN(w1) && w1El.value.trim() !== '';
    const w2filled = !isNaN(w2) && w2El.value.trim() !== '';
    w1El.classList.toggle('needs-fill', !w1filled && w2filled);
    w2El.classList.toggle('needs-fill', w1filled && !w2filled);

    const orderId = document.getElementById('wOrderId').value.trim();
    const showGoodsHint = w1filled && w2filled && !orderId;
    goodsHint.style.display = showGoodsHint ? '' : 'none';
    goodsEl.classList.toggle('needs-fill', showGoodsHint && !goodsEl.value.trim());

    if (w1filled && w2filled) {
        const r = Math.round(Math.abs(w1 - w2) * 1000) / 1000;
        val.textContent = r.toFixed(3).replace('.', ',') + ' t';
        val.className   = 'mr-val';
    } else {
        val.textContent = '–';
        val.className   = 'mr-val';
    }
}

async function saveWeighing() {
    const w1      = document.getElementById('wW1').value.trim();
    const w2      = document.getElementById('wW2').value.trim();
    const orderId = document.getElementById('wOrderId').value.trim();
    const goods   = document.getElementById('wGoods').value.trim();
    const source  = document.getElementById('wSource').value || 'weighing';

    if (!w1) {
        Swal.fire({ icon: 'warning', title: 'Podaj Wagę 1', timer: 1800, showConfirmButton: false });
        return;
    }

    // Walidacja hakowca: wszystkie 4 pola wymagane
    if (document.getElementById('hakowiecToggle').checked) {
        const fields = ['hCBrutto','hCTara','hNBrutto','hNTara'];
        for (const id of fields) {
            const el = document.getElementById(id);
            if (!el.value.trim() || isNaN(parseFloat(el.value))) {
                Swal.fire({ icon: 'warning', title: 'Hakowiec — uzupełnij wszystkie 4 pola', timer: 2200, showConfirmButton: false });
                el.focus();
                return;
            }
        }
    }

    // Hakowiec — dopisz rozbicie do uwag (#1: brutto-tara=netto, #2: ...)
    if (document.getElementById('hakowiecToggle').checked) {
        const cb = Math.round(parseFloat(document.getElementById('hCBrutto').value) * 1000);
        const ct = Math.round(parseFloat(document.getElementById('hCTara').value) * 1000);
        const nb = Math.round(parseFloat(document.getElementById('hNBrutto').value) * 1000);
        const nt = Math.round(parseFloat(document.getElementById('hNTara').value) * 1000);
        const lines = `#1: ${cb}-${ct}=${cb-ct}\n#2: ${nb}-${nt}=${nb-nt}`;
        const notesEl = document.getElementById('wNotes');
        const existing = notesEl.value.trim();
        notesEl.value = existing ? existing + '\n' + lines : lines;
    }

    // Walidacja: oba wagi + brak orderu → wymagany towar
    if (w1 && w2 && !orderId && !goods) {
        Swal.fire({ icon: 'warning', title: 'Podaj towar', text: 'Towar jest wymagany gdy wpisane są obie wagi.', timer: 2200, showConfirmButton: false });
        document.getElementById('wGoods').focus();
        return;
    }

    // Edycja wpisu z orders → uderzamy w nowy endpoint (dopuszcza częściowe ważenie — tylko w1)
    if (_editId && source === 'order') {
        const res = await fetch(`/biuro/weighings/orders/${_editId}`, {
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                weight1: w1,
                weight2: w2 || null,
                notes: document.getElementById('wNotes').value || null,
            }),
        });
        const data = await res.json();
        if (data.success) {
            closeModal();
            Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1200, showConfirmButton: false });
            setTimeout(() => location.reload(), 1200);
        } else {
            const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
            Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
        }
        return;
    }

    // Dodawanie nowego lub edycja luźnego ważenia
    const payload = {
        weighed_at:    document.getElementById('wDate').value,
        client_id:     document.getElementById('wClient').value || null,
        order_id:      orderId || null,
        plate1:        document.getElementById('wPlate1').value || null,
        plate2:        document.getElementById('wPlate2').value || null,
        weight1:       w1,
        weight2:       w2 || null,
        goods:         goods || null,
        notes:         document.getElementById('wNotes').value || null,
    };

    const url    = _editId ? `/biuro/weighings/${_editId}` : '/biuro/weighings';
    const method = _editId ? 'PUT' : 'POST';
    const res    = await fetch(url, {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload),
    });
    const data = await res.json();
    if (data.success) {
        closeModal();
        Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1200, showConfirmButton: false });
        setTimeout(() => location.reload(), 1200);
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}

async function archiveRow(source, id) {
    const result = await Swal.fire({
        title: 'Przenieść do archiwum?', icon: 'question',
        showCancelButton: true, confirmButtonColor: '#7f8c8d',
        confirmButtonText: 'Archiwizuj', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;
    const url = source === 'order'
        ? `/biuro/weighings/orders/${id}/archive`
        : `/biuro/weighings/${id}/archive`;
    const res  = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('row-' + source + '-' + id)?.remove();
        Swal.fire({ icon: 'success', title: 'Zarchiwizowano!', timer: 1200, showConfirmButton: false });
    }
}

async function deleteRow(source, id) {
    const txt = source === 'order'
        ? 'Wyzerowane zostaną wagi na zleceniu — kierowca będzie mógł wpisać ponownie.'
        : 'Ważenie zostanie usunięte.';
    const result = await Swal.fire({
        title: 'Usunąć?', text: txt,
        icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#e74c3c',
        confirmButtonText: 'Usuń', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;
    const url = source === 'order'
        ? `/biuro/weighings/orders/${id}`
        : `/biuro/weighings/${id}`;
    const res  = await fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('row-' + source + '-' + id)?.remove();
        Swal.fire({ icon: 'success', title: 'Usunięto', timer: 1200, showConfirmButton: false });
    }
}

if (window.pollPageFragment) {
    window.pollPageFragment('poll-area', 5000);
}
</script>
@endsection
