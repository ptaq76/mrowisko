<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nr rejestracyjny <span class="text-danger">*</span></label>
        <input type="text" name="plate" id="{{ isset($edit) ? 'edit_plate' : 'plate' }}"
               class="form-control text-uppercase" style="text-transform:uppercase"
               required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Typ <span class="text-danger">*</span></label>
        <select name="type" id="{{ isset($edit) ? 'edit_type' : 'type' }}" class="form-select" required>
            <option value="">– wybierz –</option>
            @foreach(\App\Models\Vehicle::TYPES as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Podtyp</label>
        <select name="subtype" id="{{ isset($edit) ? 'edit_subtype' : 'subtype' }}" class="form-select">
            <option value="">– brak –</option>
            @foreach(\App\Models\Vehicle::SUBTYPES as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Tara (kg) <span class="text-danger">*</span></label>
        <input type="number" name="tare_kg" id="{{ isset($edit) ? 'edit_tare' : 'tare_kg' }}"
               class="form-control" min="0" step="0.01" required>
    </div>
    <div class="col-12">
        <label class="form-label">Marka / opis</label>
        <input type="text" name="brand" id="{{ isset($edit) ? 'edit_brand' : 'brand' }}"
               class="form-control" placeholder="np. Volvo, Schmitz">
    </div>
</div>
