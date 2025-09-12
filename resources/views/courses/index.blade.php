@extends('layouts.app')

@section('title', 'Cursos - UHTA')

@section('content')
  <div style="max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px; color: #333;">Gestión de Cursos</h1>
    
    <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('teacher'))
        <div style="margin-bottom: 20px;">
          <button style="background: #e69a37; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer;">
            Crear Nuevo Curso
          </button>
        </div>
      @endif
      
      <p style="color: #666; text-align: center; padding: 40px;">
        Módulo de cursos en desarrollo...
        <br><br>
        <strong>Tu rol:</strong> 
        @foreach(Auth::user()->roles as $role)
          {{ $role->display_name }}{{ !$loop->last ? ', ' : '' }}
        @endforeach
      </p>
    </div>
  </div>
@endsection