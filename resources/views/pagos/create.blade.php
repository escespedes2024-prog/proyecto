@extends('layouts.app')

@section('title', 'Registrar Pago de Curso')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 650px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-weight: 700; color: var(--text-primary); margin: 0;">Registrar Pago</h2>
        <a href="{{ route('cursos.show', $inscripcion->curso_id) }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">
            Volver
        </a>
    </div>

    <!-- Resumen de la inscripción -->
    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 10px; padding: 20px; margin-bottom: 25px;">
        <h3 style="margin: 0 0 15px 0; font-weight: 700; color: var(--text-primary);">{{ $inscripcion->miembro->nombre }}</h3>
        <p style="margin: 0 0 15px 0; color: var(--text-secondary);">Curso: <strong>{{ $inscripcion->curso->nombre }}</strong></p>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); font-weight: 700;">Costo Total</div>
                <div style="font-weight: 700; color: var(--text-primary); font-size: 1.1rem;">${{ number_format($inscripcion->curso->monto_inscripcion, 2) }}</div>
            </div>
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); font-weight: 700;">Ya Pagado</div>
                <div style="font-weight: 700; color: #34d399; font-size: 1.1rem;">${{ number_format($inscripcion->monto_pagado, 2) }}</div>
            </div>
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); font-weight: 700;">Saldo Pendiente</div>
                <div style="font-weight: 700; color: #fbbf24; font-size: 1.1rem;">${{ number_format($inscripcion->saldo_pendiente, 2) }}</div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario de pago -->
    <form method="POST" action="{{ route('pagos.store', $inscripcion->id) }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Monto a Pagar ($) — Máximo ${{ number_format($inscripcion->saldo_pendiente, 2) }}</label>
            <input type="number" step="0.01" name="monto" class="form-control" value="{{ old('monto', $inscripcion->saldo_pendiente) }}" max="{{ $inscripcion->saldo_pendiente }}" min="0.01" required autocomplete="off">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Fecha de Pago</label>
                <input type="date" name="fecha_pago" class="form-control" value="{{ old('fecha_pago', date('Y-m-d')) }}" required>
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
            <label class="form-label">Comprobante / Recibo (Opcional)</label>
            <input type="text" name="comprobante" class="form-control" value="{{ old('comprobante') }}" placeholder="N° de recibo o referencia" autocomplete="off">
        </div>

        <p style="color: var(--text-secondary); font-size: 0.85rem;">
            Este pago se registrará automáticamente como ingreso en el módulo de Finanzas.
        </p>

        <div style="display: flex; gap: 15px; margin-top: 25px;">
            <button type="submit" class="btn">Guardar Pago</button>
            <a href="{{ route('cursos.show', $inscripcion->curso_id) }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>

    <!-- Historial de pagos -->
    @if($inscripcion->pagos()->withTrashed()->count())
        <h3 style="font-weight: 700; color: var(--text-primary); margin: 30px 0 15px 0;">Historial de Pagos</h3>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inscripcion->pagos()->withTrashed()->orderBy('fecha_pago')->get() as $pago)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                            <td>${{ number_format($pago->monto, 2) }}</td>
                            <td>{{ $pago->metodo_pago }}</td>
                            <td>
                                @if($pago->trashed())
                                    <span class="badge badge-inactive">Anulado</span>
                                @else
                                    <span class="badge badge-active">Vigente</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
