<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Donaciones</title>
    <link rel="stylesheet" href="{{ asset('css/solicitudes.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="container">
        <!-- Header con botón de regreso -->
        <div class="header">
            <a href="{{ route('beneficiario.menu') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="title">Solicitudes de Donaciones</h1>
        </div>

        <!-- Tabla de solicitudes -->
        <div class="table-container">
            <table class="solicitudes-table">
                <thead>
                    <tr>
                        <th>Nombre Completo</th>
                        <th>Artículo</th>
                        <th>Cantidad</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($solicitudesPendientes) > 0)
                        @foreach ($solicitudesPendientes as $solicitud)
                            <tr class="pending" data-donacion-id="{{ $solicitud->id }}">
                                <td>{{ $solicitud->nombre }} {{ $solicitud->apellido_paterno }}
                                    {{ $solicitud->apellido_materno }}</td>
                                <td>{{ $solicitud->articulo_nombre ?? 'N/A' }}</td>
                                <td>{{ $solicitud->cantidad ?? 'N/A' }}</td>
                                <td>{{ $solicitud->fecha ? \Carbon\Carbon::parse($solicitud->fecha)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn accept-btn" title="Aceptar"
                                            onclick="confirmarAccion('aceptar', this, {{ $solicitud->id }})">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                        <button class="action-btn reject-btn" title="Rechazar"
                                            onclick="confirmarAccion('rechazar', this, {{ $solicitud->id }})">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <!-- Estado vacío cuando no hay solicitudes -->
                        <tr class="pending">
                            <td colspan="5" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                No hay solicitudes de donaciones pendientes
                            </td>
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
        let donacionIdActual = null;

        // Configurar CSRF token para todas las peticiones AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function confirmarAccion(accion, boton, donacionId) {
            accionActual = accion;
            filaActual = boton.closest('tr');
            donacionIdActual = donacionId;

            const modal = document.getElementById('confirmModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const confirmBtn = document.getElementById('confirmBtn');

            if (accion === 'aceptar') {
                modalTitle.textContent = 'Confirmar Aceptación';
                modalMessage.textContent = '¿Está seguro que desea aceptar esta solicitud de donación?';
                confirmBtn.textContent = 'Aceptar Donación';
                confirmBtn.className = 'btn btn-confirm accept';
            } else {
                modalTitle.textContent = 'Confirmar Rechazo';
                modalMessage.textContent = '¿Está seguro que desea rechazar esta solicitud de donación?';
                confirmBtn.textContent = 'Rechazar Donación';
                confirmBtn.className = 'btn btn-confirm reject';
            }

            modal.style.display = 'flex';
        }

        function procesarAccion() {
            cerrarModal();
            procesarAccionReal();
        }

        function procesarAccionReal() {
            // Deshabilitar la fila mientras se procesa
            if (filaActual) {
                filaActual.style.opacity = '0.6';
                filaActual.style.pointerEvents = 'none';
            }

            const url = accionActual === 'aceptar' ?
                `/admin/donaciones/${donacionIdActual}/aprobar` :
                `/admin/donaciones/${donacionIdActual}/rechazar`;

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
                                    // Si no quedan filas, mostrar estado vacío
                                    const tbody = document.querySelector('.solicitudes-table tbody');
                                    if (tbody && tbody.children.length === 0) {
                                        tbody.innerHTML = `
                                            <tr class="pending">
                                                <td colspan="5" class="empty-state">
                                                    <i class="fas fa-inbox"></i>
                                                    No hay solicitudes de donaciones pendientes
                                                </td>
                                            </tr>
                                        `;
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

        function mostrarNotificacion() {
            const modal = document.getElementById('notificationModal');
            const icon = document.getElementById('notificationIcon');
            const message = document.getElementById('notificationMessage');

            if (accionActual === 'aceptar') {
                icon.className = 'fas fa-check-circle';
                icon.style.color = '#28a745';
                message.textContent = 'Donación aceptada exitosamente';
            } else {
                icon.className = 'fas fa-times-circle';
                icon.style.color = '#dc3545';
                message.textContent = 'Donación rechazada exitosamente';
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
