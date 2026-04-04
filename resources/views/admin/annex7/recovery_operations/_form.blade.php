{{-- resources/views/admin/annex7/recovery_operations/_form.blade.php --}}
@php $op = $operation ?? null; @endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Kod <span class="text-danger">*</span></label>
        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $op?->code) }}"
               placeholder="np. R-code/D code:R3" maxlength="50" required>
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
