<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard - UHTA')

@section('content')
  <div class="center-home">
    <img src="{{ asset('images/uhta-logo.png') }}" alt="UHTA" class="center-logo">
    <p class="welcome">¡Bienvenido(a) {{ Auth::user()->name }}!</p>
    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px; max-width: 400px; margin-left: auto; margin-right: auto;">
      <h3 style="margin-bottom: 10px; color: #333;">Tus roles:</h3>
      @foreach(Auth::user()->roles as $role)
        <span style="display: inline-block; background: #e69a37; color: white; padding: 4px 12px; border-radius: 15px; margin: 2px; font-size: 12px;">
          {{ $role->display_name }}
        </span>
      @endforeach
    </div>
  </div>
@endsection
