@extends('layouts.app')

@section('title', 'Administración - UHTA')

@section('content')
  <div style="max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px; color: #333;">Panel de Administración</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
      <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="color: #e69a37; margin-bottom: 15px;">Gestión de Usuarios</h3>
        <p style="color: #666; margin-bottom: 15px;">Administrar usuarios y sus roles</p>
        <button style="background: #e69a37; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer;">
          Ver Usuarios
        </button>
      </div>
      
      <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="color: #e69a37; margin-bottom: 15px;">Gestión de Roles</h3>
        <p style="color: #666; margin-bottom: 15px;">Configurar roles y permisos</p>
        <button style="background: #e69a37; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer;">
          Ver Roles
        </button>
      </div>
      
      <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="color: #e69a37; margin-bottom: 15px;">Configuración</h3>
        <p style="color: #666; margin-bottom: 15px;">Ajustes generales del sistema</p>
        <button style="background: #e69a37; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer;">
          Configurar
        </button>
      </div>
    </div>
    
    <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <h3 style="margin-bottom: 20px; color: #333;">Estadísticas del Sistema</h3>
      <p style="color: #666; text-align: center; padding: 20px;">
        Panel de administración en desarrollo...
        <br>
        Solo usuarios con rol de <strong>Administrador</strong> pueden acceder aquí.
      </p>
    </div>
  </div>
@endsection