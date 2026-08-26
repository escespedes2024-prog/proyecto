@extends('layouts.app')

@section('title', 'Editar Inscripción')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Editar Inscripción</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 10px; padding: 15px; margin-bottom: 25px;">
        <p style="margin: 0; color: var(--text-primary); font-weight: 600;">{{ $inscripcion->miembro->nombre }}</p>
        <p style="margin: 5px 0 0 0; color: var(--text-secondary); font-size: 0.9rem;">Curso: {{ $inscripcion->curso->nombre }}</p>
    </div>

    <form method="POST" action="{{ route('inscripciones.update', $inscripcion->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Fecha de Inscripción</label>
            <input type="date" name="fecha_inscripcion" class="form-control" value="{{ old('fecha_inscripcion', $inscripcion->fecha_inscripcion) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-control" required>
                @foreach(['Activo', 'Retirado', 'Completado'] as $estado)
                    <option value="{{ $estado }}" {{ old('estado', $inscripcion->estado) === $estado ? 'selected' : '' }}>{{ $estado }}</option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Actualizar</button>
            <a href="{{ route('inscripciones.gestion', $inscripcion->curso_id) }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>
@endsection
