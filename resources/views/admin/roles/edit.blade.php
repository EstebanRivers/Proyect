@extends('layouts.app')

@section('title', 'Editar Rol - UHTA')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="color: #333; margin-bottom: 10px;">Editar Rol: {{ $role->display_name }}</h1>
        <button onclick="window.navigateTo('{{ route('admin.roles.index') }}')"
                style="background: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            ← Volver a Roles
        </button>
    </div>

    <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <form id="editRoleForm" method="POST" action="{{ route('admin.roles.update', $role) }}">
            @csrf
            @method('PUT')
            
            <!-- Nombre del rol -->
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                    Nombre del Rol *
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $role->name) }}"
                       placeholder="ej: coordinator, secretary"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                       required>
                <small style="color: #666; font-size: 12px;">
                    Solo letras minúsculas, números, guiones y guiones bajos. Sin espacios.
                </small>
                @error('name')
                    <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Nombre para mostrar -->
            <div style="margin-bottom: 20px;">
                <label for="display_name" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                    Nombre para Mostrar *
                </label>
                <input type="text" 
                       id="display_name" 
                       name="display_name" 
                       value="{{ old('display_name', $role->display_name) }}"
                       placeholder="ej: Coordinador, Secretaria"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                       required>
                <small style="color: #666; font-size: 12px;">
                    Este es el nombre que verán los usuarios en la interfaz.
                </small>
                @error('display_name')
                    <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Descripción -->
            <div style="margin-bottom: 20px;">
                <label for="description" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                    Descripción
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          placeholder="Descripción opcional del rol y sus funciones"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: vertical;">{{ old('description', $role->description) }}</textarea>
                @error('description')
                    <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Información adicional -->
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                <h4 style="margin: 0 0 10px 0; color: #333; font-size: 14px;">Información del Rol</h4>
                <p style="margin: 0; color: #666; font-size: 13px;">
                    <strong>Usuarios con este rol:</strong> {{ $role->users()->count() }}<br>
                    <strong>Creado:</strong> {{ $role->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Última actualización:</strong> {{ $role->updated_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <!-- Botones -->
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" 
                        onclick="window.navigateTo('{{ route('admin.roles.index') }}')"
                        style="background: #6c757d; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer;">
                    Cancelar
                </button>
                <button type="submit"
                        style="background: #e69a37; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                    Actualizar Rol
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('editRoleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.navigateTo(data.redirect);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el rol');
    });
});
</script>
@endsection