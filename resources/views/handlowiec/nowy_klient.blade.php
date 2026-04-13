@extends('layouts.handlowiec')
@section('title', 'Nowy klient')

@section('styles')
<style>
.h-form-wrap { padding:0;max-width:560px;margin:0 auto; }
.h-page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;margin-bottom:18px; }
.h-label { display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:5px; }
.h-input,.h-select,.h-textarea {
    width:100%;padding:12px 14px;border:1.5px solid #dde0e5;border-radius:10px;
    font-size:15px;outline:none;margin-bottom:12px;background:#fff;
    -webkit-appearance:none;appearance:none;
    transition:border-color .15s;
}
.h-input:focus,.h-select:focus,.h-textarea:focus { border-color:#1a1a1a; }
.h-input.error,.h-select.error { border-color:#e74c3c; }
.h-textarea { min-height:72px;resize:vertical; }
.h-section { background:#fff;border-radius:14px;padding:18px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,.07); }
.h-section-title {
    font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:900;
    letter-spacing:.06em;text-transform:uppercase;
    margin-bottom:14px;padding-bottom:8px;
    border-bottom:2px solid #f0f2f5;
    display:flex;align-items:center;gap:8px;color:#1a1a1a;
}
.h-section-title i { color:#888;font-size:14px; }
.h-row { display:grid;gap:10px; }
.h-row-2 { grid-template-columns:1fr 1fr; }
.h-row-3 { grid-template-columns:2fr 1fr 2fr; }
.h-field { display:flex;flex-direction:column; }
.field-error { font-size:11px;color:#e74c3c;margin-top:-8px;margin-bottom:8px;display:none; }
.field-error.show { display:block; }
.btn-gus {
    padding:12px 14px;background:#1a3c5e;color:#fff;border:none;
    border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;
    white-space:nowrap;margin-bottom:12px;
    transition:background .15s;
}
.btn-gus:hover { background:#122a42; }
.btn-gus:disabled { opacity:.6; }
.gus-status { font-size:12px;margin-bottom:10px;min-height:16px; }
.btn-copy-addr {
    width:100%;padding:10px;margin-bottom:12px;
    background:#f4f5f7;border:1.5px dashed #ccc;border-radius:10px;
    font-size:13px;font-weight:700;color:#555;cursor:pointer;
    transition:border-color .15s,color .15s;
    display:flex;align-items:center;justify-content:center;gap:8px;
}
.btn-copy-addr:hover { border-color:#1a1a1a;color:#1a1a1a; }
.btn-submit {
    width:100%;padding:18px;background:#1a1a1a;color:#fff;border:none;
    border-radius:14px;font-size:18px;font-weight:900;
    font-family:'Barlow Condensed',sans-serif;letter-spacing:.06em;
    text-transform:uppercase;cursor:pointer;margin-top:4px;
    transition:background .15s;
}
.btn-submit:hover { background:#333; }
.btn-submit:active { transform:scale(.98); }
.nip-exists { background:#fdf2f2;border:1.5px solid #e74c3c;border-radius:10px;padding:10px 14px;font-size:13px;color:#c0392b;margin-bottom:12px;display:none;align-items:center;gap:8px; }
.nip-exists.show { display:flex; }
</style>
@endsection

@section('content')
<div class="h-form-wrap">

    <a href="{{ route('handlowiec.klienci') }}" class="h-back-btn">
        <i class="fas fa-home"></i> Powrót
    </a>

    <div class="h-page-title"><i class="fas fa-building"></i> Nowy klient</div>

    {{-- ── DANE FIRMY ── --}}
    <div class="h-section">
        <div class="h-section-title"><i class="fas fa-building"></i> Dane firmy</div>

        {{-- NIP + GUS --}}
        <label class="h-label">NIP</label>
        <div style="display:flex;gap:8px;margin-bottom:0">
            <input type="text" id="nip" class="h-input" placeholder="np. 5260300141"
                   style="margin-bottom:0;flex:1" oninput="nipChanged()">
            <button type="button" class="btn-gus" id="btnGus" onclick="fetchGus()">
                <i class="fas fa-search"></i> GUS
            </button>
        </div>
        <div class="nip-exists" id="nipExists">
            <i class="fas fa-exclamation-triangle"></i>
            Kontrahent z tym NIP-em już istnieje w systemie!
        </div>
        <div class="gus-status" id="gusStatus"></div>

        <label class="h-label">Pełna nazwa <span style="color:#e74c3c">*</span></label>
        <input type="text" id="name" class="h-input" placeholder="np. Papiernia Sp. z o.o.">
        <div class="field-error" id="err-name"></div>

        <label class="h-label">Nazwa skrócona <span style="color:#e74c3c">*</span></label>
        <input type="text" id="short_name" class="h-input" placeholder="np. PAPIERNIA">
        <div class="field-error" id="err-short_name"></div>

        <div class="h-row h-row-2">
            <div class="h-field">
                <label class="h-label">Typ <span style="color:#e74c3c">*</span></label>
                <select id="type" class="h-select">
                    <option value="">– wybierz –</option>
                    <option value="pickup">Dostawca</option>
                    <option value="sale">Odbiorca</option>
                    <option value="both">Dostawca i odbiorca</option>
                </select>
                <div class="field-error" id="err-type"></div>
            </div>
            <div class="h-field" style="display:flex;flex-direction:column;justify-content:flex-end">
                {{-- placeholder --}}
            </div>
        </div>

        <label class="h-label">Ulica i numer <span style="color:#e74c3c">*</span></label>
        <input type="text" id="street" class="h-input" placeholder="np. ul. Fabryczna 12">
        <div class="field-error" id="err-street"></div>

        <div class="h-row h-row-2">
            <div class="h-field">
                <label class="h-label">Kod pocztowy <span style="color:#e74c3c">*</span></label>
                <input type="text" id="postal_code" class="h-input" placeholder="np. 70-001">
                <div class="field-error" id="err-postal_code"></div>
            </div>
            <div class="h-field">
                <label class="h-label">Miasto</label>
                <input type="text" id="city" class="h-input" placeholder="np. Szczecin">
            </div>
        </div>
    </div>

    {{-- ── ADRES ODBIORU ── --}}
    <div class="h-section">
        <div class="h-section-title"><i class="fas fa-map-marker-alt"></i> Adres odbioru / wysyłek</div>

        <button type="button" class="btn-copy-addr" onclick="kopiujAdres()">
            <i class="fas fa-copy"></i> Skopiuj z adresu głównego
        </button>

        <label class="h-label">Ulica i numer <span style="color:#e74c3c">*</span></label>
        <input type="text" id="addr_street" class="h-input" placeholder="np. ul. Magazynowa 5">
        <div class="field-error" id="err-addr_street"></div>

        <div class="h-row h-row-2">
            <div class="h-field">
                <label class="h-label">Kod pocztowy <span style="color:#e74c3c">*</span></label>
                <input type="text" id="addr_postal_code" class="h-input" placeholder="np. 70-001">
                <div class="field-error" id="err-addr_postal_code"></div>
            </div>
            <div class="h-field">
                <label class="h-label">Miasto <span style="color:#e74c3c">*</span></label>
                <input type="text" id="addr_city" class="h-input" placeholder="np. Szczecin">
                <div class="field-error" id="err-addr_city"></div>
            </div>
        </div>

        <label class="h-label">Godziny odbioru <span style="color:#e74c3c">*</span></label>
        <input type="text" id="addr_hours" class="h-input" placeholder="np. 7:00–15:00">
        <div class="field-error" id="err-addr_hours"></div>

        <label class="h-label">Uwagi do adresu</label>
        <textarea id="addr_notes" class="h-textarea" placeholder="np. wjazd od strony ul. Bocznej"></textarea>
    </div>

    {{-- ── KONTAKT ── --}}
    <div class="h-section">
        <div class="h-section-title"><i class="fas fa-user"></i> Osoba kontaktowa</div>

        <label class="h-label">Kategoria <span style="color:#e74c3c">*</span></label>
        <select id="contact_category" class="h-select">
            <option value="">– wybierz –</option>
            <option value="awizacje">Awizacje</option>
            <option value="faktury">Faktury</option>
            <option value="handlowe">Handlowe</option>
        </select>
        <div class="field-error" id="err-contact_category"></div>

        <label class="h-label">Imię i nazwisko <span style="color:#e74c3c">*</span></label>
        <input type="text" id="contact_name" class="h-input" placeholder="np. Jan Kowalski">
        <div class="field-error" id="err-contact_name"></div>

        <div class="h-row h-row-2">
            <div class="h-field">
                <label class="h-label">Telefon</label>
                <input type="tel" id="contact_phone" class="h-input" placeholder="np. 600 123 456">
            </div>
            <div class="h-field">
                <label class="h-label">E-mail</label>
                <input type="email" id="contact_email" class="h-input" placeholder="np. jan@firma.pl">
            </div>
        </div>
        <div class="field-error" id="err-contact_tel_email">Podaj telefon lub e-mail.</div>
    </div>

    <button type="button" class="btn-submit" onclick="zapisz()">
        <i class="fas fa-plus-circle"></i> Dodaj klienta
    </button>

</div>
@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

// ── GUS ──────────────────────────────────────────────────────────────────────
async function fetchGus() {
    const nip    = document.getElementById('nip').value.trim();
    const status = document.getElementById('gusStatus');
    if (!nip) { status.innerHTML = '<span style="color:#e74c3c">Wpisz NIP.</span>'; return; }

    const btn = document.getElementById('btnGus');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    status.innerHTML = '';

    try {
        const res  = await fetch(`/gus?nip=${encodeURIComponent(nip)}`);
        const data = await res.json();

        if (data.error) {
            status.innerHTML = `<span style="color:#e74c3c"><i class="fas fa-exclamation-circle me-1"></i>${data.error}</span>`;
        } else {
            document.getElementById('name').value        = data.nazwa  || '';
            document.getElementById('street').value      = data.adres  || '';
            document.getElementById('postal_code').value = data.kod    || '';
            document.getElementById('city').value        = data.miasto || '';
            status.innerHTML = '<span style="color:#27ae60"><i class="fas fa-check-circle me-1"></i>Pobrano z GUS</span>';
        }
    } catch(e) {
        status.innerHTML = '<span style="color:#e74c3c">Błąd połączenia.</span>';
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-search"></i> GUS';
}

// ── Sprawdź NIP live ──────────────────────────────────────────────────────────
let nipTimer = null;
function nipChanged() {
    clearTimeout(nipTimer);
    document.getElementById('nipExists').classList.remove('show');
    const nip = document.getElementById('nip').value.trim();
    if (nip.length < 8) return;
    nipTimer = setTimeout(async () => {
        const res  = await fetch(`/handlowiec/check-nip?nip=${encodeURIComponent(nip)}`);
        const data = await res.json();
        if (data.exists) {
            document.getElementById('nipExists').classList.add('show');
        }
    }, 500);
}

// ── Kopiuj adres główny ───────────────────────────────────────────────────────
function kopiujAdres() {
    document.getElementById('addr_street').value      = document.getElementById('street').value;
    document.getElementById('addr_postal_code').value = document.getElementById('postal_code').value;
    document.getElementById('addr_city').value        = document.getElementById('city').value;
}

// ── Walidacja ─────────────────────────────────────────────────────────────────
const requiredFields = [
    { id: 'name',             errId: 'err-name',             msg: 'Pole wymagane.' },
    { id: 'short_name',       errId: 'err-short_name',       msg: 'Pole wymagane.' },
    { id: 'type',             errId: 'err-type',             msg: 'Wybierz typ.' },
    { id: 'street',           errId: 'err-street',           msg: 'Pole wymagane.' },
    { id: 'postal_code',      errId: 'err-postal_code',      msg: 'Pole wymagane.' },
    { id: 'addr_street',      errId: 'err-addr_street',      msg: 'Pole wymagane.' },
    { id: 'addr_postal_code', errId: 'err-addr_postal_code', msg: 'Pole wymagane.' },
    { id: 'addr_city',        errId: 'err-addr_city',        msg: 'Pole wymagane.' },
    { id: 'addr_hours',       errId: 'err-addr_hours',       msg: 'Pole wymagane.' },
    { id: 'contact_category', errId: 'err-contact_category', msg: 'Wybierz kategorię.' },
    { id: 'contact_name',     errId: 'err-contact_name',     msg: 'Pole wymagane.' },
];

function validate() {
    let ok = true;
    requiredFields.forEach(f => {
        const el  = document.getElementById(f.id);
        const err = document.getElementById(f.errId);
        if (!el || !el.value.trim()) {
            el?.classList.add('error');
            if (err) { err.textContent = f.msg; err.classList.add('show'); }
            ok = false;
        } else {
            el?.classList.remove('error');
            if (err) err.classList.remove('show');
        }
    });

    // Przynajmniej tel lub email
    const phone = document.getElementById('contact_phone').value.trim();
    const email = document.getElementById('contact_email').value.trim();
    const errTE = document.getElementById('err-contact_tel_email');
    if (!phone && !email) {
        errTE.classList.add('show');
        ok = false;
    } else {
        errTE.classList.remove('show');
    }

    return ok;
}

// ── Zapis ─────────────────────────────────────────────────────────────────────
async function zapisz() {
    if (!validate()) {
        Swal.fire({ icon: 'warning', title: 'Uzupełnij wymagane pola', timer: 1800, showConfirmButton: false });
        return;
    }

    const payload = {
        name:             document.getElementById('name').value.trim(),
        short_name:       document.getElementById('short_name').value.trim(),
        nip:              document.getElementById('nip').value.trim() || null,
        type:             document.getElementById('type').value,
        street:           document.getElementById('street').value.trim(),
        postal_code:      document.getElementById('postal_code').value.trim(),
        city:             document.getElementById('city').value.trim(),
        addr_street:      document.getElementById('addr_street').value.trim(),
        addr_postal_code: document.getElementById('addr_postal_code').value.trim(),
        addr_city:        document.getElementById('addr_city').value.trim(),
        addr_hours:       document.getElementById('addr_hours').value.trim() || null,
        addr_notes:       document.getElementById('addr_notes').value.trim() || null,
        contact_category: document.getElementById('contact_category').value,
        contact_name:     document.getElementById('contact_name').value.trim(),
        contact_phone:    document.getElementById('contact_phone').value.trim() || null,
        contact_email:    document.getElementById('contact_email').value.trim() || null,
    };

    const res  = await fetch('{{ route('handlowiec.klient-store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Klient dodany!', timer: 1500, showConfirmButton: false });
        window.location.href = '{{ route('handlowiec.klienci') }}';
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd zapisu.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}
</script>
@endsection
