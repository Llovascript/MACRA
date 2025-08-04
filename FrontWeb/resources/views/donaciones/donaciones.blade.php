<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Donación</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .bg-amber-800 { background-color: #92400e; }
        .hover\:bg-amber-900:hover { background-color: #78350f; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-amber-800 text-white p-4 shadow-md sticky top-0 z-10">
            <div class="container mx-auto flex items-center">
                <a href="{{ route('dashboard') }}" class="mr-4 text-white">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-xl font-bold">Nueva Donación</h1>
            </div>
        </div>

        <!-- Contenido -->
        <main class="container mx-auto p-4">
            @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p>{{ session('error') }}</p>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-2">Crear Solicitud de Donación</h2>
                <p class="text-gray-600 mb-6">Selecciona el artículo que deseas donar de los disponibles en nuestro sistema</p>

                <form action="{{ route('donaciones.guardar') }}" method="POST">
                    @csrf

                    <!-- Selector de artículo -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Artículo a Donar *</label>
                        
                        <div x-data="{ open: false, selected: null }" class="relative">
                            <button type="button" @click="open = !open" 
                                class="w-full flex justify-between items-center bg-white border border-gray-300 rounded-lg p-3 text-left focus:outline-none focus:ring-2 focus:ring-amber-500">
                                <span x-text="selected ? selected.nombre : 'Selecciona un artículo'" 
                                      :class="{'text-gray-400': !selected}"></span>
                                <i class="fas fa-chevron-down transition-transform duration-200" 
                                   :class="{'transform rotate-180': open}"></i>
                            </button>
                            
                            <input type="hidden" name="articulo_id" x-model="selected ? selected.id : ''" required>
                            
                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute z-10 w-full mt-1 bg-white rounded-lg shadow-lg max-h-96 overflow-auto border border-gray-300">
                                @foreach($presentaciones as $item)
                                <div @click="selected = {{ json_encode($item) }}; open = false" 
                                     class="px-4 py-2 hover:bg-amber-50 cursor-pointer border-b border-gray-100 last:border-0">
                                    <div class="font-medium text-gray-800">{{ $item['nombre'] }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $item['cantidad'] }} {{ $item['unidad'] }} • {{ $item['categoria'] }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Cantidad a Donar *</label>
                        <input type="number" name="cantidad" min="1"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                               placeholder="Ej: 10, 25, 50" required>
                        <p class="text-sm text-gray-500 mt-1 italic" 
                           x-text="selected ? 'Número de presentaciones de ' + selected.cantidad + ' ' + selected.unidad + ' que deseas donar' : 'Selecciona un artículo primero'"></p>
                    </div>

                    <!-- Submit -->
                    <button type="submit" 
                            class="w-full bg-amber-800 hover:bg-amber-900 text-white font-semibold py-3 px-4 rounded-lg flex items-center justify-center transition duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Crear Solicitud de Donación
                    </button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>