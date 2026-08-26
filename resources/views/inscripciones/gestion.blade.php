@extends('layouts.app')

@section('title', 'Gestión de Inscripciones')

@section('content')
<div class="glass-panel" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 25px;">
        <h2 style="font-weight: 700; color: var(--text-primary); margin: 0;">Inscripciones: {{ $curso->nombre }}</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('cursos.show', $curso->id) }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">
                Ver Curso
            </a>
            <a href="{{ route('inscripciones.index') }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">
                Volver
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Resumen -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 30px;">
        @foreach([
            'Cupo' => $inscripciones->whereNull('deleted_at')->count() . ' / ' . $curso->cupo_max,
            'Costo Inscripción' => $curso->tiene_pago ? '$' . number_format($curso->monto_inscripcion, 2) : 'Gratis',
            'Recaudado' => '$' . number_format($totalPagado, 2),
            $curso->tiene_pago ? 'Por Cobrar' : 'Esperado' => $curso->tiene_pago ? '$' . number_format(max(0, $esperado - $totalPagado), 2) : '$' . number_format($esperado, 2),
        ] as $label => $valor)
            <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 10px; padding: 15px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); font-weight: 700;">{{ $label }}</div>
                <div style="font-weight: 700; color: var(--text-primary); font-size: 1.05rem; margin-top: 5px;">{{ $valor }}</div>
            </div>
        @endforeach
    </div>

    <!-- Formulario de inscripción -->
    @if(!$curso->trashed())
    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 10px; padding: 20px; margin-bottom: 30px;">
        <h3 style="font-weight: 700; color: var(--text-primary); margin: 0 0 15px 0;">Inscribir Miembro</h3>
        <form method="POST" action="{{ route('inscripciones.store', $curso->id) }}" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            @csrf
            <div class="form-group" style="flex: 2; min-width: 220px; margin-bottom: 0;">
                <label class="form-label">Miembro</label>
                <select name="miembro_id" class="form-control" required>
                    <option value="">Seleccionar...</option>
                    @foreach($miembrosDisponibles as $miembro)
                        <option value="{{ $miembro->id }}" {{ old('miembro_id') == $miembro->id ? 'selected' : '' }}>{{ $miembro->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="flex: 1; min-width: 160px; margin-bottom: 0;">
                <label class="form-label">Fecha de Inscripción</label>
                <input type="date" name="fecha_inscripcion" class="form-control" value="{{ old('fecha_inscripcion', date('Y-m-d')) }}" required>
            </div>
            <button type="submit" class="btn" style="width: auto; padding: 10px 20px;" {{ $miembrosDisponibles->isEmpty() || $inscripciones->whereNull('deleted_at')->count() >= $curso->cupo_max ? 'disabled' : '' }}>
                Inscribir
            </button>
        </form>
        @if($miembrosDisponibles->isEmpty())
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 10px 0 0 0;">Todos los miembros activos ya están inscritos en este curso.</p>
        @endif
    </div>
    @endif

    <!-- Tabla de inscritos -->
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inscripciones as $i)
                    <tr style="@if($i->trashed()) opacity: 0.5; @endif">
                        <td>{{ $i->miembro->nombre }}</td>
                        <td>{{ \Carbon\Carbon::parse($i->fecha_inscripcion)->format('d/m/Y') }}</td>
                        <td>
                            @if($i->trashed())
                                <span class="badge badge-inactive">Eliminado</span>
                            @else
                                <span class="badge badge-active">{{ $i->estado }}</span>
                            @endif
                        </td>
                        @if($curso->tiene_pago)
                            <td>${{ number_format($i->monto_pagado, 2) }}</td>
                            <td>${{ number_format($i->saldo_pendiente, 2) }}</td>
                            <td>
                                <span class="badge {{ $i->estado_pago === 'Pagado' ? 'badge-active' : ($i->estado_pago === 'Pago Parcial' ? 'badge-warning' : 'badge-inactive') }}">
                                    {{ $i->estado_pago }}
                                </span>
                            </td>
                        @endif
                        <td>
                            <div class="action-buttons">
                                @if($i->trashed())
                                    <form method="POST" action="{{ route('inscripciones.restore', $i->id) }}">
                                        @csrf
                                        <button type="submit" class="btn-icon btn-restore" title="Restaurar"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg></button>
                                    </form>
                                @else
                                    <a href="{{ route('inscripciones.edit', $i->id) }}" class="btn-icon btn-edit" title="Editar Inscripción"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.83 20.082a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg></a>
                                    @if($curso->tiene_pago && $i->saldo_pendiente > 0)
                                        <a href="{{ route('pagos.create', $i->id) }}" class="btn-icon btn-edit" title="Registrar Pago"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg></a>
                                    @endif
                                    <form method="POST" action="{{ route('inscripciones.destroy', $i->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-delete" title="Eliminar Inscripción"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @if($curso->tiene_pago && $i->pagos->count())
                        <tr>
                            <td colspan="{{ $curso->tiene_pago ? 7 : 4 }}" style="background: rgba(255,255,255,0.02);">
                                <details>
                                    <summary style="cursor: pointer; font-size: 0.85rem; color: var(--text-secondary);">Historial de pagos de {{ $i->miembro->nombre }} ({{ $i->pagos->count() }})</summary>
                                    <table class="data-table" style="margin-top: 10px;">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                                <th>Método</th>
                                                <th>Comprobante</th>
                                                <th>Ingreso Vinculado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($i->pagos as $pago)
                                                <tr style="@if($pago->trashed()) opacity: 0.5; @endif">
                                                    <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                                                    <td>${{ number_format($pago->monto, 2) }} @if($pago->trashed())<span class="badge badge-inactive">Anulado</span>@endif</td>
                                                    <td>{{ $pago->metodo_pago }}</td>
                                                    <td>{{ $pago->comprobante ?? '—' }}</td>
                                                    <td>#{{ $pago->ingreso?->id ?? '—' }}</td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            @if($pago->trashed())
                                                                <form method="POST" action="{{ route('pagos.restore', $pago->id) }}">
                                                                    @csrf
                                                                    <button type="submit" class="btn-icon btn-restore" title="Restaurar Pago"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg></button>
                                                                </form>
                                                            @else
                                                                <form method="POST" action="{{ route('pagos.destroy', $pago->id) }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn-icon btn-delete" title="Anular Pago"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </details>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="{{ $curso->tiene_pago ? 7 : 4 }}" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                            No hay miembros inscritos en este curso.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
