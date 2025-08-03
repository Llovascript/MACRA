<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Donante</title>
    <link rel="stylesheet" href="css/adminDonantes.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <button class="back-btn" onclick="window.location.href='{{ route('adminDonantes') }}'">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1 class="title">Eliminar Donante</h1>
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
            <form class="donante-form" action="#" method="POST">
                @csrf

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

                <!-- Edad y Confirmar Contraseña -->
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
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <input type="password" name="password_confirmation" placeholder="Confirmar contraseña"
                                class="form-input blocked-input" readonly>
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
                            <input type="tel" name="telefono" placeholder="Teléfono"
                                class="form-input blocked-input" readonly>
                        </div>
                    </div>

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
                            <input type="text" name="rfc" placeholder="RFC" class="form-input blocked-input"
                                maxlength="13" readonly>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="select-wrapper">
                            <select name="tipo_perfil" class="form-select blocked-select" disabled>
                                <option value="">Selecciona tipo de perfil</option>
                                <option value="donante_monetario">Donante</option>
                                <option value="donante_especie">Beneficiario</option>
                            </select>
                            <svg class="select-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
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

    <!-- Modal de confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirmar Eliminación</h3>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este donante?</p>
                <p><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="button" class="btn btn-confirm delete" onclick="eliminarDonante()">Eliminar</button>
            </div>
        </div>
    </div>

    <script>
        function buscarUsuario() {
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput.value.trim();

            if (searchTerm === '') {
                alert('Por favor ingresa un término de búsqueda');
                return;
            }

            // Aquí agregarías la lógica para buscar el usuario
            console.log('Buscando usuario:', searchTerm);

            // Simulación de cargar datos del usuario
            setTimeout(() => {
                cargarDatosUsuario();
            }, 500);
        }

        function cargarDatosUsuario() {
            // Simulación de datos del usuario encontrado
            const userData = {
                nombre: 'Juan Carlos',
                apellido_paterno: 'García',
                apellido_materno: 'López',
                email: 'juan.garcia@email.com',
                edad: '35',
                telefono: '+52 442 123 4567',
                rfc: 'GALJ850315ABC',
                sitio_web: 'https://juangarcia.com',
                tipo_entidad: 'individual',
                tipo_perfil: 'donante_monetario',
                password: '••••••••••••',
                password_confirmation: '••••••••••••'
            };

            // Llenar los campos del formulario
            Object.keys(userData).forEach(key => {
                const field = document.querySelector(`[name="${key}"]`);
                if (field) {
                    field.value = userData[key];
                }
            });

            // Mostrar el botón de eliminar
            document.getElementById('deleteButtonContainer').style.display = 'block';
        }

        function confirmarEliminacion() {
            const nombre = document.querySelector('[name="nombre"]').value;
            if (!nombre) {
                alert('Primero busca y selecciona un usuario para eliminar');
                return;
            }

            document.getElementById('confirmModal').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        function eliminarDonante() {
            // Aquí agregarías la lógica para eliminar el usuario
            console.log('Eliminando donante...');

            // Simulación de eliminación exitosa
            setTimeout(() => {
                alert('Donante eliminado exitosamente');
                cerrarModal();

                // Limpiar todos los campos del formulario
                const inputs = document.querySelectorAll('.blocked-input, .blocked-select');
                inputs.forEach(input => {
                    input.value = '';
                });

                // Limpiar campo de búsqueda
                document.getElementById('searchInput').value = '';

                // Ocultar botón de eliminar
                document.getElementById('deleteButtonContainer').style.display = 'none';

                // Redirigir a adminDonantes después de eliminar
                window.location.href = '{{ route('adminDonantes') }}';
            }, 1000);
        }

        // Event listener para buscar con Enter
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarUsuario();
            }
        });

        // Cerrar modal al hacer clic fuera de él
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
    </script>
</body>

</html>
