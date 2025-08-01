<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Donante</title>
    <link rel="stylesheet" href="{{ asset('css/crudUsuarios.css') }}">
</head>
<body>
    <div class="crud-container">
        <!-- Header -->
        <div class="crud-header">
            <button class="back-btn" onclick="window.location.href='{{ route('adminDonante') }}'">
                <svg class="back-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <h1 class="page-title">Agregar Donante</h1>
        </div>

        <!-- Contenido principal -->
        <div class="crud-content">
            <div class="form-container">
                <!-- CORREGIDO: Action apunta a la ruta correcta -->
                <form id="donanteForm" class="donante-form" method="POST" action="{{ route('donantes.store') }}">
                    @csrf
                    <div class="form-grid">
                        <!-- Nombre -->
                        <div class="form-field">
                            <div class="field-icon icon-user"></div>
                            <input type="text" id="nombre" name="nombre" class="form-input" 
                                   placeholder="Nombre" value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Página web (opcional) -->
                        <div class="form-field">
                            <div class="field-icon icon-globe"></div>
                            <input type="url" id="pagina_web" name="paginaWeb" class="form-input" 
                                   placeholder="Página web (opcional)" value="{{ old('paginaWeb') }}">
                            @error('paginaWeb')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Apellido Paterno -->
                        <div class="form-field">
                            <div class="field-icon icon-user"></div>
                            <input type="text" id="apellido_paterno" name="aP" class="form-input" 
                                   placeholder="Apellido Paterno" value="{{ old('aP') }}">
                            @error('aP')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Correo electrónico -->
                        <div class="form-field">
                            <div class="field-icon icon-mail"></div>
                            <input type="email" id="correo" name="correo" class="form-input" 
                                   placeholder="Correo electrónico" value="{{ old('correo') }}" required>
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
                                   placeholder="Apellido Materno" value="{{ old('aM') }}">
                            @error('aM')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Contraseña -->
                        <div class="form-field">
                            <div class="field-icon icon-lock"></div>
                            <input type="password" id="contraseña" name="contraseña" class="form-input" 
                                   placeholder="Contraseña" required>
                            @error('contraseña')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Edad -->
                        <div class="form-field">
                            <div class="field-icon icon-calendar"></div>
                            <input type="number" id="edad" name="edad" class="form-input" 
                                   placeholder="Edad" value="{{ old('edad') }}" min="1" max="120">
                            @error('edad')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirmar contraseña -->
                        <div class="form-field">
                            <div class="field-icon icon-lock"></div>
                            <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" class="form-input" 
                                   placeholder="Confirmar contraseña" required>
                            <span class="error-message" id="password-error" style="display: none;">Las contraseñas no coinciden</span>
                        </div>

                        <!-- Teléfono -->
                        <div class="form-field">
                            <div class="field-icon icon-phone"></div>
                            <input type="tel" id="telefono" name="telefono" class="form-input" 
                                   placeholder="Teléfono" value="{{ old('telefono') }}" required>
                            @error('telefono')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tipo de entidad -->
                        <div class="form-field">
                            <div class="field-icon icon-building"></div>
                            <select id="tipo" name="tipo" class="form-select" required>
                                <option value="">Tipo de entidad</option>
                                <option value="persona" {{ old('tipo') == 'persona' ? 'selected' : '' }}>Persona</option>
                                <option value="organizacion" {{ old('tipo') == 'organizacion' ? 'selected' : '' }}>Organización</option>
                            </select>
                            @error('tipo')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- RFC -->
                        <div class="form-field">
                            <div class="field-icon icon-card"></div>
                            <input type="text" id="rfc" name="rfc" class="form-input" 
                                   placeholder="RFC" value="{{ old('rfc') }}" maxlength="13">
                            @error('rfc')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tipo de perfil (fijo para donante) -->
                        <div class="form-field">
                            <div class="field-icon icon-profile"></div>
                            <input type="text" class="form-input" value="Donante" readonly>
                            <input type="hidden" name="rol_id" value="2">
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
                        <button type="button" class="btn-reset" onclick="resetForm()">Restablecer</button>
                        <button type="submit" class="btn-submit" id="submitBtn">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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

        // Función para restablecer el formulario
        function resetForm() {
            if (confirm('¿Estás seguro de que deseas limpiar todos los campos?')) {
                document.getElementById('donanteForm').reset();
                document.getElementById('password-error').style.display = 'none';
                document.getElementById('submitBtn').disabled = false;
            }
        }

        // CORREGIDO: Validación del formulario antes de enviar (sin preventDefault)
        document.getElementById('donanteForm').addEventListener('submit', function(e) {
            const password = document.getElementById('contraseña').value;
            const confirmPassword = document.getElementById('confirmar_contraseña').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifica e inténtalo nuevamente.');
                return false;
            }
            
            // Deshabilitar el botón para evitar doble envío
            submitBtn.disabled = true;
            submitBtn.textContent = 'Agregando...';
            
            // El formulario se enviará normalmente al controlador
            return true;
        });

        // Formateo automático del RFC
        document.getElementById('rfc').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Validación del teléfono (solo números)
        document.getElementById('telefono').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9-\s\+\(\)]/g, '');
        });
    </script>

    <!-- Mostrar mensajes de éxito -->
    @if(session('success'))
        <script>
            alert('{{ session('success') }}');
            window.location.href = '{{ route('adminDonante') }}';
        </script>
    @endif

    <!-- Mostrar errores si los hay -->
    @if($errors->any() && !$errors->has('api'))
        <script>
            alert('Por favor, corrige los errores en el formulario.');
        </script>
    @endif
</body>
</html>