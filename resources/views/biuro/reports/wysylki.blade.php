@extends('layouts.app')
@section('title', 'Raport wysyłek')
@section('module_name', 'BIURO')
@section('nav_menu') @include('biuro._nav') @endsection

@section('styles')
<style>
.report-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px; }
.report-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;display:flex;align-items:center;gap:8px; }
.badge-count { display:inline-block;background:#1a1a1a;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px; }

/* Nawigacja miesięcy */
.miesiac-nav { display:flex;gap:4px;flex-wrap:wrap;margin-bottom:10px; }
.btn-miesiac { padding:5px 12px;border-radius:6px;font-size:12px;font-weight:700;border:1.5px solid #dde0e5;background:#fff;cursor:pointer;text-decoration:none;color:#555;white-space:nowrap; }
.btn-miesiac.active { background:#1a1a1a;color:#fff;border-color:#1a1a1a; }
.btn-miesiac:hover:not(.active) { background:#f4f5f7; }

/* Tygodnie */
.tydzien-nav { display:flex;gap:4px;flex-wrap:wrap;margin-bottom:10px;align-items:center; }
.tydzien-label { font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#aaa;margin-right:4px; }
.btn-tydzien { padding:4px 10px;border-radius:5px;font-size:11px;font-weight:700;border:1.5px solid #dde0e5;background:#fff;cursor:pointer;text-decoration:none;color:#555; }
.btn-tydzien.active { background:#1a1a1a;color:#fff;border-color:#1a1a1a; }
.btn-tydzien:hover:not(.active) { background:#f4f5f7; }

/* Filtry */
.filter-group { display:flex;flex-direction:column;gap:4px; }
.filter-group label { font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em; }
.filter-group input { padding:6px 10px;border:1.5px solid #dde0e5;border-radius:7px;font-size:13px;outline:none;min-width:150px; }
.filter-group input:focus { border-color:#1a1a1a; }
.btn-copy-date { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;cursor:pointer;background:#f4f5f7;color:#1a1a1a;font-size:13px;border:1px solid #dde0e5;transition:all .15s ease; }
.btn-copy-date:hover { background:#1a1a1a;color:#fff;border-color:#1a1a1a; }

/* Filtry słownikowe */
.filters-bar { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;align-items:center; }
.filters-bar select { padding:6px 10px;border:1.5px solid #dde0e5;border-radius:7px;font-size:13px;outline:none;min-width:160px; }
.filters-bar select:focus { border-color:#1a1a1a; }
.btn-filter { padding:7px 16px;background:#1a1a1a;color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer; }
.btn-reset  { padding:7px 14px;background:#f4f5f7;color:#555;border:1px solid #dde0e5;border-radius:7px;font-size:13px;cursor:pointer;text-decoration:none; }

/* Tabela */
.report-wrap { overflow-x:auto; }
table { width:100%;border-collapse:collapse;font-size:12px;white-space:nowrap; }
thead tr { background:#1a1a1a;color:#fff; }
th { padding:9px 12px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left; }
td { padding:8px 12px;border-bottom:1px solid #f0f2f5;vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr:hover td { background:#f8f9fa; }
.ls-nr { font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:900; }
.col-empty { color:#dde0e5;text-align:center; }
.badge-rekl { background:#fdecea;color:#e74c3c;padding:2px 7px;border-radius:8px;font-size:10px;font-weight:700; }
.badge-gew  { background:#e8f4fb;color:#2980b9;padding:2px 7px;border-radius:8px;font-size:10px;font-weight:700; }
.col-puste  { background:#fafafa;color:#ccc;font-size:11px;text-align:center; }
.sum-row td { font-weight:700;background:#f4f5f7;font-family:'Barlow Condensed',sans-serif;font-size:14px; }

/* Modal ceny */
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:12px;width:100%;max-width:360px;padding:24px;box-shadow:0 8px 32px rgba(0,0,0,.2); }
.modal-title { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center; }
</style>
@endsection

@section('content')

<div class="report-header">
    <div class="report-title">
        <i class="fas fa-file-invoice"></i>
        Raport Wysyłek Zagranicznych
        <span class="badge-count">{{ $wysylki->count() }}</span>
    </div>
</div>

{{-- Nawigacja miesięcy --}}
<div class="miesiac-nav">
    @foreach($miesiace as $m)
    <a href="{{ route('biuro.raporty.wysylki', array_merge(request()->except(['miesiac','tydzien']), ['miesiac' => $m->format('Y-m')])) }}"
       class="btn-miesiac {{ $miesiac === $m->format('Y-m') ? 'active' : '' }}">
        {{ $m->translatedFormat('M Y') }}
    </a>
    @endforeach
</div>

{{-- Tygodnie: ostatnie 11 + bieżący, bieżący ostatni --}}
@php
    $biezacyTydzien = now()->isoWeek;
    $biezacyRok = now()->year;
    $tygodnieNav = [];
    for ($i = 11; $i >= 0; $i--) {
        $d = now()->subWeeks($i);
        $tygodnieNav[] = ['nr' => $d->isoWeek, 'rok' => $d->year];
    }
@endphp
<div class="tydzien-nav">
    <span class="tydzien-label">Tydzień:</span>
    <a href="{{ route('biuro.raporty.wysylki', array_merge(request()->except('tydzien'), ['miesiac' => $miesiac])) }}"
       class="btn-tydzien {{ !$tydzien ? 'active' : '' }}">Wszystkie</a>
    @foreach($tygodnieNav as $tn)
    <a href="{{ route('biuro.raporty.wysylki', array_merge(request()->except('tydzien'), ['miesiac' => $miesiac, 'tydzien' => $tn['nr']])) }}"
       class="btn-tydzien {{ $tydzien == $tn['nr'] ? 'active' : '' }}">W{{ $tn['nr'] }}</a>
    @endforeach
</div>

{{-- Filtry --}}
<form method="GET" action="{{ route('biuro.raporty.wysylki') }}">
    <input type="hidden" name="miesiac" value="{{ $miesiac }}">
    @if($tydzien) <input type="hidden" name="tydzien" value="{{ $tydzien }}"> @endif
    <div class="filters-bar" style="align-items:flex-end">
        <div class="filter-group">
            <label>Data od</label>
            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}">
        </div>
        <span class="btn-copy-date" onclick="copyDateTo()" title="Ustaw datę do = data od" style="margin-bottom:1px">
            <i class="fas fa-angle-double-right"></i>
        </span>
        <div class="filter-group">
            <label>Data do</label>
            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}">
        </div>
        <select name="importer_id" style="align-self:flex-end">
            <option value="">– Odbiorca –</option>
            @foreach($importerzy as $i)
            <option value="{{ $i->id }}" {{ $filtImporter == $i->id ? 'selected' : '' }}>{{ $i->name }}</option>
            @endforeach
        </select>
        <select name="goods_id" style="align-self:flex-end">
            <option value="">– Towar –</option>
            @foreach($towary as $t)
            <option value="{{ $t->id }}" {{ $filtGoods == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
        </select>
        <select name="waste_code_id" style="align-self:flex-end">
            <option value="">– Kod odpadu –</option>
            @foreach($kodyOdpadow as $k)
            <option value="{{ $k->id }}" {{ $filtWasteCode == $k->id ? 'selected' : '' }}>{{ $k->code }} – {{ $k->description }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-filter" style="align-self:flex-end"><i class="fas fa-filter"></i> Filtruj</button>
        <a href="{{ route('biuro.raporty.wysylki', ['miesiac' => $miesiac]) }}" class="btn-reset" style="align-self:flex-end">Resetuj</a>
    </div>
</form>

{{-- Tabela --}}
<div class="report-wrap card">
    <table>
        <thead>
            <tr>
                <th>W</th>
                <th>Data wysyłki</th>
                <th>Data załadunku</th>
                <th>Odbiorca</th>
                <th>Towar</th>
                <th>Waga</th>
                <th>R</th>
                <th>Cena sprzedaży</th>
                <th class="col-puste">Koszt transportu</th>
                <th class="col-puste">Cena na placu</th>
                <th class="col-puste">Wartość</th>
                <th>Kod towaru</th>
                <th>Nr LS</th>
                <th>Kierunek</th>
            </tr>
        </thead>
        <tbody>
        @php $totalCount = 0; $orderIds = []; @endphp
        @forelse($wysylki as $w)
        @php
            $ls            = $w->lieferschein;
            $dataZaladunku = $w->warehouseLoadingItems->first()?->date;
            $tydzienNr     = $w->planned_date ? $w->planned_date->isoWeek : '–';
            $dok           = $dokumenty->get($w->lieferschein_id);
            $cena          = $w->wysylkaCena?->cena_eur;
            $transport     = $w->wysylkaTransport;
            $hasRekl       = $dok && $dok->firstWhere('typ', 'reklamacja');
            $totalCount++;
            $orderIds[]    = $w->id;
        @endphp
        <tr data-order-id="{{ $w->id }}">
            <td style="color:#aaa;font-size:11px">W{{ $tydzienNr }}</td>
            <td>{{ $w->planned_date?->format('d.m.Y') ?? '–' }}</td>
            <td>{{ $dataZaladunku ? \Carbon\Carbon::parse($dataZaladunku)->format('d.m.Y') : '–' }}</td>
            <td style="font-weight:700">{{ $ls?->importer?->name ?? '–' }}</td>
            <td>{{ $ls?->goods?->name ?? '–' }}</td>
            <td>
                @if($dok && $dok->isNotEmpty())
                    @php $d = $dok->first(); @endphp
                    @if($d->typ === 'reklamacja')
                        <span class="badge-rekl" title="Reklamacja">
                            <i class="fas fa-exclamation"></i> {{ number_format($d->masa_netto, 3, ',', ' ') }} t
                        </span>
                    @else
                        <span class="badge-gew" title="Gewichtsmeldung">
                            <i class="fas fa-check"></i> {{ number_format($d->masa_netto, 3, ',', ' ') }} t
                        </span>
                    @endif
                @else
                    <span style="color:#dde0e5">–</span>
                @endif
            </td>
            <td style="text-align:center">
                @if($hasRekl)
                    <span style="background:#f39c12;color:#fff;font-size:11px;font-weight:900;padding:2px 7px;border-radius:5px">R</span>
                @else
                    <span style="color:#dde0e5">–</span>
                @endif
            </td>
            <td>
                <div style="display:flex;align-items:center;gap:5px">
                    <span class="cena-val" style="font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:900;min-width:60px">
                        {{ $cena !== null ? number_format($cena, 2, ',', ' ') . ' €' : '–' }}
                    </span>
                    <button onclick="openCenaModal({{ $w->id }}, {{ $cena ?? 'null' }})"
                            style="background:none;border:none;color:#aaa;cursor:pointer;padding:2px 4px;font-size:12px"
                            title="Zmień cenę">
                        <i class="fas fa-pen-to-square"></i>
                    </button>
                </div>
            </td>
            <td>
                <div style="display:flex;align-items:center;gap:5px">
                    <span class="transport-val" style="font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:900;min-width:60px;{{ $transport && $transport->recznie ? 'color:#e67e22' : '' }}">
                        {{ $transport?->cena_eur !== null ? number_format($transport->cena_eur, 2, ',', ' ') . ' €' : '–' }}
                    </span>
                    @if($transport && $transport->recznie)
                        <span title="Wpisano ręcznie" style="font-size:10px;color:#e67e22"><i class="fas fa-hand-paper"></i></span>
                    @endif
                    <button onclick="openTransportModal({{ $w->id }}, {{ $transport?->cena_eur ?? 'null' }}, {{ $transport?->przewoznik_id ?? 'null' }})"
                            style="background:none;border:none;color:#aaa;cursor:pointer;padding:2px 4px;font-size:12px" title="Zmień koszt transportu">
                        <i class="fas fa-pen-to-square"></i>
                    </button>
                </div>
                @if($transport?->przewoznik)
                    <div style="font-size:10px;color:#aaa">{{ $transport->przewoznik->nazwa }}</div>
                @endif
            </td>
            <td class="col-empty">–</td>
            <td>
                @if($ls?->wasteCode)
                    <span style="font-family:'Barlow Condensed',sans-serif;font-weight:900">{{ $ls->wasteCode->code }}</span>
                @else
                    <span style="color:#dde0e5">–</span>
                @endif
            </td>
            <td><span class="ls-nr">{{ $ls?->number ?? '–' }}</span></td>
            <td>{{ $ls?->client?->short_name ?? '–' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="14" class="text-center text-muted py-4">Brak wysyłek zagranicznych w wybranym okresie</td>
        </tr>
        @endforelse
        @if($totalCount > 0)
        <tr class="sum-row">
            <td colspan="5">Razem: {{ $totalCount }} wysyłek</td>
            <td colspan="9"></td>
        </tr>
        @endif
        </tbody>
    </table>
</div>

{{-- Modal ceny --}}
<div class="modal-overlay" id="cenaModal">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-title">
            <span>Cena sprzedaży</span>
            <button style="background:none;border:none;font-size:20px;cursor:pointer;color:#aaa" onclick="closeCenaModal()">×</button>
        </div>
        <input type="hidden" id="cenaOrderId">
        <label style="display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:6px">
            Cena €/t
        </label>
        <input type="number" id="cenaInput" step="0.01" min="0"
               style="width:100%;padding:12px;border:1.5px solid #dde0e5;border-radius:8px;font-size:18px;font-weight:700;outline:none;margin-bottom:16px"
               placeholder="0.00">
        <button onclick="saveCena()"
                style="width:100%;padding:14px;background:#1a1a1a;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:700;cursor:pointer">
            <i class="fas fa-check"></i> Zapisz
        </button>
        <button onclick="closeCenaModal()"
                style="width:100%;padding:12px;background:#f4f5f7;color:#555;border:1px solid #dde0e5;border-radius:8px;font-size:14px;cursor:pointer;margin-top:8px">
            Anuluj
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF      = '{{ csrf_token() }}';

function copyDateTo() {
    const from = document.getElementById('date_from').value;
    if (from) document.getElementById('date_to').value = from;
}
const ORDER_IDS = @json($orderIds ?? []);

function openCenaModal(orderId, cena) {
    document.getElementById('cenaOrderId').value = orderId;
    document.getElementById('cenaInput').value   = cena ?? '';
    document.getElementById('cenaModal').classList.add('open');
    setTimeout(() => document.getElementById('cenaInput').focus(), 100);
}

function closeCenaModal() {
    document.getElementById('cenaModal').classList.remove('open');
}

async function saveCena() {
    const orderId = document.getElementById('cenaOrderId').value;
    const cena    = document.getElementById('cenaInput').value;

    // Zapisz dla jednego rekordu
    const res  = await fetch(`/biuro/raporty/wysylki/cena/${orderId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ cena_eur: cena || null }),
    });
    const data = await res.json();
    if (!data.success) {
        Swal.fire({ icon: 'error', title: 'Błąd zapisu', timer: 1500, showConfirmButton: false });
        return;
    }

    // Zaktualizuj wyświetlaną cenę w wierszu
    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
    if (row) {
        const span = row.querySelector('.cena-val');
        if (span) span.textContent = cena ? parseFloat(cena).toFixed(2).replace('.', ',') + ' €' : '–';
    }

    closeCenaModal();

    // Zapytaj o zastosowanie dla wszystkich widocznych
    if (ORDER_IDS.length > 1) {
        const bulk = await Swal.fire({
            title: 'Zastosować dla wszystkich?',
            text: `Czy ustawić tę cenę dla wszystkich ${ORDER_IDS.length} widocznych wysyłek?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1a1a1a',
            confirmButtonText: 'Tak, dla wszystkich',
            cancelButtonText: 'Nie, tylko ten',
        });

        if (bulk.isConfirmed) {
            const res2 = await fetch('/biuro/raporty/wysylki/cena-bulk', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ cena_eur: cena || null, order_ids: ORDER_IDS }),
            });
            const data2 = await res2.json();
            if (data2.success) {
                // Zaktualizuj wszystkie widoczne wiersze
                document.querySelectorAll('tr[data-order-id]').forEach(row => {
                    const span = row.querySelector('.cena-val');
                    if (span) span.textContent = cena ? parseFloat(cena).toFixed(2).replace('.', ',') + ' €' : '–';
                });
                Swal.fire({ icon: 'success', title: `Zaktualizowano ${data2.updated} wysyłek`, timer: 1800, showConfirmButton: false });
            }
        } else {
            Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1000, showConfirmButton: false });
        }
    } else {
        Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1000, showConfirmButton: false });
    }
}

document.getElementById('cenaModal').addEventListener('click', closeCenaModal);
document.getElementById('cenaInput').addEventListener('keydown', e => { if (e.key === 'Enter') saveCena(); });

// Transport
function openTransportModal(orderId, cena, przewoznikId) {
    document.getElementById('transportOrderId').value = orderId;
    document.getElementById('transportCena').value    = cena ?? '';
    document.getElementById('transportPrzewoznik').value = przewoznikId ?? '';
    document.getElementById('transportModal').classList.add('open');
    setTimeout(() => document.getElementById('transportCena').focus(), 100);
}

function closeTransportModal() {
    document.getElementById('transportModal').classList.remove('open');
}

async function saveTransport() {
    const orderId      = document.getElementById('transportOrderId').value;
    const cena         = document.getElementById('transportCena').value;
    const przewoznikId = document.getElementById('transportPrzewoznik').value;

    const res  = await fetch(`/biuro/raporty/wysylki/transport/${orderId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ cena_eur: cena || null, przewoznik_id: przewoznikId || null }),
    });
    const data = await res.json();
    if (data.success) {
        const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
        if (row) {
            const span = row.querySelector('.transport-val');
            if (span) {
                span.textContent = cena ? parseFloat(cena).toFixed(2).replace('.', ',') + ' €' : '–';
                span.style.color = '#e67e22';
            }
        }
        closeTransportModal();
        Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1000, showConfirmButton: false });
    } else {
        Swal.fire({ icon: 'error', title: 'Błąd', text: data.error ?? 'Błąd zapisu' });
    }
}

document.getElementById('transportModal')?.addEventListener('click', closeTransportModal);
document.getElementById('transportCena')?.addEventListener('keydown', e => { if (e.key === 'Enter') saveTransport(); });
</script>
@endsection

{{-- Modal transportu --}}
<div class="modal-overlay" id="transportModal">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-title">
            <span>Koszt transportu</span>
            <button style="background:none;border:none;font-size:20px;cursor:pointer;color:#aaa" onclick="closeTransportModal()">×</button>
        </div>
        <input type="hidden" id="transportOrderId">
        <label style="display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:6px">Przewoźnik</label>
        <select id="transportPrzewoznik" style="width:100%;padding:10px;border:1.5px solid #dde0e5;border-radius:8px;font-size:14px;outline:none;margin-bottom:14px">
            <option value="">– brak –</option>
            @foreach($przewoznicy as $p)
            <option value="{{ $p->id }}">{{ $p->nazwa }}</option>
            @endforeach
        </select>
        <label style="display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:6px">Cena €/t</label>
        <input type="number" id="transportCena" step="0.01" min="0"
               style="width:100%;padding:12px;border:1.5px solid #dde0e5;border-radius:8px;font-size:18px;font-weight:700;outline:none;margin-bottom:16px"
               placeholder="0.00">
        <button onclick="saveTransport()"
                style="width:100%;padding:14px;background:#1a1a1a;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:700;cursor:pointer">
            <i class="fas fa-check"></i> Zapisz
        </button>
        <button onclick="closeTransportModal()"
                style="width:100%;padding:12px;background:#f4f5f7;color:#555;border:1px solid #dde0e5;border-radius:8px;font-size:14px;cursor:pointer;margin-top:8px">
            Anuluj
        </button>
    </div>
</div>
