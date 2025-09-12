@extends('layouts.app')

@section('title', 'Dashboard - UHTA')

@section('content')
<div class="dashboard-container">
  <div class="center-home">
    <img src="{{ asset('images/uhta-logo.png') }}" alt="Logo UHTA" class="center-logo">
    <h1 class="welcome">¡Bienvenido(a) {{ Auth::user()->name }}!</h1>
    
    <div class="user-info-card">
      <h3>Información de tu cuenta</h3>
      <div class="user-details">
        <div class="detail-item">
          <span class="label">Email:</span>
          <span class="value">{{ Auth::user()->email }}</span>
        </div>
        <div class="detail-item">
          <span class="label">Miembro desde:</span>
          <span class="value">{{ Auth::user()->created_at->format('d/m/Y') }}</span>
        </div>
      </div>
      
      <div class="roles-section">
        <h4>Tus roles activos:</h4>
        <div class="roles-container">
          @foreach(Auth::user()->roles as $role)
            <span class="role-badge role-{{ $role->name }}">
              {{ $role->display_name }}
            </span>
          @endforeach
        </div>
      </div>
    </div>
    
    <div class="quick-actions">
      <h3>Accesos rápidos</h3>
      <div class="actions-grid">
        <a href="{{ route('profile.index') }}" class="action-card">
          <div class="action-icon">
            <img src="{{ asset('icons/user-solid-full.svg') }}" alt="" style="width:24px;height:24px">
          </div>
          <span>Mi Información</span>
        </a>
        
        @if(Auth::user()->hasAnyRole(['teacher', 'student', 'admin']))
        <a href="{{ route('courses.index') }}" class="action-card">
          <div class="action-icon">
            <img src="{{ asset('icons/desktop-solid-full.svg') }}" alt="" style="width:24px;height:24px">
          </div>
          <span>Cursos</span>
        </a>
        @endif
        
        <a href="{{ route('settings.index') }}" class="action-card">
          <div class="action-icon">
            <img src="{{ asset('icons/user-gear-solid-full.svg') }}" alt="" style="width:24px;height:24px">
          </div>
          <span>Ajustes</span>
        </a>
      </div>
    </div>
  </div>
</div>

<style>
.dashboard-container {
  max-width: 800px;
  margin: 0 auto;
  padding: var(--spacing-lg);
}

.user-info-card {
  background: white;
  padding: var(--spacing-xl);
  border-radius: var(--border-radius-lg);
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  margin: var(--spacing-xl) 0;
  border: 1px solid var(--border);
}

.user-info-card h3 {
  color: var(--text);
  margin-bottom: var(--spacing-lg);
  font-size: 20px;
  font-weight: 600;
}

.user-details {
  display: grid;
  gap: var(--spacing-md);
  margin-bottom: var(--spacing-lg);
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--spacing-md);
  background: #f8f9fa;
  border-radius: var(--border-radius-sm);
}

.detail-item .label {
  font-weight: 600;
  color: var(--text-muted);
}

.detail-item .value {
  color: var(--text);
  font-weight: 500;
}

.roles-section h4 {
  color: var(--text);
  margin-bottom: var(--spacing-md);
  font-size: 16px;
  font-weight: 600;
}

.roles-container {
  display: flex;
  flex-wrap: wrap;
  gap: var(--spacing-sm);
}

.role-badge {
  display: inline-block;
  padding: var(--spacing-sm) var(--spacing-md);
  border-radius: var(--border-radius-xl);
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.role-admin {
  background: linear-gradient(135deg, #dc3545, #c82333);
  color: white;
}

.role-teacher {
  background: linear-gradient(135deg, #28a745, #218838);
  color: white;
}

.role-student {
  background: linear-gradient(135deg, #17a2b8, #138496);
  color: white;
}

.role-billing {
  background: linear-gradient(135deg, #ffc107, #e0a800);
  color: #212529;
}

.quick-actions {
  margin-top: var(--spacing-xl);
}

.quick-actions h3 {
  color: var(--text);
  margin-bottom: var(--spacing-lg);
  font-size: 20px;
  font-weight: 600;
  text-align: center;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: var(--spacing-md);
}

.action-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--spacing-md);
  padding: var(--spacing-lg);
  background: white;
  border-radius: var(--border-radius-md);
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  text-decoration: none;
  color: var(--text);
  transition: all var(--transition-fast);
  border: 1px solid var(--border);
}

.action-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0,0,0,0.12);
  color: var(--accent);
}

.action-icon {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(230, 154, 55, 0.1);
  border-radius: var(--border-radius-md);
  transition: all var(--transition-fast);
}

.action-card:hover .action-icon {
  background: var(--accent);
  transform: scale(1.1);
}

.action-card:hover .action-icon img {
  filter: brightness(0) invert(1);
}

.action-card span {
  font-weight: 500;
  font-size: 14px;
  text-align: center;
}

@media (max-width: 600px) {
  .dashboard-container {
    padding: var(--spacing-md);
  }
  
  .user-info-card {
    padding: var(--spacing-lg);
  }
  
  .detail-item {
    flex-direction: column;
    align-items: flex-start;
    gap: var(--spacing-xs);
  }
  
  .actions-grid {
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  }
  
  .welcome {
    font-size: 24px;
  }
}
</style>
@endsection