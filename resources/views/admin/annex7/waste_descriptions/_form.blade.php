{{-- resources/views/admin/annex7/waste_descriptions/_form.blade.php --}}
@php $wd = $wasteDescription ?? null; @endphp

<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Opis <span class="text-danger">*</span></label>
        <input type="text" name="description" class="form-control @error('description') is-invalid @enderror"
               value="{{ old('description', $wd?->description) }}"
               placeholder="np. Waste wood and wood products" required>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
