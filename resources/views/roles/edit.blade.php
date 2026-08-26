@extends('layouts.app')

@section('title', 'Editar Rol')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Editar Rol</h2>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('roles.update', $role->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Nombre del Rol</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $role->nombre) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3" style="resize: vertical; min-height: 80px;">{{ old('descripcion', $role->descripcion) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-control" required style="background: rgba(15, 23, 42, 0.5); color: var(--text-primary);">
                <option value="Activo" {{ old('estado', $role->estado) == 'Activo' ? 'selected' : '' }}>Activo</option>
                <option value="Inactivo" {{ old('estado', $role->estado) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Nivel de Acceso (Jerarquía)</label>
            <input type="number" name="nivel_acceso" class="form-control" value="{{ old('nivel_acceso', $role->nivel_acceso) }}" min="1" max="100" required>
        </div>

        <div class="form-group">
            <label class="form-label">Guard Name</label>
            <input type="text" name="guard_name" class="form-control" value="{{ old('guard_name', $role->guard_name) }}" required>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Guardar Cambios</button>
            <a href="{{ route('roles.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center;">Cancelar</a>
        </div>
    </form>
</div>
@endsection
