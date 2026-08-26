@extends('layouts.app')

@section('title', 'Editar Movimiento Financiero')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Editar Movimiento Financiero</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('movimientos.update', $movimiento->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Concepto / Glosa</label>
            <input type="text" name="concepto" class="form-control" value="{{ old('concepto', $movimiento->concepto) }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Tipo de Movimiento</label>
                <select name="tipo" class="form-control" required>
                    <option value="Ingreso" {{ old('tipo', $movimiento->tipo) == 'Ingreso' ? 'selected' : '' }}>Ingreso</option>
                    <option value="Egreso" {{ old('tipo', $movimiento->tipo) == 'Egreso' ? 'selected' : '' }}>Egreso</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Monto ($)</label>
                <input type="number" step="0.01" name="monto" class="form-control" value="{{ old('monto', $movimiento->monto) }}" required autocomplete="off">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="{{ old('fecha', $movimiento->fecha) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Periodo (Mes/Año)</label>
                <input type="month" name="mes_ano" class="form-control" value="{{ old('mes_ano', $movimiento->mes_ano) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Vincular a Ingreso (Opcional)</label>
            <select name="id_ingreso" class="form-control">
                <option value="">Ninguno...</option>
                @foreach($ingresos as $ing)
                    <option value="{{ $ing->id }}" {{ old('id_ingreso', $movimiento->id_ingreso) == $ing->id ? 'selected' : '' }}>
                        Ingreso #{{ $ing->id }} - {{ $ing->tipo }} (${{ number_format($ing->monto_total, 2) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Vincular a Egreso (Opcional)</label>
            <select name="id_egreso" class="form-control">
                <option value="">Ninguno...</option>
                @foreach($egresos as $egr)
                    <option value="{{ $egr->id }}" {{ old('id_egreso', $movimiento->id_egreso) == $egr->id ? 'selected' : '' }}>
                        Egreso #{{ $egr->id }} - {{ $egr->tipo_egreso }} (${{ number_format($egr->monto, 2) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Actualizar Movimiento</button>
            <a href="{{ route('movimientos.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>
@endsection
