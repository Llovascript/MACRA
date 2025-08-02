<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Perfil</title>
    <link rel="stylesheet" href="{{ asset('css/crudUsuarios.css') }}">
</head>
<body>
    <div class="crud-container">
        <!-- Header -->
        <div class="crud-header">
            <button class="back-btn" onclick="window.location.href='{{ route('adminPerfiles') }}'">
                <svg class="back-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <h1 class="page-title">Eliminar Perfil</h1>
        </div>

        <!-- Contenido principal -->
        <div class="crud-content">
            <!-- Sección de búsqueda -->
            <div class="search-container">
                <div class="search-section">
                    <h2>Buscar Perfil para Eliminar</h2>
                    
                    <!-- Selector de tipo de perfil para búsqueda -->
                    <div class="search-tipo-selector">
                        <label class="tipo-option" data-tipo="beneficiario">
                            <input type="radio" name="tipo_busqueda" value="beneficiario" checked>
                            <span class="tipo-text">👥 Beneficiarios</span>
                        </label>
                        <label class="tipo-option" data-tipo="donante">
                            <input type="radio" name="tipo_busqueda" value="donante">
                            <span class="tipo-text">🤝 Donantes</span>
                        </label>
                    </div>
                    
                    <div class="search-row">
                        <div class="search-group">
                            <input type="text" id="buscar" name="buscar" class="search-input" 
                                   placeholder="Ingrese nombre, email o teléfono...">
                        </div>
                        <button type="button" class="btn-search" onclick="buscarPerfil()">
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
                    <h2 class="warning-title" id="warningTitle">¿Está seguro de eliminar este perfil?</h2>
                    <p class="warning-text">Esta acción no se puede deshacer. Todos los datos del perfil serán eliminados permanentemente.</p>
                </div>

                <!-- Información del perfil a eliminar -->
                <div class="perfil-info-delete">
                    <h3 id="perfilTipoEliminar">Eliminando: Beneficiario</h3>
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
                        <input type="text" id="perfil_readonly" class="form-input" readonly>
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
        let perfilSeleccionado = null;
        let tipoPerfilActual = 'beneficiario';

        // Manejar cambio de tipo de búsqueda
        document.querySelectorAll('input[name="tipo_busqueda"]').forEach(radio => {
            radio.addEventListener('change', function() {
                tipoPerfilActual = this.value;
                updateSearchInterface(this.value);
            });
        });

        function updateSearchInterface(tipoBusqueda) {
            // Actualizar selector visual
            document.querySelectorAll('.tipo-option').forEach(option => {
                option.classList.remove('selected');
            });
            document.querySelector(`[data-tipo="${tipoBusqueda}"]`).classList.add('selected');
            
            // Limpiar resultados anteriores
            document.getElementById('resultados').style.display = 'none';
            document.getElementById('confirmacionEliminacion').style.display = 'none';
            document.getElementById('buscar').value = '';
        }

        async function buscarPerfil() {
            const query = document.getElementById('buscar').value.trim();
            const tipoBusqueda = document.querySelector('input[name="tipo_busqueda"]:checked').value;
            
            if (query.length < 2) {
                alert('Por favor, ingrese al menos 2 caracteres para buscar.');
                return;
            }

            try {
                const response = await fetch(`{{ route('perfiles.search') }}?q=${encodeURIComponent(query)}&tipo_perfil=${tipoBusqueda}`);
                
                if (!response.ok) {
                    throw new Error('Error en la búsqueda');
                }
                
                const resultados = await response.json();
                mostrarResultados(resultados, tipoBusqueda);
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error al realizar la búsqueda. Por favor, inténtelo nuevamente.');
            }
        }

        function mostrarResultados(resultados, tipoBusqueda) {
            const resultadosDiv = document.getElementById('resultados');
            const listaDiv = document.getElementById('listaResultados');
            
            listaDiv.innerHTML = '';
            
            if (resultados.length === 0) {
                const tipoTexto = tipoBusqueda === 'donante' ? 'donantes' : 'beneficiarios';
                listaDiv.innerHTML = `<p class="no-results">No se encontraron ${tipoTexto}.</p>`;
            } else {
                resultados.forEach(perfil => {
                    const item = document.createElement('div');
                    item.className = 'result-item';
                    item.innerHTML = `
                        <div class="result-info">
                            <h4>${perfil.nombre} ${perfil.aP || ''} ${perfil.aM || ''}</h4>
                            <p>${perfil.correo} | ${perfil.telefono}</p>
                            <p><strong>Tipo:</strong> ${perfil.tipo}</p>
                        </div>
                        <button class="btn-delete" onclick="seleccionarParaEliminar(${perfil.id}, '${tipoBusqueda}')" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">
                            Eliminar
                        </button>
                    `;
                    listaDiv.appendChild(item);
                });
            }
            
            resultadosDiv.style.display = 'block';
        }

        async function seleccionarParaEliminar(id, tipoPerfil) {
            try {
                const response = await fetch(`{{ url('/perfiles/obtener') }}/${id}`);
                
                if (!response.ok) {
                    throw new Error('Error al obtener los datos del perfil');
                }
                
                const datos = await response.json();
                mostrarConfirmacion(datos, tipoPerfil);
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar los datos del perfil.');
            }
        }

        function mostrarConfirmacion(datos, tipoPerfil) {
            perfilSeleccionado = datos;
            const nombrePerfil = tipoPerfil === 'donante' ? 'Donante' : 'Beneficiario';

            // Actualizar títulos y mensajes
            document.getElementById('warningTitle').textContent = `¿Está seguro de eliminar este ${nombrePerfil.toLowerCase()}?`;
            document.getElementById('perfilTipoEliminar').textContent = `Eliminando: ${nombrePerfil}`;
            document.getElementById('perfil_readonly').value = nombrePerfil;

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
            if (!perfilSeleccionado) return;

            const nombrePerfil = tipoPerfilActual === 'donante' ? 'donante' : 'beneficiario';
            
            if (confirm(`¿Está completamente seguro de eliminar a ${perfilSeleccionado.nombre}? Esta acción es irreversible.`)) {
                const deleteBtn = document.getElementById('deleteBtn');
                deleteBtn.disabled = true;
                deleteBtn.textContent = 'Eliminando...';

                try {
                    const response = await fetch(`{{ url('/perfiles/eliminar') }}/${perfilSeleccionado.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        alert(`${nombrePerfil.charAt(0).toUpperCase() + nombrePerfil.slice(1)} eliminado exitosamente`);
                        window.location.href = '{{ route("adminPerfiles") }}';
                    } else {
                        alert(result.message || `Error al eliminar el ${nombrePerfil}`);
                        deleteBtn.disabled = false;
                        deleteBtn.textContent = 'Eliminar';
                    }

                } catch (error) {
                    console.error('Error:', error);
                    alert(`Error al eliminar el ${nombrePerfil}. Por favor, inténtelo nuevamente.`);
                    deleteBtn.disabled = false;
                    deleteBtn.textContent = 'Eliminar';
                }
            }
        }

        function cancelarEliminacion() {
            document.getElementById('confirmacionEliminacion').style.display = 'none';
            document.getElementById('resultados').style.display = 'none';
            document.getElementById('buscar').value = '';
            perfilSeleccionado = null;
        }

        // Permitir búsqueda con Enter
        document.getElementById('buscar').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscarPerfil();
            }
        });

        // Inicializar interfaz
        document.addEventListener('DOMContentLoaded', function() {
            updateSearchInterface('beneficiario');
        });
    </script>

    <!-- Meta tag para CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Selector de tipo para búsqueda */
        .search-tipo-selector {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .tipo-option {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tipo-option input[type="radio"] {
            display: none;
        }

        .tipo-option:hover {
            border-color: #d4a574;
            transform: translateY(-2px);
        }

        .tipo-option.selected {
            border-color: #007bff;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            transform: translateY(-2px);
        }

        .tipo-text {
            font-weight: 500;
            color: #333;
        }

        /* Información del perfil a eliminar */
        .perfil-info-delete {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: linear-gradient(135deg, #fff5f5, #ffebee);
            border: 2px solid #fed7d7;
            border-radius: 10px;
        }

        .perfil-info-delete h3 {
            color: #dc3545;
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

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

        @media (max-width: 768px) {
            .search-tipo-selector {
                flex-direction: column;
                align-items: center;
            }

            .tipo-option {
                width: 100%;
                max-width: 200px;
                justify-content: center;
            }
        }
    </style>
</body>
</html>

