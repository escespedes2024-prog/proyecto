@extends('layouts.app')

@section('title', 'Panel de Control')

@section('content')
<style>
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px; }
    .kpi-card { padding: 22px; border-radius: 14px; position: relative; overflow: hidden; }
    .kpi-card .kpi-icon { font-size: 26px; margin-bottom: 12px; opacity: .9; }
    .kpi-card .kpi-label { font-size: 13px; color: var(--text-secondary); font-weight: 500; text-transform: uppercase; letter-spacing: .4px; }
    .kpi-card .kpi-value { font-size: 30px; font-weight: 800; color: var(--text-primary); margin-top: 6px; }
    .kpi-card .kpi-sub { font-size: 12px; color: var(--text-secondary); margin-top: 8px; }
    .dash-cols { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px; }
    @media (max-width: 900px) { .dash-cols { grid-template-columns: 1fr; } }
    .chart-h { display: flex; align-items: flex-end; gap: 14px; height: 180px; padding-top: 10px; }
    .chart-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px; min-width: 0; }
    .chart-bar-wrap { width: 100%; display: flex; align-items: flex-end; justify-content: center; gap: 3px; height: 150px; }
    .bar { width: 12px; border-radius: 4px 4px 0 0; min-height: 2px; }
    .bar.ing { background: linear-gradient(180deg, var(--success), #2f9e44); }
    .bar.egr { background: linear-gradient(180deg, var(--error), #e03131); }
    .chart-label { font-size: 11px; color: var(--text-secondary); text-align: center; }
    .legend { display: flex; gap: 16px; margin-bottom: 14px; font-size: 13px; color: var(--text-secondary); }
    .legend span { display: flex; align-items: center; gap: 6px; }
    .dot { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }
    .list-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--card-border); }
    .list-item:last-child { border-bottom: none; }
    .list-item .date { font-size: 12px; color: var(--text-secondary); }
    .list-item .name { font-weight: 600; color: var(--text-primary); }
    .badge-sm { font-size: 11px; padding: 3px 8px; border-radius: 20px; }
</style>

<div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <div class="dashboard-card glass-panel" style="border-left: 4px solid var(--primary);">
        <div class="card-icon">👥</div>
        <div class="card-title">Miembros Registrados</div>
        <div class="card-value">{{ $stats['miembros'] }}</div>
        <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">{{ $stats['miembros_activos'] }} activos</div>
    </div>

    <div class="dashboard-card glass-panel" style="border-left: 4px solid var(--accent);">
        <div class="card-icon">🛡️</div>
        <div class="card-title">Ministerios</div>
        <div class="card-value">{{ $stats['ministerios'] }}</div>
        <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">{{ $stats['lideres'] }} líderes activos</div>
    </div>

    <div class="dashboard-card glass-panel" style="border-left: 4px solid var(--success);">
        <div class="card-icon">💵</div>
        <div class="card-title">Ingresos del Mes</div>
        <div class="card-value">Bs {{ number_format($stats['ingresos_mes'], 2) }}</div>
        <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">Total: Bs {{ number_format($stats['ingresos_totales'], 2) }}</div>
    </div>

    <div class="dashboard-card glass-panel" style="border-left: 4px solid var(--error);">
        <div class="card-icon">💸</div>
        <div class="card-title">Egresos del Mes</div>
        <div class="card-value">Bs {{ number_format($stats['egresos_mes'], 2) }}</div>
        <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">Balance mes: Bs {{ number_format($stats['balance_mes'], 2) }}</div>
    </div>
</div>

<div class="dash-cols">
    <div class="glass-panel" style="padding: 24px;">
        <h3 style="font-weight: 700; margin-bottom: 4px; color: var(--text-primary);">Flujo Financiero</h3>
        <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 14px;">Ingresos vs. Egresos (últimos 6 meses)</p>

        @php
            $max = max(array_merge(array_values($ingresosUltimos6->toArray()), array_values($egresosUltimos6->toArray()), [1]));
            $marcas = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));
        @endphp

        <div class="legend">
            <span><span class="dot" style="background: var(--success);"></span> Ingresos</span>
            <span><span class="dot" style="background: var(--error);"></span> Egresos</span>
        </div>

        <div class="chart-h">
            @foreach($marcas as $marca)
                @php
                    $in = $ingresosUltimos6[$marca] ?? 0;
                    $eg = $egresosUltimos6[$marca] ?? 0;
                    $hIn = $max > 0 ? round(($in / $max) * 150) : 0;
                    $hEg = $max > 0 ? round(($eg / $max) * 150) : 0;
                    $mesLabel = \Carbon\Carbon::createFromFormat('Y-m', $marca)->translatedFormat('M y');
                @endphp
                <div class="chart-col">
                    <div class="chart-bar-wrap">
                        <div class="bar ing" style="height: {{ $hIn }}px;" title="Bs {{ number_format($in, 2) }}"></div>
                        <div class="bar egr" style="height: {{ $hEg }}px;" title="Bs {{ number_format($eg, 2) }}"></div>
                    </div>
                    <div class="chart-label">{{ $mesLabel }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="glass-panel" style="padding: 24px;">
        <h3 style="font-weight: 700; margin-bottom: 4px; color: var(--text-primary);">Resumen General</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 16px;">
            <div style="background: rgba(47,158,68,.1); border-radius: 10px; padding: 14px;">
                <div style="font-size: 12px; color: var(--text-secondary);">Cursos</div>
                <div style="font-size: 24px; font-weight: 800; color: var(--text-primary);">{{ $stats['cursos'] }}</div>
            </div>
            <div style="background: rgba(37,99,235,.1); border-radius: 10px; padding: 14px;">
                <div style="font-size: 12px; color: var(--text-secondary);">Inscripciones</div>
                <div style="font-size: 24px; font-weight: 800; color: var(--text-primary);">{{ $stats['inscripciones'] }}</div>
            </div>
            <div style="background: rgba(154,52,18,.1); border-radius: 10px; padding: 14px;">
                <div style="font-size: 12px; color: var(--text-secondary);">Cultos</div>
                <div style="font-size: 24px; font-weight: 800; color: var(--text-primary);">{{ $stats['cultos'] }}</div>
            </div>
            <div style="background: rgba(12,74,110,.1); border-radius: 10px; padding: 14px;">
                <div style="font-size: 12px; color: var(--text-secondary);">Docentes</div>
                <div style="font-size: 24px; font-weight: 800; color: var(--text-primary);">{{ $stats['docentes'] }}</div>
            </div>
        </div>

        <h3 style="font-weight: 700; margin: 22px 0 10px; color: var(--text-primary); font-size: 15px;">Asistencia General</h3>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <span class="badge badge-active" style="padding: 5px 12px;">Presentes: {{ $asistenciaStats['presentes'] }}</span>
            <span class="badge badge-inactive" style="padding: 5px 12px;">Ausentes: {{ $asistenciaStats['ausentes'] }}</span>
            <span class="badge badge-warning" style="padding: 5px 12px;">Tardanzas: {{ $asistenciaStats['tardanzas'] }}</span>
        </div>
    </div>
</div>

@if($ingresosPorTipo->isNotEmpty() || $egresosPorTipo->isNotEmpty())
<div class="dash-cols">
    @if($ingresosPorTipo->isNotEmpty())
    <div class="glass-panel" style="padding: 24px;">
        <h3 style="font-weight: 700; margin-bottom: 4px; color: var(--text-primary);">Ingresos del Mes por Tipo</h3>
        <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 10px;">Desglose de recaudación del periodo actual</p>
        @php $maxTipo = max(array_merge($ingresosPorTipo->values()->all(), [1])); @endphp
        @foreach($ingresosPorTipo as $tipo => $total)
        <div style="margin-bottom: 11px;">
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                <span style="color: var(--text-primary);">{{ $tipo }}</span>
                <span style="color: var(--text-secondary);">Bs {{ number_format($total, 2) }}</span>
            </div>
            <div style="height: 8px; background: rgba(0,0,0,.08); border-radius: 6px; overflow: hidden;">
                <div style="height: 100%; width: {{ round(($total / $maxTipo) * 100) }}%; background: linear-gradient(90deg, var(--success), #2f9e44); border-radius: 6px;"></div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($egresosPorTipo->isNotEmpty())
    <div class="glass-panel" style="padding: 24px;">
        <h3 style="font-weight: 700; margin-bottom: 4px; color: var(--text-primary);">Egresos del Mes por Tipo</h3>
        <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 10px;">Desglose de gastos del periodo actual</p>
        @php $maxTipoE = max(array_merge($egresosPorTipo->values()->all(), [1])); @endphp
        @foreach($egresosPorTipo as $tipo => $total)
        <div style="margin-bottom: 11px;">
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                <span style="color: var(--text-primary);">{{ $tipo }}</span>
                <span style="color: var(--text-secondary);">Bs {{ number_format($total, 2) }}</span>
            </div>
            <div style="height: 8px; background: rgba(0,0,0,.08); border-radius: 6px; overflow: hidden;">
                <div style="height: 100%; width: {{ round(($total / $maxTipoE) * 100) }}%; background: linear-gradient(90deg, var(--error), #e03131); border-radius: 6px;"></div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endif

<div class="dash-cols">
    <div class="glass-panel" style="padding: 24px;">
        <h3 style="font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">Próximas Actividades</h3>
        @forelse($proximasActividades as $actividad)
        <div class="list-item">
            <div>
                <div class="name">{{ $actividad->nombre }}</div>
                <div class="date">{{ $actividad->ministerio->nombre ?? 'Sin ministerio' }}</div>
            </div>
            <div class="date">{{ \Carbon\Carbon::parse($actividad->fecha)->translatedFormat('d M Y') }}

                <span class="badge badge-sm @if($actividad->estado == 'Completado') badge-active @elseif($actividad->estado == 'Cancelado') badge-inactive @else badge-warning @endif" style="margin-left: 6px;">{{ $actividad->estado }}</span>
            </div>
        </div>
        @empty
        <p style="color: var(--text-secondary);">No hay actividades programadas próximamente.</p>
        @endforelse
    </div>

    <div class="glass-panel" style="padding: 24px;">
        <h3 style="font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">Próximas Sesiones</h3>
        @forelse($proximasSesiones as $sesion)
        <div class="list-item">
            <div>
                <div class="name">{{ $sesion->tema }}</div>
                <div class="date">{{ $sesion->curso->nombre ?? 'Sin curso' }}</div>
            </div>
            <div class="date">{{ \Carbon\Carbon::parse($sesion->fecha)->translatedFormat('d M Y') }}</div>
        </div>
        @empty
        <p style="color: var(--text-secondary);">No hay sesiones programadas próximamente.</p>
        @endforelse
    </div>
</div>

<div class="glass-panel" style="padding: 24px;">
    <h3 style="font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">Accesos Rápidos</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px;">
        <a href="{{ route('miembros.create') }}" class="btn" style="width: auto; justify-content: center;">➕ Miembro</a>
        <a href="{{ route('ministerios.create') }}" class="btn" style="width: auto; justify-content: center;">🛡️ Ministerios</a>
        <a href="{{ route('cursos.create') }}" class="btn" style="width: auto; justify-content: center;">🎓 Cursos</a>
        <a href="{{ route('inscripciones.index') }}" class="btn" style="width: auto; justify-content: center;">📝 Inscripciones</a>
        <a href="{{ route('asistencia.registrar') }}" class="btn" style="width: auto; justify-content: center;">✅ Asistencia</a>
        <a href="{{ route('ingresos.create') }}" class="btn" style="width: auto; justify-content: center;">💵 Registrar Ingreso</a>
        <a href="{{ route('egresos.create') }}" class="btn" style="width: auto; justify-content: center;">💸 Registrar Egreso</a>
        <a href="{{ route('contratos.create') }}" class="btn" style="width: auto; justify-content: center;">📄 Nuevo Contrato</a>
    </div>
</div>
@endsection
