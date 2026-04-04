@extends('layouts.app')

@section('title', 'Nowy kontrahent')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
    .section-label {
        font-family: var(--font-display);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--gray-3);
        padding: 12px 0 6px;
        border-bottom: 1px solid var(--gray-2);
        margin-bottom: 14px;
    }
    .btn-gus {
        background: #1a3c5e;
        color: #fff;
        border: none;
        white-space: nowrap;
        font-size: 13px;
    }
    .btn-gus:hover { background: #122a42; color: #fff; }
    .btn-gus:disabled { opacity: .6; }
    #gusStatus { font-size: 12px; margin-top: 3px; min-height: 16px; }
    .req { color: #e74c3c; margin-left: 2px; }
    .form-control.error, .form-select.error { border-color: #e74c3c; }
    .field-error { font-size: 11px; color: #e74c3c; margin-top: 3px; display: none; }
    .field-error.show { display: block; }
</style>
@endsection

@section('content')

<div class="d-flex flex-column align-items-center">
<div style="width:100%;max-width:560px">

    <div class="page-header">
        <h1>Nowy kontrahent</h1>
        <a href="{{ route('biuro.clients.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Powrót
        </a>
    </div>

    <div class="card">
        <div class="card-body px-4 py-3">
            <form id="clientForm" novalidate>
                @csrf

                {{-- Identyfikacja --}}
                <div class="section-label">Identyfikacja</div>

                <div class="row g-2 mb-2">
                    <div class="col-7">
                        <label class="form-label mb-1">NIP / VAT-DE</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="nip" name="nip"
                                   class="form-control" placeholder="np. 5260300141">
                            <button type="button" class="btn btn-gus" id="btnGus">
                                <i class="fa-solid fa-magnifying-glass"></i> GUS
                            </button>
                        </div>
                        <div id="gusStatus" class="text-muted"></div>
                    </div>
                    <div class="col-3">
                        <label class="form-label mb-1">Nr BDO</label>
                        <input type="text" name="bdo" id="bdo" class="form-control form-control-sm">
                    </div>
                    <div class="col-2">
                        <label class="form-label mb-1">Kraj</label>
                        <select name="country" class="form-select form-select-sm">
                            @foreach($countries as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Dane firmy --}}
                <div class="section-label">Dane firmy</div>

                <div class="mb-2">
                    <label class="form-label mb-1">Pełna nazwa <span class="req">*</span></label>
                    <input type="text" name="name" id="name" class="form-control form-control-sm" required>
                    <div class="field-error" id="err-name">Pole wymagane.</div>
                </div>

                <div class="mb-2">
                    <label class="form-label mb-1">Nazwa skrócona <span class="req">*</span></label>
                    <input type="text" name="short_name" id="short_name" class="form-control form-control-sm" required>
                    <div class="field-error" id="err-short_name">Pole wymagane.</div>
                </div>

                <div class="mb-2">
                    <label class="form-label mb-1">Ulica i numer <span class="req">*</span></label>
                    <input type="text" name="street" id="street" class="form-control form-control-sm" required>
                    <div class="field-error" id="err-street">Pole wymagane.</div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-4">
                        <label class="form-label mb-1">Kod pocztowy <span class="req">*</span></label>
                        <input type="text" name="postal_code" id="postal_code" class="form-control form-control-sm" required>
                        <div class="field-error" id="err-postal_code">Pole wymagane.</div>
                    </div>
                    <div class="col-8">
                        <label class="form-label mb-1">Miasto</label>
                        <input type="text" name="city" id="city" class="form-control form-control-sm">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label mb-1">Typ <span class="req">*</span></label>
                        <select name="type" id="type" class="form-select form-select-sm" required>
                            <option value="">– wybierz –</option>
                            @foreach($types as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="field-error" id="err-type">Wybierz typ kontrahenta.</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-1">Handlowiec <span class="req">*</span></label>
                        <select name="salesman_id" id="salesman_id" class="form-select form-select-sm" required>
                            <option value="">– wybierz –</option>
                            @foreach($salesmen as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                        <div class="field-error" id="err-salesman_id">Wybierz handlowca.</div>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label mb-1">Telefon</label>
                        <input type="text" name="phone" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-1">E-mail</label>
                        <input type="email" name="email" class="form-control form-control-sm">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label mb-1">Uwagi</label>
                    <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
                </div>

                <input type="hidden" name="add_address" id="add_address" value="0">

                <div class="d-flex gap-2 justify-content-end pt-2 border-top">
                    <a href="{{ route('biuro.clients.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-xmark"></i> Anuluj
                    </a>
                    <button type="submit" class="btn btn-add btn-sm" id="btnSubmit">
                        <i class="fa-solid fa-plus"></i> Dodaj kontrahenta
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
</div>

@endsection

@section('scripts')
<script>
const requiredFields = [
    { id: 'name',        errId: 'err-name',        msg: 'Pole wymagane.' },
    { id: 'short_name',  errId: 'err-short_name',  msg: 'Pole wymagane.' },
    { id: 'street',      errId: 'err-street',      msg: 'Pole wymagane.' },
    { id: 'postal_code', errId: 'err-postal_code', msg: 'Pole wymagane.' },
    { id: 'type',        errId: 'err-type',        msg: 'Wybierz typ kontrahenta.' },
    { id: 'salesman_id', errId: 'err-salesman_id', msg: 'Wybierz handlowca.' },
];

function validateForm() {
    let valid = true;
    requiredFields.forEach(f => {
        const el  = document.getElementById(f.id);
        const err = document.getElementById(f.errId);
        if (!el.value.trim()) {
            el.classList.add('error');
            err.textContent = f.msg;
            err.classList.add('show');
            valid = false;
        } else {
            el.classList.remove('error');
            err.classList.remove('show');
        }
    });
    return valid;
}

requiredFields.forEach(f => {
    document.getElementById(f.id)?.addEventListener('input', () => {
        const el  = document.getElementById(f.id);
        const err = document.getElementById(f.errId);
        if (el.value.trim()) {
            el.classList.remove('error');
            err.classList.remove('show');
        }
    });
});

// ── GUS ──────────────────────────────────────────────────────────────────────
document.getElementById('btnGus').addEventListener('click', async function() {
    const nip    = document.getElementById('nip').value.trim();
    const status = document.getElementById('gusStatus');

    if (!nip) { status.innerHTML = '<span class="text-danger">Wpisz NIP.</span>'; return; }

    this.disabled = true;
    this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    status.innerHTML = '';

    try {
        const res  = await fetch(`{{ route('biuro.clients.gus') }}?nip=${encodeURIComponent(nip)}`);
        const data = await res.json();

        if (data.error) {
            status.innerHTML = `<span class="text-danger"><i class="fa-solid fa-circle-exclamation me-1"></i>${data.error}</span>`;
        } else {
            document.getElementById('name').value        = data.nazwa  || '';
            document.getElementById('street').value      = data.adres  || '';
            document.getElementById('postal_code').value = data.kod    || '';
            document.getElementById('city').value        = data.miasto || '';
            status.innerHTML = '<span class="text-success"><i class="fa-solid fa-circle-check me-1"></i>Pobrano z GUS</span>';
        }
    } catch(e) {
        status.innerHTML = '<span class="text-danger">Błąd połączenia.</span>';
    }

    this.disabled = false;
    this.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> GUS';
});

// ── Zapis ────────────────────────────────────────────────────────────────────
document.getElementById('clientForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    if (!validateForm()) {
        Swal.fire({ icon: 'warning', title: 'Uzupełnij wymagane pola', text: 'Pola oznaczone * są wymagane.', timer: 2500, showConfirmButton: false });
        return;
    }

    const street = document.getElementById('street').value.trim();
    const city   = document.getElementById('city').value.trim();

    if (street && city) {
        const result = await Swal.fire({
            title: 'Dodać adres odbioru?',
            text: 'Czy dodać adres firmy jako punkt odbioru/dostawy?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6EBF58',
            cancelButtonColor: '#9aa3ad',
            confirmButtonText: '<i class="fa-solid fa-check"></i> Tak',
            cancelButtonText: 'Nie',
        });
        document.getElementById('add_address').value = result.isConfirmed ? '1' : '0';
    }

    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Zapisywanie...';

    try {
        const res  = await fetch('{{ route('biuro.clients.store') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: new FormData(this),
        });
        const data = await res.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Dodano!',
                text: `Firma ${data.short_name} została pomyślnie dodana do bazy.`,
                timer: 2000,
                showConfirmButton: false,
            });
            window.location.href = data.redirect;
        } else {
            const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Wystąpił błąd.';
            Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-plus"></i> Dodaj kontrahenta';
        }
    } catch(err) {
        Swal.fire({ icon: 'error', title: 'Błąd', text: 'Nie udało się zapisać.' });
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-plus"></i> Dodaj kontrahenta';
    }
});
</script>
@endsection
