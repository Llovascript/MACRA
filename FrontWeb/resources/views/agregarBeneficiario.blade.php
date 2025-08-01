<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Beneficiario</title>
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
</head>
<body>
    <div class="perfil-container">
        <!-- Header -->
        <div class="perfil-header">
            <button class="back-btn" onclick="window.location.href='{{ route('adminBeneficiarios') }}'">>
                <svg class="back-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
            <h1 class="page-title">Agregar Beneficiario</h1>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="form-container">
                <form class="beneficiario-form" method="POST" action="#">
                    @csrf
                    <!-- Datos personales -->
                    <div class="form-section">
                        <h2 class="section-title">Datos personales</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre completo:</label>
                                <input type="text" id="nombre" name="nombre"
                                    class="form-input beneficiario-field" 
                                    value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Correo electrónico:</label>
                                <input type="email" id="email" name="email"
                                    class="form-input beneficiario-field" 
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="tel" id="telefono" name="telefono"
                                    class="form-input beneficiario-field" 
                                    value="{{ old('telefono') }}" required>
                                @error('telefono')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="pagina" class="form-label">Página web:</label>
                                <input type="url" id="pagina" name="pagina"
                                    class="form-input beneficiario-field" 
                                    value="{{ old('pagina') }}" placeholder="https://ejemplo.com">
                                @error('pagina')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
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
                                    <option value="">Seleccione...</option>
                                    <option value="aguascalientes" {{ old('estado') == 'aguascalientes' ? 'selected' : '' }}>Aguascalientes</option>
                                    <option value="baja_california" {{ old('estado') == 'baja_california' ? 'selected' : '' }}>Baja California</option>
                                    <option value="queretaro" {{ old('estado') == 'queretaro' ? 'selected' : '' }}>Querétaro</option>
                                    <option value="cdmx" {{ old('estado') == 'cdmx' ? 'selected' : '' }}>Ciudad de México</option>
                                </select>
                                @error('estado')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="calles" class="form-label">Calles:</label>
                                <select id="calles" name="calles" class="form-select beneficiario-field" required>
                                    <option value="">Seleccione...</option>
                                    <option value="av_universidad" {{ old('calles') == 'av_universidad' ? 'selected' : '' }}>Av. Universidad</option>
                                    <option value="corregidora" {{ old('calles') == 'corregidora' ? 'selected' : '' }}>Corregidora</option>
                                    <option value="constituyentes" {{ old('calles') == 'constituyentes' ? 'selected' : '' }}>Constituyentes</option>
                                </select>
                                @error('calles')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="municipio" class="form-label">Municipio:</label>
                                <select id="municipio" name="municipio" class="form-select beneficiario-field" required>
                                    <option value="">Seleccione...</option>
                                    <option value="queretaro" {{ old('municipio') == 'queretaro' ? 'selected' : '' }}>Querétaro</option>
                                    <option value="corregidora" {{ old('municipio') == 'corregidora' ? 'selected' : '' }}>Corregidora</option>
                                    <option value="el_marques" {{ old('municipio') == 'el_marques' ? 'selected' : '' }}>El Marqués</option>
                                </select>
                                @error('municipio')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="no_interior" class="form-label">No. Interior:</label>
                                <input type="text" id="no_interior" name="no_interior"
                                    class="form-input beneficiario-field" 
                                    value="{{ old('no_interior') }}" placeholder="5A">
                                @error('no_interior')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="colonia" class="form-label">Colonia:</label>
                                <select id="colonia" name="colonia" class="form-select beneficiario-field" required>
                                    <option value="">Seleccione...</option>
                                    <option value="centro" {{ old('colonia') == 'centro' ? 'selected' : '' }}>Centro</option>
                                    <option value="loma_dorada" {{ old('colonia') == 'loma_dorada' ? 'selected' : '' }}>Loma Dorada</option>
                                    <option value="juriquilla" {{ old('colonia') == 'juriquilla' ? 'selected' : '' }}>Juriquilla</option>
                                </select>
                                @error('colonia')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="no_exterior" class="form-label">No. Exterior:</label>
                                <input type="text" id="no_exterior" name="no_exterior"
                                    class="form-input beneficiario-field" 
                                    value="{{ old('no_exterior') }}" placeholder="123" required>
                                @error('no_exterior')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row single-field">
                            <div class="form-group">
                                <label for="codigo_postal" class="form-label">Código Postal:</label>
                                <input type="text" id="codigo_postal" name="codigo_postal"
                                    class="form-input beneficiario-field" 
                                    value="{{ old('codigo_postal') }}" placeholder="76000" required>
                                @error('codigo_postal')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="save-section">
                            <button type="button" class="btn-reset" onclick="document.querySelector('.beneficiario-form').reset()">Restablecer</button>
                            <button type="submit" class="btn-save">Agregar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>
            alert('{{ session('success') }}');
            window.location.href = '{{ route('adminBeneficiarios') }}';
        </script>
    @endif

    @if($errors->any())
        <script>
            alert('Por favor, corrige los errores en el formulario.');
        </script>
    @endif
</body>
</html>