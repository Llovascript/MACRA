<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Perfiles</title>
    <link rel="stylesheet" href="{{ asset('css/solicitudes.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="container">
        <!-- Header con botón de regreso -->
        <div class="header">
            <a href="{{ route('adminSolicitudes') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="title">Solicitudes de Perfiles</h1>
        </div>

        <!-- Tabla de solicitudes -->
        <div class="table-container">
            <table class="solicitudes-table">
                <thead>
                    <tr>
                        <th>Nombre Completo</th>
                        <th>Correo electrónico</th>
                        <th>Rol</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($solicitudesPendientes) > 0)
                        @foreach ($solicitudesPendientes as $solicitud)
                            <tr class="pending" data-user-id="{{ $solicitud->id }}">
                                <td>{{ $solicitud->nombre }} {{ $solicitud->apellido_paterno }}
                                    {{ $solicitud->apellido_materno }}</td>
                                <td>{{ $solicitud->correo }}</td>
                                <td>{{ ucfirst($solicitud->rol_nombre) }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn accept-btn" title="Aceptar"
                                            onclick="confirmarAccion('aceptar', this, {{ $solicitud->id }})">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <button class="action-btn reject-btn" title="Rechazar"
                                            onclick="confirmarAccion('rechazar', this, {{ $solicitud->id }})">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <!-- Registros de ejemplo cuando no hay datos reales -->
                        <tr class="pending">
                            <td>No hay solicitudes pendientes</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Confirmar Acción</h3>
            </div>
            <div class="modal-body">
                <p id="modalMessage">¿Está seguro que desea realizar esta acción?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button class="btn btn-confirm" id="confirmBtn" onclick="procesarAccion()">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- Modal de notificación -->
    <div id="notificationModal" class="modal">
        <div class="modal-content notification-content">
            <div class="modal-body">
                <div class="notification-icon">
                    <i id="notificationIcon" class="fas fa-check-circle"></i>
                </div>
                <p id="notificationMessage">Proceso realizado exitosamente</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="cerrarNotificacion()">Aceptar</button>
            </div>
        </div>
    </div>

    <script>
        let accionActual = '';
        let filaActual = null;
        let userIdActual = null;

        // Configurar CSRF token para todas las peticiones AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function confirmarAccion(accion, boton, userId) {
            // Si no hay userId, usar la funcionalidad original (datos de ejemplo)
            if (!userId) {
                confirmarAccionOriginal(accion, boton);
                return;
            }

            accionActual = accion;
            filaActual = boton.closest('tr');
            userIdActual = userId;

            const modal = document.getElementById('confirmModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const confirmBtn = document.getElementById('confirmBtn');

            if (accion === 'aceptar') {
                modalTitle.textContent = 'Confirmar Aceptación';
                modalMessage.textContent = '¿Está seguro que desea aceptar esta solicitud de perfil?';
                confirmBtn.textContent = 'Aceptar Solicitud';
                confirmBtn.className = 'btn btn-confirm accept';
            } else {
                modalTitle.textContent = 'Confirmar Rechazo';
                modalMessage.textContent = '¿Está seguro que desea rechazar esta solicitud de perfil?';
                confirmBtn.textContent = 'Rechazar Solicitud';
                confirmBtn.className = 'btn btn-confirm reject';
            }

            modal.style.display = 'flex';
        }

        function procesarAccion() {
            cerrarModal();

            // Si es con userId real, procesar con backend
            if (userIdActual) {
                procesarAccionReal();
            } else {
                // Funcionalidad original para datos de ejemplo
                setTimeout(() => {
                    mostrarNotificacion();
                    actualizarFila();
                }, 500);
            }
        }

        function procesarAccionReal() {
            // Deshabilitar la fila mientras se procesa
            if (filaActual) {
                filaActual.style.opacity = '0.6';
                filaActual.style.pointerEvents = 'none';
            }

            const url = accionActual === 'aceptar' ?
                `/admin/solicitudes/${userIdActual}/aprobar` :
                `/admin/solicitudes/${userIdActual}/rechazar`;

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarNotificacion();
                        // Remover la fila después de mostrar notificación
                        setTimeout(() => {
                            if (filaActual) {
                                filaActual.style.transition = 'opacity 0.5s ease';
                                filaActual.style.opacity = '0';
                                setTimeout(() => {
                                    filaActual.remove();
                                    // Si no quedan filas, recargar la página
                                    const tbody = document.querySelector('.solicitudes-table tbody');
                                    if (tbody && tbody.children.length === 0) {
                                        location.reload();
                                    }
                                }, 500);
                            }
                        }, 1500);
                    } else {
                        mostrarNotificacionError(data.message || 'Error al procesar la solicitud');
                        // Rehabilitar la fila en caso de error
                        if (filaActual) {
                            filaActual.style.opacity = '1';
                            filaActual.style.pointerEvents = 'auto';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarNotificacionError('Error de conexión. Intente nuevamente.');

                    // Rehabilitar la fila en caso de error
                    if (filaActual) {
                        filaActual.style.opacity = '1';
                        filaActual.style.pointerEvents = 'auto';
                    }
                });
        }

        // Funciones originales para mantener compatibilidad
        function confirmarAccionOriginal(accion, boton) {
            accionActual = accion;
            filaActual = boton.closest('tr');
            userIdActual = null;

            const modal = document.getElementById('confirmModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const confirmBtn = document.getElementById('confirmBtn');

            if (accion === 'aceptar') {
                modalTitle.textContent = 'Confirmar Aceptación';
                modalMessage.textContent = '¿Está seguro que desea aceptar esta solicitud de perfil?';
                confirmBtn.textContent = 'Aceptar Solicitud';
                confirmBtn.className = 'btn btn-confirm accept';
            } else {
                modalTitle.textContent = 'Confirmar Rechazo';
                modalMessage.textContent = '¿Está seguro que desea rechazar esta solicitud de perfil?';
                confirmBtn.textContent = 'Rechazar Solicitud';
                confirmBtn.className = 'btn btn-confirm reject';
            }

            modal.style.display = 'flex';
        }

        function mostrarNotificacion() {
            const modal = document.getElementById('notificationModal');
            const icon = document.getElementById('notificationIcon');
            const message = document.getElementById('notificationMessage');

            if (accionActual === 'aceptar') {
                icon.className = 'fas fa-check-circle';
                icon.style.color = '#28a745';
                message.textContent = 'Solicitud aceptada exitosamente';
            } else {
                icon.className = 'fas fa-times-circle';
                icon.style.color = '#dc3545';
                message.textContent = 'Solicitud rechazada exitosamente';
            }

            modal.style.display = 'flex';
        }

        function mostrarNotificacionError(mensaje) {
            const modal = document.getElementById('notificationModal');
            const icon = document.getElementById('notificationIcon');
            const message = document.getElementById('notificationMessage');

            icon.className = 'fas fa-times-circle';
            icon.style.color = '#dc3545';
            message.textContent = mensaje;

            modal.style.display = 'flex';
        }

        function actualizarFila() {
            if (filaActual) {
                // Aplicar clase visual según la acción
                if (accionActual === 'aceptar') {
                    filaActual.classList.add('approved');
                    filaActual.classList.remove('rejected', 'pending');
                } else {
                    filaActual.classList.add('rejected');
                    filaActual.classList.remove('approved', 'pending');
                }

                // Cambiar botones por estado
                const actionButtons = filaActual.querySelector('.action-buttons');
                actionButtons.innerHTML = '<span class="status-text">' +
                    (accionActual === 'aceptar' ? 'Aceptada' : 'Rechazada') + '</span>';

                // Reordenar filas después de un pequeño delay para mostrar el cambio
                setTimeout(() => {
                    reordenarTabla();
                }, 1000);
            }
        }

        function reordenarTabla() {
            const tbody = document.querySelector('.solicitudes-table tbody');
            const filas = Array.from(tbody.querySelectorAll('tr'));

            // Separar filas por estado
            const pendientes = [];
            const procesadas = [];

            filas.forEach(fila => {
                if (fila.classList.contains('approved') || fila.classList.contains('rejected')) {
                    procesadas.push(fila);
                } else {
                    pendientes.push(fila);
                }
            });

            // Solo reordenar si hay necesidad (hay procesadas)
            if (procesadas.length === 0) return;

            // Agregar efecto visual de movimiento
            tbody.classList.add('reordering');

            // Marcar filas que se van a mover
            filas.forEach(fila => fila.classList.add('moving'));

            // Limpiar tbody
            tbody.innerHTML = '';

            // Reagregar filas: primero pendientes, luego procesadas
            pendientes.forEach((fila, index) => {
                setTimeout(() => {
                    fila.style.animation = 'slideUp 0.4s ease';
                    tbody.appendChild(fila);
                }, index * 50);
            });

            // Agregar procesadas después de un pequeño delay
            setTimeout(() => {
                procesadas.forEach((fila, index) => {
                    setTimeout(() => {
                        fila.style.animation = 'slideDown 0.4s ease';
                        tbody.appendChild(fila);
                    }, index * 50);
                });
            }, pendientes.length * 50 + 100);

            // Limpiar efectos después del reordenamiento
            setTimeout(() => {
                tbody.classList.remove('reordering');
                filas.forEach(fila => {
                    fila.classList.remove('moving');
                    fila.style.animation = '';
                });
            }, 800);
        }

        function cerrarModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        function cerrarNotificacion() {
            document.getElementById('notificationModal').style.display = 'none';
        }

        // Cerrar modales al hacer clic fuera de ellos
        window.onclick = function(event) {
            const confirmModal = document.getElementById('confirmModal');
            const notificationModal = document.getElementById('notificationModal');

            if (event.target === confirmModal) {
                cerrarModal();
            }
            if (event.target === notificationModal) {
                cerrarNotificacion();
            }
        }

        // Cerrar modales con la tecla Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                cerrarModal();
                cerrarNotificacion();
            }
        });
    </script>
</body>

</html>
