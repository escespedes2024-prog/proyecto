@extends('layouts.app')

@section('title', 'Editar Actividad')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Editar Actividad</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('actividades.update', $actividad->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Nombre de la Actividad</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $actividad->nombre) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Ministerio</label>
            <select name="id_ministerio" class="form-control" required>
                <option value="">Seleccione un ministerio...</option>
                @foreach($ministerios as $min)
                    <option value="{{ $min->id }}" {{ old('id_ministerio', $actividad->id_ministerio) == $min->id ? 'selected' : '' }}>{{ $min->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" class="form-control" value="{{ old('fecha', \Carbon\Carbon::parse($actividad->fecha)->format('Y-m-d')) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Lugar</label>
            <input type="text" name="lugar" class="form-control" value="{{ old('lugar', $actividad->lugar) }}">
        </div>

        <div class="form-group">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-control" required>
                <option value="Programado" {{ old('estado', $actividad->estado) == 'Programado' ? 'selected' : '' }}>Programado</option>
                <option value="En Progreso" {{ old('estado') == 'En Progreso' ? 'selected' : '' }}>En Progreso</option>
                <option value="Completado" {{ old('estado') == 'Completado' ? 'selected' : '' }}>Completado</option>
                <option value="Cancelado" {{ old('estado') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $actividad->descripcion) }}</textarea>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Actualizar Actividad</button>
            <a href="{{ route('actividades.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>
@endsection