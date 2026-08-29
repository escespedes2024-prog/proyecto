@extends('layouts.app')

@section('title', 'Editar Ministerio')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 700px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Editar Ministerio</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>
                
@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('ministerios.update', $ministerio->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Nombre del Ministerio</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $ministerio->nombre) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $ministerio->descripcion) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Líder Asignado</label>
            <select name="liderable_type" id="tipo-lider" class="form-control" onchange="toggleLiderSelect()">
                <option value="">Sin líder asignado</option>
                <option value="App\Models\Miembro" {{ old('liderable_type', $ministerio->liderable_type) == 'App\Models\Miembro' ? 'selected' : '' }}>Miembro</option>
                <option value="App\Models\LiderIglesia" {{ old('liderable_type', $ministerio->liderable_type) == 'App\Models\LiderIglesia' ? 'selected' : '' }}>Líder de Iglesia</option>
            </select>
        </div>

        <div class="form-group" id="group-miembros">
            <label class="form-label">Seleccionar Miembro</label>
            <select name="liderable_id" id="select-miembros" class="form-control">
                <option value="">Seleccione...</option>
                @foreach($miembros as $miembro)

                    <option value="{{ $miembro->id }}" {{ old('liderable_id', $ministerio->liderable_type == 'App\Models\Miembro' ? $ministerio->liderable_id : '') == $miembro->id ? 'selected' : '' }}>{{ $miembro->nombre }}</option>
                
@endforeach
            </select>
        </div>

        <div class="form-group" id="group-lideres" style="display: none;">
            <label class="form-label">Seleccionar Líder de Iglesia</label>
            <select name="liderable_id" id="select-lideres" class="form-control" disabled>
                <option value="">Seleccione...</option>
                @foreach($lideres as $lider)

                    <option value="{{ $lider->id }}" {{ old('liderable_id', $ministerio->liderable_type == 'App\Models\LiderIglesia' ? $ministerio->liderable_id : '') == $lider->id ? 'selected' : '' }}>{{ $lider->nombre }}</option>
                
@endforeach
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Actualizar Ministerio</button>
            <a href="{{ route('ministerios.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>

<script>
    function toggleLiderSelect() {
        const type = document.getElementById('tipo-lider').value;
        const miembrosGroup = document.getElementById('group-miembros');
        const lideresGroup = document.getElementById('group-lideres');
        const miembrosSelect = document.getElementById('select-miembros');
        const lideresSelect = document.getElementById('select-lideres');

        if (type === 'App\\Models\\Miembro') {
            miembrosGroup.style.display = 'block';
            lideresGroup.style.display = 'none';
            miembrosSelect.disabled = false;
            lideresSelect.disabled = true;
        } else if (type === 'App\\Models\\LiderIglesia') {
            miembrosGroup.style.display = 'none';
            lideresGroup.style.display = 'block';
            miembrosSelect.disabled = true;
            lideresSelect.disabled = false;
        } else {
            miembrosGroup.style.display = 'none';
            lideresGroup.style.display = 'none';
            miembrosSelect.disabled = true;
            lideresSelect.disabled = true;
        }
    }

    toggleLiderSelect();
</script>
@endsection