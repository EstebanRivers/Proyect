<aside class="sidebar" role="navigation" aria-label="Barra lateral">
  <div class="sidebar-top">
    <button wire:click="navigateTo('dashboard')" class="brand" style="border: none; background: none; cursor: pointer;">
      <img src="{{ asset('images/LOGO2.png') }}" alt="UHTA logo" class="brand-img">
    </button>
  </div>

  <nav class="menu" aria-label="Menú principal">
    <ul>
      <li class="@if($currentView === 'profile') active @endif">
        <button wire:click="navigateTo('profile')" style="border: none; background: none; width: 100%; text-align: left;">
          <span class="icon"><img src="{{ asset('icons/user-solid-full.svg') }}" alt="" style="width:18px;height:18px"></span>
          <span class="text">Mi Información</span>
        </button>
      </li>

      @if($user->hasAnyRole(['teacher', 'student', 'admin']))
      <li class="@if($currentView === 'courses') active @endif">
        <button wire:click="navigateTo('courses')" style="border: none; background: none; width: 100%; text-align: left;">
          <span class="icon"><img src="{{ asset('icons/desktop-solid-full.svg') }}" alt="" style="width:18px;height:18px"></span>
          <span class="text">Cursos</span>
        </button>
      </li>
      @endif

      @if($user->hasAnyRole(['billing', 'admin']))
      <li class="@if($currentView === 'billing') active @endif">
        <button wire:click="navigateTo('billing')" style="border: none; background: none; width: 100%; text-align: left;">
          <span class="icon"><img src="{{ asset('icons/money-bill-solid-full.svg') }}" alt="" style="width:18px;height:18px"></span>
          <span class="text">Facturación</span>
        </button>
      </li>
      @endif

      @if($user->hasRole('admin'))
      <li class="@if($currentView === 'admin') active @endif">
        <button wire:click="navigateTo('admin')" style="border: none; background: none; width: 100%; text-align: left;">
          <span class="icon"><img src="{{ asset('icons/clipboard-regular-full.svg') }}" alt="" style="width:18px;height:18px"></span>
          <span class="text">Control Administrativo</span>
        </button>
      </li>
      @endif

      <li class="@if($currentView === 'settings') active @endif">
        <button wire:click="navigateTo('settings')" style="border: none; background: none; width: 100%; text-align: left;">
          <span class="icon"><img src="{{ asset('icons/user-gear-solid-full.svg') }}" alt="" style="width:18px;height:18px"></span>
          <span class="text">Ajustes</span>
        </button>
      </li>
    </ul>
  </nav>

  <div class="sidebar-bottom">
    <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
      @csrf
      <button type="submit" class="btn-logout" style="width: 100%; border: none; cursor: pointer; text-align: left;">
        <span class="icon"><img src="{{ asset('icons/right-to-bracket-solid-full.svg') }}" alt="" style="width:18px;height:18px"></span>
        <span class="text">Cerrar sesión</span>
      </button>
    </form>

    <div class="brand-bottom">
      <img src="{{ asset('images/LOGO3.png') }}" alt="Mundo Imperial">
    </div>
  </div>
</aside>