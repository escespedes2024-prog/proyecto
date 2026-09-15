@extends('layouts.app')

@section('title', 'Asignar Roles')

@section('content')
<div class="glass-panel" style="padding: 30px; max-width: 760px;">
    <div style="margin-bottom: 20px;">
        <h2 style="font-weight: 700; color: var(--text-primary);">Asignar Roles</h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 4px;">Seleccione los roles para <strong style="color: var(--text-primary);">{{ $user->name }}</strong> ({{ $user->email }}).</p>
    </div>

    @if($user->roles->isNotEmpty())
        <div style="margin-bottom: 20px;">
            <span style="color: var(--text-secondary); font-size: 0.85rem; margin-right: 8px;">Roles actuales:</span>
            @foreach($user->roles as $role)
                <span class="badge badge-info">{{ $role->nombre }}</span>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('roles.user.save') }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">
            @foreach($roles as $role)
                <label style="display: flex; align-items: center; gap: 10px; padding: 14px 16px; border: 1px solid var(--card-border); border-radius: 8px; background: var(--card-bg); cursor: pointer; transition: var(--transition-smooth);">
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="form-check-input" style="width: auto; margin: 0;"
                        @if($user->roles->contains('id', $role->id)) checked @endif>
                    <span>
                        <span style="display: block; font-weight: 600; color: var(--text-primary); font-size: 0.9rem;">{{ $role->nombre }}</span>
                        @if($role->descripcion)
                            <span style="display: block; color: var(--text-secondary); font-size: 0.8rem; margin-top: 2px;">{{ $role->descripcion }}</span>
                        @endif
                    </span>
                </label>
            @endforeach
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="submit" class="btn">Guardar Roles</button>
            <a href="{{ route('roles.user') }}" class="btn" style="background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); text-decoration: none;">Volver</a>
        </div>
    </form>
</div>
@endsection