@extends('layouts.app')

@section('title', 'Nuevo Ministerio')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h2 style="font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Crear Nuevo Ministerio</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('ministerios.store') }}" onsubmit="prepareSubmit()">
        @csrf

        <div class="form-group">
            <label class="form-label">Nombre del Ministerio</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Líder del Ministerio (Opcional)</label>
            <select id="lider_selector" class="form-control" onchange="updateLiderableFields(this.value)">
                <option value="">Seleccionar líder...</option>
                <optgroup label="Miembros">
                    @foreach($miembros as $m)
                        <option value="App\Models\Miembro-{{ $m->id }}">{{ $m->nombre }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Líderes de Iglesia">
                    @foreach($lideres as $l)
                        <option value="App\Models\LiderIglesia-{{ $l->id }}">{{ $l->nombre }}</option>
                    @endforeach
                </optgroup>
            </select>
            <input type="hidden" name="liderable_type" id="liderable_type" value="{{ old('liderable_type') }}">
            <input type="hidden" name="liderable_id" id="liderable_id" value="{{ old('liderable_id') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion') }}</textarea>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn">Guardar Ministerio</button>
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
    const parts = value.split('-');
    document.getElementById('liderable_type').value = parts[0] + '\\' + parts[1] + '\\' + parts[2]; // handle multiple backslashes namespace
    // Wait, let's make it simpler.
}

// Better yet, let's write a simpler parser that works for App\Models\Miembro or App\Models\LiderIglesia:
function updateLiderableFields(value) {
    if (!value) {
        document.getElementById('liderable_type').value = '';
        document.getElementById('liderable_id').value = '';
        return;
    }
    if (value.includes('Miembro')) {
        document.getElementById('liderable_type').value = 'App\\Models\\Miembro';
        document.getElementById('liderable_id').value = value.replace('App\\Models\\Miembro-', '');
    } else if (value.includes('LiderIglesia')) {
        document.getElementById('liderable_type').value = 'App\\Models\\LiderIglesia';
        document.getElementById('liderable_id').value = value.replace('App\\Models\\LiderIglesia-', '');
    }
}
</script>
@endsection
