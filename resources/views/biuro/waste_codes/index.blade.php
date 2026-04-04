@extends('layouts.ustawienia')
@section('title', 'Kody odpadów')
@section('module_name', 'BIURO')
@section('nav_menu') @include('biuro._nav') @endsection

@section('styles')
<style>
.wrap { padding: 20px; max-width: 800px; }
.page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px; }
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
.btn-add { padding:9px 18px;background:#1a1a1a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px; }
.btn-add:hover { background:#333; }
.search-input { padding:8px 12px;border:1.5px solid #dde0e5;border-radius:8px;font-size:13px;outline:none;width:250px;margin-bottom:14px; }
.search-input:focus { border-color:#1a1a1a; }
.table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden; }
table { width:100%;border-collapse:collapse;font-size:13px; }
thead tr { background:#1a1a1a;color:#fff; }
th { padding:10px 14px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-align:left; }
td { padding:10px 14px;border-bottom:1px solid #f0f2f5;vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr:hover td { background:#f8f9fa; }
.code-badge { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;background:#1a1a1a;color:#fff;padding:2px 10px;border-radius:6px;letter-spacing:.04em; }
.btn-edit { background:#eaf4fb;border:1px solid #cce0f5;border-radius:5px;padding:5px 9px;color:#2980b9;cursor:pointer;font-size:12px; }
.btn-edit:hover { background:#2980b9;color:#fff; }
.btn-del { background:#fdecea;border:1px solid #f5c6cb;border-radius:5px;padding:5px 9px;color:#e74c3c;cursor:pointer;font-size:12px;margin-left:4px; }
.btn-del:hover { background:#e74c3c;color:#fff; }
.inactive { opacity:.45; }

/* Modal */
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:12px;width:100%;max-width:440px;padding:24px;box-shadow:0 8px 32px rgba(0,0,0,.2); }
.modal-title { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center; }
.m-label { display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:5px; }
.m-input { width:100%;padding:9px 11px;border:1.5px solid #dde0e5;border-radius:8px;font-size:14px;outline:none;margin-bottom:12px; }
.m-input:focus { border-color:#1a1a1a; }
.modal-footer { display:flex;gap:10px;justify-content:flex-end;margin-top:8px; }
.btn-cancel { padding:9px 18px;background:#f4f5f7;color:#555;border:1px solid #dde0e5;border-radius:8px;font-size:13px;cursor:pointer; }
.btn-save { padding:9px 18px;background:#1a1a1a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer; }
</style>
@endsection

@section('settings_content')
<div class="wrap">
    <div class="page-header">
        <div class="page-title"><i class="fas fa-recycle"></i> Kody odpadów</div>
        <button class="btn-add" onclick="openAdd()"><i class="fas fa-plus"></i> Dodaj kod</button>
    </div>

    <input type="text" class="search-input" placeholder="Szukaj kodu lub opisu..." oninput="filterTable(this.value)">

    <div class="table-wrap">
        <table id="codesTable">
            <thead><tr>
                <th>Kod</th><th>Opis</th><th style="width:100px">Akcje</th>
            </tr></thead>
            <tbody>
            @forelse($codes as $code)
            <tr id="cr-{{ $code->id }}" class="{{ !$code->is_active ? 'inactive' : '' }}">
                <td><span class="code-badge">{{ $code->code }}</span></td>
                <td style="font-weight:600">{{ $code->description }}</td>
                <td>
                    <button class="btn-edit" onclick="openEdit({{ $code->id }}, '{{ $code->code }}', {{ json_encode($code->description) }})">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button class="btn-del" onclick="deleteCode({{ $code->id }})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;color:#ccc;padding:32px">Brak kodów odpadów</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="codeModal">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-title">
            <span id="modalTitle">Dodaj kod odpadu</span>
            <button style="background:none;border:none;font-size:18px;cursor:pointer" onclick="closeModal()">×</button>
        </div>
        <input type="hidden" id="editId">
        <label class="m-label">Kod odpadu <span style="color:#e74c3c">*</span></label>
        <input type="text" id="wCode" class="m-input" placeholder="np. 150101" maxlength="20">
        <label class="m-label">Opis <span style="color:#e74c3c">*</span></label>
        <input type="text" id="wDesc" class="m-input" placeholder="np. Opakowania z papieru i tektury">
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal()">Anuluj</button>
            <button class="btn-save" onclick="saveCode()"><i class="fas fa-check"></i> Zapisz</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function filterTable(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#codesTable tbody tr[id]').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

function openAdd() {
    document.getElementById('editId').value = '';
    document.getElementById('wCode').value  = '';
    document.getElementById('wDesc').value  = '';
    document.getElementById('modalTitle').textContent = 'Dodaj kod odpadu';
    document.getElementById('codeModal').classList.add('open');
    document.getElementById('wCode').focus();
}

function openEdit(id, code, desc) {
    document.getElementById('editId').value = id;
    document.getElementById('wCode').value  = code;
    document.getElementById('wDesc').value  = desc;
    document.getElementById('modalTitle').textContent = 'Edytuj kod odpadu';
    document.getElementById('codeModal').classList.add('open');
}

function closeModal() {
    document.getElementById('codeModal').classList.remove('open');
}

async function saveCode() {
    const id   = document.getElementById('editId').value;
    const code = document.getElementById('wCode').value.trim();
    const desc = document.getElementById('wDesc').value.trim();
    if (!code || !desc) {
        Swal.fire({ icon:'warning', title:'Uzupełnij pola', timer:1500, showConfirmButton:false });
        return;
    }
    const url    = id ? `/biuro/waste-codes/${id}` : '/biuro/waste-codes';
    const method = id ? 'PUT' : 'POST';
    const res    = await fetch(url, {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type':'application/json', 'Accept':'application/json' },
        body: JSON.stringify({ code, description: desc }),
    });
    const data = await res.json();
    if (data.success) {
        closeModal();
        Swal.fire({ icon:'success', title:'Zapisano!', timer:1200, showConfirmButton:false });
        setTimeout(() => location.reload(), 1200);
    } else {
        const err = data.errors ? Object.values(data.errors).flat().join('\n') : (data.error ?? 'Błąd');
        Swal.fire({ icon:'error', title:'Błąd', text: err });
    }
}

async function deleteCode(id) {
    const ok = await Swal.fire({ title:'Usunąć kod?', icon:'warning', showCancelButton:true,
        confirmButtonColor:'#e74c3c', confirmButtonText:'Usuń', cancelButtonText:'Anuluj' });
    if (!ok.isConfirmed) return;
    const res  = await fetch(`/biuro/waste-codes/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('cr-' + id)?.remove();
        Swal.fire({ icon:'success', title:'Usunięto', timer:1200, showConfirmButton:false });
    } else {
        Swal.fire({ icon:'error', title:'Błąd', text: data.error });
    }
}

document.getElementById('wCode').addEventListener('keydown', e => { if(e.key==='Enter') document.getElementById('wDesc').focus(); });
document.getElementById('wDesc').addEventListener('keydown', e => { if(e.key==='Enter') saveCode(); });
</script>
@endsection
