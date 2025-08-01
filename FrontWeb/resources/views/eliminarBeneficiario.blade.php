<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Beneficiario</title>
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
</head>
<body>
    <div class="perfil-container">
        <!-- Header -->
        <div class="perfil-header">
            <button class="back-btn" onclick="window.location.href='{{ route('adminBeneficiarios') }}'">
                <svg class="back-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
            <h1 class="page-title">Eliminar Beneficiario</h1>
        </div>

        <!-- Search Section -->
        <div class="main-content">
            <div class="search-container">
                <div class="search-section">
                    <h2 class="section-title">Buscar</h2>
                    <div class="search-row">
                        <div class="search-group">
                            <input type="text" id="buscar" name="buscar" 
                                class="form-input search-input" 
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

                <!-- Results Section -->
                <div class="results-section" id="resultados" style="display: none;">
                    <h3 class="results-title">Resultados de búsqueda:</h3>
                    <div class="results-list" id="listaResultados">
                        <!-- Los resultados se cargarán aquí dinámicamente -->
                    </div>
                </div>
            </div>

            <!-- Confirmation Section (Hidden initially) -->
            <div class="form-container delete-confirmation" id="confirmacionEliminacion" style="display: none;">
                <div class="form-section">
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

                    <!-- Información del beneficiario a eliminar -->
                    <div class="beneficiario-info" id="infoBeneficiario">
                        <!-- Se llenará dinámicamente -->
                    </div>

                    <!-- Botones de confirmación -->
                    <div class="delete-actions">
                        <button type="button" class="btn-cancel" onclick="cancelarEliminacion()">Cancelar</button>
                        <button type="button" class="btn-delete" onclick="confirmarEliminacion()">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let beneficiarioSeleccionado = null;

        function buscarBeneficiario() {
            const query = document.getElementById('buscar').value.trim();
            if (query.length < 2) {
                alert('Por favor, ingrese al menos 2 caracteres para buscar.');
                return;
            }

            // Simular búsqueda (aquí se conectaría con FastAPI)
            const resultadosMock = [
                {
                    id: 1,
                    nombre: 'Juan Pérez García',
                    email: 'juan.perez@email.com',
                    telefono: '442-123-4567',
                    rfc: 'PEGJ850315ABC'
                },
                {
                    id: 2,
                    nombre: 'María González López',
                    email: 'maria.gonzalez@email.com',
                    telefono: '442-987-6543',
                    rfc: 'GOLM920425XYZ'
                }
            ];

            mostrarResultados(resultadosMock);
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
                    item.className = 'result-item delete-item';
                    item.innerHTML = `
                        <div class="result-info">
                            <h4>${beneficiario.nombre}</h4>
                            <p>${beneficiario.email} | ${beneficiario.telefono}</p>
                            <p><strong>RFC:</strong> ${beneficiario.rfc}</p>
                        </div>
                        <button class="btn-delete-select" onclick="seleccionarParaEliminar(${JSON.stringify(beneficiario).replace(/"/g, '&quot;')})">
                            Eliminar
                        </button>
                    `;
                    listaDiv.appendChild(item);
                });
            }
            
            resultadosDiv.style.display = 'block';
        }

        function seleccionarParaEliminar(beneficiario) {
            beneficiarioSeleccionado = beneficiario;
            mostrarConfirmacion(beneficiario);
        }

        function mostrarConfirmacion(beneficiario) {
            const infoDiv = document.getElementById('infoBeneficiario');
            infoDiv.innerHTML = `
                <div class="info-card">
                    <h3>Información del Beneficiario</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Nombre:</strong>
                            <span>${beneficiario.nombre}</span>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong>
                            <span>${beneficiario.email}</span>
                        </div>
                        <div class="info-item">
                            <strong>Teléfono:</strong>
                            <span>${beneficiario.telefono}</span>
                        </div>
                        <div class="info-item">
                            <strong>RFC:</strong>
                            <span>${beneficiario.rfc}</span>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('confirmacionEliminacion').style.display = 'block';
            document.getElementById('confirmacionEliminacion').scrollIntoView({ behavior: 'smooth' });
        }

        function confirmarEliminacion() {
            if (!beneficiarioSeleccionado) return;

            // Aquí se haría la llamada a FastAPI para eliminar
            if (confirm('¿Está completamente seguro de eliminar este beneficiario? Esta acción es irreversible.')) {
                // Simular eliminación exitosa
                alert('Beneficiario eliminado exitosamente.');
                window.location.href = '{{ route("adminBeneficiarios") }}';
            }
        }

        function cancelarEliminacion() {
            document.getElementById('confirmacionEliminacion').style.display = 'none';
            document.getElementById('resultados').style.display = 'none';
            document.getElementById('buscar').value = '';
            beneficiarioSeleccionado = null;
        }
    </script>

    <style>
        /* Estilos específicos para eliminar */
        .delete-confirmation {
            background: #fff5f5;
            border: 2px solid #fed7d7;
        }

        .delete-warning {
            text-align: center;
            padding: 2rem;
            border-bottom: 1px solid #fed7d7;
            margin-bottom: 2rem;
        }

        .warning-icon {
            margin-bottom: 1rem;
        }

        .warning-title {
            color: #dc3545;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .warning-text {
            color: #6c757d;
            font-size: 1rem;
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.5;
        }

        .delete-item {
            border-left: 4px solid #dc3545;
        }

        .btn-delete-select {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .btn-delete-select:hover {
            background: #c82333;
        }

        .info-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
        }

        .info-card h3 {
            margin-bottom: 1rem;
            color: #333;
            font-size: 1.25rem;
        }

        .info-grid {
            display: grid;
            gap: 1rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item strong {
            color: #495057;
            min-width: 120px;
        }

        .info-item span {
            color: #333;
            text-align: right;
            flex: 1;
        }

        .delete-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
            font-size: 1rem;
            font-weight: 500;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
            font-size: 1rem;
            font-weight: 500;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        @media (max-width: 768px) {
            .delete-actions {
                flex-direction: column;
            }
            
            .info-item {
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .info-item span {
                text-align: left;
            }
        }
    </style>
</body>
</html>