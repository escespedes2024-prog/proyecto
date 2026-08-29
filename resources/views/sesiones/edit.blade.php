@extends('layouts.app')

@section('title', 'Editar Sesión')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Editar Sesión</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>
                
@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sesiones.update', $sesion->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Curso</label>
            <select name="id_curso" id="id_curso" class="form-control" required>
                <option value="">Seleccione un curso...</option>
                @foreach($cursos as $curso)

                    <option value="{{ $curso->id }}" {{ old('id_curso', $sesion->id_curso) == $curso->id ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                
@endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Docente (Opcional)</label>
            <select name="id_docente" id="id_docente" class="form-control">
                <option value="">Sin docente...</option>
                @foreach($docentes as $docente)

                    <option value="{{ $docente->id }}" {{ old('id_docente', $sesion->id_docente) == $docente->id ? 'selected' : '' }}>{{ $docente->miembro->nombre ?? 'Docente #' . $docente->id }}</option>
                
@endforeach
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="{{ old('fecha', $sesion->fecha ? \Carbon\Carbon::parse($sesion->fecha)->format('Y-m-d') : '') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Hora</label>
                <input type="time" name="hora" class="form-control" value="{{ old('hora', $sesion->hora ? substr((string) $sesion->hora, 0, 5) : '') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Tema</label>
            <input type="text" name="tema" class="form-control" value="{{ old('tema', $sesion->tema) }}" required>
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
    const docentesPorCurso = @json($docentesPorCurso);
    const seleccionDocente = document.getElementById('id_docente');
    const cursoSelect = document.getElementById('id_curso');
    cursoSelect.addEventListener('change', filtrarDocentes);

    function filtrarDocentes() {
        const cursoId = cursoSelect.value;
        for (const opt of seleccionDocente.options) {
            if (opt.value === '') continue;
            const permitido = !cursoId || (docentesPorCurso[cursoId] || []).includes(Number(opt.value));
            opt.style.display = permitido ? '' : 'none';
        }
        if (cursoId && !(docentesPorCurso[cursoId] || []).includes(Number(seleccionDocente.value))) {
            seleccionDocente.value = '';
        }
    }

    filtrarDocentes();
</script>
@endsection
