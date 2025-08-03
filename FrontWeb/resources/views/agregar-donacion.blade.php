<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Donación</title>
    <link rel="stylesheet" href="{{ asset('css/donaciones.css') }}">
</head>
<body>
    <div class="container">
        <!-- Header con botón de regreso -->
        <div class="header">
            <button class="back-btn" onclick="window.location.href='{{ route('donaciones.estatus') }}'">
                <svg class="back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h1 class="page-title">Agregar Donación</h1>
        </div>

        <!-- Mensajes de error -->
        @if(session('error') || isset($error))
            <div class="alert alert-error">
                {{ session('error') ?? $error }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario -->
        <form action="{{ route('donaciones.guardar') }}" method="POST" class="donation-form">
            @csrf
            
            <div class="form-group">
                <label for="articuloP_id" class="form-label">
                    Selecciona la presentación del artículo:
                </label>
                <div class="select-wrapper">
                    <select name="articuloP_id" id="articuloP_id" class="form-select" required>
                        <option value="">Selecciona una opción...</option>
                        @if(isset($articulosPresentaciones) && count($articulosPresentaciones) > 0)
                            @foreach($articulosPresentaciones as $presentacion)
                                <option value="{{ $presentacion['id'] }}" {{ old('articuloP_id') == $presentacion['id'] ? 'selected' : '' }}>
                                    {{ $presentacion['cantidad'] }}{{ strtolower(substr($presentacion['unidad']['nombre'], 0, 2)) }} 
                                    {{ $presentacion['articulo']['nombre'] }}
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>No hay artículos disponibles</option>
                        @endif
                    </select>
                    <svg class="select-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div class="form-group">
                <label for="cantidad" class="form-label">
                    Agrega la cantidad a donar:
                </label>
                <input 
                    type="number" 
                    name="cantidad" 
                    id="cantidad" 
                    class="form-input" 
                    value="{{ old('cantidad', 1) }}" 
                    min="1" 
                    required
                    placeholder="1"
                >
            </div>

            <!-- Campo oculto para tipo de donante (se detecta automáticamente del usuario) -->
            <input type="hidden" name="tipo_donante" value="persona">

            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    Agregar
                </button>
            </div>
        </form>

        <!-- Información adicional -->
        <div class="info-section">
            <div class="info-card">
                <h3>Información importante</h3>
                <ul>
                    <li>Tu donación será revisada por nuestro equipo</li>
                    <li>Recibirás una confirmación una vez aprobada</li>
                    <li>Puedes ver el estatus en la sección "Estatus Solicitudes"</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Auto-ocultar alertas después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Validación del formulario
        document.querySelector('.donation-form').addEventListener('submit', function(e) {
            const articulo = document.getElementById('articuloP_id').value;
            const cantidad = document.getElementById('cantidad').value;

            if (!articulo) {
                e.preventDefault();
                alert('Por favor selecciona un artículo');
                return;
            }

            if (!cantidad || cantidad < 1) {
                e.preventDefault();
                alert('Por favor ingresa una cantidad válida');
                return;
            }

            // Mostrar indicador de carga
            const submitBtn = this.querySelector('.submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Procesando...';
        });

        // Detectar tipo de donante basado en datos del usuario (si están disponibles)
        @if(Session::has('user_data'))
            const userData = @json(Session::get('user_data'));
            if (userData && userData.tipo) {
                document.querySelector('input[name="tipo_donante"]').value = userData.tipo;
            }
        @endif
    </script>
</body>
</html>