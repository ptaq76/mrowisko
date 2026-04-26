<div class="modal fade" id="zadanieModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="zadanieForm" method="POST">
                @csrf
                <input type="hidden" name="_method_real" id="zadanie_method" value="POST">
                <input type="hidden" name="zadanie_id" id="zadanie_id" value="">
                <input type="hidden" name="batch_id_meta" id="zadanie_batch_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="zadanieModalTitle">Nowe zadanie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Treść</label>
                        <textarea name="tresc" id="zadanie_tresc" class="form-control" rows="3" required maxlength="1000"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Data</label>
                        <input type="date" name="data" id="zadanie_data" class="form-control" required>
                    </div>

                    <div class="mb-3" id="zadanie_target_wrapper">
                        <label class="form-label fw-bold d-block">Komu</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="target" value="driver" id="target_driver" checked onchange="toggleZadanieTarget()">
                            <label class="form-check-label" for="target_driver">Konkretny kierowca</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="target" value="plac" id="target_plac" onchange="toggleZadanieTarget()">
                            <label class="form-check-label" for="target_plac">Plac</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="target" value="all_drivers" id="target_all" onchange="toggleZadanieTarget()">
                            <label class="form-check-label" for="target_all">Wszyscy kierowcy (z hakowcem)</label>
                        </div>
                    </div>

                    <div class="mb-3" id="zadanie_driver_wrapper">
                        <label class="form-label fw-bold">Kierowca</label>
                        <select name="driver_id" id="zadanie_driver_id" class="form-select">
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}" {{ $driver?->id === $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Zapisz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleZadanieTarget() {
    const isDriver = document.getElementById('target_driver').checked;
    document.getElementById('zadanie_driver_wrapper').style.display = isDriver ? 'block' : 'none';
}

function openZadanieModal(zadanie = null) {
    const form = document.getElementById('zadanieForm');
    const title = document.getElementById('zadanieModalTitle');

    if (zadanie) {
        title.textContent = 'Edytuj zadanie';
        document.getElementById('zadanie_method').value = 'PUT';
        document.getElementById('zadanie_id').value = zadanie.id;
        document.getElementById('zadanie_tresc').value = zadanie.tresc;
        document.getElementById('zadanie_data').value = zadanie.data;
        document.getElementById('zadanie_target_wrapper').style.display = 'none';
    } else {
        title.textContent = 'Nowe zadanie';
        document.getElementById('zadanie_method').value = 'POST';
        document.getElementById('zadanie_id').value = '';
        document.getElementById('zadanie_tresc').value = '';
        document.getElementById('zadanie_data').value = '{{ $date->format("Y-m-d") }}';
        document.getElementById('target_driver').checked = true;
        document.getElementById('zadanie_target_wrapper').style.display = 'block';
        toggleZadanieTarget();
    }

    new bootstrap.Modal(document.getElementById('zadanieModal')).show();
}

document.getElementById('zadanieForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const method = document.getElementById('zadanie_method').value;
    const id = document.getElementById('zadanie_id').value;
    const url = method === 'PUT' ? `/biuro/zadania/${id}` : '/biuro/zadania';

    const formData = new FormData(this);
    if (method === 'PUT') formData.append('_method', 'PUT');

    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData,
    }).then(r => {
        if (r.ok || r.redirected) {
            location.reload();
        } else {
            r.json().then(d => {
                Swal.fire({ icon: 'error', title: 'Błąd', text: Object.values(d.errors || {}).flat().join('\n') || 'Nie udało się zapisać' });
            }).catch(() => Swal.fire({ icon: 'error', title: 'Błąd zapisu' }));
        }
    });
});

function deleteZadanie(id) {
    Swal.fire({
        title: 'Anulować zadanie?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Tak',
        cancelButtonText: 'Nie',
        confirmButtonColor: '#dc3545',
    }).then(r => {
        if (!r.isConfirmed) return;
        const fd = new FormData();
        fd.append('_method', 'DELETE');
        fd.append('_token', '{{ csrf_token() }}');
        fetch(`/biuro/zadania/${id}`, { method: 'POST', body: fd }).then(() => location.reload());
    });
}

function wykonajZadanie(id, url) {
    Swal.fire({
        title: 'Czy zapisać wykonanie zadania?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Tak',
        cancelButtonText: 'Nie',
        confirmButtonColor: '#6EBF58',
    }).then(r => {
        if (!r.isConfirmed) return;
        const fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fetch(url, { method: 'POST', body: fd }).then(() => location.reload());
    });
}
</script>
