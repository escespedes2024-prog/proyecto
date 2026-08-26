@extends('layouts.app')

@section('title', 'Historial de Asistencia')

@section('content')
<div class="glass-panel" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 25px;">
        <h2 style="font-weight: 700; color: var(--text-primary); margin: 0;">
            Historial de Asistencia — {{ $miembro->nombre }}
        </h2>
        <a href="{{ route('miembros.show', $miembro->id) }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">
            Volver al Miembro
        </a>
    </div>

    <!-- Resumen por curso -->
    @if($resumenPorCurso->count())
        <h3 style="font-weight: 700; color: var(--text-primary); margin-bottom: 15px;">Resumen por Curso</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 30px;">
            @foreach($resumenPorCurso as $cursoNombre => $datos)
                @php
                    $porcentaje = $datos['porcentaje'];
                    $colorBadge = $porcentaje >= 80 ? '#34d399' : ($porcentaje >= 60 ? '#fbbf24' : '#f87171');
                @endphp
                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 10px; padding: 18px;">
                    <div style="font-weight: 700; color: var(--text-primary); font-size: 1rem;">{{ $cursoNombre }}</div>
                    <div style="margin-top: 10px;">
                        <span style="font-size: 1.5rem; font-weight: 800; color: {{ $colorBadge }};">{{ $porcentaje }}%</span>
                        <span style="color: var(--text-secondary); font-size: 0.85rem; margin-left: 8px;">asistencia</span>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 5px;">
                        {{ $datos['presentes'] }} de {{ $datos['total'] }} sesiones asistidas
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Tabla detallada -->
    <h3 style="font-weight: 700; color: var(--text-primary); margin-bottom: 15px;">Detalle por Sesión</h3>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Fecha</th>
                    <th>Tema</th>
                    <th>Estado</th>
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
                        <td>{{ $asistencia->sesion?->curso?->nombre ?? 'N/A' }}</td>
                        <td>{{ $asistencia->sesion?->fecha ? \Carbon\Carbon::parse($asistencia->sesion->fecha)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $asistencia->sesion?->tema ?? 'N/A' }}</td>
                        <td><span class="badge {{ $badgeEstado }}">{{ $asistencia->estado }}</span></td>
                        <td>{{ $asistencia->observacion ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                            Este miembro no tiene registros de asistencia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
