@extends('layouts.handlowiec')
@section('title', 'Edycja: ' . $client->short_name)

@section('styles')
<style>
.h-form-wrap { padding:0;max-width:560px;margin:0 auto; }
.h-page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;margin-bottom:18px; }
.h-label { display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:5px; }
.h-input,.h-select,.h-textarea { width:100%;padding:12px 14px;border:1.5px solid #dde0e5;border-radius:10px;font-size:15px;outline:none;margin-bottom:12px;background:#fff;-webkit-appearance:none;appearance:none;transition:border-color .15s; }
.h-input:focus,.h-select:focus,.h-textarea:focus { border-color:#1a1a1a; }
.h-textarea { min-height:72px;resize:vertical; }
.h-section { background:#fff;border-radius:14px;padding:18px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,.07); }
.h-section-title { font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid #f0f2f5;display:flex;align-items:center;gap:8px;color:#1a1a1a; }
.h-section-title i { color:#888;font-size:14px; }
.h-row-2 { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
.addr-card,.contact-card { background:#f8f9fa;border-radius:10px;padding:12px 14px;margin-bottom:8px;display:flex;align-items:flex-start;justify-content:space-between;gap:10px; }
.addr-card-info,.contact-card-info { flex:1;min-width:0; }
.addr-card-title,.contact-card-title { font-weight:700;font-size:14px;color:#1a1a1a; }
.addr-card-sub,.contact-card-sub { font-size:12px;color:#888;margin-top:2px; }
.btn-del-sm { background:none;border:1px solid #e74c3c;color:#e74c3c;border-radius:8px;padding:6px 10px;cursor:pointer;font-size:12px;flex-shrink:0;transition:background .1s; }
.btn-del-sm:hover { background:#fdf2f2; }
.btn-edit-sm { background:none;border:1px solid #dde0e5;color:#555;border-radius:8px;padding:6px 10px;cursor:pointer;font-size:12px;flex-shrink:0;transition:background .1s;margin-right:4px; }
.btn-edit-sm:hover { background:#f4f5f7; }
.add-form { background:#fff;border:1.5px solid #dde0e5;border-radius:12px;padding:14px;margin-top:8px;display:none; }
.add-form.open { display:block; }
.inline-edit-form { background:#fff;border:1.5px solid #2980b9;border-radius:12px;padding:14px;margin-top:6px;display:none; }
.inline-edit-form.open { display:block; }
.btn-add-inline { width:100%;padding:10px;margin-top:4px;background:#f4f5f7;border:1.5px dashed #ccc;border-radius:10px;font-size:13px;font-weight:700;color:#555;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:border-color .15s; }
.btn-add-inline:hover { border-color:#1a1a1a;color:#1a1a1a; }
.category-badge { display:inline-block;font-size:10px;font-weight:700;padding:1px 7px;border-radius:8px;background:#e8eaed;color:#555;margin-right:6px;text-transform:uppercase;letter-spacing:.04em; }
.btn-save-main { width:100%;padding:16px;background:#1a1a1a;color:#fff;border:none;border-radius:14px;font-size:17px;font-weight:900;font-family:'Barlow Condensed',sans-serif;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;margin-top:4px;transition:background .15s; }
.btn-save-main:hover { background:#333; }
.btn-save-sm { padding:8px 16px;background:#1a1a1a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer; }
.btn-cancel-sm { padding:8px 16px;background:#f4f5f7;border:1px solid #dde0e5;color:#555;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer; }
</style>
@endsection

@section('content')
<div class="h-form-wrap">

    <a href="{{ route('handlowiec.klienci') }}" class="h-back-btn">
        <i class="fas fa-home"></i> Powrót
    </a>

    <div class="h-page-title">{{ $client->short_name }}</div>

    {{-- DANE FIRMY --}}
    <div class="h-section">
        <div class="h-section-title"><i class="fas fa-building"></i> Dane firmy</div>

        <label class="h-label">Pełna nazwa *</label>
        <input type="text" id="name" class="h-input" value="{{ $client->name }}">

        <label class="h-label">Nazwa skrócona *</label>
        <input type="text" id="short_name" class="h-input" value="{{ $client->short_name }}">

        <label class="h-label">NIP</label>
        <input type="text" id="nip" class="h-input" value="{{ $client->nip }}">

        <label class="h-label">Typ *</label>
        <select id="type" class="h-select">
            <option value="pickup" {{ $client->type === 'pickup' ? 'selected' : '' }}>Dostawca</option>
            <option value="sale"   {{ $client->type === 'sale'   ? 'selected' : '' }}>Odbiorca</option>
            <option value="both"   {{ $client->type === 'both'   ? 'selected' : '' }}>Dostawca i odbiorca</option>
        </select>

        <label class="h-label">Ulica i numer *</label>
        <input type="text" id="street" class="h-input" value="{{ $client->street }}">

        <div class="h-row-2">
            <div>
                <label class="h-label">Kod pocztowy *</label>
                <input type="text" id="postal_code" class="h-input" value="{{ $client->postal_code }}">
            </div>
            <div>
                <label class="h-label">Miasto</label>
                <input type="text" id="city" class="h-input" value="{{ $client->city }}">
            </div>
        </div>

        <button class="btn-save-main" onclick="zapiszDane()">
            <i class="fas fa-save"></i> Zapisz dane firmy
        </button>
    </div>

    {{-- ADRESY --}}
    <div class="h-section">
        <div class="h-section-title"><i class="fas fa-map-marker-alt"></i> Adresy odbioru</div>

        <div id="addrList">
            @forelse($client->addresses as $addr)
            <div class="addr-card" id="addr-{{ $addr->id }}">
                <div class="addr-card-info">
                    <div class="addr-card-title">{{ $addr->city }}@if($addr->postal_code), {{ $addr->postal_code }}@endif</div>
                    <div class="addr-card-sub">{{ $addr->street }}</div>
                    @if($addr->hours)<div class="addr-card-sub"><i class="fas fa-clock" style="font-size:10px"></i> {{ $addr->hours }}</div>@endif
                    @if($addr->notes)<div class="addr-card-sub">{{ $addr->notes }}</div>@endif
                </div>
                <div style="display:flex;gap:4px;flex-shrink:0">
                    <button class="btn-edit-sm" onclick="toggleEditAddr({{ $addr->id }})"><i class="fas fa-pen"></i></button>
                    <button class="btn-del-sm"  onclick="usunAdres({{ $addr->id }})"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="inline-edit-form" id="edit-addr-{{ $addr->id }}">
                <label class="h-label">Ulica *</label>
                <input type="text" class="h-input ea-street" value="{{ $addr->street }}">
                <div class="h-row-2">
                    <div><label class="h-label">Kod pocztowy *</label><input type="text" class="h-input ea-postal" value="{{ $addr->postal_code }}"></div>
                    <div><label class="h-label">Miasto *</label><input type="text" class="h-input ea-city" value="{{ $addr->city }}"></div>
                </div>
                <label class="h-label">Godziny</label>
                <input type="text" class="h-input ea-hours" value="{{ $addr->hours }}" placeholder="np. 7:00–15:00">
                <label class="h-label">Uwagi</label>
                <textarea class="h-textarea ea-notes">{{ $addr->notes }}</textarea>
                <div style="display:flex;gap:8px;margin-top:4px">
                    <button class="btn-save-sm" onclick="zapiszAdres({{ $addr->id }})"><i class="fas fa-save"></i> Zapisz</button>
                    <button class="btn-cancel-sm" onclick="toggleEditAddr({{ $addr->id }})">Anuluj</button>
                </div>
            </div>
            @empty
            <div style="font-size:13px;color:#aaa;text-align:center;padding:10px">Brak adresów</div>
            @endforelse
        </div>

        <button class="btn-add-inline" onclick="toggleAddAddrForm()"><i class="fas fa-plus"></i> Dodaj adres</button>
        <div class="add-form" id="addAddrForm">
            <label class="h-label">Ulica *</label><input type="text" id="na_street" class="h-input">
            <div class="h-row-2">
                <div><label class="h-label">Kod pocztowy *</label><input type="text" id="na_postal" class="h-input"></div>
                <div><label class="h-label">Miasto *</label><input type="text" id="na_city" class="h-input"></div>
            </div>
            <label class="h-label">Godziny</label><input type="text" id="na_hours" class="h-input" placeholder="np. 7:00–15:00">
            <label class="h-label">Uwagi</label><textarea id="na_notes" class="h-textarea"></textarea>
            <div style="display:flex;gap:8px;margin-top:4px">
                <button class="btn-save-sm" onclick="dodajAdres()"><i class="fas fa-save"></i> Zapisz</button>
                <button class="btn-cancel-sm" onclick="toggleAddAddrForm()">Anuluj</button>
            </div>
        </div>
    </div>

    {{-- KONTAKTY --}}
    <div class="h-section">
        <div class="h-section-title"><i class="fas fa-user"></i> Kontakty</div>

        <div id="contactList">
            @forelse($client->contacts as $contact)
            <div class="contact-card" id="contact-{{ $contact->id }}">
                <div class="contact-card-info">
                    <div class="contact-card-title">
                        <span class="category-badge">{{ \App\Models\ClientContact::CATEGORIES[$contact->category] ?? $contact->category }}</span>
                        {{ $contact->name }}
                    </div>
                    @if($contact->phone)<div class="contact-card-sub"><i class="fas fa-phone" style="font-size:10px"></i> {{ $contact->phone }}</div>@endif
                    @if($contact->email)<div class="contact-card-sub"><i class="fas fa-envelope" style="font-size:10px"></i> {{ $contact->email }}</div>@endif
                </div>
                <button class="btn-del-sm" onclick="usunKontakt({{ $contact->id }})"><i class="fas fa-trash"></i></button>
            </div>
            @empty
            <div style="font-size:13px;color:#aaa;text-align:center;padding:10px">Brak kontaktów</div>
            @endforelse
        </div>

        <button class="btn-add-inline" onclick="toggleAddContactForm()"><i class="fas fa-plus"></i> Dodaj kontakt</button>
        <div class="add-form" id="addContactForm">
            <label class="h-label">Kategoria *</label>
            <select id="nc_category" class="h-select">
                <option value="">– wybierz –</option>
                <option value="awizacje">Awizacje</option>
                <option value="faktury">Faktury</option>
                <option value="handlowe">Handlowe</option>
            </select>
            <label class="h-label">Imię i nazwisko *</label><input type="text" id="nc_name" class="h-input">
            <div class="h-row-2">
                <div><label class="h-label">Telefon</label><input type="tel" id="nc_phone" class="h-input"></div>
                <div><label class="h-label">E-mail</label><input type="email" id="nc_email" class="h-input"></div>
            </div>
            <div style="display:flex;gap:8px;margin-top:4px">
                <button class="btn-save-sm" onclick="dodajKontakt()"><i class="fas fa-save"></i> Zapisz</button>
                <button class="btn-cancel-sm" onclick="toggleAddContactForm()">Anuluj</button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
const CSRF   = '{{ csrf_token() }}';
const CLIENT = {{ $client->id }};

function selectCat(btn) {
    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('nc_category').value = btn.dataset.val;
}

async function zapiszDane() {
    const payload = {
        name:        document.getElementById('name').value.trim(),
        short_name:  document.getElementById('short_name').value.trim(),
        nip:         document.getElementById('nip').value.trim() || null,
        type:        document.getElementById('type').value,
        street:      document.getElementById('street').value.trim(),
        postal_code: document.getElementById('postal_code').value.trim(),
        city:        document.getElementById('city').value.trim(),
    };
    if (!payload.name || !payload.short_name || !payload.type || !payload.street || !payload.postal_code) {
        Swal.fire({ icon:'warning', title:'Uzupełnij wymagane pola', timer:1500, showConfirmButton:false }); return;
    }
    const res  = await fetch(`/handlowiec/klienci/${CLIENT}/update`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body:JSON.stringify(payload),
    });
    const data = await res.json();
    if (data.success) Swal.fire({ icon:'success', title:'Zapisano!', timer:1200, showConfirmButton:false });
    else Swal.fire({ icon:'error', title:'Błąd', text: data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd.' });
}

function toggleAddAddrForm() { document.getElementById('addAddrForm').classList.toggle('open'); }
function toggleEditAddr(id)  { document.getElementById(`edit-addr-${id}`).classList.toggle('open'); }
function toggleAddContactForm() { document.getElementById('addContactForm').classList.toggle('open'); }

async function dodajAdres() {
    const s = document.getElementById('na_street').value.trim();
    const p = document.getElementById('na_postal').value.trim();
    const c = document.getElementById('na_city').value.trim();
    if (!s || !p || !c) { Swal.fire({ icon:'warning', title:'Uzupełnij wymagane pola', timer:1500, showConfirmButton:false }); return; }
    const res = await fetch(`/handlowiec/klienci/${CLIENT}/addresses`, {
        method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body:JSON.stringify({ street:s, postal_code:p, city:c, hours:document.getElementById('na_hours').value.trim()||null, notes:document.getElementById('na_notes').value.trim()||null }),
    });
    const data = await res.json();
    if (data.success) { Swal.fire({ icon:'success', title:'Dodano!', timer:1000, showConfirmButton:false }); setTimeout(()=>location.reload(),1000); }
}

async function zapiszAdres(id) {
    const f = document.getElementById(`edit-addr-${id}`);
    const payload = { street:f.querySelector('.ea-street').value.trim(), postal_code:f.querySelector('.ea-postal').value.trim(), city:f.querySelector('.ea-city').value.trim(), hours:f.querySelector('.ea-hours').value.trim()||null, notes:f.querySelector('.ea-notes').value.trim()||null };
    if (!payload.street || !payload.postal_code || !payload.city) { Swal.fire({ icon:'warning', title:'Uzupełnij wymagane pola', timer:1500, showConfirmButton:false }); return; }
    const res = await fetch(`/handlowiec/klienci/${CLIENT}/addresses/${id}/update`, {
        method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body:JSON.stringify(payload),
    });
    const data = await res.json();
    if (data.success) { Swal.fire({ icon:'success', title:'Zapisano!', timer:1000, showConfirmButton:false }); setTimeout(()=>location.reload(),1000); }
}

async function usunAdres(id) {
    const r = await Swal.fire({ title:'Usunąć adres?', icon:'warning', showCancelButton:true, confirmButtonColor:'#e74c3c', confirmButtonText:'Usuń', cancelButtonText:'Anuluj' });
    if (!r.isConfirmed) return;
    const res = await fetch(`/handlowiec/klienci/${CLIENT}/addresses/${id}/delete`, {
        method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body:JSON.stringify({}),
    });
    const data = await res.json();
    if (data.success) { document.getElementById(`addr-${id}`)?.remove(); document.getElementById(`edit-addr-${id}`)?.remove(); }
}

async function dodajKontakt() {
    const name  = document.getElementById('nc_name').value.trim();
    const phone = document.getElementById('nc_phone').value.trim();
    const email = document.getElementById('nc_email').value.trim();
    if (!name) { Swal.fire({ icon:'warning', title:'Podaj imię i nazwisko', timer:1500, showConfirmButton:false }); return; }
    if (!phone && !email) { Swal.fire({ icon:'warning', title:'Podaj telefon lub e-mail', timer:1500, showConfirmButton:false }); return; }
    const res = await fetch(`/handlowiec/klienci/${CLIENT}/contacts`, {
        method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body:JSON.stringify({ category:document.getElementById('nc_category').value, name, phone:phone||null, email:email||null }),
    });
    const data = await res.json();
    if (data.success) { Swal.fire({ icon:'success', title:'Dodano!', timer:1000, showConfirmButton:false }); setTimeout(()=>location.reload(),1000); }
}

async function usunKontakt(id) {
    const r = await Swal.fire({ title:'Usunąć kontakt?', icon:'warning', showCancelButton:true, confirmButtonColor:'#e74c3c', confirmButtonText:'Usuń', cancelButtonText:'Anuluj' });
    if (!r.isConfirmed) return;
    const res = await fetch(`/handlowiec/klienci/${CLIENT}/contacts/${id}/delete`, {
        method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body:JSON.stringify({}),
    });
    const data = await res.json();
    if (data.success) document.getElementById(`contact-${id}`)?.remove();
}
</script>
@endsection
