@php $prefix = isset($edit) && $edit ? 'edit_' : 'add_'; @endphp

<div class="mb-3">
    <label class="form-label">Nazwa frakcji <span class="text-danger">*</span></label>
    <input type="text" name="name" id="{{ $prefix }}name" class="form-control" required>
    <div class="name-error text-danger small mt-1" style="display:none"></div>
</div>

<div class="row g-2 mb-3">
    <div class="col-6">
        <label class="form-label">Grupa</label>
        <select name="group_id" id="{{ $prefix }}group" class="form-select">
            <option value="">– brak –</option>
            @foreach(\App\Models\WasteFractionGroup::orderBy('name')->get() as $g)
                <option value="{{ $g->id }}">{{ $g->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6">
        <label class="form-label">Klient (dla dedykowanych)</label>
        <select name="client_id" id="{{ $prefix }}client" class="form-select">
            <option value="">– brak –</option>
            @foreach(\App\Models\Client::active()->orderBy('short_name')->get() as $c)
                <option value="{{ $c->id }}">{{ $c->short_name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row g-2 mb-2">
    <div class="col-6">
        <div class="card p-2">
            <div class="small fw-bold text-muted mb-2 text-uppercase" style="font-size:11px;letter-spacing:.06em">Forma towaru</div>
            <div class="form-check mb-1">
                <input type="hidden" name="allows_luz" value="0">
                <input type="checkbox" name="allows_luz" id="{{ $prefix }}allows_luz"
                       class="form-check-input" value="1">
                <label class="form-check-label" for="{{ $prefix }}allows_luz">Luz</label>
            </div>
            <div class="form-check mb-1">
                <input type="hidden" name="allows_belka" value="0">
                <input type="checkbox" name="allows_belka" id="{{ $prefix }}allows_belka"
                       class="form-check-input" value="1">
                <label class="form-check-label" for="{{ $prefix }}allows_belka">Belka</label>
            </div>
            <div class="form-check">
                <input type="hidden" name="sells_as_luz" value="0">
                <input type="checkbox" name="sells_as_luz" id="{{ $prefix }}sells_luz"
                       class="form-check-input" value="1">
                <label class="form-check-label" for="{{ $prefix }}sells_luz">
                    Sprzedawany jako luz
                    <span class="badge bg-info text-dark ms-1" style="font-size:10px">luz</span>
                </label>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card p-2">
            <div class="small fw-bold text-muted mb-2 text-uppercase" style="font-size:11px;letter-spacing:.06em">Widoczność</div>
            <div class="form-check mb-1">
                <input type="hidden" name="show_in_deliveries" value="0">
                <input type="checkbox" name="show_in_deliveries" id="{{ $prefix }}deliveries"
                       class="form-check-input" value="1">
                <label class="form-check-label" for="{{ $prefix }}deliveries">Dostawy</label>
            </div>
            <div class="form-check mb-1">
                <input type="hidden" name="show_in_loadings" value="0">
                <input type="checkbox" name="show_in_loadings" id="{{ $prefix }}loadings"
                       class="form-check-input" value="1">
                <label class="form-check-label" for="{{ $prefix }}loadings">Załadunki</label>
            </div>
            <div class="form-check">
                <input type="hidden" name="show_in_production" value="0">
                <input type="checkbox" name="show_in_production" id="{{ $prefix }}production"
                       class="form-check-input" value="1">
                <label class="form-check-label" for="{{ $prefix }}production">Produkcja</label>
            </div>
        </div>
    </div>
</div>
