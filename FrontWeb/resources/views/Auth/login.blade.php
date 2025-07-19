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
            <div class="signin-signup">
                <!-- Formulario de Login -->
                <form id="loginForm" class="sign-in-form" autocomplete="off">
                    <h2 class="title">Sign in</h2>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input 
                            type="email" 
                            id="login-email" 
                            name="correo" 
                            placeholder="Email" 
                            autocomplete="off"
                            autocapitalize="off"
                            autocorrect="off"
                            spellcheck="false"
                            required 
                        />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input 
                            type="password" 
                            id="login-password" 
                            name="contraseña" 
                            placeholder="Password" 
                            autocomplete="new-password"
                            required 
                        />
                    </div>
                    <input type="submit" value="Login" class="btn solid" />
                    <div id="login-error" class="error-message" style="display: none;"></div>
                    <div id="login-success" class="success-message" style="display: none;"></div>
                    <p class="social-text">Or Sign in with social platforms</p>
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
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </form>

                <!-- Formulario de Registro -->
                <form id="registerForm" class="sign-up-form">
                    <h2 class="title">Sign up</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input 
                            type="text" 
                            id="register-name" 
                            name="nombre" 
                            placeholder="Full Name" 
                            autocomplete="name"
                            required 
                        />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input 
                            type="email" 
                            id="register-email" 
                            name="correo" 
                            placeholder="Email" 
                            autocomplete="email"
                            required 
                        />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input 
                            type="password" 
                            id="register-password" 
                            name="contraseña" 
                            placeholder="Password" 
                            autocomplete="new-password"
                            required 
                        />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input 
                            type="password" 
                            id="register-confirm" 
                            name="confirmar_contraseña" 
                            placeholder="Confirm Password" 
                            autocomplete="new-password"
                            required 
                        />
                    </div>
                    <input type="hidden" name="rol_id" value="1" />
                    <input type="submit" class="btn" value="Sign up" />
                    <div id="register-error" class="error-message" style="display: none;"></div>
                    <div id="register-success" class="success-message" style="display: none;"></div>
                    <p class="social-text">Or Sign up with social platforms</p>
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
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>New here ?</h3>
                    <p>
                        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Debitis,
                        ex ratione. Aliquid!
                    </p>
                    <button class="btn transparent" id="sign-up-btn">
                        Sign up
                    </button>
                </div>
                {{-- <div class="image-placeholder">📝</div> --}}
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <h3>One of us ?</h3>
                    <p>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Nostrum
                        laboriosam ad deleniti.
                    </p>
                    <button class="btn transparent" id="sign-in-btn">
                        Sign in
                    </button>
                </div>
                {{-- <div class="image-placeholder">👤</div> --}}
            </div>
        </div>
    </div>
</body>
</html>
