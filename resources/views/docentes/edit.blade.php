@extends('layouts.app')

@section('title', 'Editar Docente')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Editar Docente</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('docentes.update', $docente->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Miembro de la Iglesia</label>
            <select name="id_miembro" class="form-control" required>
                <option value="">Seleccione un miembro...</option>
                @foreach($miembros as $m)
                    <option value="{{ $m->id }}" {{ old('id_miembro', $docente->id_miembro) == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Especialidad</label>
            <input type="text" name="especialidad" class="form-control" value="{{ old('especialidad', $docente->especialidad) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Título Profesional / Académico</label>
            <input type="text" name="titulo" class="form-control" value="{{ old('titulo', $docente->titulo) }}">
        </div>

        <div class="form-group">
            <label class="form-label">Estado</label>
            <select name="activo" class="form-control" required>
                <option value="1" {{ old('activo', $docente->activo) == '1' ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ old('activo', $docente->activo) == '0' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Actualizar Docente</button>
            <a href="{{ route('docentes.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>
@endsection
