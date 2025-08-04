<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Donaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .donacion-card {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .donacion-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
        }
        .status-pendiente {
            background-color: #fffbeb;
            color: #b45309;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-amber-800 text-white p-4 shadow-md sticky top-0 z-10">
            <div class="container mx-auto flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="mr-4 text-white">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-bold">Mis Donaciones</h1>
                </div>
                <a href="{{ route('donaciones.crear') }}" class="text-white">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
        </div>

        <!-- Contenido -->
        <main class="container mx-auto p-4">
            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <p>{{ session('success') }}</p>
            </div>
            @endif

            <!-- Estadísticas -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-amber-800">{{ count($donaciones) }}</div>
                    <div class="text-sm text-gray-600">Total</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ count($donaciones) }}</div>
                    <div class="text-sm text-gray-600">Pendientes</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-yellow-600">0</div>
                    <div class="text-sm text-gray-600">Pendientes</div>
                </div>
            </div>

            <!-- Historial -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Historial de Donaciones</h2>

                @if(isset($error))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <p>{{ $error }}</p>
                    </div>
                @endif

                @if(empty($donaciones))
                    <div class="text-center py-10">
                        <i class="fas fa-gift text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-lg font-bold text-gray-700 mb-2">No tienes donaciones</h3>
                        <p class="text-gray-500 mb-6">Crea tu primera solicitud de donación</p>
                        <a href="{{ route('donaciones.crear') }}" class="inline-flex items-center bg-amber-800 hover:bg-amber-900 text-white font-semibold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i>
                            Crear Donación
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($donaciones as $donacion)
                            <div class="donacion-card bg-white p-4 rounded-lg">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-bold text-gray-800">{{ $donacion['nombre_articulo'] }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $donacion['cantidad'] }} {{ $donacion['unidad_articulo'] }} • 
                                            {{ $donacion['categoria_articulo'] }}
                                        </p>
                                    </div>
                                    <span class="status-pendiente status-badge">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pendiente
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mt-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                        <span>{{ date('d/m/Y', strtotime($donacion['fecha'])) }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-hashtag mr-2 text-gray-400"></i>
                                        <span>ID: {{ $donacion['id'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>