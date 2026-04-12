{{-- ═══════════════════════════════════════════════════════════
     JS obsługi modala zleceń
     Dołącz na końcu sekcji scripts w planning/index.blade.php
════════════════════════════════════════════════════════════════ --}}
<script>
// ── Stan modala ───────────────────────────────────────────────
let _modalData    = null;  // dane załadowane z serwera
let _editOrderId  = null;
let _modalType    = null;  // 'pickup' | 'sale'
let _driverData   = null;  // aktualny kierowca z driverów

// ── Otwieranie modala ─────────────────────────────────────────
async function openOrderModal(orderId = null, type = null, clientId = null, pickupRequestId = null, prefillGoods = null) {
    // Załaduj dane jeśli jeszcze nie ma
    if (!_modalData) {
        const res  = await fetch(`/biuro/orders/modal-data?date={{ $date->format('Y-m-d') }}`);
        _modalData = await res.json();
        buildQuickButtons();
    }

    _editOrderId = orderId;
    _modalType   = type ?? 'pickup';

    // ── Pełny reset formularza ──────────────────────────────────
    const form = document.getElementById('orderForm');
    form.reset();

    // Wyczyść wszystkie selecty do pustej opcji
    ['order_driver','order_client','order_start','order_tractor','order_trailer'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    // Wyczyść pola tekstowe
    ['order_date','order_time','order_goods','order_notes',
     'order_ls_id','order_ls_display','order_pickup_request_id'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    // Usuń klasy walidacji
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Ukryj sekcje
    const lsSection = document.getElementById('ls_section');
    const lsTable   = document.getElementById('ls_table_wrap');
    if (lsSection) lsSection.style.display = 'none';
    if (lsTable)   lsTable.style.display   = 'none';

    document.getElementById('order_id').value    = orderId ?? '';
    document.getElementById('order_type').value  = _modalType;

    // Zapisz pickup_request_id jeśli przekazany
    const prInput = document.getElementById('order_pickup_request_id');
    if (prInput) prInput.value = pickupRequestId ?? '';

    const delBtn = document.getElementById('order_delete_btn');
    if (delBtn) delBtn.style.display = orderId ? 'block' : 'none';

    // Buduj selecty z uwzględnieniem typu zlecenia
    buildSelects(_modalType);

    // Ustaw datę z datepickera
    document.getElementById('order_date').value = currentDate;

    // Ustaw aktywnego kierowcę (bez domyślnych pojazdów)
    if (currentDriver) {
        document.getElementById('order_driver').value = currentDriver;
    }
    onDriverChange(); // ukryj/pokaż START zależnie od kierowcy

    // Ustaw kontrahenta jeśli przekazany przez przycisk
    if (clientId) {
        document.getElementById('order_client').value = clientId;
        checkIfDE(clientId);
    }

    // Prefill towarów ze zlecenia handlowca
    if (prefillGoods) {
        document.getElementById('order_goods').value = prefillGoods;
    }

    // Tytuł modala
    const header = document.getElementById('orderModalHeader');
    const driverColor = getDrColor(currentDriver);

    if (pickupRequestId) {
        header.style.background = `linear-gradient(135deg, #f39c12, #d68910)`;
    } else {
        header.style.background = orderId
            ? `linear-gradient(135deg, #3498db, #2980b9)`
            : `linear-gradient(135deg, ${driverColor}, ${shadeColor(driverColor, -15)})`;
    }

    document.getElementById('orderModalTitle').textContent  = orderId ? 'EDYCJA ZLECENIA' : 'NOWE ZLECENIE';
    document.getElementById('order_submit_label').textContent = orderId ? 'Zapisz zmiany' : 'Zapisz';

    const badge = document.getElementById('orderTypeBadge');
    if (badge) badge.textContent = _modalType === 'sale' ? 'WYSYŁKA' : (pickupRequestId ? 'ZL. HANDLOWCA' : 'ODBIÓR');

    // Jeśli edycja – załaduj dane zlecenia
    if (orderId) await loadOrderData(orderId);

    new bootstrap.Modal(document.getElementById('orderModal')).show();
}

async function loadOrderData(id) {
    const res   = await fetch(`/biuro/orders/${id}`);
    const order = await res.json();
    if (!order) return;

    _modalType = order.type;
    
    // Przebuduj selecty dla właściwego typu
    buildSelects(_modalType);

    document.getElementById('order_date').value     = order.planned_date;
    document.getElementById('order_driver').value   = order.driver_id;
    onDriverChange();
    document.getElementById('order_client').value   = order.client_id;
    document.getElementById('order_start').value    = order.start_client_id;
    document.getElementById('order_tractor').value  = order.tractor_id;
    document.getElementById('order_trailer').value  = order.trailer_id ?? '';
    document.getElementById('order_time').value     = order.planned_time ?? '';
    document.getElementById('order_goods').value    = order.fractions_note;
    document.getElementById('order_notes').value    = order.notes ?? '';
    document.getElementById('order_type').value     = order.type;

    if (order.lieferschein_id) {
        document.getElementById('order_ls_id').value      = order.lieferschein_id;
        document.getElementById('order_ls_display').value = order.lieferschein?.number ?? '';
    }

    checkIfDE(order.client_id);
    // NIE ustawiaj automatycznie pojazdów - tylko przez gwiazdkę
}

// ── Budowanie selectów ────────────────────────────────────────
function buildSelects(orderType = 'pickup') {
    const d = _modalData;

    // Kierowcy
    fillSelect('order_driver', d.drivers, r => ({ v: r.id, t: r.name }));

    // Klienci - filtruj według typu zlecenia
    const filteredClients = filterClientsByType(d.clients, orderType);
    fillSelect('order_client', filteredClients, r => ({ v: r.id, t: r.short_name }));
    
    // Start - wszystkie miejsca (bez filtrowania)
    fillSelect('order_start', d.clients, r => ({ v: r.id, t: r.short_name }));

    // Ciągniki
    fillSelect('order_tractor', d.tractors, r => ({
        v: r.id,
        t: r.plate + (r.subtype ? ` (${r.subtype})` : '')
    }));

    // Naczepy
    const trailSel = document.getElementById('order_trailer');
    if (!trailSel) return;
    trailSel.innerHTML = '<option value="">– brak –</option>';
    d.trailers.forEach(r => {
        const o = document.createElement('option');
        o.value = r.id;
        o.textContent = r.plate + (r.subtype ? ` (${r.subtype})` : '');
        trailSel.appendChild(o);
    });
}

// ── Filtrowanie klientów według typu zlecenia ─────────────────
function filterClientsByType(clients, orderType) {
    return clients.filter(c => {
        if (c.type === 'both') return true;
        if (!c.type) return true;
        return c.type === orderType;
    });
}

function fillSelect(id, items, mapper) {
    const sel = document.getElementById(id);
    if (!sel) { console.warn('fillSelect: element #' + id + ' nie istnieje'); return; }
    sel.innerHTML = '<option value="">– wybierz –</option>';
    items.forEach(r => {
        const m = mapper(r);
        const o = document.createElement('option');
        o.value = m.v; o.textContent = m.t;
        sel.appendChild(o);
    });
}

// ── Przyciski szybkie ─────────────────────────────────────────
function buildQuickButtons() {
    const d = _modalData;

    const gb = document.getElementById('goods_buttons');
    if (!gb) return;
    gb.innerHTML = '';
    (d.quickGoods || d.quick_goods || []).forEach(b => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'om-qbtn';
        btn.textContent = b.label;
        btn.onclick = () => {
            const el = document.getElementById('order_goods');
            el.value = el.value ? el.value + ', ' + b.label : b.label;
        };
        gb.appendChild(btn);
    });

    const nb = document.getElementById('notes_buttons');
    if (!nb) return;
    nb.innerHTML = '';
    (d.quickNotes || d.quick_notes || []).forEach(b => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'om-qbtn';
        btn.textContent = b.label;
        btn.onclick = () => {
            const el = document.getElementById('order_notes');
            el.value = el.value ? el.value + '\n' + b.label : b.label;
        };
        nb.appendChild(btn);
    });

    // Tabela LS
    const tbody = document.getElementById('ls_table_body');
    if (!tbody) return;
    tbody.innerHTML = '';
    if ((d.freeLs || d.free_ls || []).length) {
        (d.freeLs || d.free_ls).forEach(ls => {
            const tr = document.createElement('tr');
            tr.style.cursor = 'pointer';
            const dateMatch = ls.date && ls.date.substring(0,10) === currentDate;
            tr.innerHTML = `
                <td>${ls.number}</td>
                <td>${ls.date ? ls.date.substring(8,10) + '.' + ls.date.substring(5,7) + '.' + ls.date.substring(0,4) : '–'}</td>
                <td>${ls.goods?.name ?? '–'}</td>
                <td>${ls.time_window}</td>
                <td>${ls.client?.short_name ?? '–'}</td>
                <td>${ls.importer?.name ?? '–'}</td>`;
            if (dateMatch) {
                tr.style.background = '#fffbe6';
                tr.style.fontWeight = '700';
            }
            tr.onclick = () => selectLs(ls.id, ls.number);
            tbody.appendChild(tr);
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-2">Brak wolnych LS na ten dzień</td></tr>';
    }
}

// ── Pomocnicze ────────────────────────────────────────────────
function getDrColor(driverId) {
    if (!_modalData || !driverId) return '#6EBF58';
    const dr = _modalData.drivers.find(d => d.id == driverId);
    return dr?.color ?? '#6EBF58';
}

function shadeColor(color, pct) {
    const num = parseInt(color.replace('#',''), 16);
    const r = Math.min(255, Math.max(0, (num >> 16) + pct));
    const g = Math.min(255, Math.max(0, ((num >> 8) & 0xff) + pct));
    const b = Math.min(255, Math.max(0, (num & 0xff) + pct));
    return '#' + ((1<<24)+(r<<16)+(g<<8)+b).toString(16).slice(1);
}

function checkIfDE(clientId) {
    const section = document.getElementById('ls_section');
    if (!section) return;
    if (!_modalData || !clientId) { section.style.display = 'none'; return; }
    const client = _modalData.clients.find(c => c.id == clientId);
    section.style.display = (client?.country === 'DE') ? 'block' : 'none';
}

function setStart(which) {
    if (!_modalData) return;
    const id = which === 'leipa' ? _modalData.leipa : _modalData.ewrant;
    if (id) document.getElementById('order_start').value = id;
}

function setFavTractor() {
    const driverId = document.getElementById('order_driver').value;
    if (!_modalData || !driverId) return;
    const dr = _modalData.drivers.find(d => d.id == driverId);
    if (dr?.tractor_id) document.getElementById('order_tractor').value = dr.tractor_id;
}

const EXTERNAL_DRIVERS = [4, 7]; // Recykler, Zewnętrzny

function onDriverChange() {
    const driverId = parseInt(document.getElementById('order_driver').value);
    const startRow = document.getElementById('start_row');
    const startSel = document.getElementById('order_start');
    if (EXTERNAL_DRIVERS.includes(driverId)) {
        startRow.style.display = 'none';
        startSel.removeAttribute('required');
        startSel.value = '';
    } else {
        startRow.style.display = '';
        startSel.setAttribute('required', 'required');
    }
}

function setFavTrailer() {
    const driverId = document.getElementById('order_driver').value;
    if (!_modalData || !driverId) return;
    const dr = _modalData.drivers.find(d => d.id == driverId);
    if (dr?.trailer_id) document.getElementById('order_trailer').value = dr.trailer_id;
}

function selectLs(id, number) {
    document.getElementById('order_ls_id').value      = id;
    document.getElementById('order_ls_display').value = number;
    document.getElementById('ls_table_wrap').style.display = 'none';
}

function toggleLsTable() {
    const wrap = document.getElementById('ls_table_wrap');
    wrap.style.display = wrap.style.display === 'none' ? 'block' : 'none';
}

// Zmiana klienta → pokaż/ukryj LS (event delegation - działa zawsze)
document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'order_client') {
        checkIfDE(e.target.value);
    }
});

// ── Zapis ────────────────────────────────────────────────────
async function submitOrder() {
    const form    = document.getElementById('orderForm');
    const orderId = document.getElementById('order_id').value;
    const url     = orderId ? `/biuro/orders/${orderId}` : '{{ route('biuro.orders.store') }}';

    const formData = new FormData(form);
    if (orderId) formData.append('_method', 'PUT');

    // Walidacja po stronie klienta
    const required = ['date', 'driver_id', 'client_id', 'tractor_id', 'fractions_note'];
    let ok = true;
    required.forEach(name => {
        const el = form.querySelector(`[name="${name}"]`);
        if (!el || !el.value.trim()) {
            el?.classList.add('is-invalid');
            ok = false;
        } else {
            el?.classList.remove('is-invalid');
        }
    });
    if (!ok) {
        Swal.fire({ icon: 'warning', title: 'Uzupełnij wymagane pola', timer: 2000, showConfirmButton: false });
        return;
    }

    const res  = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData,
    });
    const data = await res.json();

    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();

        if (!orderId) {
            // Nowe zlecenie – pytaj o datę na placu
            await Swal.fire({ icon: 'success', title: 'Dodano!', text: data.message, timer: 1200, showConfirmButton: false });
            await askPlacDate(data.id, data.planned_date);
        } else {
            let msg = data.message;
            await Swal.fire({ icon: 'success', title: 'Zaktualizowano!', text: msg, timer: 2000, showConfirmButton: false });
        }
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}

async function askPlacDate(orderId, plannedDate) {
    const pd = new Date(plannedDate);

    const prevWorkday = new Date(pd);
    prevWorkday.setDate(prevWorkday.getDate() - 1);
    while (prevWorkday.getDay() === 0 || prevWorkday.getDay() === 6) {
        prevWorkday.setDate(prevWorkday.getDate() - 1);
    }

    const fmt = d => d.toISOString().split('T')[0];
    const fmtPl = d => d.toLocaleDateString('pl-PL', { weekday:'long', day:'numeric', month:'long' });

    const { value: choice } = await Swal.fire({
        title: 'Kiedy zlecenie widoczne na placu?',
        icon: 'question',
        showCancelButton: true,
        showDenyButton: true,
        showConfirmButton: true,
        confirmButtonText: `0 – tego dnia (${fmtPl(pd)})`,
        denyButtonText: `-1 – dzień wcześniej (${fmtPl(prevWorkday)})`,
        cancelButtonText: 'Nie wysyłaj na plac',
        confirmButtonColor: '#27ae60',
        denyButtonColor: '#f39c12',
    });

    let placDate = null;

    if (choice === true) {
        placDate = fmt(pd);
    } else if (choice === false) {
        placDate = fmt(prevWorkday);
    } else {
        return;
    }

    await fetch(`/biuro/orders/${orderId}/plac-date`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ plac_date: placDate }),
    });
}

// ── Usuwanie ─────────────────────────────────────────────────
async function deleteOrder() {
    const orderId = document.getElementById('order_id').value;
    const result  = await Swal.fire({
        title: 'Usunąć zlecenie?',
        text: 'Operacja jest nieodwracalna.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        confirmButtonText: 'Tak, usuń',
        cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;

    const res  = await fetch(`/biuro/orders/${orderId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Usunięto!', timer: 1500, showConfirmButton: false });
        location.reload();
    }
}
</script>

<script>
// ══════════════════════════════════════════════════════════════
//  MODAL EDYCJI ZLECENIA
// ══════════════════════════════════════════════════════════════
let _editModalData = null;
let _editOrderType = 'pickup';

async function openEditOrderModal(orderId) {
    if (!_editModalData) {
        const res   = await fetch(`/biuro/orders/modal-data?date={{ $date->format('Y-m-d') }}`);
        _editModalData = await res.json();
        buildEditQuickButtons();
    }

    const res   = await fetch(`/biuro/orders/${orderId}`);
    const order = await res.json();
    if (!order) return;

    _editOrderType = order.type;

    buildEditSelects(_editOrderType);

    document.getElementById('edit_order_id').value     = orderId;
    document.getElementById('edit_order_type').value   = order.type;
    document.getElementById('edit_order_date').value   = order.planned_date;
    document.getElementById('edit_order_driver').value = order.driver_id;
    onEditDriverChange();
    document.getElementById('edit_order_client').value = order.client_id;
    document.getElementById('edit_order_start').value  = order.start_client_id ?? '';
    document.getElementById('edit_order_tractor').value = order.tractor_id ?? '';
    document.getElementById('edit_order_trailer').value = order.trailer_id ?? '';
    document.getElementById('edit_order_time').value   = order.planned_time ?? '';
    document.getElementById('edit_order_goods').value  = order.fractions_note ?? '';
    document.getElementById('edit_order_notes').value  = order.notes ?? '';
    document.getElementById('edit_order_plac_date').value = order.plac_date ?? '';

    if (order.lieferschein_id) {
        document.getElementById('edit_order_ls_id').value      = order.lieferschein_id;
        document.getElementById('edit_order_ls_display').value = order.lieferschein?.number ?? '';
    } else {
        document.getElementById('edit_order_ls_id').value      = '';
        document.getElementById('edit_order_ls_display').value = '';
    }

    checkIfEditDE(order.client_id);

    const dr    = _editModalData.drivers.find(d => d.id == order.driver_id);
    const color = dr?.color ?? '#3498db';
    document.getElementById('editOrderModalHeader').style.background =
        `linear-gradient(135deg, ${color}, ${shadeColor(color, -15)})`;

    document.getElementById('editOrderModalTitle').textContent = 'EDYCJA ZLECENIA';
    const editBadge = document.getElementById('editOrderTypeBadge');
    if (editBadge) editBadge.textContent = order.type === 'sale' ? 'WYSYŁKA' : 'ODBIÓR';

    document.getElementById('editOrderForm').querySelectorAll('.is-invalid')
        .forEach(el => el.classList.remove('is-invalid'));

    document.getElementById('edit_ls_table_wrap').style.display = 'none';

    new bootstrap.Modal(document.getElementById('editOrderModal')).show();
}

function buildEditSelects(orderType = 'pickup') {
    const d = _editModalData;
    
    fillEditSelect('edit_order_driver',  d.drivers,  r => ({ v: r.id, t: r.name }));
    
    const filteredClients = filterEditClientsByType(d.clients, orderType);
    fillEditSelect('edit_order_client',  filteredClients,  r => ({ v: r.id, t: r.short_name }));
    
    fillEditSelect('edit_order_start',   d.clients,  r => ({ v: r.id, t: r.short_name }));
    
    fillEditSelect('edit_order_tractor', d.tractors, r => ({ v: r.id, t: r.plate + (r.subtype ? ` (${r.subtype})` : '') }));

    const trailSel = document.getElementById('edit_order_trailer');
    if (trailSel) {
        trailSel.innerHTML = '<option value="">– brak –</option>';
        (d.trailers || []).forEach(r => {
            const o = document.createElement('option');
            o.value = r.id;
            o.textContent = r.plate + (r.subtype ? ` (${r.subtype})` : '');
            trailSel.appendChild(o);
        });
    }
}

function filterEditClientsByType(clients, orderType) {
    return clients.filter(c => {
        if (c.type === 'both') return true;
        if (!c.type) return true;
        return c.type === orderType;
    });
}

function fillEditSelect(id, items, mapper) {
    const sel = document.getElementById(id);
    if (!sel) return;
    sel.innerHTML = '<option value="">– wybierz –</option>';
    (items || []).forEach(r => {
        const m = mapper(r);
        const o = document.createElement('option');
        o.value = m.v; o.textContent = m.t;
        sel.appendChild(o);
    });
}

function buildEditQuickButtons() {
    const d = _editModalData;

    const gb = document.getElementById('edit_goods_buttons');
    if (gb) {
        gb.innerHTML = '';
        (d.quickGoods || d.quick_goods || []).forEach(b => {
            const btn = document.createElement('button');
            btn.type = 'button'; btn.className = 'om-qbtn'; btn.textContent = b.label;
            btn.onclick = () => {
                const el = document.getElementById('edit_order_goods');
                el.value = el.value ? el.value + ', ' + b.label : b.label;
            };
            gb.appendChild(btn);
        });
    }

    const nb = document.getElementById('edit_notes_buttons');
    if (nb) {
        nb.innerHTML = '';
        (d.quickNotes || d.quick_notes || []).forEach(b => {
            const btn = document.createElement('button');
            btn.type = 'button'; btn.className = 'om-qbtn'; btn.textContent = b.label;
            btn.onclick = () => {
                const el = document.getElementById('edit_order_notes');
                el.value = el.value ? el.value + '\n' + b.label : b.label;
            };
            nb.appendChild(btn);
        });
    }

    const tbody = document.getElementById('edit_ls_table_body');
    if (tbody) {
        tbody.innerHTML = '';
        const lsList = d.freeLs || d.free_ls || [];
        if (lsList.length) {
            lsList.forEach(ls => {
                const tr = document.createElement('tr');
                tr.style.cursor = 'pointer';
                const dateMatch = ls.date && ls.date.substring(0,10) === document.getElementById('edit_order_date').value;
                tr.innerHTML = `
                    <td>${ls.number}</td>
                    <td>${ls.date ? ls.date.substring(8,10)+'.'+ls.date.substring(5,7)+'.'+ls.date.substring(0,4) : '–'}</td>
                    <td>${ls.goods?.name ?? '–'}</td>
                    <td>${ls.time_window}</td>
                    <td>${ls.client?.short_name ?? '–'}</td>
                    <td>${ls.importer?.name ?? '–'}</td>`;
                if (dateMatch) { tr.style.background = '#fffbe6'; tr.style.fontWeight = '700'; }
                tr.onclick = () => selectEditLs(ls.id, ls.number);
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-2">Brak wolnych LS</td></tr>';
        }
    }
}

function checkIfEditDE(clientId) {
    const section = document.getElementById('edit_ls_section');
    if (!section) return;
    if (!_editModalData || !clientId) { section.style.display = 'none'; return; }
    const client = _editModalData.clients.find(c => c.id == clientId);
    section.style.display = (client?.country === 'DE') ? 'block' : 'none';
}

function toggleEditLsTable() {
    const wrap = document.getElementById('edit_ls_table_wrap');
    wrap.style.display = wrap.style.display === 'none' ? 'block' : 'none';
}

function selectEditLs(id, number) {
    document.getElementById('edit_order_ls_id').value      = id;
    document.getElementById('edit_order_ls_display').value = number;
    document.getElementById('edit_ls_table_wrap').style.display = 'none';
}

function setEditStart(which) {
    if (!_editModalData) return;
    const id = which === 'leipa' ? _editModalData.leipa : _editModalData.ewrant;
    if (id) document.getElementById('edit_order_start').value = id;
}

function onEditDriverChange() {
    const driverId = parseInt(document.getElementById('edit_order_driver').value);
    const startRow = document.getElementById('edit_start_row');
    const startSel = document.getElementById('edit_order_start');
    if (EXTERNAL_DRIVERS.includes(driverId)) {
        startRow.style.display = 'none';
        startSel.removeAttribute('required');
        startSel.value = '';
    } else {
        startRow.style.display = '';
        startSel.setAttribute('required', 'required');
    }
}

function setEditFavTractor() {
    const driverId = document.getElementById('edit_order_driver').value;
    if (!_editModalData || !driverId) return;
    const dr = _editModalData.drivers.find(d => d.id == driverId);
    if (dr?.tractor_id) document.getElementById('edit_order_tractor').value = dr.tractor_id;
}

function setEditFavTrailer() {
    const driverId = document.getElementById('edit_order_driver').value;
    if (!_editModalData || !driverId) return;
    const dr = _editModalData.drivers.find(d => d.id == driverId);
    if (dr?.trailer_id) document.getElementById('edit_order_trailer').value = dr.trailer_id;
}

document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'edit_order_client') checkIfEditDE(e.target.value);
});

async function submitEditOrder() {
    const form    = document.getElementById('editOrderForm');
    const orderId = document.getElementById('edit_order_id').value;

    const required = ['edit_order_date','edit_order_driver','edit_order_client',
                      'edit_order_tractor','edit_order_goods'];
    let ok = true;
    required.forEach(id => {
        const el = document.getElementById(id);
        if (!el || !el.value.trim()) { el?.classList.add('is-invalid'); ok = false; }
        else el?.classList.remove('is-invalid');
    });
    if (!ok) {
        Swal.fire({ icon: 'warning', title: 'Uzupełnij wymagane pola', timer: 2000, showConfirmButton: false });
        return;
    }

    const formData = new FormData(form);
    formData.append('_method', 'PUT');

    const res  = await fetch(`/biuro/orders/${orderId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData,
    });
    const data = await res.json();

    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('editOrderModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Zaktualizowano!', text: data.message,
                          timer: 1800, showConfirmButton: false });
        location.reload();
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}

async function deleteEditOrder() {
    const orderId = document.getElementById('edit_order_id').value;
    const result  = await Swal.fire({
        title: 'Usunąć zlecenie?', text: 'Operacja jest nieodwracalna.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        confirmButtonText: 'Tak, usuń', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;

    const res  = await fetch(`/biuro/orders/${orderId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('editOrderModal')).hide();
        await Swal.fire({ icon: 'success', title: 'Usunięto!', timer: 1500, showConfirmButton: false });
        location.reload();
    }
}
</script>