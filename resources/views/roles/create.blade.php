@extends('layouts.app')

@section('title', 'Nuevo Rol')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Crear Nuevo Rol</h2>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('roles.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Nombre del Rol</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" placeholder="ej. Admin, Lider, Miembro" required>
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3" placeholder="Indique la función o alcance de este rol..." style="resize: vertical; min-height: 80px;">{{ old('descripcion') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-control" required style="background: rgba(15, 23, 42, 0.5); color: var(--text-primary);">
                <option value="Activo" {{ old('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Nivel de Acceso (Jerarquía)</label>
            <input type="number" name="nivel_acceso" class="form-control" value="{{ old('nivel_acceso', 1) }}" min="1" max="100" placeholder="ej. 1 para básico, 10 para administrador" required>
        </div>

        <div class="form-group">
            <label class="form-label">Guard Name</label>
            <input type="text" name="guard_name" class="form-control" value="{{ old('guard_name', 'web') }}" required>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Guardar Rol</button>
            <a href="{{ route('roles.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center;">Cancelar</a>
        </div>
    </form>
</div>
@endsection
