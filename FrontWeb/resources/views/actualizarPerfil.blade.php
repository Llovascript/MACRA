<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Perfil</title>
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
            <h1 class="page-title">Actualizar Perfil</h1>
        </div>

        <!-- Contenido principal -->
        <div class="crud-content">
            <!-- Sección de búsqueda -->
            <div class="search-container">
                <div class="search-section">
                    <h2>Buscar Perfil</h2>
                    
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

            <!-- Formulario de edición (oculto inicialmente) -->
            <div class="form-container" id="formularioEdicion" style="display: none;">
                <form id="perfilForm" class="perfil-form" method="POST" action="">
                    @csrf
                    @method('PUT')
                    
                    <!-- Información del perfil seleccionado -->
                    <div class="perfil-info">
                        <h3 id="perfilTipo">Actualizando: Beneficiario</h3>
                    </div>

                    <div class="form-grid">
                        <!-- Nombre -->
                        <div class="form-field">
                            <div class="field-icon icon-user"></div>
                            <input type="text" id="nombre" name="nombre" class="form-input" 
                                   placeholder="Nombre" required>
                            @error('nombre')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Página web (opcional) -->
                        <div class="form-field">
                            <div class="field-icon icon-globe"></div>
                            <input type="url" id="pagina_web" name="paginaWeb" class="form-input" 
                                   placeholder="Página web (opcional)">
                            @error('paginaWeb')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Apellido Paterno -->
                        <div class="form-field">
                            <div class="field-icon icon-user"></div>
                            <input type="text" id="apellido_paterno" name="aP" class="form-input" 
                                   placeholder="Apellido Paterno">
                            @error('aP')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Correo electrónico -->
                        <div class="form-field">
                            <div class="field-icon icon-mail"></div>
                            <input type="email" id="correo" name="correo" class="form-input" 
                                   placeholder="Correo electrónico" required>
                            @error('correo')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                            @error('api')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Apellido Materno -->
                        <div class="form-field">
                            <div class="field-icon icon-user"></div>
                            <input type="text" id="apellido_materno" name="aM" class="form-input" 
                                   placeholder="Apellido Materno">
                            @error('aM')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Contraseña (opcional para actualización) -->
                        <div class="form-field">
                            <div class="field-icon icon-lock"></div>
                            <input type="password" id="contraseña" name="contraseña" class="form-input" 
                                   placeholder="Nueva contraseña (opcional)">
                            @error('contraseña')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Edad -->
                        <div class="form-field">
                            <div class="field-icon icon-calendar"></div>
                            <input type="number" id="edad" name="edad" class="form-input" 
                                   placeholder="Edad" min="1" max="120">
                            @error('edad')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirmar contraseña -->
                        <div class="form-field">
                            <div class="field-icon icon-lock"></div>
                            <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" class="form-input" 
                                   placeholder="Confirmar nueva contraseña">
                            <span class="error-message" id="password-error" style="display: none;">Las contraseñas no coinciden</span>
                        </div>

                        <!-- Teléfono -->
                        <div class="form-field">
                            <div class="field-icon icon-phone"></div>
                            <input type="tel" id="telefono" name="telefono" class="form-input" 
                                   placeholder="Teléfono" required>
                            @error('telefono')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tipo de entidad -->
                        <div class="form-field">
                            <div class="field-icon icon-building"></div>
                            <select id="tipo" name="tipo" class="form-select" required>
                                <option value="">Tipo de entidad</option>
                                <option value="persona">Persona</option>
                                <option value="organizacion">Organización</option>
                            </select>
                            @error('tipo')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- RFC -->
                        <div class="form-field">
                            <div class="field-icon icon-card"></div>
                            <input type="text" id="rfc" name="rfc" class="form-input" 
                                   placeholder="RFC" maxlength="13">
                            @error('rfc')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tipo de perfil (readonly) -->
                        <div class="form-field">
                            <div class="field-icon icon-profile"></div>
                            <input type="text" id="perfil_readonly" class="form-input" readonly>
                        </div>
                    </div>

                    <!-- Mostrar errores generales -->
                    @if($errors->has('error'))
                        <div class="alert alert-danger">
                            {{ $errors->first('error') }}
                        </div>
                    @endif

                    <!-- Botones de acción -->
                    <div class="form-actions">
                        <button type="button" class="btn-reset" onclick="cancelarEdicion()">Cancelar</button>
                        <button type="submit" class="btn-submit" id="submitBtn">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let perfilSeleccionado = null;

        // Manejar cambio de tipo de búsqueda
        document.querySelectorAll('input[name="tipo_busqueda"]').forEach(radio => {
            radio.addEventListener('change', function() {
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
            document.getElementById('formularioEdicion').style.display = 'none';
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
                        <button class="btn-select" onclick="seleccionarPerfil(${perfil.id}, '${tipoBusqueda}')">
                            Seleccionar
                        </button>
                    `;
                    listaDiv.appendChild(item);
                });
            }
            
            resultadosDiv.style.display = 'block';
        }

        async function seleccionarPerfil(id, tipoPerfil) {
            try {
                const response = await fetch(`{{ url('/perfiles/obtener') }}/${id}`);
                
                if (!response.ok) {
                    throw new Error('Error al obtener los datos del perfil');
                }
                
                const datos = await response.json();
                cargarDatosEnFormulario(datos, tipoPerfil);
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar los datos del perfil.');
            }
        }

        function cargarDatosEnFormulario(datos, tipoPerfil) {
            perfilSeleccionado = datos;

            // Actualizar información del perfil
            const nombrePerfil = tipoPerfil === 'donante' ? 'Donante' : 'Beneficiario';
            document.getElementById('perfilTipo').textContent = `Actualizando: ${nombrePerfil}`;
            document.getElementById('perfil_readonly').value = nombrePerfil;

            // Llenar el formulario con los datos
            document.getElementById('nombre').value = datos.nombre || '';
            document.getElementById('apellido_paterno').value = datos.aP || '';
            document.getElementById('apellido_materno').value = datos.aM || '';
            document.getElementById('correo').value = datos.correo || '';
            document.getElementById('telefono').value = datos.telefono || '';
            document.getElementById('edad').value = datos.edad || '';
            document.getElementById('rfc').value = datos.rfc || '';
            document.getElementById('pagina_web').value = datos.paginaWeb || '';
            document.getElementById('tipo').value = datos.tipo || '';

            // Actualizar action del formulario
            document.querySelector('.perfil-form').action = `{{ url('/perfiles/actualizar') }}/${datos.id}`;

            // Mostrar el formulario
            document.getElementById('formularioEdicion').style.display = 'block';
            document.getElementById('formularioEdicion').scrollIntoView({ behavior: 'smooth' });
        }

        function cancelarEdicion() {
            document.getElementById('formularioEdicion').style.display = 'none';
            document.getElementById('resultados').style.display = 'none';
            document.getElementById('buscar').value = '';
            document.getElementById('perfilForm').reset();
            perfilSeleccionado = null;
        }

        // Validación de contraseñas coincidentes
        document.getElementById('confirmar_contraseña').addEventListener('input', function() {
            const password = document.getElementById('contraseña').value;
            const confirmPassword = this.value;
            const errorElement = document.getElementById('password-error');
            const submitBtn = document.getElementById('submitBtn');
            
            if (confirmPassword && password !== confirmPassword) {
                errorElement.style.display = 'block';
                this.style.borderColor = '#dc3545';
                submitBtn.disabled = true;
            } else {
                errorElement.style.display = 'none';
                this.style.borderColor = '';
                submitBtn.disabled = false;
            }
        });

        // Validación del formulario antes de enviar
        document.getElementById('perfilForm').addEventListener('submit', function(e) {
            const password = document.getElementById('contraseña').value;
            const confirmPassword = document.getElementById('confirmar_contraseña').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (password && password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifica e inténtalo nuevamente.');
                return false;
            }
            
            // Deshabilitar el botón para evitar doble envío
            submitBtn.disabled = true;
            submitBtn.textContent = 'Actualizando...';
            
            return true;
        });

        // Formateo automático del RFC
        document.getElementById('rfc').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Validación del teléfono
        document.getElementById('telefono').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9-\s\+\(\)]/g, '');
        });

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

    <!-- Mostrar mensajes de éxito -->
    @if(session('success'))
        <script>
            alert('{{ session('success') }}');
            window.location.href = '{{ route('adminPerfiles') }}';
        </script>
    @endif

    <!-- Mostrar errores si los hay -->
    @if($errors->any() && !$errors->has('api'))
        <script>
            alert('Por favor, corrige los errores en el formulario.');
        </script>
    @endif

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

        /* Información del perfil */
        .perfil-info {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .perfil-info h3 {
            color: #007bff;
            margin: 0;
            font-size: 1.25rem;
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

        .btn-select {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-select:hover {
            background: #0056b3;
        }

        .no-results {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
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

