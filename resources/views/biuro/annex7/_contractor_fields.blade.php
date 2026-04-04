{{-- biuro/annex7/_contractor_fields.blade.php --}}
<div class="row">
    <div class="col-md-3">
        <small class="text-muted">Name</small>
        <p class="fw-semibold">{{ $contractor->name ?? '–' }}</p>
    </div>
    <div class="col-md-3">
        <small class="text-muted">Address</small>
        <p>{{ $contractor->address ?? '–' }}</p>
    </div>
    <div class="col-md-2">
        <small class="text-muted">Contact</small>
        <p>{{ $contractor->contact ?? '–' }}</p>
    </div>
    <div class="col-md-2">
        <small class="text-muted">Tel</small>
        <p>{{ $contractor->tel ?? '–' }}</p>
    </div>
    <div class="col-md-2">
        <small class="text-muted">Mail</small>
        <p>{{ $contractor->mail ?? '–' }}</p>
    </div>
</div>
