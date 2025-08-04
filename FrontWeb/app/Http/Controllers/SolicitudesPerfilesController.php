<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolicitudesPerfilesController extends Controller
{
    public function index()
    {
        try {
            $db = DB::connection();
            
            // Obtener usuarios pendientes
            $solicitudesPendientes = $db->table('usuarios')
                ->leftJoin('roles', 'usuarios.rol_id', '=', 'roles.id')
                ->select(
                    'usuarios.id',
                    'usuarios.nombre',
                    'usuarios.aP as apellido_paterno',
                    'usuarios.aM as apellido_materno',
                    'usuarios.correo',
                    'roles.nombre as rol_nombre'
                )
                ->where('usuarios.del', 0)
                ->where('usuarios.aprobacion', 0)
                ->whereIn('roles.nombre', ['beneficiario', 'donante'])
                ->get();

            Log::info('Solicitudes encontradas: ' . count($solicitudesPendientes));
            
            return view('solicitudesPerfiles', compact('solicitudesPendientes'));

        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            $solicitudesPendientes = collect([]);
            return view('solicitudesPerfiles', compact('solicitudesPendientes'));
        }
    }

    public function aprobar(Request $request, $userId)
    {
        try {
            $db = DB::connection();
            
            $updated = $db->table('usuarios')
                ->where('id', $userId)
                ->update(['aprobacion' => 1]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Solicitud aprobada exitosamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function rechazar(Request $request, $userId)
    {
        try {
            $db = DB::connection();
            
            $updated = $db->table('usuarios')
                ->where('id', $userId)
                ->update(['del' => 1]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Solicitud rechazada exitosamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}