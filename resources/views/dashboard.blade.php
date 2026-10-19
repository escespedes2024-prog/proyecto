@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    // Escala del gráfico: máximo entre ingresos, egresos y 1 (evita división por cero).
    $valoresGrafico = array_merge(
        array_values($ingresosUltimos6->toArray()),
        array_values($egresosUltimos6->toArray())
    );
    $hayMovimientos = count(array_filter($valoresGrafico, fn ($v) => (float) $v > 0)) > 0;
    $maxMes = max(array_merge($valoresGrafico, [1]));
    $marcas = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));

    // Asistencia: totales acumulados de todos los registros (contexto real, no se mezclan sesiones).
    $asistTotal = $asistenciaStats['presentes'] + $asistenciaStats['ausentes'] + $asistenciaStats['tardanzas'];
    $pctAsist = fn ($n) => $asistTotal > 0 ? round(($n / $asistTotal) * 100) : 0;

    // Accesos rápidos visibles según el mismo rol que exigen las rutas (no altera permisos).
    $puedeParametrizacion = auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Secretario');
    $puedeFinanzas = auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Tesorero');

    $mesActual = ucfirst(now()->locale('es')->translatedFormat('F Y'));
    $fechaActual = ucfirst(now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y'));
@endphp

{{-- ============================ 1. Encabezado ============================ --}}
<header class="dash-header">
    <div class="dash-header__intro">
        <h1 class="dash-header__title">Dashboard</h1>
        <p class="dash-header__subtitle">Resumen general de la gestión de la iglesia</p>
    </div>
    <div class="dash-header__tools">
        <span class="dash-chip" title="Período financiero mostrado">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            <span>{{ $mesActual }}</span>
        </span>
        <span class="dash-chip">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <span>{{ $fechaActual }}</span>
        </span>
        <button type="button" class="dash-refresh" onclick="window.location.reload()" title="Actualizar datos" aria-label="Actualizar datos">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
            <span>Actualizar</span>
        </button>
    </div>
</header>

{{-- ==================== 2. Estadísticas principales ==================== --}}
<section class="dash-kpis" aria-label="Estadísticas principales">
    <article class="dash-kpi dash-kpi--members">
        <span class="dash-kpi__icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
        </span>
        <div class="dash-kpi__body">
            <span class="dash-kpi__label">Miembros registrados</span>
            <span class="dash-kpi__value">{{ number_format($stats['miembros']) }}</span>
            <span class="dash-kpi__sub">{{ number_format($stats['miembros_activos']) }} activos</span>
        </div>
    </article>

    <article class="dash-kpi dash-kpi--ministries">
        <span class="dash-kpi__icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
        </span>
        <div class="dash-kpi__body">
            <span class="dash-kpi__label">Ministerios</span>
            <span class="dash-kpi__value">{{ number_format($stats['ministerios']) }}</span>
            <span class="dash-kpi__sub">{{ number_format($stats['lideres']) }} líderes activos</span>
        </div>
    </article>

    <article class="dash-kpi dash-kpi--income">
        <span class="dash-kpi__icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
        </span>
        <div class="dash-kpi__body">
            <span class="dash-kpi__label">Ingresos del mes</span>
            <span class="dash-kpi__value dash-kpi__value--money">Bs {{ number_format((float) $stats['ingresos_mes'], 2) }}</span>
            <span class="dash-kpi__sub">Total histórico: Bs {{ number_format((float) $stats['ingresos_totales'], 2) }}</span>
        </div>
    </article>

    <article class="dash-kpi dash-kpi--expense">
        <span class="dash-kpi__icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5m-18 6.75h18M2.25 6.75a1.5 1.5 0 0 1 1.5-1.5h16.5a1.5 1.5 0 0 1 1.5 1.5v10.5a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6.75Z"/></svg>
        </span>
        <div class="dash-kpi__body">
            <span class="dash-kpi__label">Egresos del mes</span>
            <span class="dash-kpi__value dash-kpi__value--money">Bs {{ number_format((float) $stats['egresos_mes'], 2) }}</span>
            <span class="dash-kpi__sub">Balance del mes: Bs {{ number_format((float) $stats['balance_mes'], 2) }}</span>
        </div>
    </article>
</section>

{{-- ============ 3. Flujo financiero + Resumen general ============ --}}
<div class="dash-grid dash-grid--fin">
    <section class="dash-panel dash-panel--chart" aria-labelledby="dash-chart-title">
        <div class="dash-panel__head">
            <div>
                <h2 class="dash-panel__title" id="dash-chart-title">Flujo Financiero</h2>
                <p class="dash-panel__desc">Ingresos y egresos de los últimos 6 meses</p>
            </div>
            <div class="dash-legend">
                <span><i class="dash-legend__dot dash-legend__dot--in"></i>Ingresos</span>
                <span><i class="dash-legend__dot dash-legend__dot--out"></i>Egresos</span>
            </div>
        </div>

        @if($hayMovimientos)
        <figure class="dash-chart" aria-label="Gráfico de ingresos y egresos de los últimos 6 meses">
            @foreach($marcas as $marca)
                @php
                    $in = (float) ($ingresosUltimos6[$marca] ?? 0);
                    $eg = (float) ($egresosUltimos6[$marca] ?? 0);
                    $pIn = $in > 0 ? max((int) round(($in / $maxMes) * 100), 2) : 0;
                    $pEg = $eg > 0 ? max((int) round(($eg / $maxMes) * 100), 2) : 0;
                    $fechaMes = \Carbon\Carbon::createFromFormat('Y-m', $marca)->locale('es');
                    $mesCorto = ucfirst($fechaMes->translatedFormat('M'));
                    $mesLargo = ucfirst($fechaMes->translatedFormat('F Y'));
                @endphp
                <div class="dash-chart__col">
                    <div class="dash-chart__bars">
                        <span class="dash-bar dash-bar--in" style="height: {{ $pIn }}%" data-tip="Ingresos {{ $mesLargo }}: Bs {{ number_format($in, 2) }}" aria-label="Ingresos de {{ $mesLargo }}: {{ number_format($in, 2) }} bolivianos"></span>
                        <span class="dash-bar dash-bar--out" style="height: {{ $pEg }}%" data-tip="Egresos {{ $mesLargo }}: Bs {{ number_format($eg, 2) }}" aria-label="Egresos de {{ $mesLargo }}: {{ number_format($eg, 2) }} bolivianos"></span>
                    </div>
                    <span class="dash-chart__label">{{ $mesCorto }}</span>
                </div>
            @endforeach
        </figure>
        @else
        <div class="dash-empty">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.3" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
            <p>Sin movimientos financieros en los últimos 6 meses.</p>
        </div>
        @endif
    </section>

    <section class="dash-panel dash-panel--summary" aria-labelledby="dash-summary-title">
        <div class="dash-panel__head">
            <div>
                <h2 class="dash-panel__title" id="dash-summary-title">Resumen General</h2>
                <p class="dash-panel__desc">Cursos, inscripciones, cultos y docentes</p>
            </div>
        </div>

        <div class="dash-mini">
            <div class="dash-mini__item dash-mini__item--cursos">
                <span class="dash-mini__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg></span>
                <div>
                    <span class="dash-mini__value">{{ number_format($stats['cursos']) }}</span>
                    <span class="dash-mini__label">Cursos</span>
                </div>
            </div>
            <div class="dash-mini__item dash-mini__item--inscripciones">
                <span class="dash-mini__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/></svg></span>
                <div>
                    <span class="dash-mini__value">{{ number_format($stats['inscripciones']) }}</span>
                    <span class="dash-mini__label">Inscripciones</span>
                </div>
            </div>
            <div class="dash-mini__item dash-mini__item--cultos">
                <span class="dash-mini__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/></svg></span>
                <div>
                    <span class="dash-mini__value">{{ number_format($stats['cultos']) }}</span>
                    <span class="dash-mini__label">Cultos</span>
                </div>
            </div>
            <div class="dash-mini__item dash-mini__item--docentes">
                <span class="dash-mini__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.409 60.44 60.44 0 0 0-.492-6.31m-15.48 0a48.667 48.667 0 0 1 7.148-.474m-7.148 3.334a48.668 48.668 0 0 1 7.148-.474m0 0a48.667 48.667 0 0 1 7.148.474m-7.148-3.334A48.667 48.667 0 0 1 19.02 9.84m0 0A48.622 48.622 0 0 1 21.42 15.25M18 5.25a48.715 48.715 0 0 0-12 0v3.375m12-3.375a48.716 48.716 0 0 0 0 3.375M12 9.375a48.667 48.667 0 0 1 0 6.375M12 15.75a48.667 48.667 0 0 1-6 0"/></svg></span>
                <div>
                    <span class="dash-mini__value">{{ number_format($stats['docentes']) }}</span>
                    <span class="dash-mini__label">Docentes activos</span>
                </div>
            </div>
        </div>

        <div class="dash-attendance">
            <h3 class="dash-subtitle">Asistencia general</h3>
            <p class="dash-note">Registro acumulado de todas las sesiones</p>
            <div class="dash-att">
                <div class="dash-att__item dash-att__item--ok">
                    <span class="dash-att__value">{{ number_format($asistenciaStats['presentes']) }}</span>
                    <span class="dash-att__label">Presentes · {{ $pctAsist($asistenciaStats['presentes']) }}%</span>
                </div>
                <div class="dash-att__item dash-att__item--no">
                    <span class="dash-att__value">{{ number_format($asistenciaStats['ausentes']) }}</span>
                    <span class="dash-att__label">Ausentes · {{ $pctAsist($asistenciaStats['ausentes']) }}%</span>
                </div>
                <div class="dash-att__item dash-att__item--late">
                    <span class="dash-att__value">{{ number_format($asistenciaStats['tardanzas']) }}</span>
                    <span class="dash-att__label">Tardanzas · {{ $pctAsist($asistenciaStats['tardanzas']) }}%</span>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- ============ 4. Desglose financiero del mes por tipo ============ --}}
@if($ingresosPorTipo->isNotEmpty() || $egresosPorTipo->isNotEmpty())
<div class="dash-grid dash-grid--lists">
    @if($ingresosPorTipo->isNotEmpty())
    <section class="dash-panel">
        <div class="dash-panel__head">
            <div>
                <h2 class="dash-panel__title">Ingresos del mes por tipo</h2>
                <p class="dash-panel__desc">Desglose de la recaudación del período actual</p>
            </div>
        </div>
        @php $maxTipo = max(array_merge($ingresosPorTipo->values()->all(), [1])); @endphp
        <div class="dash-breakdown">
            @foreach($ingresosPorTipo as $tipo => $total)
            <div class="dash-breakdown__row">
                <div class="dash-breakdown__head">
                    <span>{{ $tipo }}</span>
                    <span>Bs {{ number_format((float) $total, 2) }}</span>
                </div>
                <div class="dash-breakdown__track">
                    <div class="dash-breakdown__fill dash-breakdown__fill--in" style="width: {{ round(((float) $total / $maxTipo) * 100) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    @if($egresosPorTipo->isNotEmpty())
    <section class="dash-panel">
        <div class="dash-panel__head">
            <div>
                <h2 class="dash-panel__title">Egresos del mes por tipo</h2>
                <p class="dash-panel__desc">Desglose de los gastos del período actual</p>
            </div>
        </div>
        @php $maxTipoE = max(array_merge($egresosPorTipo->values()->all(), [1])); @endphp
        <div class="dash-breakdown">
            @foreach($egresosPorTipo as $tipo => $total)
            <div class="dash-breakdown__row">
                <div class="dash-breakdown__head">
                    <span>{{ $tipo }}</span>
                    <span>Bs {{ number_format((float) $total, 2) }}</span>
                </div>
                <div class="dash-breakdown__track">
                    <div class="dash-breakdown__fill dash-breakdown__fill--out" style="width: {{ round(((float) $total / $maxTipoE) * 100) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endif

{{-- ============ 5. Próximas actividades y próximas sesiones ============ --}}
<div class="dash-grid dash-grid--lists">
    <section class="dash-panel" aria-labelledby="dash-actividades-title">
        <div class="dash-panel__head">
            <div>
                <h2 class="dash-panel__title" id="dash-actividades-title">Próximas Actividades</h2>
                <p class="dash-panel__desc">Agenda de actividades programadas</p>
            </div>
            @if($puedeParametrizacion)
            @if(auth()->user()->puede('actividades', 'ver'))
            <a class="dash-link" href="{{ route('actividades.index') }}">
                Ver todas
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
            @endif
            @endif
        </div>

        @forelse($proximasActividades as $actividad)
        <div class="dash-list__item">
            <div class="dash-list__main">
                <span class="dash-list__name">{{ $actividad->nombre }}</span>
                <span class="dash-list__meta">
                    <span>{{ $actividad->ministerio->nombre ?? 'Sin ministerio' }}</span>
                    @if($actividad->lugar)<span>· {{ $actividad->lugar }}</span>@endif
                </span>
            </div>
            <div class="dash-list__side">
                <span class="dash-list__date">{{ \Carbon\Carbon::parse($actividad->fecha)->locale('es')->translatedFormat('d M Y') }}</span>
                <span class="badge dash-badge @if($actividad->estado == 'Completado') badge-active @elseif($actividad->estado == 'Cancelado') badge-inactive @else badge-warning @endif">{{ $actividad->estado }}</span>
            </div>
        </div>
        @empty
        <div class="dash-empty">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.3" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            <p>No hay actividades programadas próximamente.</p>
        </div>
        @endforelse
    </section>

    <section class="dash-panel" aria-labelledby="dash-sesiones-title">
        <div class="dash-panel__head">
            <div>
                <h2 class="dash-panel__title" id="dash-sesiones-title">Próximas Sesiones</h2>
                <p class="dash-panel__desc">Sesiones de cursos por impartir</p>
            </div>
            @if($puedeParametrizacion)
            @if(auth()->user()->puede('sesiones', 'ver'))
            <a class="dash-link" href="{{ route('sesiones.index') }}">
                Ver todas
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
            @endif
            @endif
        </div>

        @forelse($proximasSesiones as $sesion)
        <div class="dash-list__item">
            <div class="dash-list__main">
                <span class="dash-list__name">{{ $sesion->tema }}</span>
                <span class="dash-list__meta">
                    <span>{{ $sesion->curso->nombre ?? 'Sin curso' }}</span>
                    @if($sesion->docente && $sesion->docente->miembro)<span>· {{ $sesion->docente->miembro->nombre }}</span>@endif
                </span>
            </div>
            <div class="dash-list__side">
                <span class="dash-list__date">{{ \Carbon\Carbon::parse($sesion->fecha)->locale('es')->translatedFormat('d M Y') }}</span>
                <span class="dash-list__time">{{ \Carbon\Carbon::parse($sesion->hora)->format('H:i') }}</span>
            </div>
        </div>
        @empty
        <div class="dash-empty">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.3" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <p>No hay sesiones programadas próximamente.</p>
        </div>
        @endforelse
    </section>
</div>

{{-- ======================== 6. Accesos rápidos ======================== --}}
<section class="dash-panel dash-panel--quick" aria-labelledby="dash-quick-title">
    <div class="dash-panel__head">
        <div>
            <h2 class="dash-panel__title" id="dash-quick-title">Accesos Rápidos</h2>
            <p class="dash-panel__desc">Acciones frecuentes según tu rol</p>
        </div>
    </div>

    <div class="dash-quick">
        @if($puedeParametrizacion)
            @if(auth()->user()->puede('miembros', 'crear'))
            <a class="dash-quick__item" href="{{ route('miembros.create') }}">
                <span class="dash-quick__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg></span>
                <span class="dash-quick__label">Nuevo miembro</span>
            </a>
            @endif
            @if(auth()->user()->puede('ministerios', 'crear'))
            <a class="dash-quick__item" href="{{ route('ministerios.create') }}">
                <span class="dash-quick__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg></span>
                <span class="dash-quick__label">Nuevo ministerio</span>
            </a>
            @endif
            @if(auth()->user()->puede('cursos', 'crear'))
            <a class="dash-quick__item" href="{{ route('cursos.create') }}">
                <span class="dash-quick__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg></span>
                <span class="dash-quick__label">Nuevo curso</span>
            </a>
            @endif
            @if(auth()->user()->puede('inscripciones', 'crear'))
            <a class="dash-quick__item" href="{{ route('inscripciones.index') }}">
                <span class="dash-quick__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/></svg></span>
                <span class="dash-quick__label">Nueva inscripción</span>
            </a>
            @endif
            @if(auth()->user()->puede('asistencia', 'crear'))
            <a class="dash-quick__item" href="{{ route('asistencia.registrar') }}">
                <span class="dash-quick__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3"/></svg></span>
                <span class="dash-quick__label">Registrar asistencia</span>
            </a>
            @endif
        @endif
        @if($puedeFinanzas)
            @if(auth()->user()->puede('ingresos', 'crear'))
            <a class="dash-quick__item" href="{{ route('ingresos.create') }}">
                <span class="dash-quick__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg></span>
                <span class="dash-quick__label">Registrar ingreso</span>
            </a>
            @endif
            @if(auth()->user()->puede('egresos', 'crear'))
            <a class="dash-quick__item" href="{{ route('egresos.create') }}">
                <span class="dash-quick__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5m-18 6.75h18M2.25 6.75a1.5 1.5 0 0 1 1.5-1.5h16.5a1.5 1.5 0 0 1 1.5 1.5v10.5a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6.75Z"/></svg></span>
                <span class="dash-quick__label">Registrar egreso</span>
            </a>
            @endif
            @if(auth()->user()->puede('contratos', 'crear'))
            <a class="dash-quick__item" href="{{ route('contratos.create') }}">
                <span class="dash-quick__icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg></span>
                <span class="dash-quick__label">Nuevo contrato</span>
            </a>
            @endif
        @endif
    </div>
</section>
@endsection
