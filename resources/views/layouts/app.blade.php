<!-- resources/views/layouts/app.blade.php -->
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="turbo-cache-control" content="no-cache">
  <title>@yield('title','Dashboard')</title>

  {{-- Vite inyecta los enlaces a CSS/JS de resources --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-turbo-permanent>
  <div class="app-container">
    @include('components.sidebar')

    <main class="main-content" id="main-content">
      @yield('content')
    </main>
  </div>
</body>
</html>
