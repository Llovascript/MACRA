<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Beneficiario</title>
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Header con botón de regreso -->
        <div class="header">
            <a href="{{ route('beneficiario.menu') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="title">Datos del Perfil - Beneficiario</h1>
        </div>

        <!-- Mostrar mensajes de error si existen -->
        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <!-- Formulario -->
        <form class="profile-form">
            <div class="form-grid">
                <!-- Columna izquierda -->
                <div class="form-column">
                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" class="form-input" placeholder="Nombre"
                                value="{{ $userData['nombre'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" class="form-input" placeholder="Apellido Paterno"
                                value="{{ $userData['apellido_paterno'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" class="form-input" placeholder="Apellido Materno"
                                value="{{ $userData['apellido_materno'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-calendar input-icon"></i>
                            <input type="number" class="form-input" placeholder="Edad"
                                value="{{ $userData['edad'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="tel" class="form-input" placeholder="Teléfono"
                                value="{{ $userData['telefono'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-id-card input-icon"></i>
                            <input type="text" class="form-input" placeholder="RFC"
                                value="{{ $userData['rfc'] ?? '' }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha -->
                <div class="form-column">
                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-globe input-icon"></i>
                            <input type="url" class="form-input" placeholder="Página web (opcional)"
                                value="{{ $userData['pagina_web'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" class="form-input" placeholder="Correo electrónico"
                                value="{{ $userData['correo'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" class="form-input" placeholder="Contraseña" value="••••••••"
                                readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" class="form-input" placeholder="Confirmar contraseña"
                                value="••••••••" readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-building input-icon"></i>
                            <input type="text" class="form-input" placeholder="Tipo de entidad"
                                value="{{ $userData['tipo_entidad'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-user-tag input-icon"></i>
                            <input type="text" class="form-input" placeholder="Tipo de perfil"
                                value="{{ $userData['rol']['nombre'] ?? '' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
    </style>
</body>

</html>
