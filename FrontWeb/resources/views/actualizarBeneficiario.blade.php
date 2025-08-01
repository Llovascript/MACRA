<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Beneficiario</title>
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
            <h1 class="page-title">Actualizar Beneficiario</h1>
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

            <!-- Form Section (Hidden initially) -->
            <div class="form-container" id="formularioEdicion" style="display: none;">
                <form class="beneficiario-form" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <!-- Datos personales -->
                    <div class="form-section">
                        <h2 class="section-title">Datos personales</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre completo:</label>
                                <input type="text" id="nombre" name="nombre"
                                    class="form-input beneficiario-field" required>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Correo electrónico:</label>
                                <input type="email" id="email" name="email"
                                    class="form-input beneficiario-field" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="tel" id="telefono" name="telefono"
                                    class="form-input beneficiario-field" required>
                            </div>

                            <div class="form-group">
                                <label for="rfc" class="form-label">RFC:</label>
                                <input type="text" id="rfc" name="rfc"
                                    class="form-input beneficiario-field" required>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="form-section">
                        <h2 class="section-title">Dirección</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="estado" class="form-label">Estado:</label>
                                <select id="estado" name="estado" class="form-select beneficiario-field" required>
                                    <option value="">Seleccione.....</option>
                                    <option value="aguascalientes">Aguascalientes</option>
                                    <option value="baja_california">Baja California</option>
                                    <option value="queretaro">Querétaro</option>
                                    <option value="cdmx">Ciudad de México</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="calle" class="form-label">Calle:</label>
                                <select id="calle" name="calle" class="form-select beneficiario-field" required>
                                    <option value="">Seleccione.....</option>
                                    <option value="av_universidad">Av. Universidad</option>
                                    <option value="corregidora">Corregidora</option>
                                    <option value="constituyentes">Constituyentes</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="municipio" class="form-label">Municipio:</label>
                                <select id="municipio" name="municipio" class="form-select beneficiario-field" required>
                                    <option value="">Seleccione.....</option>
                                    <option value="queretaro">Querétaro</option>
                                    <option value="corregidora">Corregidora</option>
                                    <option value="el_marques">El Marqués</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="no_interior" class="form-label">No. Interior:</label>
                                <input type="text" id="no_interior" name="no_interior"
                                    class="form-input beneficiario-field" placeholder="5A">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="colonia" class="form-label">Colonia:</label>
                                <select id="colonia" name="colonia" class="form-select beneficiario-field" required>
                                    <option value="">Seleccione.....</option>
                                    <option value="centro">Centro</option>
                                    <option value="loma_dorada">Loma Dorada</option>
                                    <option value="juriquilla">Juriquilla</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="no_exterior" class="form-label">No. Exterior:</label>
                                <input type="text" id="no_exterior" name="no_exterior"
                                    class="form-input beneficiario-field" placeholder="123" required>
                            </div>
                        </div>

                        <div class="form-row single-field">
                            <div class="form-group">
                                <label for="codigo_postal" class="form-label">Código Postal:</label>
                                <input type="text" id="codigo_postal" name="codigo_postal"
                                    class="form-input beneficiario-field" placeholder="76000" required>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="save-section">
                            <button type="button" class="btn-reset" onclick="cancelarEdicion()">Restablecer</button>
                            <button type="submit" class="btn-save">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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
                    telefono: '442-123-4567'
                },
                {
                    id: 2,
                    nombre: 'María González López',
                    email: 'maria.gonzalez@email.com',
                    telefono: '442-987-6543'
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
                    item.className = 'result-item';
                    item.innerHTML = `
                        <div class="result-info">
                            <h4>${beneficiario.nombre}</h4>
                            <p>${beneficiario.email} | ${beneficiario.telefono}</p>
                        </div>
                        <button class="btn-select" onclick="seleccionarBeneficiario(${beneficiario.id})">
                            Seleccionar
                        </button>
                    `;
                    listaDiv.appendChild(item);
                });
            }
            
            resultadosDiv.style.display = 'block';
        }

        function seleccionarBeneficiario(id) {
            // Simular obtención de datos del beneficiario (aquí se conectaría con FastAPI)
            const datosSimulados = {
                id: id,
                nombre: 'Juan Pérez García',
                email: 'juan.perez@email.com',
                telefono: '442-123-4567',
                rfc: 'PEGJ850315ABC',
                estado: 'queretaro',
                calle: 'av_universidad',
                municipio: 'queretaro',
                colonia: 'centro',
                no_exterior: '123',
                no_interior: '2A',
                codigo_postal: '76000'
            };

            cargarDatosEnFormulario(datosSimulados);
        }

        function cargarDatosEnFormulario(datos) {
            // Llenar el formulario con los datos
            document.getElementById('nombre').value = datos.nombre;
            document.getElementById('email').value = datos.email;
            document.getElementById('telefono').value = datos.telefono;
            document.getElementById('rfc').value = datos.rfc;
            document.getElementById('estado').value = datos.estado;
            document.getElementById('calle').value = datos.calle;
            document.getElementById('municipio').value = datos.municipio;
            document.getElementById('colonia').value = datos.colonia;
            document.getElementById('no_exterior').value = datos.no_exterior;
            document.getElementById('no_interior').value = datos.no_interior || '';
            document.getElementById('codigo_postal').value = datos.codigo_postal;

            // Actualizar action del formulario
            document.querySelector('.beneficiario-form').action = `/actualizarBeneficiario/${datos.id}`;

            // Mostrar el formulario
            document.getElementById('formularioEdicion').style.display = 'block';
            document.getElementById('formularioEdicion').scrollIntoView({ behavior: 'smooth' });
        }

        function cancelarEdicion() {
            document.getElementById('formularioEdicion').style.display = 'none';
            document.getElementById('resultados').style.display = 'none';
            document.getElementById('buscar').value = '';
        }
    </script>
</body>
</html>