<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- FontAwesome desde CDN alternativo -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Vite Assets -->
    @vite(['resources/css/style.css', 'resources/js/auth.js'])

    <title>Sign in & Sign up Form</title>
</head>

<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup form-title ">
                <!-- Formulario de Login -->
                <form id="loginForm" class="sign-in-form" autocomplete="off">
                    <h3 class=".form-title">¡Bienvenido de nuevo!</h3><br>
                    <h2 class="title">Iniciar Sesión</h2>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="login-email" name="correo" placeholder="Correo electrónico"
                            autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="login-password" name="contraseña" placeholder="Contraseña"
                            autocomplete="new-password" required />
                    </div>
                    <input type="submit" value="Iniciar Sesión" class="btn solid" />
                    <div id="login-error" class="error-message" style="display: none;"></div>
                    <div id="login-success" class="success-message" style="display: none;"></div>
                    <p class="social-text">O iniciar sesión con plataformas sociales</p>
                    <div class="social-media">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-google"></i>
                        </a>
                        </a>
                    </div>
                </form>

                <!-- Formulario de Registro -->
                <form id="registerForm" class="sign-up-form">
                    <h3 class=".form-title">Crear nueva cuenta</h3><br>
                    <h2 class="title"></h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" id="register-name" name="nombre" placeholder="Nombre" autocomplete="name"
                            required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="register-email" name="correo" placeholder="Correo electrónico"
                            autocomplete="email" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="register-password" name="contraseña" placeholder="Contraseña"
                            autocomplete="new-password" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="register-confirm" name="confirmar_contraseña"
                            placeholder="Confirmar constraseña" autocomplete="new-password" required />
                    </div>

                    <div class="input-field">
                        <i class="fas fa-user-tag"></i>
                        <select id="register-profile" name="perfil" required>
                            <option value="" disabled selected>Selecciona tu perfil</option>
                            <option value="donante">Donante</option>
                            <option value="beneficiario">Beneficiario</option>
                        </select>
                    </div>

                    <input type="hidden" name="rol_id" value="1" />
                    <input type="submit" class="btn" value="Sign up" />
                    <div id="register-error" class="error-message" style="display: none;"></div>
                    <div id="register-success" class="success-message" style="display: none;"></div>
                    <p class="social-text">O regístrate con plataformas sociales</p>
                    <div class="social-media">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-google"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <div class="logo-container">
                        <a href="{{ url('/index') }}">
                            <img src="{{ asset('images/logo-MACRA.jpg') }}" alt="MACRA" class="logo-icon">
                        </a>
                        <div class="logo-text">
                            <span class="logo-main">MACRA</span>
                            <span class="logo-sub">BANCO DE<br>ALIMENTOS</span>
                        </div>
                    </div>

                    <h3>¡Cada aliemento cuenta, cada persona importa!</h3>
                    <p>
                        Bienvenido a MACRA, un espacio donde tu ayuda se transforma en esperanza.
                    </p>
                    <button class="btn transparent" id="sign-up-btn">
                        Regístrate
                    </button>
                </div>
                {{-- <div class="image-placeholder">📝</div> --}}
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <div class="logo-container">
                        <a href="{{ url('/index') }}">
                            <img src="{{ asset('images/logo-MACRA.jpg') }}" alt="MACRA" class="logo-icon">
                        </a>
                        <div class="logo-text">
                            <span class="logo-main">MACRA</span>
                            <span class="logo-sub">BANCO DE<br>ALIMENTOS</span>
                        </div>
                    </div>
                    <h3>¡Cada aliemento cuenta, cada persona importa!</h3><br>
                    <p>
                        Bienvenido a MACRA, un espacio donde tu ayuda se transforma en esperanza.
                    </p>
                    <button class="btn transparent" id="sign-in-btn">
                        Iniciar Sesión
                    </button>
                </div>
                {{-- <div class="image-placeholder">👤</div> --}}
            </div>
        </div>
    </div>
</body>

</html>
