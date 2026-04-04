@extends('layouts.app')
@section('title', 'Reklamacje')
@section('module_name', 'BIURO')
@section('nav_menu') @include('biuro._nav') @endsection

@section('styles')
<style>
.page-bar { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px; }
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;letter-spacing:.06em;text-transform:uppercase; }
.badge-powiazany { background:#27ae60;color:#fff;font-size:11px;padding:2px 8px;border-radius:10px;font-weight:700 }
.badge-brak { background:#e2e5e9;color:#888;font-size:11px;padding:2px 8px;border-radius:10px;font-weight:700 }
.badge-reklamacja     { background:#e74c3c;color:#fff;font-size:11px;padding:2px 8px;border-radius:10px;font-weight:700 }
.badge-gewichtsmeldung{ background:#2980b9;color:#fff;font-size:11px;padding:2px 8px;border-radius:10px;font-weight:700 }
.masa { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900; }
.btn-fetch { padding:8px 16px;background:#2980b9;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px; }
.btn-bledy { padding:8px 16px;background:#e74c3c;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;display:flex;align-items:center;gap:6px; }
.filter-btns { display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px; }
</style>
@endsection

@section('content')
<div class="page-bar">
    <div class="page-title"><i class="fa-solid fa-file-circle-exclamation" style="color:#e74c3c"></i> Dokumenty wagowe</div>
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn-fetch" onclick="fetchMail()" id="fetchBtn">
            <i class="fas fa-envelope-open-text"></i> Pobierz z maila
        </button>
        <a href="{{ route('biuro.reklamacje.bledy') }}" class="btn-bledy">
            <i class="fas fa-exclamation-triangle"></i> Błędy
            @if($bledy > 0)
                <span class="badge bg-white text-danger ms-1">{{ $bledy }}</span>
            @endif
        </a>
    </div>
</div>

{{-- Filtr typu --}}
<div class="filter-btns">
    <a href="{{ route('biuro.reklamacje.index') }}"
       class="btn btn-sm {{ !$typ ? 'btn-dark' : 'btn-outline-secondary' }}">
        Wszystkie
    </a>
    <a href="{{ route('biuro.reklamacje.index', ['typ' => 'reklamacja']) }}"
       class="btn btn-sm {{ $typ==='reklamacja' ? 'btn-danger' : 'btn-outline-secondary' }}">
        <i class="fa-solid fa-file-circle-exclamation"></i> Reklamacje
    </a>
    <a href="{{ route('biuro.reklamacje.index', ['typ' => 'gewichtsmeldung']) }}"
       class="btn btn-sm {{ $typ==='gewichtsmeldung' ? 'btn-primary' : 'btn-outline-secondary' }}">
        <i class="fa-solid fa-file-circle-check"></i> Gewichtsmeldungen
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:13px">
            <thead style="background:#1a1a1a;color:#fff">
                <tr>
                    <th>Typ</th>
                    <th>Data maila</th>
                    <th>Nr Lieferschein</th>
                    <th>Masa netto</th>
                    <th>Powiązanie</th>
                    <th>Plik</th>
                    <th>Temat</th>
                </tr>
            </thead>
            <tbody>
            @forelse($reklamacje as $r)
            <tr>
                <td>
                    @if($r->typ === 'reklamacja')
                        <span class="badge-reklamacja"><i class="fas fa-exclamation"></i> Reklamacja</span>
                    @else
                        <span class="badge-gewichtsmeldung"><i class="fas fa-check"></i> Gewichtsmeldung</span>
                    @endif
                </td>
                <td style="white-space:nowrap">{{ $r->mail_date?->format('d.m.Y H:i') ?? '–' }}</td>
                <td>
                    <strong style="font-family:'Barlow Condensed',sans-serif;font-size:16px">
                        {{ $r->lieferschein }}
                    </strong>
                </td>
                <td><span class="masa">{{ number_format($r->masa_netto, 3, ',', ' ') }}</span> t</td>
                <td>
                    @if($r->lieferschein_id)
                        <span class="badge-powiazany"><i class="fas fa-link"></i> Powiązany</span>
                    @else
                        <span class="badge-brak">Brak w systemie</span>
                    @endif
                </td>
                <td>
                    @if($r->sciezka_pliku_masy)
                        <a href="{{ route('biuro.reklamacje.plik', $r->sciezka_pliku_masy) }}"
                           target="_blank" class="text-danger" title="{{ $r->plik_masa }}">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    @else
                        <span class="text-muted">–</span>
                    @endif
                </td>
                <td style="font-size:11px;color:#aaa;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    {{ $r->mail_subject ?? '–' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Brak dokumentów</td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $reklamacje->links() }}
@endsection

@section('scripts')
<script>
async function fetchMail() {
    const btn = document.getElementById('fetchBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Przetwarzanie...';
    try {
        const res  = await fetch('{{ route("biuro.reklamacje.fetch-mail") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            Swal.fire({ icon:'success', title:'Gotowe!', text: data.message, timer:2000, showConfirmButton:false });
            setTimeout(() => location.reload(), 2100);
        } else {
            Swal.fire({ icon:'error', title:'Błąd', text: data.error ?? 'Nieznany błąd' });
        }
    } catch(e) {
        Swal.fire({ icon:'error', title:'Błąd połączenia', text: e.message });
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-envelope-open-text"></i> Pobierz z maila';
}
</script>
@endsection
