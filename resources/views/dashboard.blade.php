@extends('layouts.app')

@section('title', 'Panel de Control')

@section('content')
<div class="dashboard-grid">
    <div class="dashboard-card glass-panel" style="border-left: 4px solid var(--primary);">
        <div class="card-icon">👥</div>
        <div class="card-title">Miembros Registrados</div>
        <div class="card-value">124</div>
    </div>
    
    <div class="dashboard-card glass-panel" style="border-left: 4px solid var(--accent);">
        <div class="card-icon">🛡️</div>
        <div class="card-title">Ministerios Activos</div>
        <div class="card-value">8</div>
    </div>
    
    <div class="dashboard-card glass-panel" style="border-left: 4px solid var(--success);">
        <div class="card-icon">💵</div>
        <div class="card-title">Ingresos del Mes</div>
        <div class="card-value">$4,850.00</div>
    </div>
    
    <div class="dashboard-card glass-panel" style="border-left: 4px solid var(--error);">
        <div class="card-icon">💸</div>
        <div class="card-title">Egresos del Mes</div>
        <div class="card-value">$1,240.00</div>
    </div>
</div>

<div class="glass-panel" style="padding: 30px; margin-top: 20px;">
    <h2 style="font-weight: 700; margin-bottom: 15px; color: var(--text-primary);">¡Bienvenido al Sistema de Gestión Iglesia Actúa!</h2>
    <p style="color: var(--text-secondary); line-height: 1.6; max-width: 800px;">
        Este panel te permite coordinar todas las áreas operativas, ministeriales y financieras de la congregación. Navega por las secciones de la barra lateral para gestionar cargos, líderes de la iglesia, miembros, contratos de trabajo, ministerios, actividades, finanzas y capacitaciones.
    </p>
    <div style="margin-top: 30px; display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="{{ route('miembros.index') }}" class="btn" style="width: auto;">Gestionar Miembros</a>
        <a href="{{ route('ingresos.index') }}" class="btn" style="width: auto; background: linear-gradient(135deg, var(--accent) 0%, #b5179e 100%);">Control de Ingresos</a>
    </div>
</div>
@endsection
