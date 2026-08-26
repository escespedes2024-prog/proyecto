@extends('layouts.app')

@section('title', 'Editar Sesión')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Editar Sesión</h2>

    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <ul style="color: #ef4444; margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sesiones.update', $sesion->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Curso Relacionado</label>
            <select name="id_curso" id="id_curso" class="form-control" required onchange="aplicarHorario(); seleccionarDocenteCurso();">
                <option value="">Seleccione un curso...</option>
                @foreach($cursos as $c)
                    <option
                        value="{{ $c->id }}"
                        data-hora="{{ substr($c->hora_inicio, 0, 5) }}"
                        data-inicio="{{ $c->f_inicio }}"
                        data-fin="{{ $c->f_fin }}"
                        data-docentes="{{ json_encode($docentesPorCurso->get($c->id, [])) }}"
                        {{ old('id_curso', $sesion->id_curso) == $c->id ? 'selected' : '' }}
                    >
                        {{ $c->nombre }}{{ $c->tieneHorario() ? ' (' . $c->dias_nombres . ' ' . $c->rangoHorario() . ')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <p id="aviso-horario" style="color: #22d3ee; font-size: 0.85rem; margin-top: -10px; display: none;"></p>

        <div class="form-group">
            <label class="form-label">Docente que Imparte</label>
            <select name="id_docente" id="id_docente" class="form-control">
                <option value="">Sin asignar...</option>
                @foreach($docentes as $docente)
                    <option value="{{ $docente->id }}" {{ old('id_docente', $sesion->id_docente) == $docente->id ? 'selected' : '' }}>
                        {{ $docente->miembro?->nombre ?? 'Sin nombre' }}{{ $docente->especialidad ? ' — ' . $docente->especialidad : '' }}
                    </option>
                @endforeach
            </select>
            <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                <p id="docente-info" style="color: var(--text-secondary); font-size: 0.85rem; margin: 0;"></p>
                <button type="button" id="btn-restaurar-docente" onclick="restaurarDocenteCurso()" style="display: none; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); border-radius: 6px; padding: 2px 10px; font-size: 0.8rem; cursor: pointer;">
                    Usar docente del curso
                </button>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Tema / Contenido de la Sesión</label>
            <input type="text" name="tema" class="form-control" value="{{ old('tema', $sesion->tema) }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha', $sesion->fecha) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Hora</label>
                <input type="time" name="hora" id="hora" class="form-control" value="{{ old('hora', $sesion->hora) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Observación</label>
            <textarea name="observacion" class="form-control" rows="3">{{ old('observacion', $sesion->observacion) }}</textarea>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Actualizar Sesión</button>
            <a href="{{ route('sesiones.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>

<script>
function aplicarHorario() {
    var select = document.getElementById('id_curso');
    var opcion = select.options[select.selectedIndex];
    var aviso = document.getElementById('aviso-horario');
    var fecha = document.getElementById('fecha');
    var hora = document.getElementById('hora');

    if (!opcion.value) {
        aviso.style.display = 'none';
        fecha.removeAttribute('min');
        fecha.removeAttribute('max');
        return;
    }

    if (opcion.dataset.hora) {
        hora.value = opcion.dataset.hora;
        hora.readOnly = true;
        aviso.textContent = 'Este curso tiene horario fijo: las sesiones se registrarán a esa hora y solo en los días asignados.';
        aviso.style.display = 'block';
    } else {
        hora.readOnly = false;
        aviso.style.display = 'none';
    }

    if (opcion.dataset.inicio) {
        fecha.min = opcion.dataset.inicio;
    }
    if (opcion.dataset.fin) {
        fecha.max = opcion.dataset.fin;
    }
}

var docenteActualCurso = null;

function seleccionarDocenteCurso() {
    var selectCurso = document.getElementById('id_curso');
    var selectDocente = document.getElementById('id_docente');
    var info = document.getElementById('docente-info');
    var btnRestaurar = document.getElementById('btn-restaurar-docente');
    var opcion = selectCurso.options[selectCurso.selectedIndex];

    if (!opcion.value || !opcion.dataset.docentes) {
        info.textContent = '';
        btnRestaurar.style.display = 'none';
        docenteActualCurso = null;
        return;
    }

    var docentesIds = JSON.parse(opcion.dataset.docentes);

    if (docentesIds.length > 0) {
        var docenteId = docentesIds[0];
        docenteActualCurso = docenteId;

        var oldDocente = '{{ old("id_docente", $sesion->id_docente) }}';
        if (!oldDocente || oldDocente == docenteId) {
            selectDocente.value = docenteId;
        }

        var optSeleccionado = selectDocente.options[selectDocente.selectedIndex];
        info.textContent = 'Docente del curso: ' + (optSeleccionado.text || 'Sin nombre');
        btnRestaurar.style.display = 'inline-block';
    } else {
        info.textContent = 'Este curso no tiene docente asignado.';
        btnRestaurar.style.display = 'none';
        docenteActualCurso = null;
    }
}

function restaurarDocenteCurso() {
    if (docenteActualCurso) {
        document.getElementById('id_docente').value = docenteActualCurso;
        seleccionarDocenteCurso();
    }
}

window.addEventListener('load', function() {
    aplicarHorario();
    seleccionarDocenteCurso();
});
</script>
@endsection
