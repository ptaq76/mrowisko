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

.tabs { display:flex;gap:0;margin-bottom:16px;border-bottom:2px solid #e2e5e9; }
.tab-btn { padding:10px 20px;background:none;border:none;font-size:13px;font-weight:700;color:#888;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px; }
.tab-btn.active { color:#3498db;border-bottom-color:#3498db; }
.tab-content { display:none; }
.tab-content.active { display:block; }

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
.w-result { font-family:'Barlow Condensed',sans-serif;font-size:19px;font-weight:900;color:#2d7a1a; }
.w-result.negative { color:#e74c3c; }
.btn-del-w { background:#fdecea;border:1px solid #f5c6cb;border-radius:5px;padding:5px 9px;color:#e74c3c;cursor:pointer;font-size:12px; }
.btn-del-w:hover { background:#e74c3c;color:#fff; }
.empty-state { text-align:center;padding:40px;color:#ccc; }
.empty-state i { font-size:36px;margin-bottom:8px;display:block; }

/* Modal – 2x szerszy */
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:12px;width:100%;max-width:860px;max-height:92vh;overflow-y:auto;padding:28px;box-shadow:0 8px 32px rgba(0,0,0,.2); }
.modal-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:#1a1a1a;margin-bottom:18px;display:flex;justify-content:space-between;align-items:center; }
.modal-close { background:#f0f2f5;border:none;border-radius:50%;width:32px;height:32px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center; }

.m-label { display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:5px; }
.m-input, .m-select { width:100%;padding:9px 11px;border:1.5px solid #dde0e5;border-radius:8px;font-size:15px;font-weight:600;color:#1a1a1a;outline:none;margin-bottom:0; }
.m-input:focus, .m-select:focus { border-color:#3498db; }

/* Dwukolumnowy layout modala */

.m-row-2 { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.m-row-3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px; }

/* Wynik */
.m-result { background:#e8f7e4;border-radius:8px;padding:7px 12px;display:flex;justify-content:space-between;align-items:center;border:1.5px solid #a8d8a8; }
.mr-label { font-size:11px;font-weight:700;color:#2d7a1a;text-transform:uppercase;letter-spacing:.06em; }
.mr-val   { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;color:#2d7a1a; }
.mr-val.neg { color:#e74c3c; }
.tare-filter-btn {
    padding:3px 8px;border:1px solid #bcc4ce;border-radius:4px;
    background:#f4f5f7;color:#555;cursor:pointer;
    font-size:10px;font-weight:700;letter-spacing:.03em;
    font-family:'Barlow Condensed',sans-serif;
    transition:all .12s;white-space:nowrap;
}
.tare-filter-btn:hover { background:#2c3e50;color:#fff;border-color:#2c3e50; }
.tare-filter-btn.active { background:#2c3e50;color:#fff;border-color:#2c3e50; }
.tare-filter-btn-b { border-color:#7f8c8d;color:#7f8c8d; }
.tare-filter-btn-b:hover { background:#7f8c8d;color:#fff;border-color:#7f8c8d; }
.tare-filter-btn-b.active { background:#7f8c8d;color:#fff;border-color:#7f8c8d; }
#wW1.needs-fill { border-color:#e74c3c; box-shadow:0 0 0 2px rgba(231,76,60,.2); }
#wW2.needs-fill { border-color:#e74c3c; box-shadow:0 0 0 2px rgba(231,76,60,.2); }

/* Aktywne zlecenia */
.active-orders-label { font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:6px; }
.active-orders { display:flex;flex-wrap:wrap;gap:5px;margin-bottom:0; }
.ao-btn { padding:5px 11px;border:1.5px solid #3498db;border-radius:20px;background:#eaf4fb;color:#2471a3;font-size:12px;font-weight:700;cursor:pointer;transition:all .15s;white-space:nowrap; }
.ao-btn:hover { background:#3498db;color:#fff; }
.ao-btn.selected { background:#3498db;color:#fff; }
.linked-badge { display:inline-flex;align-items:center;gap:6px;background:#e8f7e4;border:1.5px solid #27ae60;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:700;color:#1a7a3a;margin-top:6px; }
.ao-btn:hover { background:#3498db;color:#fff; }
.ao-btn.selected { background:#3498db;color:#fff; }

/* Side panel */

.modal-footer { display:flex;gap:10px;justify-content:flex-end;margin-top:18px;padding-top:14px;border-top:1px solid #e2e5e9; }
.btn-cancel { padding:10px 20px;background:#f4f5f7;color:#555;border:1px solid #dde0e5;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer; }
.btn-cancel:hover { background:#e8e9ec; }
.btn-save { padding:10px 24px;background:#3498db;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer; }
.btn-save:hover { background:#2980b9; }
</style>
@endsection

@section('content')
<div class="weighings-wrap">

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

    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('manual')">
            Ręczne <span style="background:#3498db;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px">{{ $manual->count() }}</span>
        </button>
        <button class="tab-btn" onclick="switchTab('driver')">
            Kierowcy <span style="background:#6EBF58;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px">{{ $driver->count() }}</span>
        </button>
    </div>

    {{-- Ważenia ręczne --}}
    <div id="tab-manual" class="tab-content active">
        @if($manual->isEmpty())
        <div class="empty-state"><i class="fas fa-weight"></i><p>Brak ręcznych ważeń</p></div>
        @else
        <div class="w-table-wrap">
            <table class="w-table">
                <thead><tr>
                    <th>Data</th><th>Klient</th><th>Pojazdy</th>
                    <th>Waga 1</th><th>Waga 2</th><th>Wynik</th>
                    <th>Towar</th><th>Uwagi</th><th style="width:50px"></th>
                </tr></thead>
                <tbody>
                @foreach($manual as $w)
                <tr id="wr-{{ $w->id }}" onclick="openEditModal({{ $w->id }})">
                    <td>
                        <div class="cell-dt">{{ $w->weighed_at->format('d.m.Y') }}</div>
                        <div class="cell-time">{{ $w->weighed_at->format('H:i') }}</div>
                    </td>
                    <td class="cell-client">{{ $w->client?->short_name ?? '–' }}</td>
                    <td class="plates">
                        @if($w->plate1)<span class="nr-rej">{{ $w->plate1 }}</span>@endif
                        @if($w->plate2) <span class="nr-rej">{{ $w->plate2 }}</span>@endif
                    </td>
                    <td><span class="w-val">{{ $w->weight1 ? number_format($w->weight1,3,',','') : '–' }}</span></td>
                    <td><span class="w-val">{{ $w->weight2 ? number_format($w->weight2,3,',','') : '–' }}</span></td>
                    <td>
                        @if($w->result !== null)
                        <span class="w-result {{ $w->result < 0 ? 'negative' : '' }}">
                            {{ number_format($w->result,3,',','') }}
                        </span>
                        @else<span style="color:#ccc">–</span>@endif
                    </td>
                    <td style="font-size:12px;color:#555">{{ $w->goods ?? '–' }}</td>
                    <td style="font-size:12px;color:#888;max-width:120px">{{ Str::limit($w->notes,40) }}</td>
                    <td onclick="event.stopPropagation()">
                        <div style="display:flex;gap:4px">
                            <button class="btn-del-w" onclick="deleteWeighing({{ $w->id }})" title="Usuń">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <button class="btn-arch-w" onclick="archiveWeighing({{ $w->id }})" title="Archiwizuj"
                                    style="background:#f4f5f7;border:1px solid #dde0e5;border-radius:5px;padding:5px 9px;color:#7f8c8d;cursor:pointer;font-size:12px;transition:background .15s,color .15s"
                                    onmouseover="this.style.background='#7f8c8d';this.style.color='#fff';this.style.borderColor='#7f8c8d'"
                                    onmouseout="this.style.background='#f4f5f7';this.style.color='#7f8c8d';this.style.borderColor='#dde0e5'">
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

    {{-- Ważenia kierowców --}}
    <div id="tab-driver" class="tab-content">
        @if($driver->isEmpty())
        <div class="empty-state"><i class="fas fa-truck-moving"></i><p>Brak ważeń kierowców</p></div>
        @else
        <div class="w-table-wrap">
            <table class="w-table">
                <thead><tr>
                    <th>Data</th><th>Klient</th><th>Pojazdy</th>
                    <th>Brutto</th><th>Tara</th><th>Netto</th>
                    <th>Kierowca</th><th>Notatka kierowcy</th>
                </tr></thead>
                <tbody>
                @foreach($driver as $o)
                @php $tare = $o->weight_brutto && $o->weight_netto ? round($o->weight_brutto - $o->weight_netto, 3) : null; @endphp
                <tr>
                    <td>
                        <div class="cell-dt">{{ $o->planned_date->format('d.m.Y') }}</div>
                        <div class="cell-time">{{ $o->updated_at->format('H:i') }}</div>
                    </td>
                    <td class="cell-client">
                        <span style="color:{{ $o->type==='sale'?'#f39c12':'#27ae60' }};margin-right:4px">{{ $o->type==='sale'?'↑':'↓' }}</span>
                        {{ $o->client?->short_name ?? '–' }}
                    </td>
                    <td class="plates" style="white-space:nowrap">
                        @if($o->tractor)<span class="nr-rej" style="font-size:10px;padding:1px 4px">{{ $o->tractor->plate }}</span>@endif
                        @if($o->trailer) <span class="nr-rej" style="font-size:10px;padding:1px 4px">{{ $o->trailer->plate }}</span>@endif
                    </td>
                    <td><span class="w-val">{{ $o->weight_brutto ? number_format($o->weight_brutto,3,',','') : '–' }}</span></td>
                    <td><span class="w-val" style="color:#888">{{ $tare ? number_format($tare,3,',','') : '–' }}</span></td>
                    <td><span class="w-result">{{ number_format($o->weight_netto,3,',','') }}</span></td>
                    <td style="font-size:13px;color:#555">{{ $o->driver?->name ?? '–' }}</td>
                    <td style="font-size:12px;color:#888;max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                        @if($o->driver_notes) title="{{ str_replace(['<br>', '<br/>', '<br />'], "\n", $o->driver_notes) }}" @endif>
                        {{ str_replace(['<br>', '<br/>', '<br />'], ' ', $o->driver_notes) ?? '–' }}
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
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

        <div style="display:flex;flex-direction:column;gap:12px">
            <div>

                {{-- Aktywne zlecenia --}}
                <div style="margin-bottom:8px">
                    <div class="active-orders-label">Aktywne zlecenia</div>
                    <div class="active-orders" style="margin-bottom:20px">
                        @foreach($activeOrders as $ao)
                        <button type="button" class="ao-btn"
                                data-client="{{ $ao->client_id }}"
                                data-order="{{ $ao->id }}"
                                data-plate1="{{ $ao->tractor?->plate }}"
                                data-plate2="{{ $ao->trailer?->plate }}"
                                data-type="{{ $ao->type }}"
                                data-client-name="{{ $ao->client?->short_name }}"
                                data-date="{{ $ao->planned_date->format('d.m') }}"
                                data-goods="{{ $ao->fractions_note }}"
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

                {{-- Przyciski woźaców --}}
                @if($haulers->isNotEmpty())
                <div style="margin-bottom:8px">
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

                <div style="background:#dde3ea;border-radius:10px;padding:14px 16px;margin-bottom:8px;border:2px solid #bcc4ce">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                        <span style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#555">Wskazania wagi</span>
                        <button type="button" onclick="toggleTareList()" id="tareBtnToggle"
                                style="background:#2c3e50;border:none;border-radius:6px;padding:4px 12px;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;cursor:pointer">
                            <i class="fas fa-weight-hanging"></i> TARY
                        </button>
                    </div>
                    {{-- Lista tar --}}
                    <div id="tareList" style="display:none;margin-bottom:10px;background:#fff;border-radius:8px;padding:8px">
                        {{-- Filtry numerów rej --}}
                        <div id="tareFilters" style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid #e2e5e9">
                            <button type="button" class="tare-filter-btn active" data-plate="" onclick="filterTares(this, '')">Wszystkie</button>
                            <button type="button" class="tare-filter-btn" data-plate="PNT81294" onclick="filterTares(this, 'PNT81294')">PNT81294</button>
                            <button type="button" class="tare-filter-btn" data-plate="WGM0958F" onclick="filterTares(this, 'WGM0958F')">WGM0958F</button>
                            <button type="button" class="tare-filter-btn" data-plate="WGM2624C" onclick="filterTares(this, 'WGM2624C')">WGM2624C</button>
                            <button type="button" class="tare-filter-btn" data-plate="WGM3595C" onclick="filterTares(this, 'WGM3595C')">WGM3595C</button>
                            <button type="button" class="tare-filter-btn tare-filter-btn-b" data-plate="WGM8340P" onclick="filterTares(this, 'WGM8340P')">WGM8340P</button>
                            <button type="button" class="tare-filter-btn tare-filter-btn-b" data-plate="WGM5564P" onclick="filterTares(this, 'WGM5564P')">WGM5564P</button>
                            <button type="button" class="tare-filter-btn tare-filter-btn-b" data-plate="WGM4617P" onclick="filterTares(this, 'WGM4617P')">WGM4617P</button>
                            <button type="button" class="tare-filter-btn tare-filter-btn-b" data-plate="WGM2126P" onclick="filterTares(this, 'WGM2126P')">WGM2126P</button>
                            <button type="button" class="tare-filter-btn tare-filter-btn-b" data-plate="PNTKY66" onclick="filterTares(this, 'PNTKY66')">PNTKY66</button>
                        </div>
                        <div id="tareListItems" style="display:flex;flex-direction:column;gap:4px;max-height:160px;overflow-y:auto">
                            {{-- wypełniane JS --}}
                        </div>
                    </div>
                    <div class="m-row-2">
                        <div>
                            <label class="m-label" style="color:#3498db;font-size:12px">Waga 1 [t]</label>
                            <input type="number" id="wW1" class="m-input" step="0.001" min="0" oninput="calcResult()"
                                   style="font-size:22px;font-weight:900;font-family:'Barlow Condensed',sans-serif;padding:12px 14px;border-width:2px">
                        </div>
                        <div>
                            <label class="m-label" style="color:#3498db;font-size:12px">Waga 2 [t]</label>
                            <input type="number" id="wW2" class="m-input" step="0.001" min="0" oninput="calcResult()"
                                   style="font-size:22px;font-weight:900;font-family:'Barlow Condensed',sans-serif;padding:12px 14px;border-width:2px">
                        </div>
                    </div>
                </div>

                {{-- Wynik --}}
                <div class="m-result" id="resultBox">
                    <span class="mr-label">Wynik (Waga 1 – Waga 2)</span>
                    <span class="mr-val" id="resultVal">–</span>
                </div>

                <div>
                    <label class="m-label">Towar</label>
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
<script>
const CSRF = '{{ csrf_token() }}';
let _editId = null;

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`tab-${tab}`).classList.add('active');
}

function openAddModal() {
    _editId = null;
    document.getElementById('modalTitle').textContent = 'Dodaj ważenie';
    document.getElementById('wId').value      = '';
    document.getElementById('wOrderId').value = '';
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
    document.getElementById('weighModal').classList.add('open');
}

function openEditModal(id) {
    _editId = id;
    document.getElementById('modalTitle').textContent = 'Edytuj ważenie';
    fetch(`/biuro/weighings/${id}/edit`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(d => {
            document.getElementById('wId').value      = d.id;
            document.getElementById('wClient').value  = d.client_id ?? '';
            document.getElementById('wDate').value    = d.weighed_at_input;
            document.getElementById('wPlate1').value  = d.plate1 ?? '';
            document.getElementById('wPlate2').value  = d.plate2 ?? '';
            document.getElementById('wW1').value      = d.weight1 ?? '';
            document.getElementById('wW2').value      = d.weight2 ?? '';
            document.getElementById('wGoods').value   = d.goods ?? '';
            document.getElementById('wNotes').value   = d.notes ?? '';
            document.getElementById('wOrderId').value = d.order_id ?? '';
            // Pokaż badge jeśli powiązane ze zleceniem
            const badge = document.getElementById('linkedBadge');
            const text  = document.getElementById('linkedText');
            if (d.order_id) {
                const arrow = d.order_type === 'sale' ? '↑' : '↓';
                text.textContent = 'POWIĄZANE: ' + arrow + ' ' + (d.order_label ?? '#' + d.order_id);
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
            calcResult();
            document.getElementById('weighModal').classList.add('open');
        });
}

function closeModal() {
    document.getElementById('weighModal').classList.remove('open');
}

async function selectActiveOrder(btn) {
    document.querySelectorAll('.ao-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    document.getElementById('wClient').value  = btn.dataset.client;
    document.getElementById('wOrderId').value = btn.dataset.order;

    const plate1 = btn.dataset.plate1 ? btn.dataset.plate1.toUpperCase() : '';
    const plate2 = btn.dataset.plate2 ? btn.dataset.plate2.toUpperCase() : '';
    if (plate1) document.getElementById('wPlate1').value = plate1;
    if (plate2) document.getElementById('wPlate2').value = plate2;

    // Towar ze zlecenia
    if (btn.dataset.goods) document.getElementById('wGoods').value = btn.dataset.goods;

    // Badge POWIĄZANE
    const badge = document.getElementById('linkedBadge');
    const text  = document.getElementById('linkedText');
    const arrow = btn.dataset.type === 'sale' ? '↑' : '↓';
    text.textContent = 'POWIĄZANE: ' + arrow + ' ' + btn.dataset.clientName + ' · ' + btn.dataset.date;
    badge.style.display = 'inline-flex';

    // Pobierz tarę zestawu i wstaw do odpowiedniego pola wagi
    if (plate1) {
        try {
            const params = new URLSearchParams({ plate1, plate2 });
            const res  = await fetch('/biuro/weighings/tare-for-vehicles?' + params, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.found && data.tare) {
                const tareVal = parseFloat(data.tare).toFixed(3);
                if (btn.dataset.type === 'sale') {
                    // Wysyłka: pusty pojazd → Waga 1
                    document.getElementById('wW1').value = tareVal;
                } else {
                    // Dostawa/odbiór: pusty pojazd → Waga 2
                    document.getElementById('wW2').value = tareVal;
                }
                calcResult();
                // tara wstawiona cicho – bez modyfikacji badge
            }
        } catch(e) { /* brak tary */ }
    }
}

const _tareCache = [];

async function toggleTareList() {
    const list = document.getElementById('tareList');
    if (list.style.display !== 'none') {
        list.style.display = 'none';
        return;
    }
    // Załaduj tary jeśli jeszcze nie ma
    if (_tareCache.length === 0) {
        try {
            const res  = await fetch('/biuro/weighings/all-tares', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            data.sets.forEach(s => _tareCache.push(s));
        } catch(e) { return; }
    }
    const container = document.getElementById('tareListItems');
    container.innerHTML = '';
    renderTareItems();
    list.style.display = 'block';
}

let _currentFilter = '';

function filterTares(btn, plate) {
    _currentFilter = plate;
    document.querySelectorAll('.tare-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderTareItems();
}

function renderTareItems() {
    const container = document.getElementById('tareListItems');
    container.innerHTML = '';
    const filtered = _currentFilter
        ? _tareCache.filter(s => s.label.toUpperCase().includes(_currentFilter.toUpperCase()))
        : _tareCache;
    if (filtered.length === 0) {
        container.innerHTML = '<div style="text-align:center;color:#ccc;padding:10px;font-size:12px">Brak wyników</div>';
        return;
    }
    filtered.forEach(s => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.style.cssText = 'display:flex;justify-content:space-between;align-items:center;width:100%;padding:6px 10px;border:1px solid #e2e5e9;border-radius:6px;background:#f8f9fa;cursor:pointer;font-size:13px;text-align:left';
        btn.onmouseover = () => btn.style.background = '#eaf4fb';
        btn.onmouseout  = () => btn.style.background = '#f8f9fa';
        btn.innerHTML = `<span style="font-weight:700;color:#1a1a1a">${s.label}</span><span style="font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;color:#2c3e50">${parseFloat(s.tare_kg).toFixed(3).replace('.',',')} t</span>`;
        btn.onclick = () => selectTare(s.tare_kg);
        container.appendChild(btn);
    });
}

function selectTare(tare) {
    document.getElementById('wW2').value = parseFloat(tare).toFixed(3);
    document.getElementById('tareList').style.display = 'none';
    calcResult();
}

function calcResult() {
    const w1El = document.getElementById('wW1');
    const w2El = document.getElementById('wW2');
    const w1   = parseFloat(w1El.value);
    const w2   = parseFloat(w2El.value);
    const val  = document.getElementById('resultVal');

    // Czerwony border na polu które czeka na uzupełnienie
    const w1filled = !isNaN(w1) && w1El.value.trim() !== '';
    const w2filled = !isNaN(w2) && w2El.value.trim() !== '';
    w1El.classList.toggle('needs-fill', !w1filled && w2filled);
    w2El.classList.toggle('needs-fill', w1filled && !w2filled);

    if (w1filled && w2filled) {
        const r = Math.round((w1 - w2) * 1000) / 1000;
        val.textContent = r.toFixed(3).replace('.', ',') + ' t';
        val.className   = 'mr-val' + (r < 0 ? ' neg' : '');
    } else {
        val.textContent = '–';
        val.className   = 'mr-val';
    }
}

async function saveWeighing() {
    const w1      = document.getElementById('wW1').value.trim();
    const w2      = document.getElementById('wW2').value.trim();
    const orderId = document.getElementById('wOrderId').value.trim();

    if (!w1) {
        Swal.fire({ icon: 'warning', title: 'Podaj Wagę 1', timer: 1800, showConfirmButton: false });
        return;
    }

    const payload = {
        weighed_at:    document.getElementById('wDate').value,
        client_id:     document.getElementById('wClient').value || null,
        order_id:      orderId || null,
        plate1:        document.getElementById('wPlate1').value || null,
        plate2:        document.getElementById('wPlate2').value || null,
        weight1:       w1,
        weight2:       w2 || null,
        goods:         document.getElementById('wGoods').value || null,
        notes:         document.getElementById('wNotes').value || null,
        push_to_order: false,
    };

    // Jeśli powiązane ze zleceniem i obie wagi podane - zapytaj
    if (orderId !== '' && w2 !== '' && !_editId) {
        const confirm = await Swal.fire({
            title: 'Zakończyć?',
            html: 'Przekazać wagę na plac?<br><small style="color:#888">Zlecenie otrzyma status <b>Zważone</b>, ważenie zostanie przeniesione do archiwum</small>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27ae60',
            confirmButtonText: '<i class="fas fa-check"></i> Tak, przekaż',
            cancelButtonText: 'Nie, tylko zapisz',
            reverseButtons: true,
        });
        if (confirm.isConfirmed) {
            payload.push_to_order  = true;
            payload.archive_after  = true;
        }
    }

    // Edycja – jeśli order_id istnieje i obie wagi - też zapytaj
    if (_editId && orderId !== '' && w2 !== '') {
        const confirm = await Swal.fire({
            title: 'Zakończyć?',
            html: 'Przekazać wagę na plac?<br><small style="color:#888">Zlecenie otrzyma status <b>Zważone</b>, ważenie zostanie przeniesione do archiwum</small>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27ae60',
            confirmButtonText: '<i class="fas fa-check"></i> Tak, przekaż',
            cancelButtonText: 'Nie, tylko zapisz',
            reverseButtons: true,
        });
        if (confirm.isConfirmed) {
            payload.push_to_order = true;
            payload.archive_after = true;
        }
    }

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
        Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1500, showConfirmButton: false });
        setTimeout(() => location.reload(), 1500);
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}

async function archiveWeighing(id) {
    const result = await Swal.fire({
        title: 'Przenieść do archiwum?', icon: 'question',
        showCancelButton: true, confirmButtonColor: '#7f8c8d',
        confirmButtonText: 'Archiwizuj', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;
    const res  = await fetch(`/biuro/weighings/${id}/archive`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('wr-' + id)?.remove();
        Swal.fire({ icon: 'success', title: 'Zarchiwizowano!', timer: 1200, showConfirmButton: false });
    }
}

async function deleteWeighing(id) {
    const result = await Swal.fire({
        title: 'Usunąć ważenie?', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#e74c3c',
        confirmButtonText: 'Usuń', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;
    const res  = await fetch(`/biuro/weighings/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('wr-' + id)?.remove();
        Swal.fire({ icon: 'success', title: 'Usunięto', timer: 1200, showConfirmButton: false });
    }
}
</script>
@endsection