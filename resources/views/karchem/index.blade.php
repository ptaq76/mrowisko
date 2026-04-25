@extends('layouts.karchem')

@section('content')

<div class="karchem-logo-wrapper">
    <img src="{{ asset('KARCHEM-LOGO.jpg') }}" 
         alt="Karchem Logo" 
         class="karchem-logo">
</div>

@endsection

@push('styles')
<style>
    .karchem-logo-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        margin-top: 200px; /* odstęp od góry */
    }

    .karchem-logo {
        max-width: 600px;
        width: 100%;
        height: auto;
    }
</style>
@endpush

@push('scripts')
<script>

</script>
@endpush
