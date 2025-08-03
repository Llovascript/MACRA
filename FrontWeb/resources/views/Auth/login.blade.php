<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- FontAwesome desde CDN alternativo -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Vite Assets -->
    @vite(['resources/css/style.css', 'resources/js/auth.js'])

    <title>MACRA - Iniciar Sesión y Registro</title>
</head>

<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup form-title">
                <!-- Formulario de Login -->
                <form id="loginForm" class="sign-in-form" autocomplete="off">
                    <h3 class="form-title">¡Bienvenido de nuevo!</h3>
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
                    </div>
                </form>

                <!-- Formulario de Registro -->
                <form id="registerForm" class="sign-up-form">
                    <h3 class="form-title">Crear nueva cuenta</h3>
                    <h2 class="title"></h2>

                    <!-- Primera fila -->
                    <div class="form-row">
                        <div class="input-field half-width">
                            <i class="fas fa-user"></i>
                            <input type="text" id="register-name" name="nombre" placeholder="Nombre"
                                autocomplete="name" required />
                        </div>
                        <div class="input-field half-width">
                            <i class="fas fa-globe"></i>
                            <input type="text" id="register-website" name="pagina_web"
                                placeholder="Sitio web opcional (https://...)" autocomplete="url" />
                        </div>
                    </div>

                    <!-- Segunda fila -->
                    <div class="form-row">
                        <div class="input-field half-width">
                            <i class="fas fa-user"></i>
                            <input type="text" id="register-apellido-paterno" name="apellido_paterno"
                                placeholder="Apellido Paterno" autocomplete="family-name" required />
                        </div>
                        <div class="input-field half-width">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="register-email" name="correo" placeholder="Correo electrónico"
                                autocomplete="email" required />
                        </div>
                    </div>

                    <!-- Tercera fila -->
                    <div class="form-row">
                        <div class="input-field half-width">
                            <i class="fas fa-user"></i>
                            <input type="text" id="register-apellido-materno" name="apellido_materno"
                                placeholder="Apellido Materno" autocomplete="family-name" />
                        </div>
                        <div class="input-field half-width">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="register-password" name="contraseña" placeholder="Contraseña"
                                autocomplete="new-password" required />
                        </div>
                    </div>

                    <!-- Cuarta fila -->
                    <div class="form-row">
                        <div class="input-field half-width">
                            <i class="fas fa-calendar"></i>
                            <input type="number" id="register-edad" name="edad" placeholder="Edad"
                                min="18" max="120" />
                        </div>
                        <div class="input-field half-width">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="register-confirm" name="confirmar_contraseña"
                                placeholder="Confirmar contraseña" autocomplete="new-password" required />
                        </div>
                    </div>

                    <!-- Quinta fila -->
                    <div class="form-row">
                        <div class="input-field half-width">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="register-telefono" name="telefono" placeholder="Teléfono"
                                autocomplete="tel" />
                        </div>
                        <div class="input-field half-width">
                            <i class="fas fa-building"></i>
                            <select id="register-tipo-entidad" name="tipo_entidad" required>
                                <option value="" disabled selected>Seleccionar tipo de entidad</option>
                                <option value="persona">Persona</option>
                                {{-- <option value="organizacion">Institución</option> --}}
                            </select>
                        </div>
                    </div>

                    <!-- Sexta fila -->
                    <div class="form-row">
                        <div class="input-field half-width">
                            <i class="fas fa-id-card"></i>
                            <input type="text" id="register-rfc" name="rfc" placeholder="RFC"
                                maxlength="13" />
                        </div>
                        <div class="input-field half-width">
                            <i class="fas fa-user-tag"></i>
                            <select id="register-profile" name="perfil" required>
                                <option value="" disabled selected>Seleccionar tipo de perfil</option>
                                <option value="donante">Donante</option>
                                <option value="beneficiario">Beneficiario</option>
                            </select>
                        </div>
                    </div>

                    <!-- REMOVIDO el input hidden del rol_id para que sea mapeado automáticamente -->
                    <input type="submit" class="btn" value="REGISTRARSE" />
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
                            <img src="{{ asset('images/MACRA.png') }}" alt="MACRA" class="logo-icon">
                        </a>
                        <div class="logo-text">
                            <span class="logo-main">MACRA</span>
                            <span class="logo-sub">BANCO DE<br>ALIMENTOS</span>
                        </div>
                    </div>

                    <h3>¡Cada alimento cuenta, cada persona importa!</h3>
                    <p>
                        Bienvenido a MACRA, un espacio donde tu ayuda se transforma en esperanza.
                    </p>
                    <button class="btn transparent" id="sign-up-btn">
                        Regístrate
                    </button>
                </div>
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <div class="logo-container">
                        <a href="{{ url('/index') }}">
                            <img src="{{ asset('images/MACRA.png') }}" alt="MACRA" class="logo-icon">
                        </a>
                        <div class="logo-text">
                            <span class="logo-main">MACRA</span>
                            <span class="logo-sub">BANCO DE<br>ALIMENTOS</span>
                        </div>
                    </div>
                    <h3>¡Cada alimento cuenta, cada persona importa!</h3>
                    <p>
                        Bienvenido a MACRA, un espacio donde tu ayuda se transforma en esperanza.
                    </p>
                    <button class="btn transparent" id="sign-in-btn">
                        Iniciar Sesión
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
