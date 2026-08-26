@extends('layouts.app')

@section('title', 'Detalles del Miembro')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 700px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Detalles de Miembro</h2>

    <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px;">
        <div style="display: flex; border-bottom: 1px solid var(--card-border); padding-bottom: 10px;">
            <strong style="width: 200px; color: var(--text-secondary);">Nombre Completo:</strong>
            <span>{{ $miembro->nombre }}</span>
        </div>
        <div style="display: flex; border-bottom: 1px solid var(--card-border); padding-bottom: 10px;">
            <strong style="width: 200px; color: var(--text-secondary);">Correo Electrónico:</strong>
            <span>{{ $miembro->email ?? 'N/A' }}</span>
        </div>
        <div style="display: flex; border-bottom: 1px solid var(--card-border); padding-bottom: 10px;">
            <strong style="width: 200px; color: var(--text-secondary);">Teléfono:</strong>
            <span>{{ $miembro->telefono ?? 'N/A' }}</span>
        </div>
        <div style="display: flex; border-bottom: 1px solid var(--card-border); padding-bottom: 10px;">
            <strong style="width: 200px; color: var(--text-secondary);">Dirección:</strong>
            <span>{{ $miembro->direccion ?? 'N/A' }}</span>
        </div>
        <div style="display: flex; border-bottom: 1px solid var(--card-border); padding-bottom: 10px;">
            <strong style="width: 200px; color: var(--text-secondary);">Fecha de Nacimiento:</strong>
            <span>{{ $miembro->f_nacimiento ? \Carbon\Carbon::parse($miembro->f_nacimiento)->format('d/m/Y') : 'N/A' }}</span>
        </div>
        <div style="display: flex; border-bottom: 1px solid var(--card-border); padding-bottom: 10px;">
            <strong style="width: 200px; color: var(--text-secondary);">Sexo:</strong>
            <span>{{ $miembro->sexo ?? 'N/A' }}</span>
        </div>
        <div style="display: flex; border-bottom: 1px solid var(--card-border); padding-bottom: 10px;">
            <strong style="width: 200px; color: var(--text-secondary);">Fecha de Ingreso:</strong>
            <span>{{ \Carbon\Carbon::parse($miembro->fecha_ingreso)->format('d/m/Y') }}</span>
        </div>
        <div style="display: flex; border-bottom: 1px solid var(--card-border); padding-bottom: 10px;">
            <strong style="width: 200px; color: var(--text-secondary);">Estado:</strong>
            <span>
                @if($miembro->estado == 'Activo')
                    <span class="badge badge-active">Activo</span>
                @else
                    <span class="badge badge-inactive">{{ $miembro->estado }}</span>
                @endif
            </span>
        </div>
    </div>

    <div style="display: flex; gap: 15px;">
        <a href="{{ route('miembros.edit', $miembro->id) }}" class="btn" style="width: auto;">Editar Información</a>
        <a href="{{ route('asistencia.historial', $miembro->id) }}" class="btn" style="width: auto;">Historial de Asistencia</a>
        <a href="{{ route('miembros.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center; width: auto;">Volver al Listado</a>
    </div>
</div>
@endsection
