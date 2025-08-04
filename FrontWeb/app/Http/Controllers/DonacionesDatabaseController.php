<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DonacionesDatabaseController extends Controller
{
    public function checkDonacionesStructure()
    {
        try {
            $db = DB::connection();
            
            // Verificar que las tablas existan
            $tablas = ['donaciones', 'artPresentacion', 'articulos', 'categoriasArt', 'unidades', 'usuarios'];
            $tablasExistentes = [];
            $tablasNoExisten = [];
            
            foreach ($tablas as $tabla) {
                try {
                    $estructura = $db->select("PRAGMA table_info({$tabla})");
                    $tablasExistentes[$tabla] = $estructura;
                } catch (\Exception $e) {
                    $tablasNoExisten[] = $tabla;
                }
            }

            // Contar registros en tablas existentes
            $conteos = [];
            foreach (array_keys($tablasExistentes) as $tabla) {
                try {
                    $conteos[$tabla] = $db->table($tabla)->count();
                } catch (\Exception $e) {
                    $conteos[$tabla] = 'Error: ' . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'tablas_existentes' => array_keys($tablasExistentes),
                'tablas_no_existen' => $tablasNoExisten,
                'estructuras' => $tablasExistentes,
                'conteos' => $conteos,
                'mensaje' => 'Verificación de estructura completada'
            ]);

        } catch (\Exception $e) {
            Log::error('Error verificando estructura de donaciones: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error verificando estructura: ' . $e->getMessage()
            ]);
        }
    }

    public function createBasicCategories()
    {
        try {
            $db = DB::connection();

            $categoriasBasicas = [
                ['nombre' => 'perecederos'],
                ['nombre' => 'no perecederos']
            ];

            $categoriasCreadas = [];

            foreach ($categoriasBasicas as $categoriaData) {
                // Verificar si ya existe
                $existingCategoria = $db->table('categoriasArt')
                    ->where('nombre', $categoriaData['nombre'])
                    ->first();

                if ($existingCategoria) {
                    $categoriasCreadas[] = [
                        'id' => $existingCategoria->id,
                        'nombre' => $existingCategoria->nombre,
                        'status' => 'ya_existia'
                    ];
                    continue;
                }

                // Crear categoría
                $categoriaId = $db->table('categoriasArt')->insertGetId($categoriaData);

                $categoriasCreadas[] = [
                    'id' => $categoriaId,
                    'nombre' => $categoriaData['nombre'],
                    'status' => 'creada'
                ];
            }

            Log::info('Categorías básicas procesadas', ['total' => count($categoriasCreadas)]);

            return response()->json([
                'success' => true,
                'message' => 'Categorías básicas verificadas/creadas exitosamente',
                'categorias' => $categoriasCreadas
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando categorías básicas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creando categorías: ' . $e->getMessage()
            ]);
        }
    }

    public function createBasicUnidades()
    {
        try {
            $db = DB::connection();

            $unidadesBasicas = [
                ['nombre' => 'kilogramo'],
                ['nombre' => 'gramo'],
                ['nombre' => 'litro'],
                ['nombre' => 'militro'],
                ['nombre' => 'pieza'],
                ['nombre' => 'paquete'],
                ['nombre' => 'caja']
            ];

            $unidadesCreadas = [];

            foreach ($unidadesBasicas as $unidadData) {
                // Verificar si ya existe
                $existingUnidad = $db->table('unidades')
                    ->where('nombre', $unidadData['nombre'])
                    ->first();

                if ($existingUnidad) {
                    $unidadesCreadas[] = [
                        'id' => $existingUnidad->id,
                        'nombre' => $existingUnidad->nombre,
                        'status' => 'ya_existia'
                    ];
                    continue;
                }

                // Crear unidad
                $unidadId = $db->table('unidades')->insertGetId($unidadData);

                $unidadesCreadas[] = [
                    'id' => $unidadId,
                    'nombre' => $unidadData['nombre'],
                    'status' => 'creada'
                ];
            }

            Log::info('Unidades básicas procesadas', ['total' => count($unidadesCreadas)]);

            return response()->json([
                'success' => true,
                'message' => 'Unidades básicas verificadas/creadas exitosamente',
                'unidades' => $unidadesCreadas
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando unidades básicas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creando unidades: ' . $e->getMessage()
            ]);
        }
    }

    public function createTestArticulos()
    {
        try {
            $db = DB::connection();

            // Verificar categorías disponibles
            $categorias = $db->table('categoriasArt')->get();
            if ($categorias->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay categorías en la tabla categoriasArt. Cree categorías primero.',
                    'solucion' => 'Use /admin/donaciones/create-categories'
                ]);
            }

            $perecederos = $categorias->where('nombre', 'perecederos')->first();
            $noPerecederos = $categorias->where('nombre', 'no perecederos')->first();

            // Si no existen las categorías básicas, usar las primeras disponibles
            if (!$perecederos) $perecederos = $categorias->first();
            if (!$noPerecederos) $noPerecederos = $categorias->skip(1)->first() ?? $categorias->first();

            // Verificar unidades disponibles
            $unidades = $db->table('unidades')->get();
            if ($unidades->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay unidades en la tabla unidades. Cree unidades primero.',
                    'solucion' => 'Use /admin/donaciones/create-unidades'
                ]);
            }

            // Buscar unidades específicas o usar las disponibles
            $litro = $unidades->where('nombre', 'litro')->first() ?? $unidades->first();
            $kilogramo = $unidades->where('nombre', 'kilogramo')->first() ?? $unidades->skip(1)->first() ?? $unidades->first();
            $pieza = $unidades->where('nombre', 'pieza')->first() ?? $unidades->skip(2)->first() ?? $unidades->first();

            // Definir artículos de prueba
            $articulosPrueba = [
                [
                    'nombre' => 'Leche',
                    'categoria_id' => $perecederos->id,
                    'unidad_id' => $litro->id,
                    'del' => 0
                ],
                [
                    'nombre' => 'Arroz',
                    'categoria_id' => $noPerecederos->id,
                    'unidad_id' => $kilogramo->id,
                    'del' => 0
                ],
                [
                    'nombre' => 'Jabón',
                    'categoria_id' => $noPerecederos->id,
                    'unidad_id' => $pieza->id,
                    'del' => 0
                ],
                [
                    'nombre' => 'Aceite',
                    'categoria_id' => $noPerecederos->id,
                    'unidad_id' => $litro->id,
                    'del' => 0
                ],
                [
                    'nombre' => 'Pan',
                    'categoria_id' => $perecederos->id,
                    'unidad_id' => $pieza->id,
                    'del' => 0
                ],
                [
                    'nombre' => 'Frijoles',
                    'categoria_id' => $noPerecederos->id,
                    'unidad_id' => $kilogramo->id,
                    'del' => 0
                ]
            ];

            $articulosCreados = [];

            foreach ($articulosPrueba as $articuloData) {
                // Verificar si el artículo ya existe
                $existingArticulo = $db->table('articulos')
                    ->where('nombre', $articuloData['nombre'])
                    ->where('del', 0)
                    ->first();

                if ($existingArticulo) {
                    Log::info("Artículo ya existe: {$articuloData['nombre']}");
                    continue;
                }

                // Crear artículo
                $articuloId = $db->table('articulos')->insertGetId([
                    'nombre' => $articuloData['nombre'],
                    'categoria_id' => $articuloData['categoria_id'],
                    'del' => $articuloData['del']
                ]);

                // Crear presentación del artículo
                $presentacionId = $db->table('artPresentacion')->insertGetId([
                    'cantidad' => 1,
                    'del' => 0,
                    'articulo_id' => $articuloId,
                    'unidad_id' => $articuloData['unidad_id']
                ]);

                $unidadNombre = $unidades->where('id', $articuloData['unidad_id'])->first()->nombre ?? 'N/A';

                $articulosCreados[] = [
                    'articulo_id' => $articuloId,
                    'presentacion_id' => $presentacionId,
                    'nombre' => $articuloData['nombre'],
                    'unidad' => $unidadNombre
                ];

                Log::info("Artículo creado: {$articuloData['nombre']} (ID: {$articuloId})");
            }

            return response()->json([
                'success' => true,
                'message' => 'Artículos de prueba procesados exitosamente',
                'articulos_creados' => $articulosCreados,
                'categorias_disponibles' => $categorias->pluck('nombre'),
                'unidades_disponibles' => $unidades->pluck('nombre'),
                'resumen' => [
                    'articulos_nuevos' => count($articulosCreados),
                    'categorias_usadas' => 2,
                    'unidades_usadas' => 3
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando artículos de prueba: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creando artículos: ' . $e->getMessage()
            ]);
        }
    }

    public function createTestDonaciones()
    {
        try {
            $db = DB::connection();

            Log::info('=== INICIANDO CREACIÓN DE DONACIONES CON MÚLTIPLES DONANTES ===');

            // PASO 1: Obtener TODOS los donantes disponibles
            $todosLosDonantes = $this->obtenerTodosLosDonantes($db);
            
            if ($todosLosDonantes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay donantes disponibles. Cree donantes primero.',
                    'solucion' => 'Use /admin/create-test-donante para crear donantes de prueba'
                ]);
            }

            Log::info('Donantes encontrados: ' . count($todosLosDonantes));
            foreach ($todosLosDonantes as $donante) {
                Log::info("- {$donante->nombre} {$donante->aP} {$donante->aM} (ID: {$donante->id})");
            }

            // PASO 2: Verificar artículos con presentaciones
            $articulosPresentaciones = $this->obtenerArticulosConPresentaciones($db);
            
            if ($articulosPresentaciones->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay artículos con presentaciones disponibles.',
                    'solucion' => 'Use /admin/donaciones/create-articulos para crear artículos básicos'
                ]);
            }

            // PASO 3: Verificar estatus
            $estatus = $db->table('estatusG')->first();
            if (!$estatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay estatus disponibles en la tabla estatusG'
                ]);
            }

            // PASO 4: Crear donaciones rotando entre diferentes donantes
            $donacionesCreadas = $this->crearDonacionesConRotacion(
                $db, 
                $todosLosDonantes, 
                $articulosPresentaciones, 
                $estatus
            );

            // PASO 5: Generar estadísticas
            $distribucionDonantes = [];
            foreach ($donacionesCreadas['exitosas'] as $donacion) {
                $nombre = $donacion['donante_nombre'];
                if (!isset($distribucionDonantes[$nombre])) {
                    $distribucionDonantes[$nombre] = 0;
                }
                $distribucionDonantes[$nombre]++;
            }

            return response()->json([
                'success' => true,
                'message' => 'Donaciones de prueba creadas exitosamente con MÚLTIPLES donantes',
                'donaciones_creadas' => $donacionesCreadas['exitosas'],
                'errores' => $donacionesCreadas['errores'],
                'resumen' => [
                    'total_creadas' => count($donacionesCreadas['exitosas']),
                    'total_errores' => count($donacionesCreadas['errores']),
                    'donantes_disponibles' => count($todosLosDonantes),
                    'donantes_utilizados' => count($distribucionDonantes),
                    'distribución_por_donante' => $distribucionDonantes,
                    'articulos_disponibles' => count($articulosPresentaciones)
                ],
                'instrucciones' => [
                    'Ve a /solicitudesDonaciones para ver las donaciones',
                    'Cada fila mostrará un donante diferente',
                    'Puedes aprobar/rechazar cada donación individualmente'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando donaciones de prueba: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear donaciones de prueba: ' . $e->getMessage()
            ]);
        }
    }

    public function getDonacionesCompletas()
    {
        try {
            $db = DB::connection();
            
            $donaciones = $db->table('donaciones')
                ->leftJoin('usuarios', 'donaciones.usuario_id', '=', 'usuarios.id')
                ->leftJoin('artPresentacion', 'donaciones.articuloP_id', '=', 'artPresentacion.id')
                ->leftJoin('articulos', 'artPresentacion.articulo_id', '=', 'articulos.id')
                ->leftJoin('unidades', 'artPresentacion.unidad_id', '=', 'unidades.id')
                ->leftJoin('categoriasArt', 'articulos.categoria_id', '=', 'categoriasArt.id')
                ->select(
                    'donaciones.id',
                    'donaciones.cantidad',
                    'donaciones.tipo_donante',
                    'donaciones.fecha',
                    'donaciones.aprobacion',
                    'donaciones.del',
                    // Datos del donante
                    'usuarios.nombre as donante_nombre',
                    'usuarios.aP as donante_apellido_p',
                    'usuarios.aM as donante_apellido_m',
                    'usuarios.correo as donante_correo',
                    // Datos del artículo
                    'articulos.nombre as articulo_nombre',
                    'unidades.nombre as unidad_nombre',
                    'categoriasArt.nombre as categoria_nombre'
                )
                ->orderBy('donaciones.fecha', 'desc')
                ->get();

            // Clasificar por estado
            $pendientes = $donaciones->where('del', 0)->where('aprobacion', 0);
            $aprobadas = $donaciones->where('del', 0)->where('aprobacion', 1);
            $rechazadas = $donaciones->where('del', 1);

            return response()->json([
                'success' => true,
                'donaciones' => [
                    'todas' => $donaciones,
                    'pendientes' => $pendientes->values(),
                    'aprobadas' => $aprobadas->values(),
                    'rechazadas' => $rechazadas->values()
                ],
                'estadisticas' => [
                    'total' => count($donaciones),
                    'pendientes' => count($pendientes),
                    'aprobadas' => count($aprobadas),
                    'rechazadas' => count($rechazadas)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo donaciones completas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error obteniendo donaciones: ' . $e->getMessage()
            ]);
        }
    }

    public function clearTestDonaciones()
    {
        try {
            $db = DB::connection();
            
            $deleted = $db->table('donaciones')
                ->where('cantidad', '<=', 30) // Filtro para donaciones de prueba
                ->where('aprobacion', 0)      // Solo pendientes
                ->where('del', 0)             // No eliminadas
                ->delete();

            Log::info("Donaciones de prueba eliminadas: {$deleted}");

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$deleted} donaciones de prueba exitosamente"
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminando donaciones de prueba: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error eliminando donaciones de prueba: ' . $e->getMessage()
            ]);
        }
    }

    public function checkAvailableData()
    {
        try {
            $db = DB::connection();
            
            // Verificar donantes
            $donantes = $this->obtenerTodosLosDonantes($db);
            
            // Verificar artículos
            $articulos = $this->obtenerArticulosConPresentaciones($db);

            return response()->json([
                'success' => true,
                'datos_disponibles' => [
                    'donantes' => $donantes,
                    'articulos_con_presentacion' => $articulos
                ],
                'resumen' => [
                    'total_donantes' => count($donantes),
                    'total_articulos' => count($articulos),
                    'listo_para_crear_donaciones' => count($donantes) > 0 && count($articulos) > 0
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error verificando datos disponibles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error verificando datos disponibles: ' . $e->getMessage()
            ]);
        }
    }

    // Métodos privados de soporte

    private function obtenerTodosLosDonantes($db)
    {
        try {
            // Intentar con JOIN a la tabla roles
            return $db->table('usuarios')
                ->leftJoin('roles', 'usuarios.rol_id', '=', 'roles.id')
                ->where('usuarios.del', 0)
                ->where('usuarios.aprobacion', 1)
                ->where('roles.nombre', 'donante')
                ->select('usuarios.id', 'usuarios.nombre', 'usuarios.aP', 'usuarios.aM')
                ->get();
        } catch (\Exception $e) {
            // Fallback sin JOIN si hay problemas con roles
            Log::warning('Error con JOIN a roles, usando fallback: ' . $e->getMessage());
            try {
                return $db->table('usuarios')
                    ->where('usuarios.del', 0)
                    ->where('usuarios.aprobacion', 1)
                    ->where('usuarios.rol_id', 2) // Asumiendo que 2 es donante
                    ->select('usuarios.id', 'usuarios.nombre', 'usuarios.aP', 'usuarios.aM')
                    ->get();
            } catch (\Exception $e2) {
                Log::error('Error obteniendo donantes: ' . $e2->getMessage());
                return collect([]);
            }
        }
    }

    private function obtenerArticulosConPresentaciones($db)
    {
        try {
            return $db->table('artPresentacion')
                ->leftJoin('articulos', 'artPresentacion.articulo_id', '=', 'articulos.id')
                ->leftJoin('unidades', 'artPresentacion.unidad_id', '=', 'unidades.id')
                ->where('artPresentacion.del', 0)
                ->where('articulos.del', 0)
                ->select(
                    'artPresentacion.id as presentacion_id',
                    'articulos.nombre as articulo_nombre',
                    'unidades.nombre as unidad_nombre'
                )
                ->get();
        } catch (\Exception $e) {
            Log::error('Error obteniendo artículos con presentaciones: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function crearDonacionesConRotacion($db, $donantes, $articulos, $estatus)
    {
        // Definir donaciones de prueba con variedad
        $donacionesPrueba = [
            [
                'tipo_donante' => 'Persona Física',
                'cantidad' => 5,
                'fecha' => Carbon::now()->subDays(4)->format('Y-m-d')
            ],
            [
                'tipo_donante' => 'Persona Moral',
                'cantidad' => 12,
                'fecha' => Carbon::now()->subDays(3)->format('Y-m-d')
            ],
            [
                'tipo_donante' => 'Persona Física',
                'cantidad' => 3,
                'fecha' => Carbon::now()->subDays(2)->format('Y-m-d')
            ],
            [
                'tipo_donante' => 'Persona Moral',
                'cantidad' => 25,
                'fecha' => Carbon::now()->subDays(1)->format('Y-m-d')
            ],
            [
                'tipo_donante' => 'Persona Física',
                'cantidad' => 8,
                'fecha' => Carbon::now()->format('Y-m-d')
            ],
            [
                'tipo_donante' => 'Persona Moral',
                'cantidad' => 15,
                'fecha' => Carbon::now()->format('Y-m-d')
            ],
            [
                'tipo_donante' => 'Persona Física',
                'cantidad' => 10,
                'fecha' => Carbon::now()->format('Y-m-d')
            ]
        ];

        $donacionesExitosas = [];
        $errores = [];

        foreach ($donacionesPrueba as $index => $donacionData) {
            try {
                // TÉCNICA DE ROTACIÓN: usar módulo para circular entre donantes
                $donante = $donantes[$index % count($donantes)];
                
                // Seleccionar artículo aleatorio
                $articulo = $articulos->random();

                // Preparar datos completos de la donación
                $donacionCompleta = array_merge($donacionData, [
                    'usuario_id' => $donante->id,
                    'articuloP_id' => $articulo->presentacion_id,
                    'aprobacion' => 0, // Pendiente de aprobación
                    'del' => 0,        // No eliminada
                    'estatus_id' => $estatus->id
                ]);

                // Insertar donación en la base de datos
                $donacionId = $db->table('donaciones')->insertGetId($donacionCompleta);

                $nombreCompleto = trim($donante->nombre . ' ' . ($donante->aP ?? '') . ' ' . ($donante->aM ?? ''));

                $donacionesExitosas[] = [
                    'id' => $donacionId,
                    'donante_nombre' => $nombreCompleto,
                    'donante_id' => $donante->id,
                    'articulo' => $articulo->articulo_nombre ?? 'N/A',
                    'unidad' => $articulo->unidad_nombre ?? 'N/A',
                    'cantidad' => $donacionData['cantidad'],
                    'tipo_donante' => $donacionData['tipo_donante'],
                    'fecha' => $donacionData['fecha']
                ];

                Log::info('Donación creada exitosamente', [
                    'numero' => $index + 1,
                    'donacion_id' => $donacionId,
                    'donante' => $nombreCompleto,
                    'articulo' => $articulo->articulo_nombre ?? 'N/A'
                ]);

            } catch (\Exception $e) {
                $errores[] = "Error creando donación " . ($index + 1) . ": " . $e->getMessage();
                Log::error('Error creando donación de prueba', [
                    'numero' => $index + 1,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'exitosas' => $donacionesExitosas,
            'errores' => $errores
        ];
    }
}