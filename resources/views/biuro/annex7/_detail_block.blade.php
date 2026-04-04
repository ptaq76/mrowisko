{{-- biuro/annex7/_detail_block.blade.php --}}
<div class="card shadow-sm mb-3">
    <div class="card-header fw-semibold">{{ $title }}</div>
    <div class="card-body">
        @include('biuro.annex7._contractor_fields', compact('contractor'))
    </div>
</div>
