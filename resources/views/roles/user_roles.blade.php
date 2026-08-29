@extends('layouts.app')

@section('title', 'Roles de Usuarios')

@section('content')
<div class="glass-panel" style="padding: 30px;">
    <div style="margin-bottom: 20px;">
        <h2 style="font-weight: 700; color: var(--text-primary);">Asignación de Roles a Usuarios</h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 4px;">Marque los roles que desea asignar a cada usuario y pulse "Guardar".</p>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Correo Electrónico</th>
                    <th>Roles Actuales</th>
                    <th>Asignar Roles</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)

                    <tr>
                        <td style="font-weight: 600;">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->roles->isNotEmpty())
                                @foreach($user->roles as $role)

                                    <span class="badge badge-info">{{ $role->nombre }}</span>
                                
@endforeach
                            @else
                                <span style="color: var(--text-secondary);">Sin roles</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('roles.user.save') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">

                                @foreach($roles as $role)

                                    <label style="display: flex; align-items: center; gap: 5px; font-size: 0.85rem; color: var(--text-primary); white-space: nowrap;">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="form-check-input" style="width: auto;"
                                            @if($user->roles->contains('id', $role->id)) checked @endif>
                                        {{ $role->nombre }}
                                    </label>
                                
@endforeach

                                <button type="submit" class="btn" style="width: auto; padding: 6px 14px; font-size: 0.85rem;">Guardar</button>
                            </form>
                            <form method="POST" action="{{ route('roles.user.clear', $user->id) }}" style="display: inline; margin-left: 4px;">
                                @csrf
                                <button type="submit" class="btn" style="width: auto; padding: 6px 14px; font-size: 0.85rem; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary);">Limpiar Roles</button>
                            </form>
                        </td>
                    </tr>
                
@empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                
@endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $users->links() }}

    </div>
</div>
@endsection