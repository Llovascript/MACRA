<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Perfil</title>
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
            <h1 class="page-title">Agregar Perfil</h1>
        </div>

        <!-- Contenido principal -->
        <div class="crud-content">
            <div class="form-container">
                <form id="perfilForm" class="perfil-form" method="POST" action="{{ route('perfiles.store') }}">
                    @csrf
                    
                    <!-- Selector de tipo de perfil -->
                    <div class="tipo-perfil-selector">
                        <h2 class="selector-title">Tipo de Perfil</h2>
                        <div class="perfil-options">
                            <label class="perfil-option {{ old('tipo_perfil') == 'beneficiario' ? 'selected' : '' }}" data-tipo="beneficiario">
                                <input type="radio" name="tipo_perfil" value="beneficiario" {{ old('tipo_perfil', 'beneficiario') == 'beneficiario' ? 'checked' : '' }} required>
                                <div class="option-content">
                                    <span class="option-icon">👥</span>
                                    <span class="option-text">Beneficiario</span>
                                </div>
                            </label>
                            <label class="perfil-option {{ old('tipo_perfil') == 'donante' ? 'selected' : '' }}" data-tipo="donante">
                                <input type="radio" name="tipo_perfil" value="donante" {{ old('tipo_perfil') == 'donante' ? 'checked' : '' }} required>
                                <div class="option-content">
                                    <span class="option-icon">🤝</span>
                                    <span class="option-text">Donante</span>
                                </div>
                            </label>
                        </div>
                        @error('tipo_perfil')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

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
                            <input type="url" id="paginaWeb" name="paginaWeb" class="form-input" 
                                   placeholder="Página web (opcional)" value="{{ old('paginaWeb') }}">
                            @error('paginaWeb')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Apellido Paterno -->
                        <div class="form-field">
                            <div class="field-icon icon-user"></div>
                            <input type="text" id="aP" name="aP" class="form-input" 
                                   placeholder="Apellido Paterno" value="{{ old('aP') }}">
                            @error('aP')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Correo electrónico -->
                        <div class="form-field">
                            <div class="field-icon icon-mail"></div>
                            <input type="email" id="correo" name="correo" class="form-input" 
                                   placeholder="Correo electrónico" value="{{ old('correo') }}" required autocomplete="email">
                            @error('correo')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                            @error('api')
                                <div class="error-message api-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Apellido Materno -->
                        <div class="form-field">
                            <div class="field-icon icon-user"></div>
                            <input type="text" id="aM" name="aM" class="form-input" 
                                   placeholder="Apellido Materno" value="{{ old('aM') }}">
                            @error('aM')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Contraseña -->
                        <div class="form-field">
                            <div class="field-icon icon-lock"></div>
                            <input type="password" id="contraseña" name="contraseña" class="form-input" 
                                   placeholder="Contraseña" required autocomplete="new-password">
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
                                   placeholder="Confirmar contraseña" required autocomplete="new-password">
                            <span class="error-message" id="password-error" style="display: none;">Las contraseñas no coinciden</span>
                        </div>

                        <!-- Teléfono -->
                        <div class="form-field">
                            <div class="field-icon icon-phone"></div>
                            <input type="tel" id="telefono" name="telefono" class="form-input" 
                                   placeholder="Teléfono" value="{{ old('telefono') }}" required autocomplete="tel">
                            @error('telefono')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tipo de entidad -->
                        <div class="form-field">
                            <div class="field-icon icon-building"></div>
                            <select id="tipo" name="tipo" class="form-select" required>
                                <option value="">Seleccione tipo de entidad</option>
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
                                   placeholder="RFC" value="{{ old('rfc') }}" maxlength="13" autocomplete="off">
                            @error('rfc')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tipo de perfil (readonly, se actualiza dinámicamente) -->
                        <div class="form-field">
                            <div class="field-icon icon-profile"></div>
                            <input type="text" id="perfil_readonly" class="form-input" value="Beneficiario" readonly>
                        </div>
                    </div>

                    <!-- Mostrar errores generales -->
                    @if($errors->has('error'))
                        <div class="alert alert-danger">
                            <strong>Error:</strong> {{ $errors->first('error') }}
                        </div>
                    @endif

                    <!-- Botones de acción -->
                    <div class="form-actions">
                        <button type="button" class="btn-reset" onclick="resetForm()">Restablecer</button>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span id="submitText">Agregar Beneficiario</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Actualizar interfaz cuando se cambia el tipo de perfil
        document.querySelectorAll('input[name="tipo_perfil"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const tipoPerfil = this.value;
                updateInterfaceForProfile(tipoPerfil);
            });
        });

        function updateInterfaceForProfile(tipoPerfil) {
            const perfilReadonly = document.getElementById('perfil_readonly');
            const submitText = document.getElementById('submitText');
            const pageTitle = document.querySelector('.page-title');
            
            // Actualizar selector visual
            document.querySelectorAll('.perfil-option').forEach(option => {
                option.classList.remove('selected');
            });
            document.querySelector(`[data-tipo="${tipoPerfil}"]`).classList.add('selected');
            
            // Actualizar textos
            if (tipoPerfil === 'donante') {
                perfilReadonly.value = 'Donante';
                submitText.textContent = 'Agregar Donante';
                pageTitle.textContent = 'Agregar Donante';
            } else {
                perfilReadonly.value = 'Beneficiario';
                submitText.textContent = 'Agregar Beneficiario';
                pageTitle.textContent = 'Agregar Beneficiario';
            }
        }

        // Inicializar interfaz
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSeleccionado = document.querySelector('input[name="tipo_perfil"]:checked')?.value || 'beneficiario';
            updateInterfaceForProfile(tipoSeleccionado);
        });

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
                document.getElementById('perfilForm').reset();
                document.getElementById('password-error').style.display = 'none';
                document.getElementById('submitBtn').disabled = false;
                
                // Restablecer tipo de perfil por defecto
                updateInterfaceForProfile('beneficiario');
                
                // Limpiar errores de API
                const apiErrors = document.querySelectorAll('.api-error');
                apiErrors.forEach(error => error.style.display = 'none');
            }
        }

        // Validación del formulario antes de enviar
        document.getElementById('perfilForm').addEventListener('submit', function(e) {
            const password = document.getElementById('contraseña').value;
            const confirmPassword = document.getElementById('confirmar_contraseña').value;
            const submitBtn = document.getElementById('submitBtn');
            const tipo = document.getElementById('tipo').value;
            const tipoPerfil = document.querySelector('input[name="tipo_perfil"]:checked')?.value;
            
            // Validar contraseñas
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifica e inténtalo nuevamente.');
                return false;
            }
            
            // Validar tipo de entidad
            if (!tipo) {
                e.preventDefault();
                alert('Por favor, selecciona el tipo de entidad.');
                document.getElementById('tipo').focus();
                return false;
            }

            // Validar tipo de perfil
            if (!tipoPerfil) {
                e.preventDefault();
                alert('Por favor, selecciona el tipo de perfil.');
                return false;
            }
            
            // Deshabilitar el botón para evitar doble envío
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span id="submitText">Agregando...</span>';
            
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
            console.log('Errores de validación:', @json($errors->all()));
            alert('Por favor, corrige los errores en el formulario.');
        </script>
    @endif

    <style>
        /* Selector de tipo de perfil */
        .tipo-perfil-selector {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .selector-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
        }

        .perfil-options {
            display: flex;
            gap: 2rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .perfil-option {
            background: white;
            border: 3px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 150px;
            position: relative;
        }

        .perfil-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .perfil-option:hover {
            border-color: #d4a574;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .perfil-option.selected {
            border-color: #007bff;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        .option-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .option-icon {
            font-size: 2rem;
        }

        .option-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
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
        
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .api-error {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 4px;
            padding: 8px;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .perfil-options {
                flex-direction: column;
                align-items: center;
            }

            .perfil-option {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
</body>
</html>

