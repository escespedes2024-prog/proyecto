@extends('layouts.app')

@section('title', 'Asignar Roles a Usuarios')

@section('content')
<div class="glass-panel" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-weight: 700; color: var(--text-primary);">Administrar Roles de Usuario</h2>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Asignar Roles</th>
                    <th style="width: 120px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="@if($user->trashed()) opacity: 0.5; @endif">
                        <td>
                            <strong style="color: var(--text-primary);">{{ $user->name }}</strong>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <form id="form-role-{{ $user->id }}" method="POST" action="{{ route('roles.user.save') }}">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                
                                <div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center;">
                                    @foreach($roles as $role)
                                        <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; color: var(--text-primary); font-weight: 500; font-size: 0.9rem; user-select: none;">
                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                                   {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                                                   style="width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer;"
                                                   @if($user->trashed()) disabled @endif>
                                            {{ $role->nombre }}
                                        </label>
                                    @endforeach
                                    @if($roles->isEmpty())
                                        <span class="badge badge-inactive">No hay roles definidos</span>
                                    @endif
                                </div>
                            </form>
                        </td>
                        <td style="text-align: center;">
                            @if(!$user->trashed())
                                <div class="action-buttons" style="justify-content: center;">
                                    <button type="submit" form="form-role-{{ $user->id }}" class="btn-icon btn-restore" title="Guardar Cambios de Rol">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    </button>

                                    <form method="POST" action="{{ route('roles.user.clear', $user->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-icon btn-delete" title="Eliminar Relación de Roles" onclick="return confirm('¿Está seguro de que desea eliminar todos los roles asignados a este usuario?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="badge badge-inactive">Inactivo</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                            No hay usuarios disponibles.
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
