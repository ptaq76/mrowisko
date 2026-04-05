@extends('layouts.ustawienia')
@section('title', 'Pojazdy – Paliwo')
@section('module_name', 'BIURO')
@section('nav_menu') @include('biuro._nav') @endsection

@section('styles')
<style>
.wrap { padding:20px;max-width:800px; }
.page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px; }
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
.btn-add { padding:9px 18px;background:#1a1a1a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px; }
.group-block { margin-bottom:20px; }
.group-title { font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;color:#fff;background:#1a1a1a;padding:6px 14px;border-radius:8px 8px 0 0;display:flex;align-items:center;gap:8px; }
.table-wrap { background:#fff;border-radius:0 0 10px 10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden;border:1px solid #e2e5e9;border-top:none; }
table { width:100%;border-collapse:collapse;font-size:13px; }
td { padding:9px 14px;border-bottom:1px solid #f0f2f5;vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr:hover td { background:#f8f9fa; }
tr.inactive td { opacity:.4; }
.btn-edit { background:#eaf4fb;border:1px solid #cce0f5;border-radius:5px;padding:4px 8px;color:#2980b9;cursor:pointer;font-size:12px; }
.btn-del  { background:#fdecea;border:1px solid #f5c6cb;border-radius:5px;padding:4px 8px;color:#e74c3c;cursor:pointer;font-size:12px;margin-left:4px; }
.toggle-btn { border:none;background:none;cursor:pointer;font-size:16px; }
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:12px;width:100%;max-width:420px;padding:24px;box-shadow:0 8px 32px rgba(0,0,0,.2); }
.modal-title { font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center; }
.m-label { display:block;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:5px; }
.m-input,.m-select { width:100%;padding:9px 11px;border:1.5px solid #dde0e5;border-radius:8px;font-size:14px;outline:none;margin-bottom:12px; }
.m-input:focus,.m-select:focus { border-color:#1a1a1a; }
.modal-footer { display:flex;gap:10px;justify-content:flex-end; }
.btn-cancel { padding:9px 18px;background:#f4f5f7;color:#555;border:1px solid #dde0e5;border-radius:8px;font-size:13px;cursor:pointer; }
.btn-save   { padding:9px 18px;background:#1a1a1a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer; }
</style>
@endsection

@section('settings_content')
<div class="wrap">
    <div class="page-header">
        <div class="page-title"><i class="fas fa-gas-pump"></i> Pojazdy – Paliwo</div>
        <button class="btn-add" onclick="openAdd()"><i class="fas fa-plus"></i> Dodaj pojazd</button>
    </div>

    @forelse($groups as $group)
    <div class="group-block">
        <div class="group-title">
            <i class="fas fa-layer-group"></i> {{ $group->nazwa }}
            <span style="font-size:11px;font-weight:400;opacity:.6">({{ $group->vehicles->count() }})</span>
        </div>
        <div class="table-wrap">
            <table>
                <tbody>
                @forelse($group->vehicles as $v)
                <tr id="vr-{{ $v->id }}" class="{{ !$v->active ? 'inactive' : '' }}">
                    <td style="font-weight:700;font-size:14px">{{ $v->nazwa }}</td>
                    <td style="width:80px;text-align:center">
                        <button class="toggle-btn" onclick="toggle({{ $v->id }})" id="vtgl-{{ $v->id }}" title="Aktywny/Nieaktywny">
                            {{ $v->active ? '✅' : '⬜' }}
                        </button>
                    </td>
                    <td style="width:100px;text-align:right">
                        <button class="btn-edit" onclick="openEdit({{ $v->id }}, {{ json_encode($v->nazwa) }}, {{ $v->grupa_id }})"><i class="fas fa-pen"></i></button>
                        <button class="btn-del"  onclick="del({{ $v->id }})"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="color:#ccc;text-align:center;padding:12px">Brak pojazdów</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <p class="text-muted">Brak grup pojazdów.</p>
    @endforelse
</div>

<div class="modal-overlay" id="vModal">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-title">
            <span id="vModalTitle">Dodaj pojazd</span>
            <button style="background:none;border:none;font-size:18px;cursor:pointer" onclick="closeModal()">×</button>
        </div>
        <input type="hidden" id="vId">
        <label class="m-label">Nazwa *</label>
        <input type="text" id="vNazwa" class="m-input" placeholder="np. KOMATSU 90">
        <label class="m-label">Grupa *</label>
        <select id="vGrupa" class="m-select">
            <option value="">– wybierz grupę –</option>
            @foreach($groups as $g)
            <option value="{{ $g->id }}">{{ $g->nazwa }}</option>
            @endforeach
        </select>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal()">Anuluj</button>
            <button class="btn-save" onclick="save()"><i class="fas fa-check"></i> Zapisz</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
function openAdd() {
    document.getElementById('vId').value=''; document.getElementById('vNazwa').value=''; document.getElementById('vGrupa').value='';
    document.getElementById('vModalTitle').textContent='Dodaj pojazd';
    document.getElementById('vModal').classList.add('open');
    document.getElementById('vNazwa').focus();
}
function openEdit(id, nazwa, grupaId) {
    document.getElementById('vId').value=id; document.getElementById('vNazwa').value=nazwa; document.getElementById('vGrupa').value=grupaId;
    document.getElementById('vModalTitle').textContent='Edytuj pojazd';
    document.getElementById('vModal').classList.add('open');
}
function closeModal() { document.getElementById('vModal').classList.remove('open'); }

async function save() {
    const id=document.getElementById('vId').value;
    const nazwa=document.getElementById('vNazwa').value.trim();
    const grupa_id=document.getElementById('vGrupa').value;
    if(!nazwa||!grupa_id){Swal.fire({icon:'warning',title:'Uzupełnij pola',timer:1500,showConfirmButton:false});return;}
    const url=id?`/biuro/fuel-vehicles/${id}`:'/biuro/fuel-vehicles', method=id?'PUT':'POST';
    const res=await fetch(url,{method,headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({nazwa,grupa_id})});
    const data=await res.json();
    if(data.success){closeModal();Swal.fire({icon:'success',title:'Zapisano!',timer:1200,showConfirmButton:false});setTimeout(()=>location.reload(),1200);}
    else{const e=data.errors?Object.values(data.errors).flat().join('\n'):(data.error??'Błąd');Swal.fire({icon:'error',title:'Błąd',text:e});}
}
async function toggle(id) {
    const res=await fetch(`/biuro/fuel-vehicles/${id}/toggle`,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
    const data=await res.json();
    if(data.success){
        document.getElementById('vtgl-'+id).textContent=data.active?'✅':'⬜';
        document.getElementById('vr-'+id).className=data.active?'':'inactive';
    }
}
async function del(id) {
    const ok=await Swal.fire({title:'Usunąć pojazd?',icon:'warning',showCancelButton:true,confirmButtonColor:'#e74c3c',confirmButtonText:'Usuń',cancelButtonText:'Anuluj'});
    if(!ok.isConfirmed)return;
    const res=await fetch(`/biuro/fuel-vehicles/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
    const data=await res.json();
    if(data.success){document.getElementById('vr-'+id)?.remove();Swal.fire({icon:'success',title:'Usunięto',timer:1200,showConfirmButton:false});}
    else Swal.fire({icon:'error',title:'Błąd',text:data.error});
}
</script>
@endsection
