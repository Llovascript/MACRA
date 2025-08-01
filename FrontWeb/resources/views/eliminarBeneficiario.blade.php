<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Beneficiario</title>
    <link rel="stylesheet" href="{{ asset('css/crudUsuarios.css') }}">
</head>
<body>
    <div class="crud-container">
        <!-- Header -->
        <div class="crud-header">
            <button class="back-btn" onclick="window.location.href='{{ route('adminBeneficiarios') }}'">
                <svg class="back-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <h1 class="page-title">Eliminar Beneficiario</h1>
        </div>

        <!-- Contenido principal -->
        <div class="crud-content">
            <!-- Sección de búsqueda -->
            <div class="search-container">
                <div class="search-section">
                    <h2>Buscar Beneficiario</h2>
                    <div class="search-row">
                        <div class="search-group">
                            <input type="text" id="buscar" name="buscar" class="search-input" 
                                   placeholder="Ingrese nombre, email o teléfono...">
                        </div>
                        <button type="button" class="btn-search" onclick="buscarBeneficiario()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                                <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Resultados de búsqueda -->
            <div class="results-section" id="resultados" style="display: none;">
                <h3 class="results-title">Resultados de búsqueda:</h3>
                <div class="results-list" id="listaResultados">
                    <!-- Los resultados se cargarán dinámicamente -->
                </div>
            </div>

            <!-- Confirmación de eliminación (oculto inicialmente) -->
            <div class="form-container delete-confirmation" id="confirmacionEliminacion" style="display: none;">
                <div class="delete-warning">
                    <div class="warning-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                            <path d="M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" 
                                  stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h2 class="warning-title">¿Está seguro de eliminar este beneficiario?</h2>
                    <p class="warning-text">Esta acción no se puede deshacer. Todos los datos del beneficiario serán eliminados permanentemente.</p>
                </div>

                <!-- Formulario de datos readonly -->
                <div class="form-grid">
                    <!-- Nombre -->
                    <div class="form-field">
                        <div class="field-icon icon-user"></div>
                        <input type="text" id="nombre" class="form-input" readonly>
                    </div>

                    <!-- Página web -->
                    <div class="form-field">
                        <div class="field-icon icon-globe"></div>
                        <input type="text" id="pagina_web" class="form-input" readonly>
                    </div>

                    <!-- Apellido Paterno -->
                    <div class="form-field">
                        <div class="field-icon icon-user"></div>
                        <input type="text" id="apellido_paterno" class="form-input" readonly>
                    </div>

                    <!-- Correo electrónico -->
                    <div class="form-field">
                        <div class="field-icon icon-mail"></div>
                        <input type="email" id="correo" class="form-input" readonly>
                    </div>

                    <!-- Apellido Materno -->
                    <div class="form-field">
                        <div class="field-icon icon-user"></div>
                        <input type="text" id="apellido_materno" class="form-input" readonly>
                    </div>

                    <!-- Contraseña (oculta) -->
                    <div class="form-field">
                        <div class="field-icon icon-lock"></div>
                        <input type="password" class="form-input" value="••••••••" readonly>
                    </div>

                    <!-- Edad -->
                    <div class="form-field">
                        <div class="field-icon icon-calendar"></div>
                        <input type="text" id="edad" class="form-input" readonly>
                    </div>

                    <!-- Campo vacío para simetría -->
                    <div class="form-field">
                        <div class="field-icon icon-lock"></div>
                        <input type="password" class="form-input" value="••••••••" readonly>
                    </div>

                    <!-- Teléfono -->
                    <div class="form-field">
                        <div class="field-icon icon-phone"></div>
                        <input type="tel" id="telefono" class="form-input" readonly>
                    </div>

                    <!-- Tipo de entidad -->
                    <div class="form-field">
                        <div class="field-icon icon-building"></div>
                        <input type="text" id="tipo" class="form-input" readonly>
                    </div>

                    <!-- RFC -->
                    <div class="form-field">
                        <div class="field-icon icon-card"></div>
                        <input type="text" id="rfc" class="form-input" readonly>
                    </div>

                    <!-- Tipo de perfil -->
                    <div class="form-field">
                        <div class="field-icon icon-profile"></div>
                        <input type="text" class="form-input" value="Beneficiario" readonly>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-actions">
                    <button type="button" class="btn-reset" onclick="cancelarEliminacion()">Cancelar</button>
                    <button type="button" class="btn-delete" onclick="confirmarEliminacion()" id="deleteBtn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let beneficiarioSeleccionado = null;

        async function buscarBeneficiario() {
            const query = document.getElementById('buscar').value.trim();
            if (query.length < 2) {
                alert('Por favor, ingrese al menos 2 caracteres para buscar.');
                return;
            }

            try {
                // Llamada real a la API de Laravel que se conecta con FastAPI
                const response = await fetch(`{{ route('beneficiarios.search') }}?q=${encodeURIComponent(query)}`);
                
                if (!response.ok) {
                    throw new Error('Error en la búsqueda');
                }
                
                const resultados = await response.json();
                mostrarResultados(resultados);
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error al realizar la búsqueda. Por favor, inténtelo nuevamente.');
            }
        }

        function mostrarResultados(resultados) {
            const resultadosDiv = document.getElementById('resultados');
            const listaDiv = document.getElementById('listaResultados');
            
            listaDiv.innerHTML = '';
            
            if (resultados.length === 0) {
                listaDiv.innerHTML = '<p class="no-results">No se encontraron beneficiarios.</p>';
            } else {
                resultados.forEach(beneficiario => {
                    const item = document.createElement('div');
                    item.className = 'result-item';
                    item.innerHTML = `
                        <div class="result-info">
                            <h4>${beneficiario.nombre} ${beneficiario.aP || ''} ${beneficiario.aM || ''}</h4>
                            <p>${beneficiario.correo} | ${beneficiario.telefono}</p>
                            <p><strong>Tipo:</strong> ${beneficiario.tipo}</p>
                        </div>
                        <button class="btn-delete" onclick="seleccionarParaEliminar(${beneficiario.id})" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">
                            Eliminar
                        </button>
                    `;
                    listaDiv.appendChild(item);
                });
            }
            
            resultadosDiv.style.display = 'block';
        }

        async function seleccionarParaEliminar(id) {
            try {
                // Obtener datos completos del beneficiario desde la API
                const response = await fetch(`{{ url('/beneficiarios/obtener') }}/${id}`);
                
                if (!response.ok) {
                    throw new Error('Error al obtener los datos del beneficiario');
                }
                
                const datos = await response.json();
                mostrarConfirmacion(datos);
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar los datos del beneficiario.');
            }
        }

        function mostrarConfirmacion(datos) {
            beneficiarioSeleccionado = datos;

            // Llenar los campos readonly con los datos
            document.getElementById('nombre').value = datos.nombre || '';
            document.getElementById('apellido_paterno').value = datos.aP || '';
            document.getElementById('apellido_materno').value = datos.aM || '';
            document.getElementById('correo').value = datos.correo || '';
            document.getElementById('telefono').value = datos.telefono || '';
            document.getElementById('edad').value = datos.edad ? `${datos.edad} años` : '';
            document.getElementById('rfc').value = datos.rfc || '';
            document.getElementById('pagina_web').value = datos.paginaWeb || 'Sin página web';
            document.getElementById('tipo').value = datos.tipo === 'persona' ? 'Persona' : 'Organización';

            // Mostrar la confirmación
            document.getElementById('confirmacionEliminacion').style.display = 'block';
            document.getElementById('confirmacionEliminacion').scrollIntoView({ behavior: 'smooth' });
        }

        async function confirmarEliminacion() {
            if (!beneficiarioSeleccionado) return;

            if (confirm(`¿Está completamente seguro de eliminar a ${beneficiarioSeleccionado.nombre}? Esta acción es irreversible.`)) {
                const deleteBtn = document.getElementById('deleteBtn');
                deleteBtn.disabled = true;
                deleteBtn.textContent = 'Eliminando...';

                try {
                    // Llamada real a la API para eliminar
                    const response = await fetch(`{{ url('/eliminarBeneficiario') }}/${beneficiarioSeleccionado.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        alert('Beneficiario eliminado exitosamente');
                        window.location.href = '{{ route("adminBeneficiarios") }}';
                    } else {
                        alert(result.message || 'Error al eliminar el beneficiario');
                        deleteBtn.disabled = false;
                        deleteBtn.textContent = 'Eliminar';
                    }

                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar el beneficiario. Por favor, inténtelo nuevamente.');
                    deleteBtn.disabled = false;
                    deleteBtn.textContent = 'Eliminar';
                }
            }
        }

        function cancelarEliminacion() {
            document.getElementById('confirmacionEliminacion').style.display = 'none';
            document.getElementById('resultados').style.display = 'none';
            document.getElementById('buscar').value = '';
            beneficiarioSeleccionado = null;
        }

        // Permitir búsqueda con Enter
        document.getElementById('buscar').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscarBeneficiario();
            }
        });
    </script>

    <!-- Meta tag para CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Estilos específicos para campos readonly */
        .form-input[readonly] {
            background-color: #f1f3f4;
            color: #5f6368;
            cursor: not-allowed;
        }

        .form-input[readonly]:focus {
            background-color: #f1f3f4;
            box-shadow: none;
        }

        .result-item {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .result-info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .result-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .no-results {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }

        .delete-warning {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #fff5f5;
            border-radius: 10px;
            border: 1px solid #fed7d7;
        }

        .warning-icon {
            margin-bottom: 15px;
        }

        .warning-title {
            color: #dc3545;
            margin-bottom: 10px;
        }

        .warning-text {
            color: #666;
        }
    </style>
</body>
</html>
