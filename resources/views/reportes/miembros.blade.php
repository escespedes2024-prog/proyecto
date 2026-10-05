@extends('layouts.app')

@section('title', 'Reporte de Miembros')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
        .print-area { padding: 0; }
        .sidebar, .header, .no-print { display: none !important; }
    }
</style>

<div class="glass-panel no-print" style="padding: 24px; margin-bottom: 20px;">
    <h3 style="font-weight: 700; margin-bottom: 16px; color: var(--text-primary);">Filtros</h3>
    <form method="GET" action="{{ route('reportes.miembros') }}" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
        <div>
            <label class="form-label">Estado</label>
            <select name="estado" class="form-control" style="min-width: 160px;">
                <option value="">Todos</option>
                <option value="Activo" {{ $estado == 'Activo' ? 'selected' : '' }}>Activo</option>
                <option value="Inactivo" {{ $estado == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
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

<div class="glass-panel print-area" style="padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 2px solid var(--card-border); padding-bottom: 12px;">
        <div>
            <h3 style="font-weight: 800; color: var(--text-primary);">Iglesia Actúa</h3>
            <p style="color: var(--text-secondary); font-size: 13px;">Reporte de Miembros</p>
        </div>
        <div style="text-align: right; font-size: 13px; color: var(--text-secondary);">
            <div>Generado: {{ now()->translatedFormat('d/m/Y H:i') }}</div>
            @if($desde || $hasta)
            <div>Periodo: {{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : 'inicio' }} - {{ $hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : 'actual' }}</div>
            @endif
            @if($estado !== '') <div>Estado: {{ $estado }}</div> @endif
        </div>
    </div>

    <div style="display: flex; gap: 16px; margin-bottom: 18px; flex-wrap: wrap;">
        <div style="background: rgba(47,158,68,.12); padding: 10px 16px; border-radius: 10px;"><b>{{ $totales['total'] }}</b> Total</div>
        <div style="background: rgba(37,99,235,.12); padding: 10px 16px; border-radius: 10px;"><b>{{ $totales['activos'] }}</b> Activos</div>
        <div style="background: rgba(220,38,38,.12); padding: 10px 16px; border-radius: 10px;"><b>{{ $totales['inactivos'] }}</b> Inactivos</div>
    </div>

    @if($miembros->isEmpty())
        <p style="color: var(--text-secondary);">No hay miembros que coincidan con los filtros.</p>
    @else
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Sexo</th>
                    <th>Fecha Ingreso</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($miembros as $i => $miembro)

                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $miembro->nombre }}</td>
                    <td>{{ $miembro->telefono ?? '-' }}</td>
                    <td>{{ $miembro->sexo ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($miembro->fecha_ingreso)->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge @if($miembro->estado == 'Activo') badge-active @else badge-inactive @endif">{{ $miembro->estado }}</span>
                    </td>
                </tr>
                
@endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
