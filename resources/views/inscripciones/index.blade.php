@extends('layouts.app')

@section('title', 'Administrar Inscripciones')

@section('content')
<div class="glass-panel" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-weight: 700; color: var(--text-primary);">Cursos e Inscripciones</h2>
        <a href="{{ route('cursos.create') }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem;">
            + Nuevo Curso
        </a>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Inscritos</th>
                    <th>Inscripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cursos as $curso)

                    <tr style="@if($curso->trashed()) opacity: 0.5; @endif">
                        <td>{{ $curso->nombre }}</td>
                        <td>{{ $curso->f_inicio ? \Carbon\Carbon::parse($curso->f_inicio)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $curso->f_fin ? \Carbon\Carbon::parse($curso->f_fin)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $curso->inscripciones_count }} / {{ $curso->cupo_max ?? '∞' }}</td>
                        <td>
                            @if($curso->tiene_pago)
                                <span style="font-weight: 600; color: var(--success);">Bs {{ number_format($curso->monto_inscripcion, 2) }}</span>
                            @else
                                <span class="badge badge-active">Gratuito</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('inscripciones.gestion', $curso->id) }}" class="btn-icon" title="Gestionar Inscripciones"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg></a>
                            </div>
                        </td>
                    </tr>
                
@empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 30px;">
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
