@extends('layouts.app')
@section('title', 'Edycja klienta')
@section('module_name', 'HANDLOWIEC')
@section('nav_menu') @include('handlowiec._nav') @endsection

@section('styles')
<style>
.h-form-wrap { padding:16px;max-width:560px;margin:0 auto; }
.h-page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;margin-bottom:18px; }
.h-label { display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:5px; }
.h-input,.h-textarea { width:100%;padding:12px 14px;border:1.5px solid #dde0e5;border-radius:10px;font-size:16px;outline:none;margin-bottom:14px;background:#fff; }
.h-input:focus,.h-textarea:focus { border-color:#1a1a1a; }
.h-textarea { min-height:80px;resize:vertical; }
.h-section { background:#fff;border-radius:12px;padding:16px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,.07); }
.h-section-title { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;letter-spacing:.05em;text-transform:uppercase;margin-bottom:12px;color:#1a1a1a; }
.btn-row { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
.btn-cancel { padding:14px;background:#f4f5f7;border:1.5px solid #dde0e5;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;text-align:center;text-decoration:none;color:#555;display:block; }
.btn-save { padding:14px;background:#1a1a1a;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer; }
</style>
@endsection

@section('content')
<div class="h-form-wrap">
    <div class="h-page-title">
        <a href="{{ route('handlowiec.klienci') }}" style="color:#aaa;margin-right:8px"><i class="fas fa-arrow-left"></i></a>
        {{ $client->short_name ?? $client->name }}
    </div>

    <div class="h-section">
        <div class="h-section-title">Dane podstawowe</div>
        <label class="h-label">Nazwa pełna</label>
        <input type="text" id="f_name" class="h-input" value="{{ $client->name }}">

        <label class="h-label">Skrót</label>
        <input type="text" id="f_short_name" class="h-input" value="{{ $client->short_name }}">

        <label class="h-label">NIP</label>
        <input type="text" id="f_nip" class="h-input" value="{{ $client->nip }}">

        <label class="h-label">BDO</label>
        <input type="text" id="f_bdo" class="h-input" value="{{ $client->bdo }}">
    </div>

    <div class="h-section">
        <div class="h-section-title">Kontakt</div>
        <label class="h-label">Telefon</label>
        <input type="tel" id="f_phone" class="h-input" value="{{ $client->phone }}">

        <label class="h-label">E-mail</label>
        <input type="email" id="f_email" class="h-input" value="{{ $client->email }}">
    </div>

    <div class="h-section">
        <div class="h-section-title">Adres</div>
        <label class="h-label">Ulica</label>
        <input type="text" id="f_street" class="h-input" value="{{ $client->street }}">

        <div style="display:grid;grid-template-columns:1fr 2fr;gap:10px">
            <div>
                <label class="h-label">Kod pocztowy</label>
                <input type="text" id="f_postal_code" class="h-input" value="{{ $client->postal_code }}" style="margin-bottom:0">
            </div>
            <div>
                <label class="h-label">Miasto</label>
                <input type="text" id="f_city" class="h-input" value="{{ $client->city }}" style="margin-bottom:0">
            </div>
        </div>
    </div>

    <div class="h-section">
        <div class="h-section-title">Uwagi</div>
        <textarea id="f_notes" class="h-textarea">{{ $client->notes }}</textarea>
    </div>

    <div class="btn-row">
        <a href="{{ route('handlowiec.klienci') }}" class="btn-cancel">Anuluj</a>
        <button class="btn-save" onclick="zapisz()"><i class="fas fa-check"></i> Zapisz</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function zapisz() {
    const body = {
        name:        document.getElementById('f_name').value,
        short_name:  document.getElementById('f_short_name').value,
        nip:         document.getElementById('f_nip').value,
        bdo:         document.getElementById('f_bdo').value,
        phone:       document.getElementById('f_phone').value,
        email:       document.getElementById('f_email').value,
        street:      document.getElementById('f_street').value,
        postal_code: document.getElementById('f_postal_code').value,
        city:        document.getElementById('f_city').value,
        notes:       document.getElementById('f_notes').value,
    };

    if (!body.name.trim()) {
        Swal.fire({ icon: 'warning', title: 'Podaj nazwę klienta', timer: 1500, showConfirmButton: false });
        return;
    }

    const res  = await fetch('/handlowiec/klienci/{{ $client->id }}', {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(body),
    });
    const data = await res.json();

    if (data.success) {
        Swal.fire({ icon: 'success', title: 'Zapisano!', timer: 1200, showConfirmButton: false });
        setTimeout(() => window.location.href = '{{ route("handlowiec.klienci") }}', 1200);
    } else {
        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : 'Błąd zapisu.';
        Swal.fire({ icon: 'error', title: 'Błąd', text: errors });
    }
}
</script>
@endsection
