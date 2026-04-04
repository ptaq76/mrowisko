@extends('layouts.app')

@section('title', 'Edycja LS: ' . $lieferschein->number)
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
    .pdf-placeholder { flex: 1; display: flex; align-items: center; justify-content: center; color: var(--gray-3); flex-direction: column; gap: 8px; }
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
                <input type="file" id="pdfFileInputTrigger" accept=".pdf" style="display:none"
                       onchange="previewUploadedPdf(this)">
            </label>
            @if($lieferschein->pdf_path)
                <a href="{{ route('biuro.ls.pdf', $lieferschein) }}" target="_blank"
                   class="btn btn-outline-danger btn-sm text-nowrap">
                    <i class="fas fa-file-pdf"></i> Aktualny PDF
                </a>
            @endif
        </div>
        <div id="pdfPreview" class="pdf-placeholder">
            @if($lieferschein->pdf_path)
                <iframe src="{{ Storage::url($lieferschein->pdf_path) }}#toolbar=0"
                        style="width:100%;height:100%;border:none;flex:1"></iframe>
            @else
                <i class="fas fa-file-pdf" style="font-size:3rem;opacity:.2"></i>
                <span>Brak załączonego PDF</span>
                <small>Wybierz plik z listy powyżej</small>
            @endif
        </div>
    </div>

    {{-- Prawa: Formularz --}}
    <div class="ls-form-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0" style="font-family:var(--font-display);font-weight:700">
                Edycja: {{ $lieferschein->number }}
            </h5>
            <a href="{{ route('biuro.ls.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Powrót
            </a>
        </div>

        <form id="lsForm" method="POST" action="{{ route('biuro.ls.update', $lieferschein) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="pdf_path" id="pdfPath" value="{{ $lieferschein->pdf_path }}">
            {{-- Pole pliku wewnątrz formularza --}}
            <input type="file" name="pdf_file" id="pdfFileInput" accept=".pdf" style="display:none">

            <div class="mb-2">
                <label class="form-label mb-1">Numer LS <span class="text-danger">*</span></label>
                <input type="text" name="number" class="form-control form-control-sm"
                       value="{{ $lieferschein->number }}" required>
            </div>

            <div class="mb-2">
                <label class="form-label mb-1">Data <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control form-control-sm"
                       value="{{ $lieferschein->date->format('Y-m-d') }}" required>
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
                <input type="text" name="time_window" id="ls_window" class="form-control form-control-sm"
                       value="{{ $lieferschein->time_window }}" required>
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
                        <option value="{{ $imp->id }}" {{ $lieferschein->importer_id == $imp->id ? 'selected' : '' }}>
                            {{ $imp->name }}
                        </option>
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
                        <option value="{{ $g->id }}" {{ $lieferschein->goods_id == $g->id ? 'selected' : '' }}>
                            {{ $g->name }}
                        </option>
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
                        <option value="{{ $c->id }}" {{ $lieferschein->client_id == $c->id ? 'selected' : '' }}>
                            {{ $c->short_name }}
                        </option>
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

            <div class="d-flex gap-2 justify-content-between pt-2 border-top">
                <button type="button" class="btn btn-danger btn-sm" id="btnDelete">
                    <i class="fa-solid fa-trash"></i> Usuń LS
                </button>
                <div class="d-flex gap-2">
                    <a href="{{ route('biuro.ls.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-xmark"></i> Anuluj
                    </a>
                    <button type="submit" class="btn btn-save btn-sm">
                        <i class="fa-solid fa-floppy-disk"></i> Zapisz zmiany
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewUploadedPdf(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    // Skopiuj plik do inputa wewnątrz formularza
    const formInput = document.getElementById('pdfFileInput');
    const dt = new DataTransfer();
    dt.items.add(file);
    formInput.files = dt.files;

    // Podgląd
    const url = URL.createObjectURL(file);
    const preview = document.getElementById('pdfPreview');
    preview.innerHTML = '';
    preview.style.display = 'block';
    const iframe = document.createElement('iframe');
    iframe.src = url + '#toolbar=0';
    iframe.style.cssText = 'width:100%;height:100%;border:none;min-height:500px;display:block';
    preview.appendChild(iframe);
    // Wyczyść wybór z serwera
    document.getElementById('pdfSelect').value = '';
    document.getElementById('pdfPath').value   = '';
}

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
}

document.getElementById('pdfSelect').addEventListener('change', function() {
    if (!this.value) return;
    document.getElementById('pdfPath').value = this.value;
    document.getElementById('pdfPreview').innerHTML =
        `<iframe src="/storage/${this.value}#toolbar=0" style="width:100%;height:100%;border:none;flex:1"></iframe>`;
});

document.getElementById('lsForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const required = ['number', 'date', 'time_window', 'importer_id', 'goods_id', 'client_id'];
    let missing = false;
    required.forEach(name => {
        const el = this.querySelector(`[name="${name}"]`);
        if (!el || !el.value.trim()) { el?.classList.add('is-invalid'); missing = true; }
        else el?.classList.remove('is-invalid');
    });
    if (missing) {
        Swal.fire({ icon: 'warning', title: 'Uzupełnij wymagane pola', timer: 2000, showConfirmButton: false });
        return;
    }

    const formData = new FormData(this);

    const res  = await fetch(this.action, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData,
    });
    const data = await res.json();

    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Zapisano!', text: data.message, timer: 2000, showConfirmButton: false });
        window.location.href = '{{ route('biuro.ls.index') }}';
    } else {
        const err = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message ?? 'Błąd zapisu');
        Swal.fire({ icon: 'error', title: 'Błąd', text: err });
    }
});

loadPdfFiles();

document.getElementById('btnDelete').addEventListener('click', async function() {
    const result = await Swal.fire({
        title: 'Usunąć LS {{ $lieferschein->number }}?',
        text: 'Operacja usunie również plik PDF. Nie można cofnąć.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#9aa3ad',
        confirmButtonText: 'Tak, usuń',
        cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;

    const res = await fetch('{{ route('biuro.ls.destroy', $lieferschein) }}', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Usunięto!', timer: 1500, showConfirmButton: false });
        window.location.href = '{{ route('biuro.ls.index') }}';
    }
});
</script>
@endsection
