<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Perfiles</title>
    <link rel="stylesheet" href="{{ asset('css/crudUsuarios.css') }}">
</head>
<body>
    <div class="admin-container">
        <!-- Header con botón de regreso -->
        <div class="admin-header">
            <button class="back-btn" onclick="window.location.href='{{ route('admin.menu') }}'">
                <svg class="back-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <!-- Título principal -->
        <div class="main-content">
            <h1 class="page-title">Administración de Perfiles</h1>
            
            <!-- Menú de opciones -->
            <div class="menu-grid">
                <!-- Agregar Perfil -->
                <div class="menu-card" onclick="window.location.href='{{ route('perfiles.create') }}'">
                    <div class="card-icon agregar">
                        <img src="{{ asset('images/agregar.png') }}" alt="Agregar" class="icon-image">
                    </div>
                    <h3 class="card-title">Agregar</h3>
                </div>

                <!-- Actualizar Perfil -->
                <div class="menu-card" onclick="window.location.href='{{ route('perfiles.updateForm') }}'">
                    <div class="card-icon actualizar">
                        <img src="{{ asset('images/actualizar.png') }}" alt="Actualizar" class="icon-image">
                    </div>
                    <h3 class="card-title">Actualizar</h3>
                </div>

                <!-- Eliminar Perfil -->
                <div class="menu-card" onclick="window.location.href='{{ route('perfiles.deleteForm') }}'">
                    <div class="card-icon eliminar">
                        <img src="{{ asset('images/eliminar.png') }}" alt="Eliminar" class="icon-image">
                    </div>
                    <h3 class="card-title">Eliminar</h3>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Mostrar mensajes de éxito -->
    @if(session('success'))
        <script>
            alert('{{ session('success') }}');
        </script>
    @endif

    <style>
        .subtitle {
            text-align: center;
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 3rem;
            font-weight: 300;
        }

        .card-description {
            margin: 0.5rem 0 0 0;
            color: #6c757d;
            font-size: 0.9rem;
            text-align: center;
        }

        .quick-access {
            margin-top: 4rem;
            text-align: center;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 500;
            color: #333;
            margin-bottom: 2rem;
        }

        .quick-buttons {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .quick-btn {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px solid #d4a574;
            border-radius: 15px;
            padding: 1.5rem 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1rem;
            color: #333;
            text-decoration: none;
            min-width: 200px;
            justify-content: center;
        }

        .quick-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: #c19660;
        }

        .quick-btn.beneficiario:hover {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-color: #2196f3;
        }

        .quick-btn.donante:hover {
            background: linear-gradient(135deg, #e8f5e8, #c8e6c8);
            border-color: #4caf50;
        }

        .quick-icon {
            font-size: 1.5rem;
        }

        .quick-text {
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .quick-buttons {
                flex-direction: column;
                align-items: center;
            }

            .quick-btn {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</body>
</html>

