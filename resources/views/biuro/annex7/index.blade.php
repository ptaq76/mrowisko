@extends('layouts.app')

@section('title', 'Annex 7')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fa-solid fa-truck-ramp-box me-2"></i>Annex 7 – Dokumenty przewozowe</h4>
        <a href="{{ route('biuro.annex7.create') }}" class="btn btn-add">
            <i class="fa-solid fa-plus"></i> Nowy dokument
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Arranger (pole 1)</th>
                        <th>Importer (pole 2)</th>
                        <th>Carrier (pole 5)</th>
                        <th>Data wysyłki</th>
                        <th>Status</th>
                        <th style="width:120px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td>{{ $s->arranger->name }}</td>
                        <td>{{ $s->importer->name }}</td>
                        <td>{{ $s->carrier->name }}</td>
                        <td>{{ $s->date_shipment->format('d.m.Y') }}</td>
                        <td>
                            @if($s->status === 'generated')
                                <span class="badge bg-success">Wygenerowany</span>
                            @else
                                <span class="badge bg-secondary">Szkic</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('biuro.annex7.show', $s) }}" class="btn btn-edit btn-sm" title="Podgląd">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('biuro.annex7.pdf', $s) }}" class="btn btn-sm btn-danger" target="_blank" title="PDF">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
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

    <div class="mt-3">
        {{ $shipments->links() }}
    </div>

</div>
@endsection
