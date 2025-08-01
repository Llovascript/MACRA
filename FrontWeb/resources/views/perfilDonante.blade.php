<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Donante</title>
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
</head>

<body>
    <div class="perfil-container">
        <!-- Header -->
        <div class="perfil-header">
            <button class="back-btn" onclick="window.location.href='{{ route('donante.menu') }}'">
                <svg class="back-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
            <h1 class="page-title">Perfil</h1>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="form-container">
                <form class="donador-form">
                    <!-- Datos personales -->
                    <div class="form-section">
                        <h2 class="section-title">Datos personales</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre completo:</label>
                                <input type="text" id="nombre" name="nombre" class="form-input donador-field"
                                    readonly>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Correo electrónico:</label>
                                <input type="email" id="email" name="email" class="form-input donador-field"
                                    readonly>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="tel" id="telefono" name="telefono" class="form-input donador-field"
                                    readonly>
                            </div>

                            <div class="form-group">
                                <label for="rfc" class="form-label">RFC:</label>
                                <input type="text" id="rfc" name="rfc" class="form-input donador-field"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="form-section">
                        <h2 class="section-title">Dirección</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="estado" class="form-label">Estado:</label>
                                <select id="estado" name="estado" class="form-select donador-field">
                                    <option value="">Seleccione.....</option>
                                    <option value="aguascalientes">Aguascalientes</option>
                                    <option value="baja_california">Baja California</option>
                                    <option value="queretaro">Querétaro</option>
                                    <option value="cdmx">Ciudad de México</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="calle" class="form-label">Calle:</label>
                                <select id="calle" name="calle" class="form-select donador-field">
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
                                <select id="municipio" name="municipio" class="form-select donador-field">
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
                                <select id="colonia" name="colonia" class="form-select donador-field">
                                    <option value="">Seleccione.....</option>
                                    <option value="centro">Centro</option>
                                    <option value="loma_dorada">Loma Dorada</option>
                                    <option value="juriquilla">Juriquilla</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="no_exterior" class="form-label">No. Exterior:</label>
                                <input type="text" id="no_exterior" name="no_exterior"
                                    class="form-input donador-field" placeholder="123">
                            </div>
                        </div>

                        <div class="form-row single-field">
                            <div class="form-group">
                                <label for="codigo_postal" class="form-label">Código Postal:</label>
                                <input type="text" id="codigo_postal" name="codigo_postal"
                                    class="form-input donador-field" placeholder="76000">
                            </div>
                        </div>

                        <!-- Botón de guardar -->
                        <div class="save-section">
                            <button type="button" class="btn-save" id="guardarBtn">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Campos requeridos de dirección
            const requiredFields = [
                'estado', 'calle', 'municipio', 'colonia', 'no_exterior', 'codigo_postal'
            ];

            const guardarBtn = document.getElementById('guardarBtn');

            // Event listener para el botón guardar
            guardarBtn.addEventListener('click', function() {
                // Verificar que todos los campos estén completos
                let allFieldsComplete = true;
                let emptyFields = [];

                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        allFieldsComplete = false;
                        // Obtener el texto del label para mostrar cuál campo falta
                        const label = document.querySelector(`label[for="${fieldId}"]`);
                        if (label) {
                            emptyFields.push(label.textContent.replace(':', ''));
                        }
                    }
                });

                if (allFieldsComplete) {
                    // Mostrar confirmación
                    if (confirm(
                            '¿Estás seguro de que deseas guardar los datos de tu dirección? Esta acción completará tu perfil.'
                        )) {
                        // Aquí puedes agregar la lógica para enviar los datos
                        alert('¡Datos guardados exitosamente! Tu perfil ha sido completado.');

                        // Opcional: deshabilitar los campos después de guardar
                        requiredFields.forEach(fieldId => {
                            const field = document.getElementById(fieldId);
                            field.disabled = true;
                        });

                        // Deshabilitar el botón después de guardar
                        guardarBtn.disabled = true;
                        guardarBtn.textContent = 'Datos Guardados';
                        guardarBtn.style.background = '#6b7280';
                    }
                } else {
                    // Mostrar qué campos faltan por completar
                    alert(
                        `Por favor, completa los siguientes campos antes de guardar:\n\n• ${emptyFields.join('\n• ')}`
                    );
                }
            });
        });
    </script>
</body>

</html>
