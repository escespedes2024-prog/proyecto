@extends('layouts.app')

@section('title', 'Detalle del Contrato')

@section('content')
<div class="glass-panel" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 25px;">
        <h2 style="font-weight: 700; color: var(--text-primary); margin: 0;">Contrato de {{ $contrato->miembro->nombre ?? 'N/A' }}</h2>
        <div style="display: flex; gap: 10px;">
            @if(!$contrato->trashed())
                <a href="{{ route('contratos.renovar', $contrato->id) }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem;">↻ Renovar</a>
                <a href="{{ route('contratos.edit', $contrato->id) }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">Editar</a>
            @endif
            <a href="{{ route('contratos.index') }}" class="btn" style="width: auto; padding: 8px 16px; font-size: 0.9rem; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">Volver</a>
        </div>
    </div>

    <!-- Datos del contrato -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 15px; margin-bottom: 30px;">
        @php
            $badgeClase = match($contrato->vigencia) {
                'Vigente' => 'badge-active',
                'Por vencer', 'Futuro' => 'badge-warning',
                default => 'badge-inactive',
            };
            $tipoBadge = match($contrato->tipo_compensacion) {
                'Salario' => 'badge-active',
                'Bono' => 'badge-info',
                'Beneficios' => 'badge-warning',
                'Voluntario' => 'badge-inactive',
            };
        @endphp
        @foreach([
            'Miembro' => $contrato->miembro->nombre ?? 'N/A',
            'Líder Asignado' => $contrato->lider->nombre ?? 'N/A',
            'Cargo' => $contrato->lider?->cargo?->nombre ?? 'Sin cargo',
            'Tipo de Compensación' => '<span class="badge ' . $tipoBadge . '">' . $contrato->tipo_compensacion . '</span>',
            'Salario / Monto' => '$' . number_format($contrato->salario, 2),
            'Inicio' => \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y'),
            'Fin' => $contrato->fecha_fin ? \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') : 'Indefinido',
            'Vigencia' => $contrato->vigencia,
        ] as $label => $valor)
            <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 10px; padding: 15px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); font-weight: 700;">{{ $label }}</div>
                <div style="font-weight: 700; color: var(--text-primary); font-size: 1rem; margin-top: 5px;">
                    @if(in_array($label, ['Vigencia', 'Tipo de Compensación']))
                        {!! $valor !!}
                    @else
                        {{ $valor }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Resumen económico -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 15px; margin-bottom: 30px;">
        <div style="background: rgba(52, 211, 153, 0.08); border: 1px solid rgba(52, 211, 153, 0.3); border-radius: 10px; padding: 15px;">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #34d399; font-weight: 700;">Total Pagado en Salarios</div>
            <div style="font-weight: 700; color: #34d399; font-size: 1.2rem; margin-top: 5px;">${{ number_format($totalPagado, 2) }}</div>
        </div>
        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 10px; padding: 15px;">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); font-weight: 700;">Egresos Registrados</div>
            <div style="font-weight: 700; color: var(--text-primary); font-size: 1.2rem; margin-top: 5px;">{{ $contrato->egresos->whereNull('deleted_at')->count() }}</div>
        </div>
    </div>

    <!-- Egresos vinculados -->
    <h3 style="font-weight: 700; color: var(--text-primary); margin-bottom: 15px;">Pagos de Salario Registrados</h3>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                    <th>Responsable</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contrato->egresos as $egreso)
                    <tr style="@if($egreso->trashed()) opacity: 0.5; @endif">
                        <td>{{ \Carbon\Carbon::parse($egreso->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $egreso->tipo_egreso }}</td>
                        <td style="font-weight: 600;">${{ number_format($egreso->monto, 2) }}</td>
                        <td>{{ $egreso->descripcion ?? '—' }}</td>
                        <td>{{ $egreso->responsable }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                            No hay pagos registrados para este contrato.
                            <br><br>
                            <a href="{{ route('egresos.create') }}" style="color: #22d3ee;">Registrar egreso →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
