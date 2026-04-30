@extends('layouts.app')

@section('title', 'Lieferschein')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('styles')
<style>
    .week-btn { min-width: 38px; }
    .ls-table { 
        width: 100%; 
        border-collapse: collapse; 
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 15px; 
    }
    .ls-table th {
        background: #1a1a1a; color: #fff;
        padding: 10px 12px; text-align: left;
        font-family: 'Barlow Condensed', sans-serif; 
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .05em; text-transform: uppercase; white-space: nowrap;
    }
    .ls-table td { 
        padding: 9px 12px; 
        vertical-align: middle; 
        font-weight: 500;
    }
    .ls-table tr.line-light td { border-bottom: 1px solid #d3d3d3; }
    .ls-table tr.line-dark td  { border-bottom: 2px solid #1a1a1a; }
    .ls-table tr:hover td { background: var(--green-light); }
    
    .status-done    { color: #6EBF58; }
    .status-pending { color: #e2e5e9; }
    
    .actions-cell { display: flex; align-items: center; gap: 8px; min-height: 28px; min-width: 70px; }
    
    .col-waste-code { 
        font-size: 13px; 
        color: #888; 
        font-weight: 600;
    }
    
    /* Nagłówek */
    .ls-header {
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 700;
        letter-spacing: .04em;
    }
</style>
@endsection

@section('content')
@php
    use Carbon\Carbon;
    $today = Carbon::now();
    $weeks = collect(range(-2, 5));
    $currentData = request('data') ?? $startOfWeek->format('Y-m-d');
@endphp

{{-- Pasek nawigacji --}}
<div class="d-flex justify-content-between align-items-center bg-white rounded shadow-sm p-3 mb-3 border">
    <div class="fw-bold fs-5 ls-header">LIEFERSCHEIN</div>
    <div class="d-flex gap-1">
        @foreach($weeks as $offset)
            @php
                $weekDate   = $today->copy()->startOfWeek()->addWeeks($offset);
                $weekNumber = $weekDate->isoWeek;
                $isActive   = $currentData === $weekDate->format('Y-m-d');
                $isCurrent  = $offset === 0;
            @endphp
            <a href="{{ route('biuro.ls.index', ['data' => $weekDate->format('Y-m-d')]) }}"
               class="btn btn-sm week-btn {{ $isActive ? 'btn-dark' : ($isCurrent ? 'btn-success' : 'btn-outline-secondary') }}">
                {{ $weekNumber }}
            </a>
        @endforeach
    </div>
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="btn btn-link p-0 text-dark" data-bs-toggle="modal"
                data-bs-target="#lsTableModal" style="font-size:1.25rem" title="Podgląd">
            <i class="mdi mdi-view-list-outline"></i>
        </button>
        <button class="btn btn-success btn-sm" onclick="fetchFromMail()" id="fetchBtn" title="Pobierz PDF ze skrzynki ls@iantra.pl">
            <i class="fas fa-envelope-open-text"></i> Pobierz z maila
        </button>
        <a href="{{ route('biuro.ls.create') }}" class="text-dark" style="font-size:1.25rem" title="Dodaj LS">
            <i class="fas fa-plus-square"></i>
        </a>
    </div>
</div>

{{-- Tabela --}}
<div id="poll-area" class="card" style="width:75%;margin:0 auto">
    <div class="table-responsive">
        <table class="ls-table">
            <thead>
                <tr>
                    <th>Dzień</th>
                    <th>Data</th>
                    <th>Towary</th>
                    <th>Kod</th>
                    <th>Okienko</th>
                    <th>Kierunek</th>
                    <th>Numer</th>
                    <th>Importer</th>
                    <th style="width:90px">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($weekDays as $date => $items)
                    @php
                        $dayName = mb_convert_case(Carbon::parse($date)->locale('pl')->translatedFormat('l'), MB_CASE_TITLE);
                        $dateFormatted = Carbon::parse($date)->format('d.m');
                    @endphp
                    @if($items->isEmpty())
                        <tr class="line-dark">
                            <td style="color:var(--gray-3)">{{ $dayName }}</td>
                            <td style="color:var(--gray-3)">{{ $dateFormatted }}</td>
                            <td colspan="7"></td>
                        </tr>
                    @else
                        @foreach($items as $item)
                        @php 
                            $lineClass = $loop->last ? 'line-dark' : 'line-light'; 
                            $hasOrder = $item->order !== null;
                        @endphp
                        <tr class="{{ $lineClass }}">
                            <td style="color:var(--gray-3)">{{ $dayName }}</td>
                            <td>{{ Carbon::parse($item->date)->format('d.m') }}</td>
                            <td>{{ $item->goods?->name ?? '–' }}</td>
                            <td class="col-waste-code">{{ $item->wasteCode?->code ?? '–' }}</td>
                            <td class="text-nowrap">{{ $item->time_window }}</td>
                            <td>{{ $item->client?->short_name ?? '–' }}</td>
                            <td><strong>{{ $item->number }}</strong></td>
                            <td>{{ $item->importer?->name ?? '–' }}</td>
                            <td>
                                <div class="actions-cell">
                                    {{-- PDF --}}
                                    @if($item->pdf_path)
                                        <a href="{{ route('biuro.ls.pdf', $item) }}?v={{ $item->updated_at->timestamp }}" target="_blank"
                                           class="text-danger" title="PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @else
                                        <span style="width:14px;display:inline-block"></span>
                                    @endif

                                    {{-- Edytuj --}}
                                    <a href="{{ route('biuro.ls.edit', $item) }}" class="text-dark" title="Edytuj">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Przypisany do zlecenia --}}
                                    <span title="{{ $hasOrder ? 'Przypisany do zlecenia' : 'Brak zlecenia' }}">
                                        <i class="fa-solid fa-square-check {{ $hasOrder ? 'status-done' : 'status-pending' }}"></i>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal: podgląd tabelki --}}
<div class="modal fade" id="lsTableModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">Zestawienie tygodniowe</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered" style="font-family:'Barlow Condensed',sans-serif;font-size:14px">
                    <tbody>
                        @foreach($weekDays as $date => $items)
                            @foreach($items as $item)
                            <tr>
                                <td>{{ Carbon::parse($item->date)->format('Y-m-d') }}</td>
                                <td>{{ $item->importer?->name ?? '–' }}</td>
                                <td>{{ $item->goods?->name ?? '–' }}</td>
                                <td>{{ $item->wasteCode?->code ?? '–' }}</td>
                                <td>{{ $item->client?->short_name ?? '–' }}</td>
                                <td>{{ $item->number }}</td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
async function fetchFromMail() {
    const btn = document.getElementById('fetchBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Pobieranie...';

    try {
        const res  = await fetch('{{ route("biuro.ls.fetch-mail") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Gotowe!', text: data.message, confirmButtonText: 'OK' })
                .then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Błąd', text: data.error ?? 'Nieznany błąd' });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Błąd połączenia', text: e.message });
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-envelope-open-text"></i> Pobierz z maila';
}

// POLLING: tabela LS odświeża się sama co 5s
if (window.pollPageFragment) {
    window.pollPageFragment('poll-area', 5000);
}
</script>
@endsection
