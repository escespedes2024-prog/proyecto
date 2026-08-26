@extends('layouts.app')

@section('title', 'Editar Miembro')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 700px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Editar Miembro</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('miembros.update', $miembro->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Nombre Completo</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $miembro->nombre) }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $miembro->email) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $miembro->telefono) }}">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $miembro->direccion) }}">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Fecha de Nacimiento</label>
                <input type="date" name="f_nacimiento" class="form-control" value="{{ old('f_nacimiento', $miembro->f_nacimiento) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Sexo</label>
                <select name="sexo" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Masculino" {{ old('sexo', $miembro->sexo) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old('sexo', $miembro->sexo) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso" class="form-control" value="{{ old('fecha_ingreso', $miembro->fecha_ingreso) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control" required>
                    <option value="Activo" {{ old('estado', $miembro->estado) == 'Activo' ? 'selected' : '' }}>Activo</option>
                    <option value="Inactivo" {{ old('estado', $miembro->estado) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Actualizar Miembro</button>
            <a href="{{ route('miembros.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>
@endsection
