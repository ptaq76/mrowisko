@extends('layouts.kierowca')

@section('title', 'Produkcja belek')

@section('styles')
<style>
.back-btn {
    display:flex !important;
    align-items:center !important;
    justify-content:center !important;
    gap:10px !important;
    background:#1a1a1a !important;
    color:#fff !important;
    font-family:'Barlow Condensed',sans-serif !important;
    font-size:20px !important;
    font-weight:800 !important;
    letter-spacing:.06em !important;
    text-transform:uppercase !important;
    width:80% !important;
    margin:0 auto 14px auto !important;
    padding:16px !important;
    border-radius:12px !important;
    border:none !important;
    cursor:pointer !important;
    text-decoration:none !important;
}
.back-btn:hover,.back-btn:active { background:#333 !important;color:#fff !important; }
.page-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase;
    color: #1a1a1a; margin-bottom: 12px;
}

.btn-add-big {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; padding: 18px;
    background: #2980b9; color: #fff;
    border: none; border-radius: 12px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 900;
    letter-spacing: .06em; text-transform: uppercase;
    cursor: pointer; text-decoration: none;
    margin-bottom: 16px;
    box-shadow: 0 3px 8px rgba(41,128,185,.3);
}
.btn-add-big:active { filter: brightness(.9); }

.prod-table {
    background: #fff; border-radius: 12px;
    overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,.07);
}
.prod-table table { width: 100%; border-collapse: collapse; font-size: 13px; }
.prod-table thead tr { background: #2980b9; color: #fff; }
.prod-table th {
    padding: 10px 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase; text-align: left;
}
.prod-table td { padding: 10px 10px; border-bottom: 1px solid #f0f2f5; vertical-align: middle; }
.prod-table tr:last-child td { border-bottom: none; }
.prod-table tr:hover td { background: #f0f7fd; }

.fraction-name { font-weight: 700; color: #1a1a1a; font-size: 13px; }
.bales-val { font-family: 'Barlow Condensed', sans-serif; font-size: 20px; font-weight: 900; color: #2980b9; }
.weight-val { font-size: 12px; color: #555; }
.op-val { font-size: 11px; color: #aaa; }

.del-btn {
    background: #fdecea; border: none; border-radius: 6px;
    padding: 7px 10px; color: #e74c3c; cursor: pointer; font-size: 14px;
}
.del-btn:active { background: #e74c3c; color: #fff; }

.empty-state { text-align: center; padding: 40px 20px; color: #ccc; }
.empty-state i { font-size: 40px; margin-bottom: 10px; display: block; }
</style>
@endsection

@section('content')

<button type="button"
        onclick="window.location.href='{{ route('plac.dashboard') }}'"
        style="display:flex;align-items:center;justify-content:center;gap:10px;background:#1a1a1a;color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;width:80%;margin:0 auto 14px;padding:16px;border-radius:12px;border:none;cursor:pointer">
    <i class="fas fa-home"></i> Powrót
</button>

<div class="page-title">Produkcja belek</div>

<a href="{{ route('plac.production.create') }}" class="btn-add-big">
    <i class="fas fa-plus-circle"></i> DODAJ PRODUKCJĘ
</a>

@if($todayItems->isEmpty())
    <div class="empty-state">
        <i class="fas fa-cogs"></i>
        <p style="font-size:14px;font-weight:600">Brak wpisów produkcji</p>
    </div>
@else
<div class="prod-table">
    <table>
        <thead>
            <tr>
                <th>Towar</th>
                <th>Bel.</th>
                <th>Waga</th>
                <th>Operator</th>
                <th style="width:36px"></th>
            </tr>
        </thead>
        <tbody id="prodList">
            @foreach($todayItems as $item)
            <tr id="pi-{{ $item->id }}">
                <td>
                    <div class="fraction-name">{{ $item->fraction?->name ?? '?' }}</div>
                    <div style="font-size:10px;color:#aaa">{{ $item->date->format('d.m.Y') }}</div>
                </td>
                <td><span class="bales-val">{{ $item->bales }}</span></td>
                <td><span class="weight-val">{{ number_format($item->weight_kg, 0, ',', ' ') }} kg</span></td>
                <td><span class="op-val">{{ $item->operator?->name ?? $item->operator?->login ?? '–' }}</span></td>
                <td>
                    <button class="del-btn" onclick="deleteItem({{ $item->id }})" title="Usuń">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function deleteItem(id) {
    const result = await Swal.fire({
        title: 'Usunąć wpis?', icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        confirmButtonText: 'Usuń', cancelButtonText: 'Anuluj',
    });
    if (!result.isConfirmed) return;

    const res  = await fetch(`/plac/production/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('pi-' + id)?.remove();
        Swal.fire({ icon: 'success', title: 'Usunięto', timer: 1200, showConfirmButton: false });
    }
}
</script>
@endsection
