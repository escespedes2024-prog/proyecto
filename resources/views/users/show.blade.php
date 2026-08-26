@extends('layouts.app')

@section('title', 'Detalles del Usuario')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 25px; color: var(--text-primary);">Detalles del Usuario</h2>

    <div style="display: flex; flex-direction: column; gap: 20px;">
        <div style="border-bottom: 1px solid var(--card-border); padding-bottom: 15px;">
            <span style="font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Nombre</span>
            <div style="font-size: 1.2rem; color: var(--text-primary); font-weight: 600; margin-top: 5px;">{{ $user->name }}</div>
        </div>

        <div style="border-bottom: 1px solid var(--card-border); padding-bottom: 15px;">
            <span style="font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Correo Electrónico</span>
            <div style="font-size: 1.1rem; color: var(--text-primary); margin-top: 5px;">{{ $user->email }}</div>
        </div>

        <div style="border-bottom: 1px solid var(--card-border); padding-bottom: 15px;">
            <span style="font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Fecha de Registro</span>
            <div style="font-size: 1.1rem; color: var(--text-primary); margin-top: 5px;">{{ $user->created_at->format('d/m/Y H:i') }}</div>
        </div>

        <div style="border-bottom: 1px solid var(--card-border); padding-bottom: 15px;">
            <span style="font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Estado de Cuenta</span>
            <div style="margin-top: 5px;">
                @if($user->trashed())
                    <span class="badge badge-inactive">Eliminado Lógicamente</span>
                @else
                    <span class="badge badge-active">Activo</span>
                @endif
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 15px; margin-top: 40px;">
        @if(!$user->trashed())
            <a href="{{ route('users.edit', $user->id) }}" class="btn" style="text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center;">Editar Usuario</a>
        @endif
        <a href="{{ route('users.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center;">Volver al Listado</a>
    </div>
</div>
@endsection
