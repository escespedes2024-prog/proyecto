@extends('layouts.app')

@section('title', 'Módulo de Asistencia')

@section('content')
<div class="glass-panel" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-weight: 700; color: var(--text-primary);">Asistencia General</h2>
    </div>

    <!-- Filtros -->
    <form method="GET" action="{{ route('asistencia.index') }}" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; padding: 15px; border: 1px solid var(--card-border); border-radius: 10px; margin-bottom: 25px;">
        <div class="form-group" style="flex: 2; min-width: 200px; margin-bottom: 0;">
            <label class="form-label">Buscar Miembro</label>
            <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Nombre del miembro...">
        </div>
        <div class="form-group" style="flex: 1; min-width: 160px; margin-bottom: 0;">
            <label class="form-label">Curso</label>
            <select name="curso_id" class="form-control">
                <option value="">Todos...</option>
                @foreach($cursos as $curso)
                    <option value="{{ $curso->id }}" {{ request('curso_id') == $curso->id ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="flex: 1; min-width: 140px; margin-bottom: 0;">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-control">
                <option value="">Todos...</option>
                @foreach(['Presente', 'Ausente', 'Tardanza', 'Justificado'] as $e)
                    <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>{{ $e }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="flex: 1; min-width: 140px; margin-bottom: 0;">
            <label class="form-label">Desde</label>
            <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
        </div>
        <div class="form-group" style="flex: 1; min-width: 140px; margin-bottom: 0;">
            <label class="form-label">Hasta</label>
            <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
        </div>
        <button type="submit" class="btn" style="width: auto; padding: 10px 20px;">Buscar</button>
        @if(request()->filled('q') || request()->filled('curso_id') || request()->filled('estado') || request()->filled('desde') || request()->filled('hasta'))
            <a href="{{ route('asistencia.index') }}" class="btn" style="width: auto; padding: 10px 20px; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">Limpiar</a>
        @endif
    </form>

    <!-- Resumen rápido -->
    @if($asistencias->count())
        @php
            $totalRegistros = $asistencias->total();
            $totalPresentes = $asistencias->getCollection()->where('estado', 'Presente')->count();
            $totalAusentes = $asistencias->getCollection()->where('estado', 'Ausente')->count();
            $totalTardanzas = $asistencias->getCollection()->where('estado', 'Tardanza')->count();
            $totalJustificados = $asistencias->getCollection()->where('estado', 'Justificado')->count();
        @endphp
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 15px; margin-bottom: 25px;">
            <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 10px; padding: 12px 15px;">
                <div style="color: var(--text-secondary); font-size: 0.8rem;">Total Registros</div>
                <div style="font-weight: 700; font-size: 1.3rem; color: var(--text-primary);">{{ $totalRegistros }}</div>
            </div>
            <div style="background: rgba(52, 211, 153, 0.08); border: 1px solid rgba(52, 211, 153, 0.3); border-radius: 10px; padding: 12px 15px;">
                <div style="color: #34d399; font-weight: 700;">Presentes</div>
                <div style="font-weight: 700; font-size: 1.3rem;">{{ $totalPresentes }}</div>
            </div>
            <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px; padding: 12px 15px;">
                <div style="color: #f87171; font-weight: 700;">Ausentes</div>
                <div style="font-weight: 700; font-size: 1.3rem;">{{ $totalAusentes }}</div>
            </div>
            <div style="background: rgba(251, 191, 36, 0.08); border: 1px solid rgba(251, 191, 36, 0.3); border-radius: 10px; padding: 12px 15px;">
                <div style="color: #fbbf24; font-weight: 700;">Tardanzas</div>
                <div style="font-weight: 700; font-size: 1.3rem;">{{ $totalTardanzas }}</div>
            </div>
            <div style="background: rgba(34, 211, 238, 0.08); border: 1px solid rgba(34, 211, 238, 0.3); border-radius: 10px; padding: 12px 15px;">
                <div style="color: #22d3ee; font-weight: 700;">Justificados</div>
                <div style="font-weight: 700; font-size: 1.3rem;">{{ $totalJustificados }}</div>
            </div>
        </div>
    @endif

    <!-- Tabla -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Miembro</th>
                    <th>Curso</th>
                    <th>Sesión</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Registrado por</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asistencias as $asistencia)
                    @php
                        $badgeEstado = match($asistencia->estado) {
                            'Presente' => 'badge-active',
                            'Ausente' => 'badge-inactive',
                            'Tardanza' => 'badge-warning',
                            'Justificado' => 'badge-info',
                        };
                    @endphp
                    <tr>
                        <td style="font-weight: 600;">{{ $asistencia->miembro->nombre ?? 'N/A' }}</td>
                        <td>{{ $asistencia->sesion?->curso?->nombre ?? 'N/A' }}</td>
                        <td>{{ $asistencia->sesion?->tema ?? 'N/A' }}</td>
                        <td>{{ $asistencia->sesion?->fecha ? \Carbon\Carbon::parse($asistencia->sesion->fecha)->format('d/m/Y') : 'N/A' }}</td>
                        <td><span class="badge {{ $badgeEstado }}">{{ $asistencia->estado }}</span></td>
                        <td>{{ $asistencia->user?->name ?? 'N/A' }}</td>
                        <td>{{ $asistencia->observacion ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                            No hay registros de asistencia con los filtros seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $asistencias->links() }}
    </div>
</div>
@endsection
