<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // URL base de tu API FastAPI
    private $apiUrl = 'http://localhost:5001';
    private $adminKey = 'admin-secret-2024';
    
    /**
     * Obtener headers con autenticación de admin
     */
    private function getAdminHeaders()
    {
        return [
            'X-Admin-Key' => $this->adminKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    /**
     * Mostrar el menú principal del administrador
     */
    public function index()
    {
        return view('menuAdmin');
    }

    /**
     * Mostrar el menú de gestión de beneficiarios
     */
    public function beneficiariosMenu()
    {
        return view('adminBeneficiarios');
    }

    /**
     * Mostrar el menú de gestión de donantes
     */
    public function donantesMenu()
    {
        return view('adminDonantes');
    }

    /**
     * Obtener estadísticas del dashboard
     */
    public function getEstadisticas()
    {
        try {
            Log::info('Solicitando estadísticas desde Laravel');

            // Usar el nuevo endpoint de estadísticas con headers de autenticación
            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->get($this->apiUrl . '/admin/estadisticas');

            Log::info('Respuesta de estadísticas:', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 200)
            ]);

            if ($response->successful()) {
                $estadisticas = $response->json();
                Log::info('Estadísticas obtenidas exitosamente:', $estadisticas);
                return response()->json($estadisticas);
            } else {
                Log::error('Error al obtener estadísticas:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                // Devolver estadísticas por defecto en caso de error
                return response()->json([
                    'total_usuarios' => 0,
                    'total_beneficiarios' => 0,
                    'total_donantes' => 0,
                    'usuarios_activos' => 0,
                    'usuarios_pendientes' => 0
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas:', [
                'error' => $e->getMessage()
            ]);
            
            // Devolver estadísticas por defecto en caso de error
            return response()->json([
                'total_usuarios' => 0,
                'total_beneficiarios' => 0,
                'total_donantes' => 0,
                'usuarios_activos' => 0,
                'usuarios_pendientes' => 0
            ]);
        }
    }

    /**
     * Obtener usuarios recientes
     */
    public function getUsuariosRecientes()
    {
        try {
            Log::info('Obteniendo usuarios recientes');

            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->get($this->apiUrl . '/admin/usuarios/search', [
                    'limit' => 10,
                    'skip' => 0
                ]);

            if ($response->successful()) {
                $usuarios = $response->json();
                
                // Formatear datos para la vista
                $usuariosFormateados = collect($usuarios)->map(function($usuario) {
                    return [
                        'id' => $usuario['id'],
                        'nombre_completo' => trim($usuario['nombre'] . ' ' . ($usuario['aP'] ?? '') . ' ' . ($usuario['aM'] ?? '')),
                        'correo' => $usuario['correo'],
                        'rol' => $this->getRolNombre($usuario['rol_id']),
                        'estatus' => $this->getEstatusNombre($usuario['estatus_id']),
                        'tipo' => ucfirst($usuario['tipo'])
                    ];
                });

                Log::info('Usuarios recientes obtenidos:', ['count' => $usuariosFormateados->count()]);
                return response()->json($usuariosFormateados);
            } else {
                Log::error('Error al obtener usuarios recientes:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Error al cargar usuarios'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error al obtener usuarios recientes:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Error al cargar usuarios'], 500);
        }
    }

    /**
     * Buscar usuarios por término
     */
    public function buscarUsuarios(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $rol = $request->get('rol', ''); // 'beneficiario', 'donante', o vacío para todos
            
            Log::info('Búsqueda de usuarios desde admin:', [
                'query' => $query,
                'rol' => $rol
            ]);

            $params = [
                'search' => $query,
                'limit' => 20
            ];

            // Filtrar por rol si se especifica
            if ($rol === 'beneficiario') {
                $params['rol_id'] = 3;
            } elseif ($rol === 'donante') {
                $params['rol_id'] = 2;
            }

            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->get($this->apiUrl . '/admin/usuarios/search', $params);

            if ($response->successful()) {
                $usuarios = $response->json();
                
                $resultados = collect($usuarios)->map(function($usuario) {
                    return [
                        'id' => $usuario['id'],
                        'nombre_completo' => trim($usuario['nombre'] . ' ' . ($usuario['aP'] ?? '') . ' ' . ($usuario['aM'] ?? '')),
                        'correo' => $usuario['correo'],
                        'telefono' => $usuario['telefono'],
                        'rol' => $this->getRolNombre($usuario['rol_id']),
                        'estatus' => $this->getEstatusNombre($usuario['estatus_id']),
                        'tipo' => ucfirst($usuario['tipo'])
                    ];
                });

                Log::info('Usuarios encontrados en búsqueda:', ['count' => $resultados->count()]);
                return response()->json($resultados);
            } else {
                Log::error('Error en búsqueda de usuarios:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Error en la búsqueda'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error en búsqueda de usuarios:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Aprobar o rechazar usuario
     */
    public function cambiarAprobacion(Request $request, $id)
    {
        try {
            $aprobacion = $request->get('aprobacion', false);
            $nuevoEstatus = $aprobacion ? 1 : 2; // 1 = activo, 2 = inactivo

            Log::info('Cambiando aprobación de usuario:', [
                'id' => $id,
                'aprobacion' => $aprobacion,
                'nuevoEstatus' => $nuevoEstatus
            ]);

            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->put($this->apiUrl . "/admin/usuarios/{$id}", [
                    'aprobacion' => $aprobacion,
                    'estatus_id' => $nuevoEstatus
                ]);

            if ($response->successful()) {
                Log::info('Aprobación cambiada exitosamente:', ['id' => $id]);
                return response()->json([
                    'success' => true,
                    'message' => $aprobacion ? 'Usuario aprobado exitosamente' : 'Usuario rechazado exitosamente'
                ]);
            } else {
                $errorData = $response->json();
                Log::error('Error al cambiar aprobación:', [
                    'id' => $id,
                    'status' => $response->status(),
                    'error' => $errorData
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $errorData['detail'] ?? 'Error al cambiar estado del usuario'
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Error al cambiar aprobación de usuario:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Métodos auxiliares
     */
    private function getRolNombre($rolId)
    {
        switch ($rolId) {
            case 1:
                return 'Administrador';
            case 2:
                return 'Donante';
            case 3:
                return 'Beneficiario';
            default:
                return 'Desconocido';
        }
    }

    private function getEstatusNombre($estatusId)
    {
        switch ($estatusId) {
            case 1:
                return 'Activo';
            case 2:
                return 'Inactivo';
            case 3:
                return 'Pendiente';
            case 4:
                return 'Aprobado';
            case 5:
                return 'Rechazado';
            default:
                return 'Desconocido';
        }
    }
}