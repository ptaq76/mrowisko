@extends('layouts.app')

@section('title', 'Annex 7 – Nowy dokument')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex align-items-center mb-3">
        <button onclick="history.back()" class="btn btn-sm btn-outline-secondary me-3" style="text-decoration:none">
            <i class="fa-solid fa-arrow-left"></i> Wróć
        </button>
        <h4 class="mb-0"><i class="fa-solid fa-truck-ramp-box me-2"></i>Nowy dokument Annex 7</h4>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('biuro.annex7.store') }}" method="POST">
        @csrf

        {{-- NAGŁÓWEK --}}
        <div class="card mb-3 border-dark">
            <div class="card-body text-center py-2">
                <h5 class="mb-0 fw-bold">ANNEX 7</h5>
                <small class="text-muted">Information to accompany shipments of waste as referred to in Article 3(2) and (4) of Regulation (EC) No 1013/2006</small>
            </div>
        </div>

        {{-- RZĄD 1: pola 1 i 2 --}}
        <div class="row g-2 mb-2">
            <div class="col-md-6">
                <div class="card h-100 border-secondary">
                    <div class="card-header py-1 bg-secondary text-white fw-semibold"><small>1. Person who arranges the shipment</small></div>
                    <div class="card-body py-2">
                        <div class="mb-2">
                            <label class="form-label form-label-sm mb-1">Name</label>
                            <select name="arranger_id" id="arranger_id" class="form-select form-select-sm" required>
                                <option value="">– wybierz –</option>
                                @foreach($arrangers as $c)
                                    <option value="{{ $c->id }}" {{ old('arranger_id') == $c->id ? 'selected' : '' }}>{{ $c->displayName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-1">
                            <div class="col-12">
                                <label class="form-label form-label-sm mb-0 text-muted">Address</label>
                                <input type="text" id="arranger_address" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Contact</label>
                                <input type="text" id="arranger_contact" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Tel</label>
                                <input type="text" id="arranger_tel" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Mail</label>
                                <input type="text" id="arranger_mail" class="form-control form-control-sm bg-light" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 border-secondary">
                    <div class="card-header py-1 bg-secondary text-white fw-semibold"><small>2. Importer / Consignee</small></div>
                    <div class="card-body py-2">
                        <div class="mb-2">
                            <label class="form-label form-label-sm mb-1">Name</label>
                            <select name="importer_id" id="importer_id" class="form-select form-select-sm" required>
                                <option value="">– wybierz –</option>
                                @foreach($importers as $c)
                                    <option value="{{ $c->id }}" {{ old('importer_id') == $c->id ? 'selected' : '' }}>{{ $c->displayName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-1">
                            <div class="col-12">
                                <label class="form-label form-label-sm mb-0 text-muted">Address</label>
                                <input type="text" id="importer_address" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Contact</label>
                                <input type="text" id="importer_contact" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Tel</label>
                                <input type="text" id="importer_tel" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Mail</label>
                                <input type="text" id="importer_mail" class="form-control form-control-sm bg-light" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RZĄD 2: pola 3 i 4 --}}
        <div class="row g-2 mb-2">
            <div class="col-md-6">
                <div class="card h-100 border-secondary">
                    <div class="card-header py-1 bg-secondary text-white fw-semibold"><small>3. Actual quantity</small></div>
                    <div class="card-body py-2">
                        <label class="form-label form-label-sm mb-0 text-muted">t</label>
                        <input type="text" class="form-control form-control-sm bg-light" value="................." readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-secondary">
                    <div class="card-header py-1 bg-secondary text-white fw-semibold"><small>4. Actual date of shipment</small></div>
                    <div class="card-body py-2">
                        <input type="date" name="date_shipment"
                               class="form-control form-control-sm"
                               value="{{ old('date_shipment', now()->format('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- RZĄD 3: pole 5 --}}
        <div class="row g-2 mb-2">
            <div class="col-12">
                <div class="card border-secondary">
                    <div class="card-header py-1 bg-secondary text-white fw-semibold"><small>5. First carrier</small></div>
                    <div class="card-body py-2">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-1">Name</label>
                                <select name="carrier_id" id="carrier_id" class="form-select form-select-sm" required>
                                    <option value="">– wybierz –</option>
                                    @foreach($carriers as $c)
                                        <option value="{{ $c->id }}" {{ old('carrier_id') == $c->id ? 'selected' : '' }}>{{ $c->displayName() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label form-label-sm mb-0 text-muted">Address</label>
                                <input type="text" id="carrier_address" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Contact</label>
                                <input type="text" id="carrier_contact" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Tel</label>
                                <input type="text" id="carrier_tel" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Mail</label>
                                <input type="text" id="carrier_mail" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-sm mb-0 text-muted">Means of transport</label>
                                <input type="text" name="carrier_means_of_transport"
                                       class="form-control form-control-sm"
                                       value="{{ old('carrier_means_of_transport') }}"
                                       placeholder="np. TIR">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-sm mb-1">Date of transfer</label>
                                <input type="date" name="carrier_date_transfer"
                                       class="form-control form-control-sm"
                                       value="{{ old('carrier_date_transfer', now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RZĄD 4: pole 6 i (8+9) --}}
        <div class="row g-2 mb-2">
            <div class="col-md-6">
                <div class="card h-100 border-secondary">
                    <div class="card-header py-1 bg-secondary text-white fw-semibold"><small>6. Waste generator</small></div>
                    <div class="card-body py-2">
                        <div class="mb-2">
                            <label class="form-label form-label-sm mb-1">Name</label>
                            <select name="generator_id" id="generator_id" class="form-select form-select-sm" required>
                                <option value="">– wybierz –</option>
                                @foreach($generators as $c)
                                    <option value="{{ $c->id }}" {{ old('generator_id') == $c->id ? 'selected' : '' }}>{{ $c->displayName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-1">
                            <div class="col-12">
                                <label class="form-label form-label-sm mb-0 text-muted">Address</label>
                                <input type="text" id="generator_address" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Contact</label>
                                <input type="text" id="generator_contact" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Tel</label>
                                <input type="text" id="generator_tel" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Mail</label>
                                <input type="text" id="generator_mail" class="form-control form-control-sm bg-light" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row g-2 h-100">
                    <div class="col-12">
                        <div class="card border-secondary">
                            <div class="card-header py-1 bg-secondary text-white fw-semibold"><small>8. Recovery operation</small></div>
                            <div class="card-body py-2">
                                <select name="recovery_operation_id" class="form-select form-select-sm" required>
                                    <option value="">– wybierz –</option>
                                    @foreach($recoveryOperations as $op)
                                        <option value="{{ $op->id }}" {{ old('recovery_operation_id') == $op->id ? 'selected' : '' }}>{{ $op->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border-secondary">
                            <div class="card-header py-1 bg-secondary text-white fw-semibold"><small>9. Usual description of the waste</small></div>
                            <div class="card-body py-2">
                                <select name="waste_description_id" class="form-select form-select-sm" required>
                                    <option value="">– wybierz –</option>
                                    @foreach($wasteDescriptions as $wd)
                                        <option value="{{ $wd->id }}" {{ old('waste_description_id') == $wd->id ? 'selected' : '' }}>{{ $wd->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RZĄD 5: pole 7 i 10 --}}
        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <div class="card h-100 border-secondary">
                    <div class="card-header py-1 bg-secondary text-white fw-semibold"><small>7. Recovery facility</small></div>
                    <div class="card-body py-2">
                        <div class="mb-2">
                            <label class="form-label form-label-sm mb-1">Name</label>
                            <select name="recovery_id" id="recovery_id" class="form-select form-select-sm" required>
                                <option value="">– wybierz –</option>
                                @foreach($recoveryFacilities as $c)
                                    <option value="{{ $c->id }}" {{ old('recovery_id') == $c->id ? 'selected' : '' }}>{{ $c->displayName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-1">
                            <div class="col-12">
                                <label class="form-label form-label-sm mb-0 text-muted">Address</label>
                                <input type="text" id="recovery_address" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Contact</label>
                                <input type="text" id="recovery_contact" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Tel</label>
                                <input type="text" id="recovery_tel" class="form-control form-control-sm bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm mb-0 text-muted">Mail</label>
                                <input type="text" id="recovery_mail" class="form-control form-control-sm bg-light" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 border-secondary">
                    <div class="card-header py-1 bg-secondary text-white fw-semibold"><small>10. Waste identification (Basel/OECD/EU)</small></div>
                    <div class="card-body py-2">
                        <select name="waste_code_id" class="form-select form-select-sm" required>
                            <option value="">– wybierz –</option>
                            @foreach($wasteCodes as $wc)
                                <option value="{{ $wc->id }}" {{ old('waste_code_id') == $wc->id ? 'selected' : '' }}>{{ $wc->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Zapisz dokument
            </button>
            <button type="button" onclick="history.back()" class="btn btn-outline-secondary">
                Anuluj
            </button>
        </div>

    </form>
</div>
@endsection

@section('scripts')
<script>
const contractorUrl = "{{ route('biuro.annex7.contractor-data', ':id') }}";

function fillContractor(selectId, prefix) {
    const id = document.getElementById(selectId).value;
    const fields = ['address', 'contact', 'tel', 'mail'];
    if (!id) {
        fields.forEach(f => {
            const el = document.getElementById(prefix + '_' + f);
            if (el) el.value = '';
        });
        return;
    }
    fetch(contractorUrl.replace(':id', id))
        .then(r => r.json())
        .then(data => {
            fields.forEach(f => {
                const el = document.getElementById(prefix + '_' + f);
                if (el) el.value = data[f] ?? '';
            });
        });
}

document.getElementById('arranger_id').addEventListener('change',  () => fillContractor('arranger_id',  'arranger'));
document.getElementById('importer_id').addEventListener('change',  () => fillContractor('importer_id',  'importer'));
document.getElementById('carrier_id').addEventListener('change',   () => fillContractor('carrier_id',   'carrier'));
document.getElementById('generator_id').addEventListener('change', () => fillContractor('generator_id', 'generator'));
document.getElementById('recovery_id').addEventListener('change',  () => fillContractor('recovery_id',  'recovery'));
</script>
@endsection
