@extends('layouts.app')

@section('title', 'Reporte Financiero')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
        .sidebar, .header, .no-print { display: none !important; }
    }
</style>

<div class="glass-panel no-print" style="padding: 24px; margin-bottom: 20px;">
    <h3 style="font-weight: 700; margin-bottom: 16px; color: var(--text-primary);">Filtros</h3>
    <form method="GET" action="{{ route('reportes.financiero') }}" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
        <div>
            <label class="form-label">Desde</label>
            <input type="date" name="desde" class="form-control" value="{{ $desde }}">
        </div>
        <div>
            <label class="form-label">Hasta</label>
            <input type="date" name="hasta" class="form-control" value="{{ $hasta }}">
        </div>
        <div>
            <button type="submit" class="btn" style="width: auto;">Generar Reporte</button>
        </div>
    </form>
</div>

<div class="glass-panel no-print" style="padding: 16px 24px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <a href="{{ route('reportes.index') }}" class="btn" style="width: auto; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary);">← Volver a Reportes</a>
    <button onclick="window.print()" class="btn" style="width: auto;">🖨️ Imprimir</button>
</div>

<div class="glass-panel" style="padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 2px solid var(--card-border); padding-bottom: 12px;">
        <div>
            <h3 style="font-weight: 800; color: var(--text-primary);">Iglesia Actúa</h3>
            <p style="color: var(--text-secondary); font-size: 13px;">Reporte Financiero</p>
        </div>
        <div style="text-align: right; font-size: 13px; color: var(--text-secondary);">
            <div>Generado: {{ now()->translatedFormat('d/m/Y H:i') }}</div>
            <div>Periodo: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</div>
        </div>
    </div>

    <div style="display: flex; gap: 16px; margin-bottom: 18px; flex-wrap: wrap;">
        <div style="background: rgba(47,158,68,.12); padding: 10px 16px; border-radius: 10px;">Total Ingresos<br><b style="font-size: 18px;">Bs {{ number_format($totalIngresos, 2) }}</b></div>
        <div style="background: rgba(220,38,38,.12); padding: 10px 16px; border-radius: 10px;">Total Egresos<br><b style="font-size: 18px;">Bs {{ number_format($totalEgresos, 2) }}</b></div>
        <div style="background: rgba(37,99,235,.12); padding: 10px 16px; border-radius: 10px;">Balance<br><b style="font-size: 18px; color: {{ $balance >= 0 ? 'var(--success)' : 'var(--error)' }};">Bs {{ number_format($balance, 2) }}</b></div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
        <div>
            <h4 style="font-weight: 700; margin-bottom: 10px; color: var(--text-primary);">Ingresos por Tipo</h4>
            @forelse($ingresosPorTipo as $tipo => $total)
                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid var(--card-border); font-size: 14px;">
                    <span>{{ $tipo }}</span>
                    <span><b>Bs {{ number_format($total, 2) }}</b></span>
                </div>
            @empty
                <p style="color: var(--text-secondary); font-size: 14px;">Sin ingresos en el periodo.</p>
            @endforelse
        </div>
        <div>
            <h4 style="font-weight: 700; margin-bottom: 10px; color: var(--text-primary);">Egresos por Tipo</h4>
            @forelse($egresosPorTipo as $tipo => $total)
                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid var(--card-border); font-size: 14px;">
                    <span>{{ $tipo }}</span>
                    <span><b>Bs {{ number_format($total, 2) }}</b></span>
                </div>
            @empty
                <p style="color: var(--text-secondary); font-size: 14px;">Sin egresos en el periodo.</p>
            @endforelse
        </div>
    </div>

    <h4 style="font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">Detalle de Movimientos</h4>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Concepto / Descripción</th>
                    <th>Ingreso</th>
                    <th>Egreso</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingresos as $ingreso)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($ingreso->fecha)->format('d/m/Y') }}</td>
                        <td>Ingreso</td>
                        <td>{{ $ingreso->tipo }} - {{ $ingreso->descripcion ?? '' }}</td>
                        <td style="color: var(--success);">Bs {{ number_format($ingreso->monto_total, 2) }}</td>
                        <td>-</td>
                    </tr>
                @empty
                @endforelse
                @forelse($egresos as $egreso)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($egreso->fecha)->format('d/m/Y') }}</td>
                        <td>Egreso</td>
                        <td>{{ $egreso->tipo_egreso }} - {{ $egreso->descripcion ?? '' }}</td>
                        <td>-</td>
                        <td style="color: var(--error);">Bs {{ number_format($egreso->monto, 2) }}</td>
                    </tr>
                @empty
                @endforelse
                @if($ingresos->isEmpty() && $egresos->isEmpty())
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 24px;">No hay movimientos en el periodo seleccionado.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
