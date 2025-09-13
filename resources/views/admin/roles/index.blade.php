@extends('layouts.app')

@section('title', 'Gestión de Roles - UHTA')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="color: #333; margin: 0;">Gestión de Roles</h1>
        <button onclick="window.navigateTo('{{ route('admin.roles.create') }}')" 
                style="background: #e69a37; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
            + Crear Nuevo Rol
        </button>
    </div>

    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tabla de roles -->
    <div style="background: white; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="padding: 20px; border-bottom: 1px solid #eee;">
            <h3 style="margin: 0; color: #333;">Roles del Sistema</h3>
        </div>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #555;">Nombre</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #555;">Nombre para Mostrar</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #555;">Descripción</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: #555;">Usuarios</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: #555;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px;">
                                <code style="background: #f1f3f4; padding: 4px 8px; border-radius: 4px; font-size: 13px;">
                                    {{ $role->name }}
                                </code>
                            </td>
                            <td style="padding: 15px; font-weight: 500;">{{ $role->display_name }}</td>
                            <td style="padding: 15px; color: #666;">
                                {{ $role->description ?: 'Sin descripción' }}
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <span style="background: #e69a37; color: white; padding: 4px 12px; border-radius: 15px; font-size: 12px; font-weight: 500;">
                                    {{ $role->users_count }}
                                </span>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button onclick="window.navigateTo('{{ route('admin.roles.edit', $role) }}')"
                                            style="background: #17a2b8; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                        Editar
                                    </button>
                                    @if($role->users_count == 0)
                                        <button onclick="deleteRole({{ $role->id }}, '{{ $role->display_name }}')"
                                                style="background: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                            Eliminar
                                        </button>
                                    @else
                                        <button disabled
                                                style="background: #6c757d; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: not-allowed; font-size: 12px;"
                                                title="No se puede eliminar porque tiene usuarios asignados">
                                            Eliminar
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px; text-align: center; color: #666;">
                                No hay roles registrados en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Botón para gestionar usuarios -->
    <div style="margin-top: 20px; text-align: center;">
        <button onclick="window.navigateTo('{{ route('admin.roles.users') }}')"
                style="background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
            Gestionar Asignación de Roles a Usuarios
        </button>
    </div>
</div>

<script>
function deleteRole(roleId, roleName) {
    if (confirm(`¿Estás seguro de que quieres eliminar el rol "${roleName}"?`)) {
        fetch(`/admin/roles/${roleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
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
            alert('Error al eliminar el rol');
        });
    }
}
</script>
@endsection