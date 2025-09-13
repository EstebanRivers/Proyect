@extends('layouts.app')

@section('title', 'Mi Información - UHTA')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="color: #333; margin: 0; font-size: 24px; font-weight: 600;">PERFIL</h1>
        <p style="color: #333; margin: 0; font-size: 16px;">¡Bienvenido(a) {{ Auth::user()->name }}!</p>
    </div>

    <!-- Contenedor principal del perfil -->
    <div style="background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden;">
        
        <!-- Sección superior con foto y nombre -->
        <div style="text-align: center; padding: 40px 30px 30px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
            <!-- Foto de perfil -->
            <div style="margin-bottom: 20px;">
                @if(Auth::user()->foto_perfil)
                    <img src="{{ asset('storage/' . Auth::user()->foto_perfil) }}" 
                         alt="Foto de perfil"
                         style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #2c5aa0; box-shadow: 0 4px 15px rgba(44, 90, 160, 0.3);">
                @else
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: #2c5aa0; margin: 0 auto; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(44, 90, 160, 0.3);">
                        <svg width="60" height="60" fill="white" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                @endif
            </div>
            
            <!-- Nombre completo -->
            <h2 style="color: #2c5aa0; margin: 0; font-size: 22px; font-weight: 600; border-bottom: 2px solid #2c5aa0; display: inline-block; padding-bottom: 5px;">
                {{ Auth::user()->name }}
            </h2>
        </div>

        <!-- Información detallada -->
        <div style="padding: 30px; background: #f8f9fa;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                
                <!-- Carrera -->
                <div style="grid-column: 1 / -1;">
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">Carrera:</span>
                        <span style="color: #333; font-size: 14px; margin-left: 10px;">
                            {{ Auth::user()->carrera ?: 'No especificada' }}
                        </span>
                    </div>
                </div>

                <!-- Matrícula -->
                <div>
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">Matrícula:</span>
                        <span style="color: #333; font-size: 14px; margin-left: 10px;">
                            {{ Auth::user()->matricula ?: 'No asignada' }}
                        </span>
                    </div>
                </div>

                <!-- Semestre -->
                <div>
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">Semestre:</span>
                        <span style="color: #333; font-size: 14px; margin-left: 10px;">
                            {{ Auth::user()->semestre ?: 'No especificado' }}
                        </span>
                    </div>
                </div>

                <!-- Correo -->
                <div>
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">Correo:</span>
                        <span style="color: #333; font-size: 14px; margin-left: 10px;">
                            {{ Auth::user()->email }}
                        </span>
                    </div>
                </div>

                <!-- Teléfono -->
                <div>
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">Teléfono:</span>
                        <span style="color: #333; font-size: 14px; margin-left: 10px;">
                            {{ Auth::user()->telefono ?: 'No especificado' }}
                        </span>
                    </div>
                </div>

                <!-- CURP -->
                <div>
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">CURP:</span>
                        <span style="color: #333; font-size: 14px; margin-left: 10px;">
                            {{ Auth::user()->curp ?: 'No especificado' }}
                        </span>
                    </div>
                </div>

                <!-- Edad -->
                <div>
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">Edad:</span>
                        <span style="color: #333; font-size: 14px; margin-left: 10px;">
                            @if(Auth::user()->edad)
                                {{ Auth::user()->edad }} años
                            @elseif(Auth::user()->fecha_nacimiento)
                                {{ \Carbon\Carbon::parse(Auth::user()->fecha_nacimiento)->age }} años
                            @else
                                No especificada
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Fecha de nacimiento -->
                <div>
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 120px;">Fecha de nacimiento:</span>
                        <span style="color: #333; font-size: 14px; margin-left: 10px;">
                            {{ Auth::user()->fecha_nacimiento ? \Carbon\Carbon::parse(Auth::user()->fecha_nacimiento)->format('d/m/Y') : 'No especificada' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sección de Dirección -->
            <div style="border-top: 2px solid #dee2e6; padding-top: 20px;">
                <h3 style="color: #2c5aa0; margin: 0 0 15px 0; font-size: 16px; font-weight: 600;">Dirección:</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Colonia -->
                    <div>
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">Colonia:</span>
                            <span style="color: #333; font-size: 14px; margin-left: 10px;">
                                {{ Auth::user()->colonia ?: 'No especificada' }}
                            </span>
                        </div>
                    </div>

                    <!-- Calle -->
                    <div>
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">Calle:</span>
                            <span style="color: #333; font-size: 14px; margin-left: 10px;">
                                {{ Auth::user()->calle ?: 'No especificada' }}
                            </span>
                        </div>
                    </div>

                    <!-- Ciudad -->
                    <div>
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">Ciudad:</span>
                            <span style="color: #333; font-size: 14px; margin-left: 10px;">
                                {{ Auth::user()->ciudad ?: 'No especificada' }}
                            </span>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div>
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">Estado:</span>
                            <span style="color: #333; font-size: 14px; margin-left: 10px;">
                                {{ Auth::user()->estado ?: 'No especificado' }}
                            </span>
                        </div>
                    </div>

                    <!-- Código Postal -->
                    <div>
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <span style="color: #2c5aa0; font-weight: 600; font-size: 14px; min-width: 80px;">C.P.:</span>
                            <span style="color: #333; font-size: 14px; margin-left: 10px;">
                                {{ Auth::user()->codigo_postal ?: 'No especificado' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información adicional del sistema -->
            <div style="border-top: 2px solid #dee2e6; padding-top: 20px; margin-top: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <span style="color: #666; font-size: 12px;">
                            <strong>Roles asignados:</strong>
                            @foreach(Auth::user()->roles as $role)
                                <span style="background: #e69a37; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px;">
                                    {{ $role->display_name }}
                                </span>
                            @endforeach
                        </span>
                    </div>
                    <div style="text-align: right;">
                        <span style="color: #666; font-size: 12px;">
                            <strong>Miembro desde:</strong> {{ Auth::user()->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .profile-container {
        margin: 10px;
        padding: 15px;
    }
    
    .profile-grid {
        grid-template-columns: 1fr !important;
        gap: 15px !important;
    }
    
    .profile-header {
        flex-direction: column !important;
        text-align: center !important;
        gap: 10px;
    }
}
</style>
@endsection