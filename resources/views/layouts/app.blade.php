<!-- resources/views/layouts/app.blade.php -->
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="Sistema de gestión UHTA">
  <meta name="theme-color" content="#e69a37">
  <title>@yield('title','Dashboard')</title>

  {{-- Preload critical resources --}}
  <link rel="preload" href="{{ asset('images/LOGO2.png') }}" as="image">
  <link rel="preload" href="{{ asset('images/LOGO3.png') }}" as="image">
  
  {{-- Vite inyecta los enlaces a CSS/JS de resources --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
  {{-- Overlay para móvil --}}
  <div class="sidebar-overlay" id="sidebar-overlay"></div>
  
  <div class="app-container">
    @include('components.sidebar')

    <main class="main-content" id="main-content">
      @yield('content')
    </main>
  </div>
  
  {{-- Script para manejo móvil --}}
  <script>
    // Manejo básico del sidebar en móvil
    document.addEventListener('DOMContentLoaded', function() {
      const overlay = document.getElementById('sidebar-overlay');
      const sidebar = document.querySelector('.sidebar');
      
      // Cerrar sidebar al hacer clic en overlay
      if (overlay) {
        overlay.addEventListener('click', function() {
          sidebar.classList.remove('mobile-open');
          overlay.classList.remove('active');
        });
      }
      
      // Función global para abrir sidebar en móvil
      window.toggleSidebar = function() {
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
      };
    });
  </script>
</body>
</html>
