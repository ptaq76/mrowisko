<div class="row g-3">

    <div class="col-12">
        <label class="form-label">Pełna nazwa</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $client->name ?? '') }}">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Nazwa skrócona</label>
        <input type="text" name="short_name" class="form-control @error('short_name') is-invalid @enderror"
               value="{{ old('short_name', $client->short_name ?? '') }}">
        @error('short_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">NIP / VAT-DE</label>
        <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
               value="{{ old('nip', $client->nip ?? '') }}">
        @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Nr BDO</label>
        <input type="text" name="bdo" class="form-control @error('bdo') is-invalid @enderror"
               value="{{ old('bdo', $client->bdo ?? '') }}">
        @error('bdo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Typ</label>
        <select name="type" class="form-select @error('type') is-invalid @enderror">
            @foreach($types as $val => $label)
                <option value="{{ $val }}" {{ old('type', $client->type ?? '') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Kraj</label>
        <select name="country" class="form-select @error('country') is-invalid @enderror">
            @foreach($countries as $val => $label)
                <option value="{{ $val }}" {{ old('country', $client->country ?? 'PL') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Handlowiec</label>
        <select name="salesman_id" class="form-select @error('salesman_id') is-invalid @enderror">
            <option value="">– brak –</option>
            @foreach($salesmen as $salesman)
                <option value="{{ $salesman->id }}"
                    {{ old('salesman_id', $client->salesman_id ?? '') == $salesman->id ? 'selected' : '' }}>
                    {{ $salesman->name }}
                </option>
            @endforeach
        </select>
        @error('salesman_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12"><hr class="my-1"></div>

    <div class="col-md-6">
        <label class="form-label">Ulica</label>
        <input type="text" name="street" class="form-control"
               value="{{ old('street', $client->street ?? '') }}">
    </div>

    <div class="col-md-2">
        <label class="form-label">Kod pocztowy</label>
        <input type="text" name="postal_code" class="form-control"
               value="{{ old('postal_code', $client->postal_code ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Miasto</label>
        <input type="text" name="city" class="form-control"
               value="{{ old('city', $client->city ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Telefon</label>
        <input type="text" name="phone" class="form-control"
               value="{{ old('phone', $client->phone ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $client->email ?? '') }}">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Uwagi</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $client->notes ?? '') }}</textarea>
    </div>

    @isset($client)
    <div class="col-12">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                   value="1" {{ old('is_active', $client->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Aktywny kontrahent</label>
        </div>
    </div>
    @endisset

</div>
