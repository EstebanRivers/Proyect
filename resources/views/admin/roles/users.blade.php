@extends('layouts.app')

@section('title', 'Asignación de Roles - UHTA')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="color: #333; margin-bottom: 10px;">Asignación de Roles a Usuarios</h1>
        <button onclick="window.navigateTo('{{ route('admin.roles.index') }}')"
                style="background: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            ← Volver a Gestión de Roles
        </button>
    </div>

    <!-- Formulario de asignación rápida -->
    <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h3 style="margin: 0 0 20px 0; color: #333;">Asignación Rápida de Rol</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">Usuario</label>
                <select id="quickAssignUser" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    <option value="">Seleccionar usuario...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">Rol</label>
                <select id="quickAssignRole" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    <option value="">Seleccionar rol...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button onclick="quickAssignRole()" 
                        style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    Asignar Rol
                </button>
            </div>
        </div>
    </div>

    <!-- Lista de usuarios con sus roles -->
    <div style="background: white; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="padding: 20px; border-bottom: 1px solid #eee;">
            <h3 style="margin: 0; color: #333;">Usuarios y sus Roles</h3>
        </div>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #555;">Usuario</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #555;">Email</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #555;">Roles Asignados</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: #555;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr style="border-bottom: 1px solid #eee;" id="user-row-{{ $user->id }}">
                            <td style="padding: 15px; font-weight: 500;">{{ $user->name }}</td>
                            <td style="padding: 15px; color: #666;">{{ $user->email }}</td>
                            <td style="padding: 15px;">
                                <div style="display: flex; flex-wrap: wrap; gap: 6px;" id="user-roles-{{ $user->id }}">
                                    @forelse($user->roles as $role)
                                        <span style="background: #e69a37; color: white; padding: 4px 12px; border-radius: 15px; font-size: 12px; font-weight: 500; display: flex; align-items: center; gap: 6px;"
                                              id="role-badge-{{ $user->id }}-{{ $role->id }}">
                                            {{ $role->display_name }}
                                            <button onclick="removeUserRole({{ $user->id }}, {{ $role->id }}, '{{ $user->name }}', '{{ $role->display_name }}')"
                                                    style="background: rgba(255,255,255,0.3); border: none; border-radius: 50%; width: 16px; height: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 10px; color: white;"
                                                    title="Remover rol">
                                                ×
                                            </button>
                                        </span>
                                    @empty
                                        <span style="color: #666; font-style: italic; font-size: 14px;">Sin roles asignados</span>
                                    @endforelse
                                </div>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <select onchange="assignRoleToUser(this, {{ $user->id }}, '{{ $user->name }}')"
                                        style="padding: 6px 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                                    <option value="">+ Asignar rol</option>
                                    @foreach($roles as $role)
                                        @if(!$user->hasRole($role->name))
                                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 40px; text-align: center; color: #666;">
                                No hay usuarios registrados en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function quickAssignRole() {
    const userId = document.getElementById('quickAssignUser').value;
    const roleId = document.getElementById('quickAssignRole').value;
    
    if (!userId || !roleId) {
        alert('Por favor selecciona un usuario y un rol');
        return;
    }
    
    assignRole(userId, roleId);
}

function assignRoleToUser(select, userId, userName) {
    const roleId = select.value;
    if (!roleId) return;
    
    assignRole(userId, roleId);
    select.value = '';
}

function assignRole(userId, roleId) {
    fetch('{{ route("admin.roles.assign") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            user_id: userId,
            role_id: roleId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al asignar el rol');
    });
}

function removeUserRole(userId, roleId, userName, roleName) {
    if (confirm(`¿Estás seguro de que quieres remover el rol "${roleName}" de ${userName}?`)) {
        fetch('{{ route("admin.roles.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                user_id: userId,
                role_id: roleId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al remover el rol');
        });
    }
}
</script>
@endsection