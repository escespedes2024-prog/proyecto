@extends('layouts.app')

@section('title', 'Detalle del Curso')

@section('content')
<div class="glass-panel" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 25px;">
        <h2 style="font-weight: 700; color: var(--text-primary); margin: 0;">{{ $curso->nombre }}</h2>
        <div style="display: flex; gap: 10px;">
            @if(!$curso->trashed() && $curso->tieneHorario() && $curso->f_fin)
                <form method="POST" action="{{ route('sesiones.generar', $curso->id) }}" onsubmit="return confirm('Se crearán las sesiones de los días {{ $curso->dias_nombres }} a las {{ substr($curso->hora_inicio, 0, 5) }} entre {{ \Carbon\Carbon::parse($curso->f_inicio)->format('d/m/Y') }} y {{ \Carbon\Carbon::parse($curso->f_fin)->format('d/m/Y') }}. ¿Continuar?');">
                    @csrf
                    <button type="submit" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem;">⚙ Generar Sesiones</button>
                </form>
            @endif
            <a href="{{ route('inscripciones.gestion', $curso->id) }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem;">
                Gestionar Inscripciones
            </a>
            <a href="{{ route('cursos.index') }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">
                Volver
            </a>
        </div>
    </div>

    <!-- Datos del curso -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 30px;">
        @foreach([
            'Inicio' => \Carbon\Carbon::parse($curso->f_inicio)->format('d/m/Y'),
            'Fin' => $curso->f_fin ? \Carbon\Carbon::parse($curso->f_fin)->format('d/m/Y') : 'N/A',
            'Horario' => $curso->tieneHorario() ? $curso->dias_nombres . ' · ' . $curso->rangoHorario() : 'Sin horario fijo',
            'Cupo' => $inscripciones->whereNull('deleted_at')->count() . ' / ' . $curso->cupo_max,
            'Costo Inscripción' => $curso->tiene_pago ? '$' . number_format($curso->monto_inscripcion, 2) : 'Gratis',
        ] as $label => $valor)
            <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 10px; padding: 15px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); font-weight: 700;">{{ $label }}</div>
                <div style="font-weight: 700; color: var(--text-primary); font-size: 1.05rem; margin-top: 5px;">{{ $valor }}</div>
            </div>
        @endforeach
    </div>

    <!-- Docentes -->
    @if($curso->docentes->count())
        <h3 style="font-weight: 700; color: var(--text-primary); margin-bottom: 15px;">Docentes</h3>
        <p style="color: var(--text-secondary); margin-bottom: 30px;">{{ $curso->docentes->map(fn ($d) => $d->miembro?->nombre ?? '—')->implode(', ') }}</p>
    @endif

    <!-- Inscritos (solo lectura) -->
    <h3 style="font-weight: 700; color: var(--text-primary); margin-bottom: 15px;">Miembros Inscritos</h3>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Miembro</th>
                    <th>F. Inscripción</th>
                    <th>Estado</th>
                    @if($curso->tiene_pago)
                        <th>Pagado</th>
                        <th>Saldo</th>
                        <th>Estado Pago</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($inscripciones->whereNull('deleted_at') as $i)
                    <tr>
                        <td>{{ $i->miembro->nombre }}</td>
                        <td>{{ \Carbon\Carbon::parse($i->fecha_inscripcion)->format('d/m/Y') }}</td>
                        <td><span class="badge badge-active">{{ $i->estado }}</span></td>
                        @if($curso->tiene_pago)
                            <td>${{ number_format($i->monto_pagado, 2) }}</td>
                            <td>${{ number_format($i->saldo_pendiente, 2) }}</td>
                            <td>
                                <span class="badge {{ $i->estado_pago === 'Pagado' ? 'badge-active' : ($i->estado_pago === 'Pago Parcial' ? 'badge-warning' : 'badge-inactive') }}">
                                    {{ $i->estado_pago }}
                                </span>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $curso->tiene_pago ? 6 : 3 }}" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                            No hay miembros inscritos en este curso.
                            <br><br>
                            <a href="{{ route('inscripciones.gestion', $curso->id) }}" style="color: #22d3ee;">Ir a gestionar inscripciones →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
