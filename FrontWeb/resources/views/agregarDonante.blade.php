<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Donante</title>
    <link rel="stylesheet" href="{{ asset('css/adminDonantes.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <button class="back-btn" onclick="window.location.href='{{ route('adminDonantes') }}'">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1 class="title">Agregar Donante</h1>
        </div>

        <!-- Formulario -->
        <div class="form-section">
            <form class="donante-form" action="{{ route('agregar.donante') }}" method="POST">
                @csrf

                <!-- Nombre y Sitio web -->
                <div class="form-row">
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <input type="text" name="nombre" placeholder="Nombre" class="form-input"
                                value="{{ old('nombre') }}" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                            </svg>
                            <input type="url" name="sitio_web" placeholder="Sitio web opcional (https://...)"
                                class="form-input" value="{{ old('sitio_web') }}">
                        </div>
                    </div>
                </div>

                <!-- Apellido Paterno y Correo -->
                <div class="form-row">
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <input type="text" name="apellido_paterno" placeholder="Apellido Paterno"
                                class="form-input" value="{{ old('apellido_paterno') }}" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <input type="email" name="email" placeholder="Correo electrónico" class="form-input"
                                value="{{ old('email') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Apellido Materno y Contraseña -->
                <div class="form-row">
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <input type="text" name="apellido_materno" placeholder="Apellido Materno"
                                class="form-input" value="{{ old('apellido_materno') }}" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <input type="password" name="password" placeholder="Contraseña" class="form-input"
                                required>
                        </div>
                    </div>
                </div>

                <!-- Edad y Confirmar Contraseña -->
                <div class="form-row">
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2z">
                                </path>
                            </svg>
                            <input type="number" name="edad" placeholder="Edad" class="form-input"
                                min="18" max="100" value="{{ old('edad') }}" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <input type="password" name="password_confirmation" placeholder="Confirmar contraseña"
                                class="form-input" required>
                        </div>
                    </div>
                </div>

                <!-- Teléfono y Tipo de Entidad -->
                <div class="form-row">
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            <input type="tel" name="telefono" placeholder="Teléfono" class="form-input"
                                value="{{ old('telefono') }}" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="select-wrapper">
                            <select name="tipo_entidad" class="form-select" required>
                                <option value="">Selecciona tipo de entidad</option>
                                <option value="persona" {{ old('tipo_entidad') == 'persona' ? 'selected' : '' }}>
                                    Persona</option>
                                <option value="institucion"
                                    {{ old('tipo_entidad') == 'institucion' ? 'selected' : '' }}>Institución</option>
                            </select>
                            <svg class="select-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- RFC y Tipo de Perfil -->
                <div class="form-row">
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <input type="text" name="rfc" placeholder="RFC" class="form-input"
                                maxlength="13" value="{{ old('rfc') }}">
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="select-wrapper">
                            <select name="tipo_perfil" class="form-select" required>
                                <option value="">Selecciona tipo de perfil</option>
                                <option value="donante_monetario"
                                    {{ old('tipo_perfil') == 'donante_monetario' ? 'selected' : '' }}>Donante</option>
                            </select>
                            <svg class="select-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Botón de agregar -->
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Agregar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de notificación -->
    <div id="notificationModal" class="modal notification-modal">
        <div class="modal-content notification-content">
            <div class="modal-body">
                <div class="notification-icon">
                    <i id="notificationIcon" class="fas fa-check-circle"></i>
                </div>
                <h4 id="notificationTitle">Proceso Completado</h4>
                <p id="notificationMessage">Proceso realizado exitosamente</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="cerrarNotificacion()">Aceptar</button>
            </div>
        </div>
    </div>

    <style>
        /* Estilos para el modal de notificaciones */
        .notification-modal {
            display: none;
            position: fixed;
            z-index: 1500;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .notification-content {
            background-color: #ffffff;
            border-radius: 12px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease-out;
            text-align: center;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .notification-modal .modal-body {
            padding: 30px 20px 20px;
        }

        .notification-icon {
            margin-bottom: 20px;
        }

        .notification-icon i {
            font-size: 4rem;
            color: #28a745;
        }

        .notification-icon i.fa-check-circle {
            color: #28a745;
        }

        .notification-icon i.fa-exclamation-circle {
            color: #dc3545;
        }

        .notification-icon i.fa-exclamation-triangle {
            color: #ffc107;
        }

        .notification-icon i.fa-info-circle {
            color: #17a2b8;
        }

        #notificationTitle {
            margin: 0 0 15px 0;
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
        }

        #notificationMessage {
            margin: 0;
            font-size: 1rem;
            color: #666;
            line-height: 1.5;
        }

        .notification-modal .modal-footer {
            padding: 15px 20px 25px;
            border-top: none;
            display: flex;
            justify-content: center;
        }

        .notification-modal .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 120px;
        }

        .notification-modal .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .notification-modal .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }

        .notification-modal .btn-primary:active {
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .notification-content {
                margin: 20px;
                width: calc(100% - 40px);
            }

            .notification-icon i {
                font-size: 3rem;
            }

            #notificationTitle {
                font-size: 1.2rem;
            }

            #notificationMessage {
                font-size: 0.9rem;
            }
        }
    </style>

    <script>
        // Funciones para manejar notificaciones
        function mostrarNotificacion(tipo, titulo, mensaje, redirect = null) {
            const modal = document.getElementById('notificationModal');
            const icon = document.getElementById('notificationIcon');
            const title = document.getElementById('notificationTitle');
            const message = document.getElementById('notificationMessage');

            // Configurar icono según el tipo
            icon.className = 'fas ';
            switch (tipo) {
                case 'success':
                    icon.className += 'fa-check-circle';
                    break;
                case 'error':
                    icon.className += 'fa-exclamation-circle';
                    break;
                case 'warning':
                    icon.className += 'fa-exclamation-triangle';
                    break;
                case 'info':
                    icon.className += 'fa-info-circle';
                    break;
                default:
                    icon.className += 'fa-check-circle';
            }

            // Establecer contenido
            title.textContent = titulo;
            message.textContent = mensaje;

            // Guardar redirección si existe
            if (redirect) {
                modal.setAttribute('data-redirect', redirect);
            } else {
                modal.removeAttribute('data-redirect');
            }

            // Mostrar modal
            modal.style.display = 'flex';

            // Enfocar el botón para accesibilidad
            setTimeout(() => {
                const acceptBtn = modal.querySelector('.btn-primary');
                if (acceptBtn) acceptBtn.focus();
            }, 100);
        }

        function cerrarNotificacion() {
            const modal = document.getElementById('notificationModal');
            const redirect = modal.getAttribute('data-redirect');

            modal.style.display = 'none';

            // Redirigir si se especificó una URL
            if (redirect) {
                setTimeout(() => {
                    window.location.href = redirect;
                }, 300);
            }
        }

        // Manejar notificaciones desde el servidor (Laravel session)
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('notification'))
                const notification = @json(session('notification'));
                mostrarNotificacion(
                    notification.type,
                    notification.title,
                    notification.message,
                    notification.redirect || null
                );
            @endif
        });

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('notificationModal');
                if (modal && modal.style.display === 'flex') {
                    cerrarNotificacion();
                }
            }
        });

        // Cerrar modal al hacer clic fuera
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('notificationModal');
            if (event.target === modal) {
                cerrarNotificacion();
            }
        });
    </script>
</body>

</html>
