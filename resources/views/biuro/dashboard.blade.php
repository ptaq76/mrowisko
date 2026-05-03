@extends('layouts.app')

@section('title', 'Dashboard')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.dashboard-tile {
    transition: transform .15s, box-shadow .15s;
    cursor: pointer;
    color: var(--black);
}
.dashboard-tile:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,.1);
    border-color: var(--green);
}
</style>
@endsection

@section('content')
<div class="page-header">
    <h1>Panel biura</h1>
</div>

<div class="row g-3" style="max-width:900px">
    <div class="col-md-4">
        <a href="{{ route('biuro.planning.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-calendar-alt fa-2x mb-2" style="color:#6EBF58"></i>
                <div class="fw-bold">Planowanie</div>
                <div class="text-muted small mt-1">Kalendarz zleceń kierowców</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('biuro.ls.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-id-badge fa-2x mb-2" style="color:#3498db"></i>
                <div class="fw-bold">Lieferschein</div>
                <div class="text-muted small mt-1">Lista LS, dodawanie, edycja</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('biuro.clients.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-building fa-2x mb-2" style="color:#9b59b6"></i>
                <div class="fw-bold">Kontrahenci</div>
                <div class="text-muted small mt-1">Baza klientów, adresy, kontakty</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('biuro.vehicles.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-truck fa-2x mb-2" style="color:#e67e22"></i>
                <div class="fw-bold">Pojazdy</div>
                <div class="text-muted small mt-1">Ciągniki, naczepy, solo</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('biuro.fractions.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-recycle fa-2x mb-2" style="color:#27ae60"></i>
                <div class="fw-bold">Frakcje odpadów</div>
                <div class="text-muted small mt-1">Rodzaje i formy towarów</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('biuro.importers.index') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-ship fa-2x mb-2" style="color:#2980b9"></i>
                <div class="fw-bold">Importerzy</div>
                <div class="text-muted small mt-1">Lista importerów LS</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('biuro.migration-report') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 dashboard-tile">
                <i class="fa-solid fa-database fa-2x mb-2" style="color:#c0392b"></i>
                <div class="fw-bold">Raport migracji</div>
                <div class="text-muted small mt-1">Co się powiązało, co kuleje</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#migrationRunModal">
            <div class="card h-100 text-center p-4 dashboard-tile" style="border-color:#c0392b">
                <i class="fa-solid fa-rotate fa-2x mb-2" style="color:#c0392b"></i>
                <div class="fw-bold">Uruchom migrację</div>
                <div class="text-muted small mt-1">Pełen db:seed (wymaga hasła)</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#testDataRunModal">
            <div class="card h-100 text-center p-4 dashboard-tile" style="border-color:#f39c12">
                <i class="fa-solid fa-flask fa-2x mb-2" style="color:#f39c12"></i>
                <div class="fw-bold">Dane testowe</div>
                <div class="text-muted small mt-1">TestDataSeeder: zerowanie magazynu + zlecenia (wymaga hasła)</div>
            </div>
        </a>
    </div>
</div>

{{-- Modal: uruchomienie migracji --}}
<div class="modal fade" id="migrationRunModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>
                    Uruchom migrację (db:seed)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>Uwaga:</strong> Operacja czyści tabele docelowe i przepisuje dane ze starej bazy.
                    Może trwać kilkadziesiąt sekund. <strong>Nie zamykaj okna</strong> w trakcie wykonywania.
                </div>

                <div id="migPanelForm">
                    <div class="mb-3">
                        <label for="migPassword" class="form-label">Hasło migracji</label>
                        <input type="password" id="migPassword" class="form-control" autocomplete="new-password" placeholder="z .env (MIGRATION_PASSWORD)">
                        <div id="migError" class="form-text text-danger" style="display:none"></div>
                    </div>
                </div>

                <div id="migPanelRunning" style="display:none">
                    <div class="d-flex align-items-center mb-3">
                        <div class="spinner-border text-danger me-3" role="status"></div>
                        <div>
                            <div class="fw-bold">Migracja w toku...</div>
                            <div class="text-muted small">Cierpliwości — to może potrwać.</div>
                        </div>
                    </div>
                </div>

                <div id="migPanelResult" style="display:none">
                    <div id="migResultStatus" class="alert" role="alert"></div>
                    <label class="form-label small text-muted mb-1">Output:</label>
                    <pre id="migOutput" style="background:#1a1a1a;color:#dcdcdc;padding:12px;border-radius:6px;font-size:12px;max-height:400px;overflow:auto;white-space:pre-wrap"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                <button type="button" class="btn btn-danger" id="migRunBtn">
                    <i class="fa-solid fa-play me-1"></i> Uruchom migrację
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: uruchomienie seedera danych testowych --}}
<div class="modal fade" id="testDataRunModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-flask text-warning me-2"></i>
                    Dane testowe (TestDataSeeder)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>Uwaga:</strong> Seeder zeruje stan magazynu (korekty inwentaryzacyjne)
                    i dodaje przykładowe zlecenia na dziś. Operacja modyfikuje produkcyjne dane.
                </div>

                <div id="tdPanelForm">
                    <div class="mb-3">
                        <label for="tdPassword" class="form-label">Hasło</label>
                        <input type="password" id="tdPassword" class="form-control" autocomplete="new-password" placeholder="z .env (MIGRATION_PASSWORD)">
                        <div id="tdError" class="form-text text-danger" style="display:none"></div>
                    </div>
                </div>

                <div id="tdPanelRunning" style="display:none">
                    <div class="d-flex align-items-center mb-3">
                        <div class="spinner-border text-warning me-3" role="status"></div>
                        <div>
                            <div class="fw-bold">Seedowanie w toku...</div>
                            <div class="text-muted small">Chwilę to potrwa.</div>
                        </div>
                    </div>
                </div>

                <div id="tdPanelResult" style="display:none">
                    <div id="tdResultStatus" class="alert" role="alert"></div>
                    <label class="form-label small text-muted mb-1">Output:</label>
                    <pre id="tdOutput" style="background:#1a1a1a;color:#dcdcdc;padding:12px;border-radius:6px;font-size:12px;max-height:400px;overflow:auto;white-space:pre-wrap"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                <button type="button" class="btn btn-warning" id="tdRunBtn">
                    <i class="fa-solid fa-play me-1"></i> Uruchom seeder
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function setupSeederModal(opts) {
    const btn   = document.getElementById(opts.btn);
    const pwd   = document.getElementById(opts.pwd);
    const err   = document.getElementById(opts.err);
    const form  = document.getElementById(opts.form);
    const run   = document.getElementById(opts.run);
    const res   = document.getElementById(opts.res);
    const out   = document.getElementById(opts.out);
    const stat  = document.getElementById(opts.stat);
    const modal = document.getElementById(opts.modal);

    if (!btn) return;

    function reset() {
        form.style.display = '';
        run.style.display  = 'none';
        res.style.display  = 'none';
        err.style.display  = 'none';
        btn.disabled = false;
        pwd.value = '';
    }

    modal.addEventListener('hidden.bs.modal', reset);

    btn.addEventListener('click', async function () {
        if (!pwd.value) {
            err.textContent = 'Podaj hasło.';
            err.style.display = '';
            pwd.focus();
            return;
        }

        err.style.display = 'none';
        form.style.display = 'none';
        run.style.display  = '';
        btn.disabled = true;

        try {
            const r = await fetch(opts.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ password: pwd.value }),
            });
            const data = await r.json();

            run.style.display = 'none';
            res.style.display = '';

            if (data.success) {
                stat.className = 'alert alert-success';
                stat.innerHTML = '<i class="fa-solid fa-check-circle me-2"></i>' + opts.successMsg;
            } else {
                stat.className = 'alert alert-danger';
                stat.innerHTML = '<i class="fa-solid fa-xmark-circle me-2"></i>' + (data.error || opts.failMsg);
            }
            out.textContent = data.output || '(brak outputu)';
        } catch (e) {
            run.style.display = 'none';
            res.style.display = '';
            stat.className = 'alert alert-danger';
            stat.innerHTML = '<i class="fa-solid fa-xmark-circle me-2"></i>Błąd sieci: ' + e.message;
            out.textContent = '';
            btn.disabled = false;
        }
    });
}

setupSeederModal({
    btn: 'migRunBtn', pwd: 'migPassword', err: 'migError',
    form: 'migPanelForm', run: 'migPanelRunning', res: 'migPanelResult',
    out: 'migOutput', stat: 'migResultStatus', modal: 'migrationRunModal',
    url: '{{ route('biuro.migration.run') }}',
    successMsg: 'Migracja zakończona sukcesem.',
    failMsg: 'Migracja nie powiodła się.',
});

setupSeederModal({
    btn: 'tdRunBtn', pwd: 'tdPassword', err: 'tdError',
    form: 'tdPanelForm', run: 'tdPanelRunning', res: 'tdPanelResult',
    out: 'tdOutput', stat: 'tdResultStatus', modal: 'testDataRunModal',
    url: '{{ route('biuro.test-data.run') }}',
    successMsg: 'Seeder zakończony sukcesem.',
    failMsg: 'Seeder nie powiódł się.',
});
</script>
@endsection
@endsection
