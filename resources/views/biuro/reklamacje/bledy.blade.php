@extends('layouts.app')
@section('title', 'Reklamacje – Błędy')
@section('module_name', 'BIURO')
@section('nav_menu') @include('biuro._nav') @endsection

@section('styles')
<style>
.page-bar { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px; }
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase; }
.status-nowy         { background:#fdecea;color:#e74c3c;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700 }
.status-zweryfikowany{ background:#d4edda;color:#27ae60;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700 }
.status-pominiety    { background:#e2e5e9;color:#888;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700 }
.blad-text { font-size:12px;color:#c0392b;background:#fdf2f2;padding:4px 8px;border-radius:5px; }
</style>
@endsection

@section('content')
<div class="page-bar">
    <div class="page-title"><i class="fas fa-exclamation-triangle" style="color:#e74c3c"></i> Błędy reklamacji</div>
    <a href="{{ route('biuro.reklamacje.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Powrót
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:13px">
            <thead style="background:#1a1a1a;color:#fff">
                <tr>
                    <th>Data</th>
                    <th>Temat maila</th>
                    <th>Opis błędu</th>
                    <th>Pliki</th>
                    <th>Status</th>
                    <th style="width:120px">Zmień status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($bledy as $b)
            <tr id="br-{{ $b->id }}">
                <td style="white-space:nowrap">{{ $b->mail_date?->format('d.m.Y H:i') ?? '–' }}</td>
                <td style="font-size:12px;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    {{ $b->mail_subject ?? '–' }}
                </td>
                <td><span class="blad-text">{{ $b->blad }}</span></td>
                <td style="font-size:11px">
                    @if($b->plik_1 && $b->folder_temp)
                        <a href="{{ route('biuro.reklamacje.plik', $b->folder_temp . '/' . $b->plik_1) }}"
                           target="_blank" class="d-block text-danger mb-1" title="{{ $b->plik_1 }}">
                            <i class="fas fa-file-pdf"></i> {{ Str::limit($b->plik_1, 30) }}
                        </a>
                    @endif
                    @if($b->plik_2 && $b->folder_temp)
                        <a href="{{ route('biuro.reklamacje.plik', $b->folder_temp . '/' . $b->plik_2) }}"
                           target="_blank" class="d-block text-danger" title="{{ $b->plik_2 }}">
                            <i class="fas fa-file-pdf"></i> {{ Str::limit($b->plik_2, 30) }}
                        </a>
                    @endif
                    @if(!$b->plik_1 && !$b->plik_2)
                        <span class="text-muted">–</span>
                    @endif
                </td>
                <td>
                    <span class="status-{{ $b->status }}" id="bs-{{ $b->id }}">{{ $b->status }}</span>
                </td>
                <td>
                    <select class="form-select form-select-sm" onchange="changeStatus({{ $b->id }}, this.value)">
                        <option value="nowy"          {{ $b->status==='nowy' ? 'selected' : '' }}>Nowy</option>
                        <option value="zweryfikowany" {{ $b->status==='zweryfikowany' ? 'selected' : '' }}>Zweryfikowany</option>
                        <option value="pominiety"     {{ $b->status==='pominiety' ? 'selected' : '' }}>Pominięty</option>
                    </select>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Brak błędów</td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $bledy->links() }}
@endsection

@section('scripts')
<script>
async function changeStatus(id, status) {
    const res  = await fetch(`/biuro/reklamacje/bledy/${id}`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type':'application/json', 'Accept':'application/json' },
        body: JSON.stringify({ status }),
    });
    const data = await res.json();
    if (data.success) {
        const span = document.getElementById('bs-' + id);
        span.className = 'status-' + status;
        span.textContent = status;
    }
}
</script>
@endsection
