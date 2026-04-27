@extends('layouts.app')
@section('title', 'Archiwum Ważeń')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
.wrap { padding: 20px; }
.page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a; }
.btn-back { padding:8px 16px;background:#f4f5f7;color:#555;border:1px solid #dde0e5;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:6px; }
.btn-back:hover { background:#e8e9ec; }
.w-table-wrap { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden; }
.w-table { width:100%;border-collapse:collapse;font-size:13px; }
.w-table thead tr { background:#7f8c8d;color:#fff; }
.w-table th { padding:10px 12px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-align:left; }
.w-table td { padding:10px 12px;border-bottom:1px solid #f0f2f5;vertical-align:middle; }
.w-table tr:last-child td { border-bottom:none; }
.cell-dt { font-weight:700;font-size:13px;white-space:nowrap; }
.cell-time { font-size:11px;color:#aaa; }
.cell-client { font-weight:700;font-size:13px;color:#555; }
.nr-rej { display:inline-block;background:#fff;border:2px solid #aaa;padding:1px 5px;border-radius:4px;font-weight:800;font-size:11px;color:#888; }
.w-val    { font-family:'Barlow Condensed',sans-serif;font-size:17px;font-weight:800;color:#aaa; }
.w-result { font-family:'Barlow Condensed',sans-serif;font-size:19px;font-weight:900;color:#888; }
.btn-unarch { background:#f4f5f7;border:1px solid #dde0e5;border-radius:5px;padding:5px 9px;color:#555;cursor:pointer;font-size:12px; }
.btn-unarch:hover { background:#6EBF58;color:#fff;border-color:#6EBF58; }
.empty-state { text-align:center;padding:48px;color:#ccc; }
.empty-state i { font-size:48px;margin-bottom:12px;display:block; }
</style>
@endsection

@section('content')
<div class="wrap">
    <div class="page-header">
        <div class="page-title" style="color:#7f8c8d">
            <i class="fas fa-archive"></i> Archiwum ważeń
        </div>
        <a href="{{ route('biuro.weighings.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Powrót
        </a>
    </div>

    @if($weighings->isEmpty())
    <div class="empty-state"><i class="fas fa-archive"></i><p>Archiwum jest puste</p></div>
    @else
    <div class="w-table-wrap">
        <table class="w-table">
            <thead><tr>
                <th>Data</th><th>Klient</th><th>Pojazdy</th>
                <th>Waga 1</th><th>Waga 2</th><th>Wynik</th>
                <th>Towar</th><th>Uwagi</th><th style="width:80px"></th>
            </tr></thead>
            <tbody>
            @foreach($weighings as $w)
            <tr id="wr-{{ $w->id }}">
                <td>
                    <div class="cell-dt">{{ $w->weighed_at->format('d.m.Y') }}</div>
                    <div class="cell-time">{{ $w->weighed_at->format('H:i') }}</div>
                </td>
                <td class="cell-client">{{ $w->client?->short_name ?? '–' }}</td>
                <td>
                    @if($w->plate1)<span class="nr-rej" style="font-size:10px;padding:1px 4px">{{ $w->plate1 }}</span>@endif
                    @if($w->plate2) <span class="nr-rej" style="font-size:10px;padding:1px 4px">{{ $w->plate2 }}</span>@endif
                </td>
                <td><span class="w-val">{{ $w->weight1 ? number_format($w->weight1,3,',','') : '–' }}</span></td>
                <td><span class="w-val">{{ $w->weight2 ? number_format($w->weight2,3,',','') : '–' }}</span></td>
                <td>
                    @if($w->result !== null)
                    <span class="w-result">{{ number_format($w->result,3,',','') }}</span>
                    @else<span style="color:#ccc">–</span>@endif
                </td>
                <td style="font-size:12px;color:#888">{{ $w->goods ?? '–' }}</td>
                <td style="font-size:12px;color:#aaa;max-width:100px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                    @if($w->notes) title="{{ $w->notes }}" @endif>
                    {{ $w->notes ?? '–' }}
                </td>
                <td>
                    <button class="btn-unarch" onclick="unarchive({{ $w->id }})" title="Przywróć">
                        <i class="fas fa-undo"></i> Przywróć
                    </button>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
async function unarchive(id) {
    const res  = await fetch(`/biuro/weighings/${id}/unarchive`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('wr-' + id)?.remove();
        Swal.fire({ icon: 'success', title: 'Przywrócono!', timer: 1200, showConfirmButton: false });
    }
}
</script>
@endsection