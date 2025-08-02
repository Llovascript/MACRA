<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DonacionesDatabaseController extends Controller
{
    /**
     * Verificar estructura de tablas relacionadas con donaciones
     */
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
            return response()->json([
                'success' => false,
                'error' => 'Error verificando estructura: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Crear categorías básicas si no existen
     */
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

            return response()->json([
                'success' => true,
                'message' => 'Categorías básicas verificadas/creadas exitosamente',
                'categorias' => $categoriasCreadas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creando categorías: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Crear unidades básicas si no existen
     */
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

            return response()->json([
                'success' => true,
                'message' => 'Unidades básicas verificadas/creadas exitosamente',
                'unidades' => $unidadesCreadas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creando unidades: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Crear donaciones de prueba realistas
     */
    public function createTestDonaciones()
    {
        try {
            $db = DB::connection();

            // Verificar que existan donantes
            $donantes = [];
            try {
                $donantes = $db->table('usuarios')
                    ->leftJoin('roles', 'usuarios.rol_id', '=', 'roles.id')
                    ->where('usuarios.del', 0)
                    ->where('usuarios.aprobacion', 1)
                    ->where('roles.nombre', 'donante')
                    ->select('usuarios.id', 'usuarios.nombre', 'usuarios.aP', 'usuarios.aM')
                    ->get();
            } catch (\Exception $e) {
                // Fallback sin JOIN
                $donantes = $db->table('usuarios')
                    ->where('usuarios.del', 0)
                    ->where('usuarios.aprobacion', 1)
                    ->where('usuarios.rol_id', 2) // Asumiendo que 2 es donante
                    ->select('usuarios.id', 'usuarios.nombre', 'usuarios.aP', 'usuarios.aM')
                    ->get();
            }

            if ($donantes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay donantes disponibles. Cree donantes primero usando /admin/create-test-donante',
                    'solucion' => 'Vaya a /admin/create-test-donante para crear un donante de prueba'
                ]);
            }

            // Verificar que existan artículos con presentaciones
            $articulosPresentaciones = [];
            try {
                $articulosPresentaciones = $db->table('artPresentacion')
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
                return response()->json([
                    'success' => false,
                    'message' => 'Error accediendo a artículos: ' . $e->getMessage(),
                    'solucion' => 'Verifique que las tablas artPresentacion, articulos y unidades existan'
                ]);
            }

            if ($articulosPresentaciones->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay artículos con presentaciones disponibles.',
                    'solucion' => 'Use /admin/donaciones/create-articulos para crear artículos básicos'
                ]);
            }

            // Verificar que exista al menos un estatus
            $estatus = [];
            try {
                $estatus = $db->table('estatusG')->first();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede acceder a la tabla estatusG: ' . $e->getMessage()
                ]);
            }

            if (!$estatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay estatus disponibles en la tabla estatusG'
                ]);
            }

            // Donaciones de prueba realistas (CON COLUMNAS CORRECTAS)
            $donacionesPrueba = [
                [
                    'tipo_donante' => 'Persona Física',
                    'cantidad' => 5,
                    'fecha' => Carbon::now()->subDays(3)->format('Y-m-d'),
                    'aprobacion' => 0,
                    'del' => 0,
                    'estatus_id' => $estatus->id
                ],
                [
                    'tipo_donante' => 'Persona Moral',
                    'cantidad' => 10,
                    'fecha' => Carbon::now()->subDays(2)->format('Y-m-d'),
                    'aprobacion' => 0,
                    'del' => 0,
                    'estatus_id' => $estatus->id
                ],
                [
                    'tipo_donante' => 'Persona Física',
                    'cantidad' => 20,
                    'fecha' => Carbon::now()->subDays(1)->format('Y-m-d'),
                    'aprobacion' => 0,
                    'del' => 0,
                    'estatus_id' => $estatus->id
                ],
                [
                    'tipo_donante' => 'Persona Moral',
                    'cantidad' => 15,
                    'fecha' => Carbon::now()->format('Y-m-d'),
                    'aprobacion' => 0,
                    'del' => 0,
                    'estatus_id' => $estatus->id
                ],
                [
                    'tipo_donante' => 'Persona Física',
                    'cantidad' => 8,
                    'fecha' => Carbon::now()->format('Y-m-d'),
                    'aprobacion' => 0,
                    'del' => 0,
                    'estatus_id' => $estatus->id
                ]
            ];

            $donacionesCreadas = [];
            $errores = [];

            foreach ($donacionesPrueba as $index => $donacionData) {
                try {
                    // Seleccionar donante y artículo aleatorio
                    $donante = $donantes->random();
                    $articulo = $articulosPresentaciones->random();

                    $donacionCompleta = array_merge($donacionData, [
                        'usuario_id' => $donante->id,
                        'articuloP_id' => $articulo->presentacion_id
                    ]);

                    $donacionId = $db->table('donaciones')->insertGetId($donacionCompleta);

                    $donacionesCreadas[] = [
                        'id' => $donacionId,
                        'donante_nombre' => $donante->nombre . ' ' . ($donante->aP ?? '') . ' ' . ($donante->aM ?? ''),
                        'articulo' => $articulo->articulo_nombre ?? 'N/A',
                        'unidad' => $articulo->unidad_nombre ?? 'N/A',
                        'cantidad' => $donacionData['cantidad'],
                        'tipo_donante' => $donacionData['tipo_donante'],
                        'fecha' => $donacionData['fecha']
                    ];

                    Log::info('Donación de prueba creada', [
                        'donacion_id' => $donacionId,
                        'donante' => $donante->nombre,
                        'articulo' => $articulo->articulo_nombre ?? 'N/A'
                    ]);

                } catch (\Exception $e) {
                    $errores[] = "Error creando donación {$index}: " . $e->getMessage();
                    Log::error("Error creando donación de prueba {$index}", ['error' => $e->getMessage()]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Donaciones de prueba creadas exitosamente',
                'donaciones_creadas' => $donacionesCreadas,
                'errores' => $errores,
                'resumen' => [
                    'total_creadas' => count($donacionesCreadas),
                    'total_errores' => count($errores),
                    'donantes_disponibles' => count($donantes),
                    'articulos_disponibles' => count($articulosPresentaciones)
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

    /**
     * Crear artículos y presentaciones de prueba si no existen
     */
    public function createTestArticulos()
    {
        try {
            $db = DB::connection();

            // Verificar categorías
            $categorias = $db->table('categoriasArt')->get();
            if ($categorias->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay categorías en la tabla categoriasArt. Cree categorías primero.'
                ]);
            }

            $perecederos = $categorias->where('nombre', 'perecederos')->first();
            $noPerecederos = $categorias->where('nombre', 'no perecederos')->first();

            // Si no existen las categorías básicas, usar las primeras disponibles
            if (!$perecederos) $perecederos = $categorias->first();
            if (!$noPerecederos) $noPerecederos = $categorias->skip(1)->first() ?? $categorias->first();

            // Verificar unidades
            $unidades = $db->table('unidades')->get();
            if ($unidades->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay unidades en la tabla unidades. Cree unidades primero.'
                ]);
            }

            // Buscar unidades específicas o usar las disponibles
            $litro = $unidades->where('nombre', 'litro')->first() ?? $unidades->first();
            $kilogramo = $unidades->where('nombre', 'kilogramo')->first() ?? $unidades->skip(1)->first() ?? $unidades->first();
            $pieza = $unidades->where('nombre', 'pieza')->first() ?? $unidades->skip(2)->first() ?? $unidades->first();

            // Artículos de prueba
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
                ]
            ];

            $articulosCreados = [];

            foreach ($articulosPrueba as $articuloData) {
                // Verificar si ya existe
                $existingArticulo = $db->table('articulos')
                    ->where('nombre', $articuloData['nombre'])
                    ->where('del', 0)
                    ->first();

                if ($existingArticulo) {
                    continue;
                }

                // Crear artículo
                $articuloId = $db->table('articulos')->insertGetId([
                    'nombre' => $articuloData['nombre'],
                    'categoria_id' => $articuloData['categoria_id'],
                    'del' => $articuloData['del']
                ]);

                // Crear presentación
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
            }

            return response()->json([
                'success' => true,
                'message' => 'Artículos de prueba creados exitosamente',
                'articulos_creados' => $articulosCreados,
                'categorias_disponibles' => $categorias,
                'unidades_disponibles' => $unidades
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creando artículos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener donaciones con información completa para testing
     */
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

            // Separar por estado
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
            return response()->json([
                'success' => false,
                'error' => 'Error obteniendo donaciones: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Limpiar donaciones de prueba
     */
    public function clearTestDonaciones()
    {
        try {
            $db = DB::connection();
            
            $deleted = $db->table('donaciones')
                ->where('cantidad', '<=', 20) // Eliminar donaciones de prueba basándose en cantidad pequeña
                ->where('aprobacion', 0)
                ->where('del', 0)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$deleted} donaciones de prueba"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error eliminando donaciones de prueba: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Verificar datos disponibles para crear donaciones de prueba
     */
    public function checkAvailableData()
    {
        try {
            $db = DB::connection();
            
            // Verificar usuarios donantes
            $donantes = [];
            try {
                $donantes = $db->table('usuarios')
                    ->leftJoin('roles', 'usuarios.rol_id', '=', 'roles.id')
                    ->select('usuarios.id', 'usuarios.nombre', 'usuarios.aP', 'usuarios.aM', 'roles.nombre as rol')
                    ->where('usuarios.del', 0)
                    ->where('usuarios.aprobacion', 1)
                    ->where('roles.nombre', 'donante')
                    ->get();
            } catch (\Exception $e) {
                // Si hay error con roles, intentar sin JOIN
                try {
                    $donantes = $db->table('usuarios')
                        ->select('usuarios.id', 'usuarios.nombre', 'usuarios.aP', 'usuarios.aM', 'usuarios.rol_id')
                        ->where('usuarios.del', 0)
                        ->where('usuarios.aprobacion', 1)
                        ->where('usuarios.rol_id', 2) // Asumiendo que 2 es donante
                        ->get();
                } catch (\Exception $e2) {
                    $donantes = collect([]);
                }
            }

            // Verificar artículos con presentaciones
            $articulos = [];
            try {
                $articulos = $db->table('artPresentacion')
                    ->leftJoin('articulos', 'artPresentacion.articulo_id', '=', 'articulos.id')
                    ->leftJoin('categoriasArt', 'articulos.categoria_id', '=', 'categoriasArt.id')
                    ->leftJoin('unidades', 'artPresentacion.unidad_id', '=', 'unidades.id')
                    ->select(
                        'artPresentacion.id as presentacion_id',
                        'articulos.id as articulo_id',
                        'articulos.nombre as articulo_nombre',
                        'categoriasArt.nombre as categoria',
                        'unidades.nombre as unidad',
                        'artPresentacion.cantidad as cantidad_presentacion'
                    )
                    ->where('artPresentacion.del', 0)
                    ->where('articulos.del', 0)
                    ->get();
            } catch (\Exception $e) {
                $articulos = collect([]);
            }

            return response()->json([
                'success' => true,
                'datos_disponibles' => [
                    'donantes' => $donantes,
                    'articulos_con_presentacion' => $articulos
                ],
                'resumen' => [
                    'total_donantes' => count($donantes),
                    'total_articulos' => count($articulos)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error verificando datos disponibles: ' . $e->getMessage()
            ]);
        }
    }
}