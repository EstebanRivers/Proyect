<!-- resources/views/partials/sidebar.blade.php -->
<aside class="sidebar" role="navigation" aria-label="Barra lateral">
  <div class="sidebar-top">
    <a href="{{ route('home') }}" class="brand">
      <!-- tu logo arriba -->
      <img src="{{ asset('images/LOGO2.png') }}" alt="UHTA logo" class="brand-img">
    </a>
  </div>

  <nav class="menu" aria-label="Menú principal">
    <ul>
      <li class="@if(request()->routeIs('profile.*')) active @endif">
        <a href="{{ route('profile.index') }}">
          <span class="icon" ><img src="{{ asset('icons/user-solid-full.svg') }}" alt="" style="width:18px;height:18px"></span>
          <span class="text">Mi Información</span>
        </a>
      </li>

      <li class="@if(request()->routeIs('courses.*')) active @endif">
        <a href="{{ route('courses.index') }}">
          <span class="icon" ><img src="{{ asset('icons/desktop-solid-full.svg') }}" alt="" style="width:18px;height:18px"></span>
          <span class="text">Cursos</span>
        </a>
      </li>

      <li class="@if(request()->routeIs('billing.*')) active @endif">
        <a href="{{ route('billing.index') }}">
          <span class="icon" ><img src="{{ asset('icons/money-bill-solid-full.svg') }}" alt="" style="width:18px;height:18px"></span>
          <span class="text">Facturación</span>
        </a>
      </li>

      <li class="@if(request()->routeIs('admin.*')) active @endif">
        <a href="{{ route('admin.index') }}">
          <span class="icon" ><img src="{{ asset('icons/clipboard-regular-full.svg') }}" alt="" style="width:18px;height:18px"></span>
          <span class="text">Control Administrativo</span>
        </a>
      </li>

      <li class="@if(request()->routeIs('settings.*')) active @endif">
        <a href="{{ route('settings.index') }}">
          <span class="icon" ><img src="{{ asset('icons/user-gear-solid-full.svg') }}" alt="" style="width:18px;height:18px"></span>
          <span class="text">Ajustes</span>
        </a>
      </li>
    </ul>
  </nav>

  <div class="sidebar-bottom">
    <a href="{{ route('logout') }}" class="btn-logout">
        <span class="icon" ><img src="{{ asset('icons/right-to-bracket-solid-full.svg') }}" alt="" style="width:18px;height:18px"></span>
      Cerrar sesión
    </a>

    <div class="brand-bottom">
      <img src="{{ asset('images/LOGO3.png') }}" alt="Mundo Imperial">
    </div>
  </div>
</aside>
