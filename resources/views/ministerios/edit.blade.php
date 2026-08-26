@extends('layouts.app')

@section('title', 'Editar Ministerio')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Editar Ministerio</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
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
            <label class="form-label">Líder del Ministerio (Opcional)</label>
            @php
                $currentLiderValue = $ministerio->liderable_type && $ministerio->liderable_id 
                    ? class_basename($ministerio->liderable_type) . '-' . $ministerio->liderable_id 
                    : '';
            @endphp
            <select id="lider_selector" class="form-control" onchange="updateLiderableFields(this.value)">
                <option value="">Seleccionar líder...</option>
                <optgroup label="Miembros">
                    @foreach($miembros as $m)
                        <option value="Miembro-{{ $m->id }}" @if($currentLiderValue == "Miembro-".$m->id) selected @endif>{{ $m->nombre }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Líderes de Iglesia">
                    @foreach($lideres as $l)
                        <option value="LiderIglesia-{{ $l->id }}" @if($currentLiderValue == "LiderIglesia-".$l->id) selected @endif>{{ $l->nombre }}</option>
                    @endforeach
                </optgroup>
            </select>
            <input type="hidden" name="liderable_type" id="liderable_type" value="{{ old('liderable_type', $ministerio->liderable_type) }}">
            <input type="hidden" name="liderable_id" id="liderable_id" value="{{ old('liderable_id', $ministerio->liderable_id) }}">
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion', $ministerio->descripcion) }}</textarea>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Actualizar Ministerio</button>
            <a href="{{ route('ministerios.index') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none; text-align: center;">Cancelar</a>
        </div>
    </form>
</div>

<script>
function updateLiderableFields(value) {
    if (!value) {
        document.getElementById('liderable_type').value = '';
        document.getElementById('liderable_id').value = '';
        return;
    }
    if (value.startsWith('Miembro-')) {
        document.getElementById('liderable_type').value = 'App\\Models\\Miembro';
        document.getElementById('liderable_id').value = value.replace('Miembro-', '');
    } else if (value.startsWith('LiderIglesia-')) {
        document.getElementById('liderable_type').value = 'App\\Models\\LiderIglesia';
        document.getElementById('liderable_id').value = value.replace('LiderIglesia-', '');
    }
}
</script>
@endsection
