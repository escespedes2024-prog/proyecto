@extends('layouts.app')

@section('title', 'Inscripciones a Cursos')

@section('content')
<div class="glass-panel" style="padding: 30px;">
    <div style="margin-bottom: 20px;">
        <h2 style="font-weight: 700; color: var(--text-primary);">Gestión de Inscripciones</h2>
        <p style="color: var(--text-secondary); margin: 5px 0 0 0;">Selecciona un curso para inscribir miembros y gestionar sus pagos.</p>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Inscritos / Cupo</th>
                    <th>Pago</th>
                    <th>Costo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cursos as $curso)
                    <tr style="@if($curso->trashed()) opacity: 0.5; @endif">
                        <td>{{ $curso->nombre }}</td>
                        <td>{{ \Carbon\Carbon::parse($curso->f_inicio)->format('d/m/Y') }}</td>
                        <td>{{ $curso->f_fin ? \Carbon\Carbon::parse($curso->f_fin)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $curso->inscripciones_count }} / {{ $curso->cupo_max }}
                            @if(!$curso->trashed() && $curso->inscripciones_count >= $curso->cupo_max)
                                <span class="badge badge-inactive">Cupo Lleno</span>
                            @endif
                        </td>
                        <td>
                            @if($curso->tiene_pago)
                                <span class="badge badge-active">${{ number_format($curso->monto_inscripcion, 2) }}</span>
                            @else
                                <span class="badge badge-warning">Gratis</span>
                            @endif
                        </td>
                        <td>${{ number_format($curso->monto_inscripcion, 2) }}</td>
                        <td>
                            <div class="action-buttons">
                                @if($curso->trashed())
                                    <span class="badge badge-inactive">Curso eliminado</span>
                                @else
                                    <a href="{{ route('inscripciones.gestion', $curso->id) }}" class="btn-icon btn-edit" title="Gestionar Inscripciones"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                            No hay cursos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $cursos->links() }}
    </div>
</div>
@endsection
