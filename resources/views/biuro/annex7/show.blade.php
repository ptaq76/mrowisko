@extends('layouts.app')

@section('title', 'Annex 7 – Nowy dokument')
@section('module_name', 'BIURO')

@section('nav_menu')
    @include('biuro._nav')
@endsection


@section('content')
<div class="container-fluid py-3">

    <div class="d-flex align-items-center mb-3 gap-2">
        <button onclick="history.back()" class="btn btn-sm btn-outline-secondary" style="text-decoration:none">
            <i class="bi bi-arrow-left"></i> Wróć
        </button>
        <h4 class="mb-0">Annex 7 #{{ $annex7->id }}</h4>
        <span class="badge {{ $annex7->status === 'generated' ? 'bg-success' : 'bg-secondary' }}">
            {{ $annex7->status === 'generated' ? 'Wygenerowany' : 'Szkic' }}
        </span>
        <a href="{{ route('biuro.annex7.pdf', $annex7) }}" class="btn btn-danger ms-auto" target="_blank">
            <i class="bi bi-file-pdf"></i> Pobierz PDF
        </a>
    </div>

    @include('biuro.annex7._detail_block', ['title' => '1. Person who arranges the shipment', 'contractor' => $annex7->arranger])
    @include('biuro.annex7._detail_block', ['title' => '2. Importer / Consignee', 'contractor' => $annex7->importer])

    <div class="card shadow-sm mb-3">
        <div class="card-header fw-semibold">4. Date of shipment</div>
        <div class="card-body">{{ $annex7->date_shipment->format('d.m.Y') }}</div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header fw-semibold">5. First carrier</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    @include('biuro.annex7._contractor_fields', ['contractor' => $annex7->carrier])
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Means of transport</small>
                    <p>{{ $annex7->carrier->means_of_transport ?? '–' }}</p>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Date of transfer</small>
                    <p>{{ $annex7->carrier_date_transfer?->format('d.m.Y') ?? '–' }}</p>
                </div>
            </div>
        </div>
    </div>

    @include('biuro.annex7._detail_block', ['title' => '6. Waste generator', 'contractor' => $annex7->generator])

    @include('biuro.annex7._detail_block', ['title' => '7. Recovery facility (= Importer)', 'contractor' => $annex7->importer])

    <div class="card shadow-sm mb-3">
        <div class="card-header fw-semibold">8 / 9 / 10 – Waste details</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <small class="text-muted">8. Recovery operation</small>
                    <p>{{ $annex7->recoveryOperation->code }} – {{ $annex7->recoveryOperation->description }}</p>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">9. Usual description</small>
                    <p>{{ $annex7->wasteDescription->description }}</p>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">10. Waste identification</small>
                    <p>{{ $annex7->wasteCode->code }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
