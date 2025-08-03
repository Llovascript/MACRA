<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatus Solicitudes</title>
    <link rel="stylesheet" href="{{ asset('css/donaciones.css') }}">
</head>
<body>
    <div class="container">
        <!-- Header con botón de regreso -->
        <div class="header">
            <button class="back-btn" onclick="window.location.href='{{ route('donante.menu') }}'">
                <svg class="back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h1 class="page-title">Estatus Solicitudes</h1>
        </div>

        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error') || isset($error))
            <div class="alert alert-error">
                {{ session('error') ?? $error }}
            </div>
        @endif

        <!-- Tabla de donaciones -->
        <div class="table-container">
            <table class="donations-table">
                <thead>
                    <tr>
                        <th>Donación realizada</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($donaciones) && count($donaciones) > 0)
                        @foreach($donaciones as $donacion)
                            <tr class="table-row" onclick="verDetalles({{ $donacion['id'] }})">
                                <td class="donation-info">
                                    <div class="donation-details">
                                        <span class="donation-item">{{ $donacion['cantidad'] ?? 'N/A' }} 
                                            @if(isset($donacion['articuloP']))
                                                {{ $donacion['articuloP']['articulo']['nombre'] ?? 'Artículo' }}
                                            @endif
                                        </span>
                                        <span class="donation-date">{{ \Carbon\Carbon::parse($donacion['fecha'])->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td class="status-cell">
                                    @php
                                        $statusClass = 'status-pending';
                                        $statusText = 'En proceso';
                                        
                                        if (isset($donacion['estatus']['nombre'])) {
                                            switch($donacion['estatus']['nombre']) {
                                                case 'aprobado':
                                                    $statusClass = 'status-approved';
                                                    $statusText = 'Aprobado';
                                                    break;
                                                case 'rechazado':
                                                    $statusClass = 'status-rejected';
                                                    $statusText = 'Rechazado';
                                                    break;
                                                case 'pendiente':
                                                default:
                                                    $statusClass = 'status-pending';
                                                    $statusText = 'En proceso';
                                                    break;
                                            }
                                        }
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="2" class="no-data">
                                <div class="no-donations">
                                    <p>No tienes donaciones registradas</p>
                                    <small>Agrega tu primera donación usando el botón de abajo</small>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Botón de agregar -->
        <div class="add-button-container">
            <button class="add-btn" onclick="window.location.href='{{ route('donaciones.agregar') }}'">
                Agregar
            </button>
        </div>
    </div>

    <!-- Modal para detalles de donación -->
    <div id="detallesModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detalles de Donación</h3>
                <span class="close-modal" onclick="cerrarModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Contenido se carga dinámicamente -->
            </div>
        </div>
    </div>

    <script>
        function verDetalles(donacionId) {
            // Mostrar modal con detalles de la donación
            fetch(`/donaciones/obtener/${donacionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Error al cargar los detalles');
                        return;
                    }
                    
                    document.getElementById('modalBody').innerHTML = `
                        <div class="detail-row">
                            <strong>Artículo:</strong> ${data.articuloP?.articulo?.nombre || 'N/A'}
                        </div>
                        <div class="detail-row">
                            <strong>Cantidad:</strong> ${data.cantidad} ${data.articuloP?.unidad?.nombre || ''}
                        </div>
                        <div class="detail-row">
                            <strong>Fecha:</strong> ${new Date(data.fecha).toLocaleDateString('es-ES')}
                        </div>
                        <div class="detail-row">
                            <strong>Tipo de donante:</strong> ${data.tipo_donante}
                        </div>
                        <div class="detail-row">
                            <strong>Estado:</strong> ${data.estatus?.nombre || 'N/A'}
                        </div>
                        <div class="detail-row">
                            <strong>Aprobación:</strong> ${data.aprobacion ? 'Aprobada' : 'Pendiente'}
                        </div>
                    `;
                    
                    document.getElementById('detallesModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los detalles');
                });
        }

        function cerrarModal() {
            document.getElementById('detallesModal').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('detallesModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Auto-ocultar alertas después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>