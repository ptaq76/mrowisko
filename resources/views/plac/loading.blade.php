@extends('layouts.kierowca')
@section('title', 'LOADING')
@section('content')
<a href="{{ route('plac.dashboard') }}" style="display:flex;align-items:center;gap:8px;color:#888;font-size:14px;font-weight:600;text-decoration:none;margin-bottom:14px">
    <i class="fas fa-home"></i> Powrót
</a>
<div style="text-align:center;padding:48px 20px;color:#ccc">
    <i class="fas fa-hard-hat" style="font-size:48px;margin-bottom:12px;display:block"></i>
    <p style="font-size:16px;font-weight:600">W budowie</p>
</div>
@endsection
