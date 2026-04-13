@extends('layouts.handlowiec')
@section('title', 'Nowe zlecenie')
@section('module_name', 'HANDLOWIEC')
@section('nav_menu') @include('handlowiec._nav') @endsection

@section('styles')
<style>
.h-form-wrap { padding:16px;max-width:560px;margin:0 auto; }
.h-back-btn {
    display:flex;align-items:center;justify-content:center;gap:10px;
    width:100%;padding:14px;margin-bottom:18px;
    background:#f4f5f7;border:1.5px solid #dde0e5;border-radius:12px;
    font-family:'Barlow Condensed',sans-serif;font-size:17px;font-weight:900;
    letter-spacing:.04em;text-transform:uppercase;
    text-decoration:none;color:#555;cursor:pointer;
    transition:background .12s;
}
.h-back-btn:hover { background:#e2e5e9;color:#1a1a1a; }
.h-page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;margin-bottom:18px; }
.h-label { display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:5px; }
.h-input,.h-select,.h-textarea {
    width:100%;padding:12px 14px;border:1.5px solid #dde0e5;border-radius:10px;
    font-size:16px;outline:none;margin-bottom:14px;background:#fff;
    -webkit-appearance:none;appearance:none;
}
.h-input:focus,.h-select:focus,.h-textarea:focus { border-color:#1a1a1a; }
.h-textarea { min-height:80px;resize:vertical; }
.h-section { background:#fff;border-radius:12px;padding:16px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,.07); }
.h-section-title { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;letter-spacing:.05em;text-transform:uppercase;margin-bottom:12px;display:flex;align-items:center;justify-content:space-between; }
.item-card { background:#f8f9fa;border-radius:10px;padding:12px;margin-bottom:10px;border:1.5px solid #e2e5e9; }
.item-card .h-label { margin-bottom:4px; }
.item-card .h-input { margin-bottom:8px;padding:10px 12px;font-size:15px; }
.btn-add-item { width:100%;padding:12px;background:#f4f5f7;border:2px dashed #ccc;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;color:#888;margin-bottom:14px; }
.btn-add-item:hover { border-color:#1a1a1a;color:#1a1a1a; }
.btn-remove-item { background:none;border:none;color:#e74c3c;font-size:18px;cursor:pointer;padding:0;float:right;margin-top:-2px; }
.btn-submit { width:100%;padding:18px;background:#1a1a1a;color:#fff;border:none;border-radius:12px;font-size:18px;font-weight:700;font-family:'Barlow Condensed',sans-serif;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;margin-top:4px; }
.btn-submit:active { transform:scale(.98); }
.h-historia-btn { padding:8px 14px;background:#f4f5f7;border:1.5px solid #dde0e5;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;color:#555; }
.h-historia-btn:hover { background:#e2e5e9; }
.historia-item { border-bottom:1px solid #f0f2f5;padding:10px 0; }
.historia-item:last-child { border-bottom:none; }
.status-pill { display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;color:#fff; }
.btn-powtorz { font-size:11px;padding:4px 10px;background:#1a1a1a;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:700;margin-top:6px; }
.btn-powtorz:hover { background:#333; }
</style>
@endsection

@section('content')
<div class="h-form-wrap">

    <a href="{{ route('handlowiec.dashboard') }}" class="h-back-btn">
        <i class="fas fa-home"></i> Powrót
    </a>

    <div class="h-page-title"><i class="fas fa-plus-circle"></i> Nowe zlecenie</div>

    {{-- Klient --}}
    <div class="h-section">
        <div class="h-section-title">
            <span>Klient</span>
            <button type="button" class="h-historia-btn" id="btnHistoria" style="display:none"
                    onclick="pokazHistorie()">
                <i class="fas fa-history"></i> Historia
            </button>
        </div>
        <label class="h-label">Wybierz klienta *</label>
        <select id="clientId" class="h-select" onchange="klientWybrany(this.value)">
            <option value="">– wybierz –</option>
            @foreach($klienci as $k)
            <option value="{{ $k->id }}" data-name="{{ $k->short_name }}">
                {{ $k->short_name ?? $k->name }}
            </option>
            @endforeach
        </select>

        {{-- Historia --}}
        <div id="historiaWrap" style="display:none;margin-top:4px">
            <div style="font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">
                Ostatnie zlecenia
            </div>
            <div id="historiaList"></div>
        </div>
    </div>

    {{-- Data --}}
    <div class="h-section">
        <div class="h-section-title">Data odbioru</div>
        <label class="h-label">Data planowanego odbioru *</label>
        <input type="date" id="requestedDate" class="h-input"
               min="{{ now()->toDateString() }}"
               value="{{ now()->addDay()->toDateString() }}">
    </div>

    {{-- Towary --}}
    <div class="h-section">
        <div class="h-section-title">Towary</div>
        <div id="itemsContainer"></div>
        <button type="button" class="btn-add-item" onclick="dodajTowar()">
            <i class="fas fa-plus"></i> Dodaj towar
        </button>
    </div>

    {{-- Uwagi --}}
    <div class="h-section">
        <div class="h-section-title">Uwagi</div>
        <textarea id="notes" class="h-textarea" placeholder="Opcjonalne uwagi do zlecenia..."></textarea>
    </div>

    <button class="btn-submit" onclick="wyslijZlecenie()">
        <i class="fas fa-paper-plane"></i> Wyślij zlecenie
    </button>
</div>
@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
let itemCounter = 0;

dodajTowar();

function dodajTowar(nazwa = '', ilosc = '', cena = '') {
    itemCounter++;
    const id = itemCounter;
    const container = document.getElementById('itemsContainer');
    const div = document.createElement('div');
    div.className = 'item-card';
    div.id = 'item-' + id;
    div.innerHTML = `
        <button type="button" class="btn-remove-item" onclick="usunTowar(${id})" title="Usuń">×</button>
        <label class="h-label">Nazwa towaru *</label>
        <input type="text" class="h-input item-nazwa" placeholder="np. Karton Czysty BELKA" autocomplete="off" value="${escHtml(nazwa)}">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
            <div>
                <label class="h-label">Ilość</label>
                <input type="text" class="h-input item-ilosc" placeholder="np. 3 tony" value="${escHtml(ilosc)}">
            </div>
            <div>
                <label class="h-label">Cena (zł/t)</label>
                <input type="number" class="h-input item-cena" placeholder="0.00" step="0.01" min="0" value="${escHtml(cena)}">
            </div>
        </div>
    `;
    container.appendChild(div);
    if (!nazwa) div.querySelector('.item-nazwa').focus();
}

function escHtml(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function usunTowar(id) {
    if (document.querySelectorAll('.item-card').length <= 1) {
        Swal.fire({ icon: 'warning', title: 'Minimum 1 towar', timer: 1200, showConfirmButton: false });
        return;
    }
    document.getElementById('item-' + id)?.remove();
}

function klientWybrany(clientId) {
    const btn  = document.getElementById('btnHistoria');
    const wrap = document.getElementById('historiaWrap');
    if (clientId) {
        btn.style.display = 'block';
    } else {
        btn.style.display = 'none';
        wrap.style.display = 'none';
    }
}

function formatDate(str) {
    if (!str) return '–';
    const d = new Date(str);
    return d.toLocaleDateString('pl-PL', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

async function pokazHistorie() {
    const clientId = document.getElementById('clientId').value;
    if (!clientId) return;

    const res  = await fetch(`/handlowiec/historia-klienta/${clientId}`, {
        headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();

    const wrap = document.getElementById('historiaWrap');
    const list = document.getElementById('historiaList');

    if (!data.length) {
        list.innerHTML = '<div style="color:#aaa;font-size:13px">Brak historii zleceń</div>';
    } else {
        const colors = { nowe:'#f39c12', przyjete:'#2980b9', zrealizowane:'#27ae60', anulowane:'#e74c3c', odrzucone_biuro:'#8e44ad' };
        list.innerHTML = data.map((z, idx) => {
            const kolor   = colors[z.status] ?? '#aaa';
            const towary  = (z.items ?? []).map(i => {
                let meta = i.nazwa;
                if (i.ilosc) meta += ` · ${i.ilosc}`;
                if (i.cena)  meta += ` · ${parseFloat(i.cena).toFixed(2).replace('.',',')} zł/t`;
                return meta;
            }).join('<br>');

            const itemsJson = JSON.stringify(z.items ?? []);

            return `
            <div class="historia-item">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                    <div>
                        <div style="font-weight:700;font-size:13px;margin-bottom:3px">${formatDate(z.requested_date)}</div>
                        <div style="color:#888;font-size:12px;line-height:1.5">${towary || '–'}</div>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0">
                        <span class="status-pill" style="background:${kolor}">${z.status}</span>
                        <button class="btn-powtorz" onclick='powtorzZlecenie(${itemsJson})'>
                            <i class="fas fa-redo"></i> Powtórz
                        </button>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    wrap.style.display = wrap.style.display === 'none' ? 'block' : 'none';
}

function powtorzZlecenie(items) {
    // Wyczyść obecne towary
    document.getElementById('itemsContainer').innerHTML = '';
    itemCounter = 0;

    // Dodaj towary ze zlecenia
    if (items && items.length) {
        items.forEach(i => dodajTowar(i.nazwa ?? '', i.ilosc ?? '', i.cena ?? ''));
    } else {
        dodajTowar();
    }

    // Zamknij historię i przewiń do towarów
    document.getElementById('historiaWrap').style.display = 'none';
    document.getElementById('itemsContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

async function wyslijZlecenie() {
    const clientId      = document.getElementById('clientId').value;
    const requestedDate = document.getElementById('requestedDate').value;
    const notes         = document.getElementById('notes').value;

    if (!clientId) {
        Swal.fire({ icon: 'warning', title: 'Wybierz klienta', timer: 1500, showConfirmButton: false });
        return;
    }
    if (!requestedDate) {
        Swal.fire({ icon: 'warning', title: 'Podaj datę odbioru', timer: 1500, showConfirmButton: false });
        return;
    }

    const items = [];
    let ok = true;
    document.querySelectorAll('.item-card').forEach(card => {
        const nazwa = card.querySelector('.item-nazwa').value.trim();
        const ilosc = card.querySelector('.item-ilosc').value.trim();
        const cena  = card.querySelector('.item-cena').value.trim();
        if (!nazwa) {
            card.querySelector('.item-nazwa').style.borderColor = '#e74c3c';
            ok = false;
        } else {
            card.querySelector('.item-nazwa').style.borderColor = '#dde0e5';
            items.push({ nazwa, ilosc: ilosc || null, cena: cena || null });
        }
    });

    if (!ok) {
        Swal.fire({ icon: 'warning', title: 'Uzupełnij nazwy towarów', timer: 1500, showConfirmButton: false });
        return;
    }

    const klientName = document.getElementById('clientId').selectedOptions[0]?.dataset.name ?? '';
    const confirm = await Swal.fire({
        title: 'Wysłać zlecenie?',
        html: `<strong>${klientName}</strong><br>${requestedDate}<br><small>${items.length} pozycj${items.length === 1 ? 'a' : 'e'}</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1a1a1a',
        confirmButtonText: '<i class="fas fa-paper-plane"></i> Wyślij',
        cancelButtonText: 'Anuluj',
    });
    if (!confirm.isConfirmed) return;

    const res  = await fetch('/handlowiec/zlecenia', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ client_id: clientId, requested_date: requestedDate, notes, items }),
    });
    const data = await res.json();

    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Zlecenie wysłane!', timer: 1800, showConfirmButton: false });
        window.location.href = '{{ route("handlowiec.zlecenia") }}';
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd zapisu.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}
</script>
@endsection