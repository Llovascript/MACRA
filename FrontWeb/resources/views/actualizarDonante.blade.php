<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Donante</title>
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
</head>
<body>
    <div class="perfil-container">
        <!-- Header -->
        <div class="perfil-header">
            <button class="back-btn" onclick="window.location.href='{{ route('adminDonante') }}'">
                <svg class="back-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
            <h1 class="page-title">Actualizar Donante</h1>
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
                                placeholder="Ingrese nombre, email, teléfono o RFC...">
                        </div>
                        <button type="button" class="btn-search" onclick="buscarDonante()">
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
                <form class="donador-form" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <!-- Datos personales -->
                    <div class="form-section">
                        <h2 class="section-title">Datos personales</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre completo:</label>
                                <input type="text" id="nombre" name="nombre"
                                    class="form-input donador-field" required>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Correo electrónico:</label>
                                <input type="email" id="email" name="email"
                                    class="form-input donador-field" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="tel" id="telefono" name="telefono"
                                    class="form-input donador-field" required>
                            </div>

                            <div class="form-group">
                                <label for="rfc" class="form-label">RFC:</label>
                                <input type="text" id="rfc" name="rfc"
                                    class="form-input donador-field" required maxlength="13">
                            </div>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="form-section">
                        <h2 class="section-title">Dirección</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="estado" class="form-label">Estado:</label>
                                <select id="estado" name="estado" class="form-select donador-field" required>
                                    <option value="">Seleccione.....</option>
                                    <option value="aguascalientes">Aguascalientes</option>
                                    <option value="baja_california">Baja California</option>
                                    <option value="queretaro">Querétaro</option>
                                    <option value="cdmx">Ciudad de México</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="calle" class="form-label">Calle:</label>
                                <select id="calle" name="calle" class="form-select donador-field" required>
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
                                <select id="municipio" name="municipio" class="form-select donador-field" required>
                                    <option value="">Seleccione.....</option>
                                    <option value="queretaro">Querétaro</option>
                                    <option value="corregidora">Corregidora</option>
                                    <option value="el_marques">El Marqués</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="no_interior" class="form-label">No. Interior:</label>
                                <input type="text" id="no_interior" name="no_interior"
                                    class="form-input donador-field" placeholder="5A">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="colonia" class="form-label">Colonia:</label>
                                <select id="colonia" name="colonia" class="form-select donador-field" required>
                                    <option value="">Seleccione.....</option>
                                    <option value="centro">Centro</option>
                                    <option value="loma_dorada">Loma Dorada</option>
                                    <option value="juriquilla">Juriquilla</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="no_exterior" class="form-label">No. Exterior:</label>
                                <input type="text" id="no_exterior" name="no_exterior"
                                    class="form-input donador-field" placeholder="123" required>
                            </div>
                        </div>

                        <div class="form-row single-field">
                            <div class="form-group">
                                <label for="codigo_postal" class="form-label">Código Postal:</label>
                                <input type="text" id="codigo_postal" name="codigo_postal"
                                    class="form-input donador-field" placeholder="76000" required>
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
        function buscarDonante() {
            const query = document.getElementById('buscar').value.trim();
            if (query.length < 2) {
                alert('Por favor, ingrese al menos 2 caracteres para buscar.');
                return;
            }

            // Simular búsqueda (aquí se conectaría con FastAPI)
            const resultadosMock = [
                {
                    id: 1,
                    nombre: 'Carlos Ramírez Sánchez',
                    email: 'carlos.ramirez@empresa.com',
                    telefono: '442-555-0123',
                    rfc: 'RASC850215ABC'
                },
                {
                    id: 2,
                    nombre: 'Ana Patricia Morales',
                    email: 'ana.morales@company.com',
                    telefono: '442-555-0456',
                    rfc: 'MORA920312XYZ'
                }
            ];

            mostrarResultados(resultadosMock);
        }

        function mostrarResultados(resultados) {
            const resultadosDiv = document.getElementById('resultados');
            const listaDiv = document.getElementById('listaResultados');
            
            listaDiv.innerHTML = '';
            
            if (resultados.length === 0) {
                listaDiv.innerHTML = '<p class="no-results">No se encontraron donantes.</p>';
            } else {
                resultados.forEach(donante => {
                    const item = document.createElement('div');
                    item.className = 'result-item';
                    item.innerHTML = `
                        <div class="result-info">
                            <h4>${donante.nombre}</h4>
                            <p>${donante.email} | ${donante.telefono}</p>
                            <p><strong>RFC:</strong> ${donante.rfc}</p>
                        </div>
                        <button class="btn-select" onclick="seleccionarDonante(${donante.id})">
                            Seleccionar
                        </button>
                    `;
                    listaDiv.appendChild(item);
                });
            }
            
            resultadosDiv.style.display = 'block';
        }

        function seleccionarDonante(id) {
            // Simular obtención de datos del donante (aquí se conectaría con FastAPI)
            const datosSimulados = {
                id: id,
                nombre: 'Carlos Ramírez Sánchez',
                email: 'carlos.ramirez@empresa.com',
                telefono: '442-555-0123',
                rfc: 'RASC850215ABC',
                estado: 'queretaro',
                calle: 'av_universidad',
                municipio: 'queretaro',
                colonia: 'centro',
                no_exterior: '456',
                no_interior: '3B',
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
            document.querySelector('.donador-form').action = `/actualizarDonante/${datos.id}`;

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