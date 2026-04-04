@extends('layouts.app')

@section('content')
<div class="row g-0" style="margin: -28px;">
    <div class="col-auto" style="width: 220px; flex-shrink: 0;">
        @include('biuro.ustawienia._sidebar')
    </div>
    <div class="col" style="padding: 28px; min-width: 0;">
        @yield('settings_content')
    </div>
</div>
@endsection
