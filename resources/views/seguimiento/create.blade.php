@extends('layouts.app')

@section('title', 'Nuevo Seguimiento')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Registrar Seguimiento de Actividad</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('seguimiento.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Actividad Relacionada</label>
            <select name="id_actividad" class="form-control" required>
                <option value="">Seleccione una actividad...</option>
                @foreach($actividades as $act)
                    <option value="{{ $act->id }}" {{ old('id_actividad') == $act->id ? 'selected' : '' }}>{{ $act->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Responsable del Registro</label>
            <input type="text" name="responsable" class="form-control" value="{{ old('responsable') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Fecha de Registro</label>
            <input type="date" name="fecha_registro" class="form-control" value="{{ old('fecha_registro', date('Y-m-d')) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Porcentaje de Avance</label>
            <input type="number" name="porcentaje_avance" class="form-control" value="{{ old('porcentaje_avance', 0) }}" min="0" max="100" required>
        </div>

        <div class="form-group">
            <label class="form-label">Estado de la Tarea</label>
            <select name="estado" class="form-control" required>
                <option value="En Progreso" {{ old('estado', 'En Progreso') == 'En Progreso' ? 'selected' : '' }}>En Progreso</option>
                <option value="Completado" {{ old('estado') == 'Completado' ? 'selected' : '' }}>Completado</option>
                <option value="Detenido" {{ old('estado') == 'Detenido' ? 'selected' : '' }}>Detenido</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Observación</label>
            <textarea name="observacion" class="form-control" rows="3">{{ old('observacion') }}</textarea>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Guardar Seguimiento</button>
            <a href="{{ route('seguimiento.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>
@endsection
