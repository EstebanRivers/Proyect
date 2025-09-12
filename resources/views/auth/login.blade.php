<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - UHTA</title>
    @vite(['resources/css/login.css', 'resources/js/app.js'])
</head>
<body>
    <div class="login-container">
    <!-- Imagen lado izquierdo -->
    <div class="login-left"></div>   

    <!-- Formulario lado derecho -->
    <div class="login-right">
        <div class="login-form">
            
            <!-- Logo -->
            <div class="logo">
                <img src="{{ asset('images/uhta-logo.png') }}" alt="Logo UHTA" loading="lazy">
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Campo usuario/correo -->
                <div class="form-group">
                    <label for="email">Usuario o correo electrónico</label>
                    <input type="text" id="email" name="email" 
                           placeholder="Ingrese su usuario"
                           value="{{ old('email') }}" required autofocus
                           autocomplete="username">
                    @error('email')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo contraseña -->
                <div class="form-group password-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" 
                               placeholder="Ingrese su contraseña"
                               required autocomplete="current-password">
                        <span class="toggle-password" onclick="togglePassword()">
                            <i class="icon-eye"></i>
                        </span>
                    </div>
                    @error('password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botón ingresar -->
                <button type="submit" class="login-btn">Ingresar</button>
            </form>
        </div>
    </div>
</div>

<!-- Script para mostrar/ocultar contraseña -->
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
                
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Recordarme</label>
                </div>
                
                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>
            
            <div style="text-align: center; margin-top: 1.5rem; color: #666; font-size: 14px;">
                <p>Usuarios de prueba:</p>
                <p><strong>Admin:</strong> test@example.com</p>
                <p><strong>Profesor:</strong> profesor@example.com</p>
                <p><strong>Estudiante:</strong> estudiante@example.com</p>
                <p><em>Contraseña para todos: password</em></p>
            </div>
        </div>
    </div>
</body>
</html>