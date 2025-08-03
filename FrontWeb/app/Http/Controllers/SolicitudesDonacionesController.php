<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolicitudesDonacionesController extends Controller
{
    /**
     * Mostrar las solicitudes de donaciones pendientes
     */
    public function index()
    {
        try {
            $db = DB::connection();
            
            // Obtener donaciones pendientes de aprobación 
            $solicitudesPendientes = $db->table('donaciones')
                ->leftJoin('usuarios', 'donaciones.usuario_id', '=', 'usuarios.id')
                ->leftJoin('artPresentacion', 'donaciones.articuloP_id', '=', 'artPresentacion.id')
                ->leftJoin('articulos', 'artPresentacion.articulo_id', '=', 'articulos.id')
                ->select(
                    'donaciones.id',
                    'usuarios.nombre',
                    'usuarios.aP as apellido_paterno',
                    'usuarios.aM as apellido_materno',
                    'articulos.nombre as articulo_nombre',
                    'donaciones.cantidad',
                    'donaciones.fecha',
                    'donaciones.aprobacion'
                )
                ->where('donaciones.del', 0) 
                ->where('donaciones.aprobacion', 0) 
                ->orderBy('donaciones.fecha', 'desc')
                ->get();

            Log::info('Solicitudes de donaciones encontradas: ' . count($solicitudesPendientes));
            
            return view('solicitudesDonaciones', compact('solicitudesPendientes'));

        } catch (\Exception $e) {
            Log::error('Error obteniendo solicitudes de donaciones: ' . $e->getMessage());
            $solicitudesPendientes = collect([]);
            return view('solicitudesDonaciones', compact('solicitudesPendientes'));
        }
    }

    /**
     * Aprobar una donación
     */
    public function aprobar(Request $request, $donacionId)
    {
        try {
            $db = DB::connection();
            
            $updated = $db->table('donaciones')
                ->where('id', $donacionId)
                ->where('del', 0) // Cambiado de del_flag a del
                ->update(['aprobacion' => 1]);

            if ($updated) {
                Log::info('Donación aprobada exitosamente', ['donacion_id' => $donacionId]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Donación aprobada exitosamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo aprobar la donación'
            ]);

        } catch (\Exception $e) {
            Log::error('Error aprobando donación: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Rechazar una donación
     */
    public function rechazar(Request $request, $donacionId)
    {
        try {
            $db = DB::connection();
            
            // Marcar como eliminada (soft delete)
            $updated = $db->table('donaciones')
                ->where('id', $donacionId)
                ->update(['del' => 1]); // Cambiado de del_flag a del

            if ($updated) {
                Log::info('Donación rechazada exitosamente', ['donacion_id' => $donacionId]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Donación rechazada exitosamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo rechazar la donación'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rechazando donación: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de donaciones
     */
    public function estadisticas()
    {
        try {
            $db = DB::connection();
            
            $stats = [
                'pendientes' => $db->table('donaciones')
                    ->where('del', 0) // Cambiado de del_flag a del
                    ->where('aprobacion', 0)
                    ->count(),
                'aprobadas' => $db->table('donaciones')
                    ->where('del', 0) // Cambiado de del_flag a del
                    ->where('aprobacion', 1)
                    ->count(),
                'rechazadas' => $db->table('donaciones')
                    ->where('del', 1) // Cambiado de del_flag a del
                    ->count(),
                'total' => $db->table('donaciones')->count()
            ];

            return response()->json([
                'success' => true,
                'estadisticas' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo estadísticas'
            ]);
        }
    }
}