<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Beneficiario</title>
    <link rel="stylesheet" href="{{ asset('css/adminBeneficiarios.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <button class="back-btn" onclick="window.location.href='{{ route('adminBeneficiarios') }}'">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1 class="title">Eliminar Beneficiario</h1>
        </div>

        <!-- Barra de búsqueda -->
        <div class="search-section">
            <div class="search-container">
                <div class="search-wrapper">
                    <input type="text" name="buscar_usuario" placeholder="Buscar usuario" class="search-input"
                        id="searchInput">
                    <button type="button" class="search-btn" onclick="buscarUsuario()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="form-section">
            <form class="donante-form">
                <input type="hidden" id="usuario_id">

                <!-- Nombre y Sitio web -->
                <div class="form-row">
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <input type="text" name="nombre" placeholder="Nombre" class="form-input blocked-input"
                                readonly>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                            </svg>
                            <input type="url" name="sitio_web" placeholder="Sitio web opcional (https://...)"
                                class="form-input blocked-input" readonly>
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
                                class="form-input blocked-input" readonly>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <input type="email" name="email" placeholder="Correo electrónico"
                                class="form-input blocked-input" readonly>
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
                                class="form-input blocked-input" readonly>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <input type="password" name="password" placeholder="Contraseña"
                                class="form-input blocked-input" readonly>
                        </div>
                    </div>
                </div>

                <!-- Edad y Teléfono -->
                <div class="form-row">
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2z">
                                </path>
                            </svg>
                            <input type="number" name="edad" placeholder="Edad" class="form-input blocked-input"
                                min="18" max="100" readonly>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            <input type="tel" name="telefono" placeholder="Teléfono"
                                class="form-input blocked-input" readonly>
                        </div>
                    </div>
                </div>

                <!-- Tipo de Entidad y RFC -->
                <div class="form-row">
                    <div class="input-group">
                        <div class="select-wrapper">
                            <select name="tipo_entidad" class="form-select blocked-select" disabled>
                                <option value="">Selecciona tipo de entidad</option>
                                <option value="individual">Persona</option>
                            </select>
                            <svg class="select-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <input type="text" name="rfc" placeholder="RFC" class="form-input blocked-input"
                                maxlength="13" readonly>
                        </div>
                    </div>
                </div>

                <!-- Botón de eliminar -->
                <div class="form-actions" id="deleteButtonContainer" style="display: none;">
                    <button type="button" class="submit-btn delete-btn"
                        onclick="confirmarEliminacion()">Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirmar Eliminación</h3>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este beneficiario?</p>
                <p><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="button" class="btn btn-confirm delete"
                    onclick="eliminarBeneficiario()">Eliminar</button>
            </div>
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

        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 0;
            border: none;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            padding: 20px 20px 10px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.25rem;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-body p {
            margin: 10px 0;
            color: #666;
        }

        .modal-body strong {
            color: #d32f2f;
        }

        .modal-footer {
            padding: 15px 20px 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }

        .btn-confirm.delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-confirm.delete:hover {
            background-color: #c82333;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .delete-btn {
            background-color: #dc3545 !important;
        }

        .delete-btn:hover {
            background-color: #c82333 !important;
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
        // Configuración
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const buscarUrl = "{{ route('buscar.beneficiario.eliminar') }}";
        const eliminarUrl = "{{ route('eliminar.beneficiario') }}";

        // Funciones de notificación
        function mostrarNotificacion(tipo, titulo, mensaje, redirect = null) {
            const modal = document.getElementById('notificationModal');
            const icon = document.getElementById('notificationIcon');
            const title = document.getElementById('notificationTitle');
            const message = document.getElementById('notificationMessage');

            const iconClasses = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            icon.className = `fas ${iconClasses[tipo] || iconClasses.success}`;
            title.textContent = titulo;
            message.textContent = mensaje;

            if (redirect) {
                modal.setAttribute('data-redirect', redirect);
            } else {
                modal.removeAttribute('data-redirect');
            }

            modal.style.display = 'flex';
            setTimeout(() => modal.querySelector('.btn-primary')?.focus(), 100);
        }

        function cerrarNotificacion() {
            const modal = document.getElementById('notificationModal');
            const redirect = modal.getAttribute('data-redirect');

            modal.style.display = 'none';

            if (redirect) {
                setTimeout(() => window.location.href = redirect, 300);
            }
        }

        // Búsqueda de usuario
        function buscarUsuario() {
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput.value.trim();

            if (!searchTerm) {
                mostrarNotificacion('warning', 'Campo Requerido', 'Por favor ingresa un término de búsqueda');
                return;
            }

            const searchBtn = document.querySelector('.search-btn');
            const originalContent = searchBtn.innerHTML;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            searchBtn.disabled = true;

            fetch(buscarUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        buscar_usuario: searchTerm
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        mostrarNotificacion(data.type, data.title, data.message);
                    } else if (data.beneficiario) {
                        cargarDatosUsuario(data.beneficiario);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarNotificacion('error', 'Error de Conexión', 'No se pudo conectar con el servidor');
                })
                .finally(() => {
                    searchBtn.innerHTML = originalContent;
                    searchBtn.disabled = false;
                });
        }

        // Cargar datos del usuario en el formulario
        function cargarDatosUsuario(beneficiario) {
            const fields = {
                'usuario_id': beneficiario.id,
                'nombre': beneficiario.nombre,
                'apellido_paterno': beneficiario.aP,
                'apellido_materno': beneficiario.aM,
                'email': beneficiario.correo,
                'edad': beneficiario.edad,
                'telefono': beneficiario.telefono,
                'rfc': beneficiario.rfc,
                'sitio_web': beneficiario.paginaWeb
            };

            // Llenar campos
            Object.entries(fields).forEach(([name, value]) => {
                const element = document.getElementById(name) || document.querySelector(`[name="${name}"]`);
                if (element) element.value = value || '';
            });

            // Selects
            document.querySelector('[name="tipo_entidad"]').value = 'individual';

            // Mostrar contraseñas ocultas
            document.querySelector('[name="password"]').value = '••••••••••••';

            // Mostrar botón eliminar
            document.getElementById('deleteButtonContainer').style.display = 'block';
        }

        function confirmarEliminacion() {
            const nombre = document.querySelector('[name="nombre"]').value;
            if (!nombre) {
                mostrarNotificacion('warning', 'Usuario No Seleccionado',
                    'Primero busca y selecciona un usuario para eliminar');
                return;
            }

            document.getElementById('confirmModal').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        function eliminarBeneficiario() {
            const usuarioId = document.getElementById('usuario_id').value;

            if (!usuarioId) {
                mostrarNotificacion('error', 'Error de Validación', 'No se ha seleccionado un usuario válido');
                return;
            }

            // Mostrar indicador de carga en el botón
            const deleteBtn = document.querySelector('.btn-confirm.delete');
            const originalContent = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';
            deleteBtn.disabled = true;

            // Hacer petición DELETE al controlador
            fetch(eliminarUrl, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        usuario_id: usuarioId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Cerrar modal de confirmación primero
                    cerrarModal();

                    if (data.success) {
                        // Limpiar formulario
                        const inputs = document.querySelectorAll('.blocked-input, .blocked-select');
                        inputs.forEach(input => {
                            input.value = '';
                        });

                        document.getElementById('searchInput').value = '';
                        document.getElementById('usuario_id').value = '';
                        document.getElementById('deleteButtonContainer').style.display = 'none';

                        // Mostrar notificación de éxito
                        mostrarNotificacion(data.type, data.title, data.message, data.redirect);
                    } else {
                        mostrarNotificacion(data.type, data.title, data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    cerrarModal();
                    mostrarNotificacion('error', 'Error de Conexión', 'No se pudo conectar con el servidor');
                })
                .finally(() => {
                    // Restaurar botón
                    deleteBtn.innerHTML = originalContent;
                    deleteBtn.disabled = false;
                });
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Notificaciones del servidor
            @if (session('notification'))
                const notification = @json(session('notification'));
                mostrarNotificacion(notification.type, notification.title, notification.message, notification
                    .redirect || null);
            @endif

            // Búsqueda automática desde URL
            const urlParams = new URLSearchParams(window.location.search);
            const buscarParam = urlParams.get('buscar');
            if (buscarParam) {
                document.getElementById('searchInput').value = buscarParam;
                buscarUsuario();
            }

            // Búsqueda con Enter
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    buscarUsuario();
                }
            });

            // Cerrar modales con Escape
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const confirmModal = document.getElementById('confirmModal');
                    const notificationModal = document.getElementById('notificationModal');

                    if (confirmModal && confirmModal.style.display === 'flex') {
                        cerrarModal();
                    } else if (notificationModal && notificationModal.style.display === 'flex') {
                        cerrarNotificacion();
                    }
                }
            });

            // Cerrar modales al hacer clic fuera
            document.addEventListener('click', function(event) {
                const confirmModal = document.getElementById('confirmModal');
                const notificationModal = document.getElementById('notificationModal');

                if (event.target === confirmModal) {
                    cerrarModal();
                } else if (event.target === notificationModal) {
                    cerrarNotificacion();
                }
            });
        });
    </script>
</body>

</html>
