@extends('layouts.app')

@section('title', 'Nowy Lieferschein')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
    #main { padding: 0 !important; }
    .ls-layout { display: flex; height: calc(100vh - 58px); }
    .ls-pdf-panel { flex: 1; background: #f4f5f7; border-right: 1px solid #e2e5e9; display: flex; flex-direction: column; }
    .ls-form-panel { width: 420px; flex-shrink: 0; overflow-y: auto; padding: 20px; }
    .ls-pdf-panel iframe { flex: 1; border: none; width: 100%; }
    .pdf-select-bar { padding: 10px 12px; border-bottom: 1px solid #e2e5e9; background: #fff; }
    .pdf-placeholder { flex: 1; display: flex; align-items: center; justify-content: center; color: var(--gray-3); }
    .section-label { font-family: var(--font-display); font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--gray-3); padding: 10px 0 6px; border-bottom: 1px solid var(--gray-2); margin-bottom: 12px; }
    .quick-btn { padding: 2px 6px; font-size: 11px; line-height: 1.4; height: auto; }
</style>
@endsection

@section('content')
<div class="ls-layout">

    {{-- Lewa: PDF --}}
    <div class="ls-pdf-panel">
        <div class="pdf-select-bar d-flex gap-2 align-items-center flex-wrap">
            <select id="pdfSelect" class="form-select form-select-sm flex-grow-1">
                <option value="">– wybierz plik z serwera –</option>
            </select>
            <label class="btn btn-outline-secondary btn-sm text-nowrap mb-0" style="cursor:pointer">
                <i class="fas fa-upload"></i> Wgraj PDF
                <input type="file" name="pdf_file" id="pdfFileInput" accept=".pdf" style="display:none"
                       onchange="previewUploadedPdf(this)">
            </label>
        </div>
        <div id="pdfPreview" class="pdf-placeholder">
            <span>Wybierz plik PDF aby zobaczyć podgląd</span>
        </div>
    </div>

    {{-- Prawa: Formularz --}}
    <div class="ls-form-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0" style="font-family:var(--font-display);font-weight:700">Nowy Lieferschein</h5>
            <a href="{{ route('biuro.ls.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Powrót
            </a>
        </div>

        <form id="lsForm" method="POST" action="{{ route('biuro.ls.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="pdf_path" id="pdfPath">

            <div class="mb-2">
                <label class="form-label mb-1">Numer LS <span class="text-danger">*</span></label>
                <input type="text" name="number" class="form-control form-control-sm" required>
            </div>

            <div class="mb-2">
                <label class="form-label mb-1">Data <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control form-control-sm" required>
            </div>

            
            <div class="mb-2">
                <label class="form-label mb-1">Kod odpadu</label>
                <select name="waste_code_id" class="form-select form-select-sm">
                    <option value="">– brak –</option>
                    @foreach($wasteCodes as $wc)
                    <option value="{{ $wc->id }}"
                        {{ (isset($lieferschein) ? $lieferschein->waste_code_id : old('waste_code_id')) == $wc->id ? 'selected' : '' }}>
                        {{ $wc->code }} – {{ $wc->description }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label mb-1">Okienko <span class="text-danger">*</span></label>
                <input type="text" name="time_window" id="ls_window" class="form-control form-control-sm" required>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    @foreach($timeWindows as $tw)
                        <button type="button" class="btn btn-outline-primary quick-btn"
                                onclick="document.getElementById('ls_window').value='{{ $tw }}'">{{ $tw }}</button>
                    @endforeach
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label mb-1">Importer <span class="text-danger">*</span></label>
                <select name="importer_id" id="ls_importer" class="form-select form-select-sm" required>
                    <option value="">– wybierz –</option>
                    @foreach($importers as $imp)
                        <option value="{{ $imp->id }}">{{ $imp->name }}</option>
                    @endforeach
                </select>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    @foreach($topImporters as $ti)
                        <button type="button" class="btn btn-outline-primary quick-btn"
                                onclick="document.getElementById('ls_importer').value='{{ $ti['id'] }}'">
                            {{ $ti['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label mb-1">Towar <span class="text-danger">*</span></label>
                <select name="goods_id" id="ls_goods" class="form-select form-select-sm" required>
                    <option value="">– wybierz –</option>
                    @foreach($goods as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    @foreach($goods->take(6) as $g)
                        <button type="button" class="btn btn-outline-primary quick-btn"
                                onclick="document.getElementById('ls_goods').value='{{ $g->id }}'">
                            {{ $g->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label mb-1">Kierunek (odbiorca) <span class="text-danger">*</span></label>
                <select name="client_id" id="ls_client" class="form-select form-select-sm" required>
                    <option value="">– wybierz –</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}">{{ $c->short_name }}</option>
                    @endforeach
                </select>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    @foreach($topClients as $tc)
                        <button type="button" class="btn btn-outline-primary quick-btn"
                                onclick="document.getElementById('ls_client').value='{{ $tc['id'] }}'">
                            {{ $tc['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end pt-2 border-top">
                <a href="{{ route('biuro.ls.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-xmark"></i> Anuluj
                </a>
                <button type="submit" class="btn btn-add btn-sm">
                    <i class="fa-solid fa-plus"></i> Zapisz LS
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function loadPdfFiles() {
    const res   = await fetch('{{ route('biuro.ls.pdfFiles') }}');
    const files = await res.json();
    const sel   = document.getElementById('pdfSelect');
    files.forEach(f => {
        const opt = document.createElement('option');
        opt.value = f.relative_path;
        opt.textContent = f.filename;
        sel.appendChild(opt);
    });
    if (files.length) {
        sel.value = files[0].relative_path;
        showPdf(files[0].relative_path);
    }
}

function previewUploadedPdf(input) {
    if (!input.files || !input.files[0]) return;
    const url = URL.createObjectURL(input.files[0]);
    document.getElementById('pdfPreview').innerHTML =
        `<iframe src="${url}#toolbar=0" style="width:100%;height:100%;border:none"></iframe>`;
    document.getElementById('pdfSelect').value = '';
    document.getElementById('pdfPath').value   = '';
}

function showPdf(path) {
    document.getElementById('pdfPath').value = path;
    document.getElementById('pdfPreview').innerHTML =
        `<iframe src="/storage/${path}#toolbar=0" style="width:100%;height:100%;border:none"></iframe>`;
}

document.getElementById('pdfSelect').addEventListener('change', function() {
    if (this.value) showPdf(this.value);
    else document.getElementById('pdfPath').value = '';
});

// Walidacja przed zapisem
document.getElementById('lsForm').addEventListener('submit', function(e) {
    const required = ['number', 'date', 'time_window', 'importer_id', 'goods_id', 'client_id'];
    let missing = false;
    required.forEach(name => {
        const el = this.querySelector(`[name="${name}"]`);
        if (!el || !el.value.trim()) { el?.classList.add('is-invalid'); missing = true; }
        else el?.classList.remove('is-invalid');
    });
    if (missing) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Uzupełnij wymagane pola', timer: 2000, showConfirmButton: false });
    }
});

loadPdfFiles();
</script>
@endsection
