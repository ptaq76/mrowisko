@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('module_name', 'ADMINISTRATOR')
@section('nav_menu') @include('admin._nav') @endsection

@section('styles')
<style>
.admin-wrap { padding: 20px; }
.page-title { font-family:'Barlow Condensed',sans-serif;font-size:24px;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#1a1a1a;margin-bottom:20px; }
.stats-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:24px; }
.stat-card { background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 4px rgba(0,0,0,.07);text-align:center; }
.stat-val { font-family:'Barlow Condensed',sans-serif;font-size:36px;font-weight:900;color:#1a1a1a; }
.stat-label { font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-top:2px; }
.stat-card.blue .stat-val { color:#3498db; }
.stat-card.green .stat-val { color:#27ae60; }
.stat-card.orange .stat-val { color:#f39c12; }
.stat-card.red .stat-val { color:#e74c3c; }
.section-title { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:#888;margin-bottom:12px;margin-top:24px; }
.drivers-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px; }
.driver-card { background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden; }
.driver-header { padding:12px 14px;display:flex;align-items:center;justify-content:space-between;background:#1a1a1a;color:#fff; }
.driver-name { font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900; }
.driver-count { font-size:11px;background:rgba(255,255,255,.2);padding:2px 8px;border-radius:10px; }
.order-row { padding:10px 14px;border-bottom:1px solid #f0f2f5;display:flex;align-items:center;justify-content:space-between;font-size:13px; }
.order-row:last-child { border-bottom:none; }
.or-client { font-weight:700; }
.or-status { font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px; }
.s-planned   { background:#f4f5f7;color:#888; }
.s-loaded    { background:#fef9e7;color:#d68910; }
.s-weighed   { background:#e8f4fd;color:#2471a3; }
.s-delivered { background:#e8f7e4;color:#1a7a3a; }
.s-closed    { background:#eee;color:#aaa; }
.or-type { font-size:10px;color:#aaa; }
.no-orders { padding:16px 14px;color:#ccc;font-size:13px;text-align:center; }
.view-all { display:block;text-align:center;padding:8px;font-size:12px;font-weight:700;color:#3498db;text-decoration:none;border-top:1px solid #f0f2f5; }
.view-all:hover { background:#f0f7fd; }
</style>
@endsection

@section('content')
<div class="admin-wrap">
    <div class="page-title"><i class="fas fa-gauge" style="color:#3498db"></i> Panel Administratora</div>

    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-val">{{ $stats['orders_today'] }}</div>
            <div class="stat-label">Zlecenia dziś</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-val">{{ $stats['orders_active'] }}</div>
            <div class="stat-label">W realizacji</div>
        </div>
        <div class="stat-card green">
            <div class="stat-val">{{ $stats['weighings_today'] }}</div>
            <div class="stat-label">Ważenia dziś</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ $stats['warehouse_bales'] }}</div>
            <div class="stat-label">Belek w magazynie</div>
        </div>
        <div class="stat-card red">
            <div class="stat-val">{{ $stats['users'] }}</div>
            <div class="stat-label">Użytkownicy</div>
        </div>
    </div>

    <div class="section-title">Zlecenia kierowców – dzisiaj</div>
    <div class="drivers-grid">
        @foreach($drivers as $driver)
        <div class="driver-card">
            <div class="driver-header">
                <span class="driver-name">{{ $driver->name }}</span>
                <span class="driver-count">{{ $driver->todayOrders->count() }} zleceń</span>
            </div>
            @forelse($driver->todayOrders as $order)
            <div class="order-row">
                <div>
                    <div class="or-client">{{ $order->client?->short_name }}</div>
                    <div class="or-type">{{ $order->type === 'sale' ? '↑ Wysyłka' : '↓ Odbiór' }} · {{ $order->planned_time ? substr($order->planned_time,0,5) : '–' }}</div>
                </div>
                <span class="or-status s-{{ $order->status }}">
                    {{ ['planned'=>'Zaplanowane','loaded'=>'Załadowane','weighed'=>'Zważone','delivered'=>'Dostarczone','closed'=>'Zamknięte'][$order->status] ?? $order->status }}
                </span>
            </div>
            @empty
            <div class="no-orders"><i class="fas fa-calendar-times"></i> Brak zleceń</div>
            @endforelse
            <a href="{{ route('admin.drivers.show', $driver) }}" class="view-all">
                <i class="fas fa-arrow-right"></i> Wszystkie zlecenia kierowcy
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
