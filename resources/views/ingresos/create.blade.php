@extends('layouts.app')

@section('title', 'Nuevo Ingreso')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Registrar Nuevo Ingreso</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('ingresos.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Monto Total ($)</label>
            <input type="number" step="0.01" name="monto_total" class="form-control" value="{{ old('monto_total') }}" required autocomplete="off">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d')) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Método de Pago</label>
                <select name="metodo_pago" class="form-control" required>
                    <option value="Efectivo" {{ old('metodo_pago') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                    <option value="Transferencia" {{ old('metodo_pago') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                    <option value="Tarjeta" {{ old('metodo_pago') == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Tipo de Ingreso</label>
            <select name="tipo" class="form-control" required>
                <option value="Diezmo" {{ old('tipo') == 'Diezmo' ? 'selected' : '' }}>Diezmo</option>
                <option value="Ofrenda" {{ old('tipo', 'Ofrenda') == 'Ofrenda' ? 'selected' : '' }}>Ofrenda</option>
                <option value="Donación" {{ old('tipo') == 'Donación' ? 'selected' : '' }}>Donación</option>
                <option value="Inscripción Curso" {{ old('tipo') == 'Inscripción Curso' ? 'selected' : '' }}>Inscripción Curso</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Asociar a Actividad (Opcional)</label>
            <select name="id_actividad" class="form-control">
                <option value="">Ninguna...</option>
                @foreach($actividades as $act)
                    <option value="{{ $act->id }}" {{ old('id_actividad') == $act->id ? 'selected' : '' }}>{{ $act->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Asociar a Culto (Opcional)</label>
            <select name="id_culto" class="form-control">
                <option value="">Ninguno...</option>
                @foreach($cultos as $c)
                    <option value="{{ $c->id }}" {{ old('id_culto') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Guardar Ingreso</button>
            <a href="{{ route('ingresos.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>
@endsection
