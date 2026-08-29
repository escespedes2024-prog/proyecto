@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div style="margin-bottom: 20px;">
    <h2 style="font-weight: 700; color: var(--text-primary);">Reportes</h2>
    <p style="color: var(--text-secondary);">Selecciona el tipo de reporte que deseas generar.</p>
</div>

<div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
    <a href="{{ route('reportes.miembros') }}" class="dashboard-card glass-panel" style="text-decoration: none; border: 1px solid var(--card-border); transition: transform .2s; display:block;">
        <div class="card-icon">👥</div>
        <div class="card-title">Reporte de Miembros</div>
        <div class="card-sub" style="color: var(--text-secondary); font-size: 13px; margin-top: 6px;">Listado de miembros con filtros por estado y fecha de ingreso.</div>
    </a>

    <a href="{{ route('reportes.asistencia') }}" class="dashboard-card glass-panel" style="text-decoration: none; border: 1px solid var(--card-border); transition: transform .2s; display:block;">
        <div class="card-icon">📋</div>
        <div class="card-title">Reporte de Asistencia</div>
        <div class="card-sub" style="color: var(--text-secondary); font-size: 13px; margin-top: 6px;">Resumen de asistencia por curso y rango de fechas.</div>
    </a>

    <a href="{{ route('reportes.financiero') }}" class="dashboard-card glass-panel" style="text-decoration: none; border: 1px solid var(--card-border); transition: transform .2s; display:block;">
        <div class="card-icon">💵</div>
        <div class="card-title">Reporte Financiero</div>
        <div class="card-sub" style="color: var(--text-secondary); font-size: 13px; margin-top: 6px;">Ingresos vs. egresos por rango de fechas con balance.</div>
    </a>
</div>
@endsection
