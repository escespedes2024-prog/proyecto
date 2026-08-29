@extends('layouts.auth')

@section('title', 'Bienvenido')

@section('content')
<div class="auth-card glass-panel">
    <div class="auth-header">
        <h1 class="auth-title">Iglesia Actúa</h1>
        <p class="auth-subtitle">Sistema de Gestión Eclesiástica</p>
    </div>

    <p style="text-align: center; color: var(--text-secondary); margin-bottom: 24px;">
        Bienvenido al sistema de la Iglesia Actúa. Acceda con su cuenta administrativa para gestionar la información de la iglesia.
    </p>

    <div style="display: flex; flex-direction: column; gap: 12px;">
        <a href="{{ route('login') }}" class="btn" style="justify-content: center;">Iniciar Sesión</a>
        <a href="{{ route('register') }}" class="btn" style="justify-content: center; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">Registrarse</a>
    </div>
</div>
@endsection