{{-- resources/views/admin/annex7/contractors/_form.blade.php --}}
@php $c = $contractor ?? null; @endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nazwa <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $c?->name) }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Rola <span class="text-danger">*</span></label>
        <select name="role" class="form-select @error('role') is-invalid @enderror" required onchange="toggleMeans(this)">
            <option value="">– wybierz –</option>
            <option value="arranger"  {{ old('role', $c?->role) === 'arranger'  ? 'selected' : '' }}>Pole 1 – Arranger</option>
            <option value="importer"  {{ old('role', $c?->role) === 'importer'  ? 'selected' : '' }}>Pole 2 – Importer / Consignee</option>
            <option value="carrier"   {{ old('role', $c?->role) === 'carrier'   ? 'selected' : '' }}>Pole 5 – Carrier</option>
            <option value="generator" {{ old('role', $c?->role) === 'generator' ? 'selected' : '' }}>Pole 6 – Generator</option>
        </select>
        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">Adres</label>
        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
               value="{{ old('address', $c?->address) }}">
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Osoba kontaktowa</label>
        <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror"
               value="{{ old('contact', $c?->contact) }}">
        @error('contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Telefon</label>
        <input type="text" name="tel" class="form-control @error('tel') is-invalid @enderror"
               value="{{ old('tel', $c?->tel) }}">
        @error('tel') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">E-mail</label>
        <input type="email" name="mail" class="form-control @error('mail') is-invalid @enderror"
               value="{{ old('mail', $c?->mail) }}">
        @error('mail') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6" id="means_wrap" style="{{ old('role', $c?->role) === 'carrier' ? '' : 'display:none' }}">
        <label class="form-label">Środek transportu</label>
        <input type="text" name="means_of_transport" class="form-control @error('means_of_transport') is-invalid @enderror"
               value="{{ old('means_of_transport', $c?->means_of_transport) }}"
               placeholder="np. TIR, samochód ciężarowy">
        @error('means_of_transport') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<script>
function toggleMeans(el) {
    document.getElementById('means_wrap').style.display = el.value === 'carrier' ? '' : 'none';
}
</script>
