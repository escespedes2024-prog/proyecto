@extends('layouts.app')

@section('title', 'Nuevo Egreso')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Registrar Nuevo Egreso</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('egresos.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Monto ($)</label>
            <input type="number" step="0.01" name="monto" class="form-control" value="{{ old('monto') }}" required autocomplete="off">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d')) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Responsable del Pago</label>
                <input type="text" name="responsable" class="form-control" value="{{ old('responsable') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Tipo de Egreso</label>
            <select name="tipo_egreso" class="form-control" required>
                <option value="Mantenimiento" {{ old('tipo_egreso') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                <option value="Servicios Básicos" {{ old('tipo_egreso') == 'Servicios Básicos' ? 'selected' : '' }}>Servicios Básicos</option>
                <option value="Salarios / Honorarios" {{ old('tipo_egreso') == 'Salarios / Honorarios' ? 'selected' : '' }}>Salarios / Honorarios</option>
                <option value="Ayuda Comunitaria" {{ old('tipo_egreso') == 'Ayuda Comunitaria' ? 'selected' : '' }}>Ayuda Comunitaria</option>
                <option value="Otros Egresos" {{ old('tipo_egreso') == 'Otros Egresos' ? 'selected' : '' }}>Otros Egresos</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Asociar a Líder de Iglesia (Opcional)</label>
            <select name="id_lider_iglesia" class="form-control">
                <option value="">Ninguno...</option>
                @foreach($lideres as $l)
                    <option value="{{ $l->id }}" {{ old('id_lider_iglesia') == $l->id ? 'selected' : '' }}>{{ $l->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Asociar a Contrato de Trabajo (Opcional)</label>
            <select name="id_contrato" class="form-control">
                <option value="">Ninguno...</option>
                @foreach($contratos as $c)
                    <option value="{{ $c->id }}" {{ old('id_contrato') == $c->id ? 'selected' : '' }}>
                        Contrato #{{ $c->id }} - {{ $c->miembro->nombre ?? 'N/A' }} (${{ number_format($c->salario, 2) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Número de Comprobante / Recibo</label>
            <input type="text" name="comprobante" class="form-control" value="{{ old('comprobante') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Descripción del Egreso</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Guardar Egreso</button>
            <a href="{{ route('egresos.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>
@endsection
