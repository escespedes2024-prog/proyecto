@extends('layouts.app')

@section('title', 'Reporte de Asistencia')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
        .sidebar, .header, .no-print { display: none !important; }
    }
</style>

<div class="glass-panel no-print" style="padding: 24px; margin-bottom: 20px;">
    <h3 style="font-weight: 700; margin-bottom: 16px; color: var(--text-primary);">Filtros</h3>
    <form method="GET" action="{{ route('reportes.asistencia') }}" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
        <div>
            <label class="form-label">Curso</label>
            <select name="curso_id" class="form-control" style="min-width: 200px;">
                <option value="">Todos los cursos</option>
                @foreach($cursos as $curso)

                    <option value="{{ $curso->id }}" {{ $cursoId == $curso->id ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                
@endforeach
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

<div class="glass-panel" style="padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 2px solid var(--card-border); padding-bottom: 12px;">
        <div>
            <h3 style="font-weight: 800; color: var(--text-primary);">Iglesia Actúa</h3>
            <p style="color: var(--text-secondary); font-size: 13px;">Reporte de Asistencia</p>
        </div>
        <div style="text-align: right; font-size: 13px; color: var(--text-secondary);">
            <div>Generado: {{ now()->translatedFormat('d/m/Y H:i') }}</div>
            @if($desde || $hasta)
            <div>Periodo: {{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : 'inicio' }} - {{ $hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : 'actual' }}</div>
            @endif
            @if($cursoId) <div>Curso: {{ $cursos->firstWhere('id', $cursoId)->nombre ?? '' }}</div> @endif
        </div>
    </div>

    <div style="display: flex; gap: 16px; margin-bottom: 18px; flex-wrap: wrap;">
        <div style="background: rgba(47,158,68,.12); padding: 10px 16px; border-radius: 10px;"><b>{{ $resumen['presentes'] }}</b> Presentes</div>
        <div style="background: rgba(220,38,38,.12); padding: 10px 16px; border-radius: 10px;"><b>{{ $resumen['ausentes'] }}</b> Ausentes</div>
        <div style="background: rgba(245,158,11,.12); padding: 10px 16px; border-radius: 10px;"><b>{{ $resumen['tardanzas'] }}</b> Tardanzas</div>
        <div style="background: rgba(99,102,241,.12); padding: 10px 16px; border-radius: 10px;"><b>{{ $resumen['justificados'] }}</b> Justificados</div>
    </div>

    @if($registros->isEmpty())
        <p style="color: var(--text-secondary);">No hay registros de asistencia que coincidan con los filtros.</p>
    @else
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Miembro</th>
                    <th>Curso</th>
                    <th>Sesión / Tema</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $i => $r)

                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $r->miembro->nombre ?? '-' }}</td>
                    <td>{{ $r->sesion->curso->nombre ?? '-' }}</td>
                    <td>{{ $r->sesion->tema ?? '-' }}</td>
                    <td>{{ $r->sesion ? \Carbon\Carbon::parse($r->sesion->fecha)->format('d/m/Y') : '-' }}</td>
                    <td>
                        <span class="badge @if($r->estado == 'Presente') badge-active @elseif($r->estado == 'Ausente') badge-inactive @else badge-warning @endif">{{ $r->estado }}</span>
                    </td>
                </tr>
                
@endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
