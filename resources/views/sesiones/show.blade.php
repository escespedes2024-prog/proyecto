@extends('layouts.app')

@section('title', 'Detalle de Sesión')

@section('content')
<div class="glass-panel" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 25px;">
        <h2 style="font-weight: 700; color: var(--text-primary); margin: 0;">Asistencia: {{ $sesion->curso->nombre ?? 'N/A' }}</h2>
        <a href="{{ route('sesiones.index') }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">
            Volver
        </a>
    </div>

    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <ul style="color: #ef4444; margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Datos de la sesión -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 25px;">
        @foreach([
            'Fecha' => \Carbon\Carbon::parse($sesion->fecha)->format('d/m/Y'),
            'Hora' => $sesion->hora,
            'Tema' => $sesion->tema,
            'Dictado por' => $sesion->docente?->miembro?->nombre ?? 'Sin docente asignado',
            'Estado' => $sesion->trashed() ? 'Eliminada' : ($sesionPasada ? 'Finalizada' : 'Programada'),
        ] as $label => $valor)
            <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 10px; padding: 15px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); font-weight: 700;">{{ $label }}</div>
                <div style="font-weight: 700; color: var(--text-primary); font-size: 1rem; margin-top: 5px;">
                    @if($label === 'Estado')
                        <span class="badge {{ $sesion->trashed() ? 'badge-inactive' : ($sesionPasada ? 'badge-active' : 'badge-warning') }}">{{ $valor }}</span>
                    @else
                        {{ $valor }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Resumen de asistencia registrada -->
    @if($asistencias->count())
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 25px;">
            <div style="background: rgba(52, 211, 153, 0.08); border: 1px solid rgba(52, 211, 153, 0.3); border-radius: 10px; padding: 12px 15px;">
                <span style="color: #34d399; font-weight: 700;">Presentes:</span> <strong>{{ $resumen['Presente'] }}</strong>
            </div>
            <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px; padding: 12px 15px;">
                <span style="color: #f87171; font-weight: 700;">Ausentes:</span> <strong>{{ $resumen['Ausente'] }}</strong>
            </div>
            <div style="background: rgba(251, 191, 36, 0.08); border: 1px solid rgba(251, 191, 36, 0.3); border-radius: 10px; padding: 12px 15px;">
                <span style="color: #fbbf24; font-weight: 700;">Tardanzas:</span> <strong>{{ $resumen['Tardanza'] }}</strong>
            </div>
            <div style="background: rgba(34, 211, 238, 0.08); border: 1px solid rgba(34, 211, 238, 0.3); border-radius: 10px; padding: 12px 15px;">
                <span style="color: #22d3ee; font-weight: 700;">Justificados:</span> <strong>{{ $resumen['Justificado'] }}</strong>
            </div>
        </div>
    @endif

    <!-- Formulario de asistencia -->
    @if($sesion->trashed())
        <p style="text-align: center; color: var(--text-secondary); padding: 20px;">La sesión está eliminada. Restáurala para gestionar asistencia.</p>
    @elseif($inscritos->isEmpty())
        <p style="text-align: center; color: var(--text-secondary); padding: 20px;">
            No hay miembros inscritos en este curso.
            <br><br>
            <a href="{{ route('inscripciones.gestion', $sesion->id_curso) }}" style="color: #22d3ee;">Ir a gestionar inscripciones →</a>
        </p>
    @else
        <!-- Botón marcar todos presente -->
        <div style="margin-bottom: 20px;">
            <form method="POST" action="{{ route('asistencia.marcarTodos', $sesion->id) }}" style="display: inline;" onsubmit="return confirm('¿Marcar a todos los inscritos como PRESENTES?')">
                @csrf
                <button type="submit" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.85rem;">
                    ✓ Marcar todos Presente
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('asistencia.store', $sesion->id) }}">
            @csrf
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Miembro</th>
                            <th style="text-align: center;">Presente</th>
                            <th style="text-align: center;">Ausente</th>
                            <th style="text-align: center;">Tardanza</th>
                            <th style="text-align: center;">Justificado</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inscritos as $inscripcion)
                            @php
                                $guardada = $asistencias->get($inscripcion->miembro_id);
                                // Si la sesión ya pasó y no tiene registro, default = Ausente
                                $defaultEstado = $guardada?->estado ?? ($sesionPasada ? 'Ausente' : 'Presente');
                            @endphp
                            <tr>
                                <td style="font-weight: 600;">{{ $inscripcion->miembro->nombre }}</td>
                                @foreach(['Presente', 'Ausente', 'Tardanza', 'Justificado'] as $estado)
                                    <td style="text-align: center;">
                                        <input type="radio"
                                               name="asistencia[{{ $inscripcion->miembro_id }}][estado]"
                                               value="{{ $estado }}"
                                               {{ old('asistencia.' . $inscripcion->miembro_id . '.estado', $defaultEstado) === $estado ? 'checked' : '' }}
                                               required>
                                    </td>
                                @endforeach
                                <td>
                                    <input type="text"
                                           name="asistencia[{{ $inscripcion->miembro_id }}][observacion]"
                                           class="form-control"
                                           value="{{ old('asistencia.' . $inscripcion->miembro_id . '.observacion', $guardada?->observacion) }}"
                                           placeholder="Opcional"
                                           maxlength="255"
                                           style="min-width: 180px;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 25px;">
                <button type="submit" class="btn">Guardar Asistencia</button>
            </div>
        </form>
    @endif
</div>
@endsection
