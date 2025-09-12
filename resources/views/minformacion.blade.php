<!-- resources/views/profile/index.blade.php -->
@extends('layouts.app')

@section('title', 'Mi Información - UHTA')

@section('content')
  <div style="max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px; color: #333;">Mi Información</h1>
    
    <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div>
          <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #555;">Nombre:</label>
          <p style="padding: 10px; background: #f8f9fa; border-radius: 8px;">{{ Auth::user()->name }}</p>
        </div>
        <div>
          <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #555;">Email:</label>
          <p style="padding: 10px; background: #f8f9fa; border-radius: 8px;">{{ Auth::user()->email }}</p>
        </div>
      </div>
      
      <div>
        <label style="display: block; font-weight: 600; margin-bottom: 10px; color: #555;">Roles asignados:</label>
        <div>
          @foreach(Auth::user()->roles as $role)
            <span style="display: inline-block; background: #e69a37; color: white; padding: 8px 16px; border-radius: 20px; margin: 4px; font-size: 14px;">
              {{ $role->display_name }}
            </span>
          @endforeach
        </div>
      </div>
      
      <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
        <p style="color: #666; font-size: 14px;">
          <strong>Fecha de registro:</strong> {{ Auth::user()->created_at->format('d/m/Y H:i') }}
        </p>
      </div>
    </div>
  </div>
@endsection
