@extends('layouts.app')

@section('title', 'Ajustes - UHTA')

@section('content')
  <div style="max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px; color: #333;">Configuración de Usuario</h1>
    
    <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <div style="margin-bottom: 30px;">
        <h3 style="margin-bottom: 15px; color: #333;">Preferencias de Cuenta</h3>
        <div style="display: grid; gap: 15px;">
          <label style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" style="width: 18px; height: 18px;">
            <span>Recibir notificaciones por email</span>
          </label>
          <label style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" style="width: 18px; height: 18px;">
            <span>Mostrar perfil público</span>
          </label>
          <label style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" checked style="width: 18px; height: 18px;">
            <span>Recordar sesión</span>
          </label>
        </div>
      </div>
      
      <div style="margin-bottom: 30px;">
        <h3 style="margin-bottom: 15px; color: #333;">Cambiar Contraseña</h3>
        <div style="display: grid; gap: 15px;">
          <input type="password" placeholder="Contraseña actual" style="padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
          <input type="password" placeholder="Nueva contraseña" style="padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
          <input type="password" placeholder="Confirmar nueva contraseña" style="padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
          <button style="background: #e69a37; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer;">
            Actualizar Contraseña
          </button>
        </div>
      </div>
      
      <div style="padding-top: 20px; border-top: 1px solid #eee;">
        <p style="color: #666; font-size: 14px; text-align: center;">
          Configuración de usuario disponible para todos los roles
        </p>
      </div>
    </div>
  </div>
@endsection