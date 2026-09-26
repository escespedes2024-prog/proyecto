@extends('layouts.app')

@section('title', 'Administrar Asistencia')

@section('content')
<div class="glass-panel" style="padding: 24px; margin-bottom: 20px;">
    <h3 style="font-weight: 700; margin-bottom: 16px; color: var(--text-primary);">Filtros</h3>
    <form method="GET" action="{{ route('asistencia.index') }}" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
        <div>
            <label class="form-label">Miembro</label>
            <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Buscar por nombre...">
        </div>
        <div>
            <label class="form-label">Curso</label>
            <select name="curso_id" class="form-control" style="min-width: 180px;">
                <option value="">Todos los cursos</option>
                @foreach($cursos as $curso)

                    <option value="{{ $curso->id }}" {{ request('curso_id') == $curso->id ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                
@endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Estado</label>
            <select name="estado" class="form-control">
                <option value="">Todos</option>
                @foreach(['Presente', 'Ausente', 'Tardanza', 'Justificado'] as $estado)

                    <option value="{{ $estado }}" {{ request('estado') == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                
@endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Desde</label>
            <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
        </div>
        <div>
            <label class="form-label">Hasta</label>
            <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
        </div>
        <div>
            <button type="submit" class="btn" style="width: auto;">Filtrar</button>
        </div>
        @if(request()->has('q') || request()->has('curso_id') || request()->has('estado') || request()->has('desde') || request()->has('hasta'))
        <div>
            <a href="{{ route('asistencia.index') }}" class="btn" style="width: auto; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">Limpiar</a>
        </div>
        @endif
    </form>
</div>

<div class="glass-panel" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-weight: 700; color: var(--text-primary);">Lista de Asistencia</h2>
        <a href="{{ route('asistencia.registrar') }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem;">
            + Registrar Asistencia
        </a>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Miembro</th>
                    <th>Curso</th>
                    <th>Sesión / Tema</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Registrado Por</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asistencias as $asistencia)

                    <tr>
                        <td>{{ $asistencia->miembro->nombre ?? 'N/A' }}</td>
                        <td>{{ $asistencia->sesion?->curso?->nombre ?? 'N/A' }}</td>
                        <td>{{ $asistencia->sesion?->tema ?? 'N/A' }}</td>
                        <td>{{ $asistencia->sesion ? \Carbon\Carbon::parse($asistencia->sesion->fecha)->format('d/m/Y') : 'N/A' }}</td>
                        <td>
                            <span class="badge
                                @if($asistencia->estado == 'Presente') badge-active
                                @elseif($asistencia->estado == 'Ausente') badge-inactive
                                @elseif($asistencia->estado == 'Tardanza') badge-warning
                                @else badge-info
                                @endif">{{ $asistencia->estado }}</span>
                            @if($asistencia->observacion)
                                <div style="font-size: 12px; color: var(--text-secondary);">{{ $asistencia->observacion }}</div>
                            @endif
                        </td>
                        <td>{{ $asistencia->user->name ?? 'N/A' }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('asistencia.historial', $asistencia->miembro_id) }}" class="btn-icon" title="Ver historial">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Zm3.75 2.121a3.375 3.375 0 1 1 0 4.5 3.375 3.375 0 0 1 0-4.5Z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                
@empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                            No hay registros de asistencia.
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
