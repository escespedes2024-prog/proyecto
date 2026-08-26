@extends('layouts.app')

@section('title', 'Nuevo Curso')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Crear Nuevo Curso</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('cursos.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Nombre del Curso</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Fecha de Inicio</label>
                <input type="date" name="f_inicio" class="form-control" value="{{ old('f_inicio', date('Y-m-d')) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Fecha de Fin (Opcional)</label>
                <input type="date" name="f_fin" class="form-control" value="{{ old('f_fin') }}">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Días de la Semana (opcional)</label>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                @foreach([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'] as $num => $nombre)
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--text-secondary); font-size: 0.9rem; cursor: pointer;">
                        <input type="checkbox" name="dias_semana[]" value="{{ $num }}" {{ in_array($num, old('dias_semana', [])) ? 'checked' : '' }}> {{ $nombre }}
                    </label>
                @endforeach
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Hora de Inicio</label>
                <input type="time" name="hora_inicio" class="form-control" value="{{ old('hora_inicio') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Hora de Fin</label>
                <input type="time" name="hora_fin" class="form-control" value="{{ old('hora_fin') }}">
            </div>
        </div>

        <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: -10px;">
            Si defines días y hora, las sesiones solo podrán crearse en ese horario.
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Cupo Máximo</label>
                <input type="number" name="cupo_max" class="form-control" value="{{ old('cupo_max', 30) }}" min="1" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tiene Costo de Inscripción?</label>
                <select name="tiene_pago" class="form-control" onchange="toggleMonto(this.value)" required>
                    <option value="0" {{ old('tiene_pago', '0') == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('tiene_pago') == '1' ? 'selected' : '' }}>Sí</option>
                </select>
            </div>
        </div>

        <div class="form-group" id="monto_group" style="display: none;">
            <label class="form-label">Monto de Inscripción ($)</label>
            <input type="number" step="0.01" name="monto_inscripcion" id="monto_inscripcion" class="form-control" value="{{ old('monto_inscripcion', 0.00) }}">
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Guardar Curso</button>
            <a href="{{ route('cursos.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>

<script>
function toggleMonto(val) {
    const group = document.getElementById('monto_group');
    if (val === '1') {
        group.style.display = 'block';
    } else {
        group.style.display = 'none';
        document.getElementById('monto_inscripcion').value = '0.00';
    }
}

// Trigger onload
window.onload = function() {
    toggleMonto('{{ old("tiene_pago", "0") }}');
}
</script>
@endsection
