<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PerfilDonanteController extends Controller
{
    public function index(Request $request)
    {
        $token = Session::get('access_token');
        
        if ($token) {
            try {
                Log::info('Intentando obtener datos del donante desde API FastAPI...');
                
                $apiUrls = [
                    'http://127.0.0.1:5001/auth/me',
                    'http://localhost:5001/auth/me',
                    'http://127.0.0.1:8000/auth/me',
                    'http://localhost:8000/auth/me'
                ];
                
                foreach ($apiUrls as $url) {
                    try {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $token,
                            'Content-Type' => 'application/json',
                        ])->timeout(5)->get($url);
                        
                        if ($response->successful()) {
                            $apiData = $response->json();
                            Log::info('Datos del donante obtenidos exitosamente de API: ' . $url);
                            
                            // MAPEO CORRECTO PARA DATOS DE API
                            $userData = [
                                'id' => $apiData['id'] ?? '',
                                'nombre' => $apiData['nombre'] ?? '',
                                'apellido_paterno' => $apiData['aP'] ?? '',      // ✅ Campo correcto de API
                                'apellido_materno' => $apiData['aM'] ?? '',      // ✅ Campo correcto de API  
                                'edad' => $apiData['edad'] ?? '',
                                'telefono' => $apiData['telefono'] ?? '',
                                'correo' => $apiData['correo'] ?? '',
                                'rfc' => $apiData['rfc'] ?? '',
                                'pagina_web' => $apiData['paginaWeb'] ?? '',     // ✅ Campo correcto de API
                                'tipo_entidad' => $apiData['tipo'] ?? '',        // ✅ Campo correcto de API
                                'rol' => ['nombre' => ucfirst($apiData['rol']['nombre'] ?? '')],
                                'aprobacion' => (bool) ($apiData['aprobacion'] ?? false),
                                'estatus' => ['nombre' => 'Activo'], // Default para API
                                'created_at' => $apiData['fundacion'] ?? 'No disponible'
                            ];
                            
                            Log::info('Datos mapeados correctamente para la vista: ' . json_encode($userData));
                            return view('perfilDonante', compact('userData'));
                        }
                        
                    } catch (\Exception $e) {
                        Log::warning("Error conectando con API {$url}: " . $e->getMessage());
                        continue;
                    }
                }
                
            } catch (\Exception $e) {
                Log::error('Error general obteniendo datos de donante de API: ' . $e->getMessage());
            }
        }
        
        // Fallback a base de datos si no hay token o API falla
        try {
            Log::info('Intentando obtener datos del donante desde base de datos...');
            
            $db = DB::connection();
            
            $usuario = $db->table('usuarios')
                ->leftJoin('roles', 'usuarios.rol_id', '=', 'roles.id')
                ->leftJoin('estatusG', 'usuarios.estatus_id', '=', 'estatusG.id')
                ->select(
                    'usuarios.*',
                    'roles.nombre as rol_nombre',
                    'estatusG.nombre as estatus_nombre'
                )
                ->where('usuarios.del', 0)
                ->where('roles.nombre', 'donante')
                ->orderBy('usuarios.id', 'desc')
                ->first();
            
            if ($usuario) {
                // MAPEO PARA DATOS DE BASE DE DATOS
                $userData = [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre ?? '',
                    'apellido_paterno' => $usuario->aP ?? '',
                    'apellido_materno' => $usuario->aM ?? '',
                    'edad' => $usuario->edad ?? '',
                    'telefono' => $usuario->telefono ?? '',
                    'correo' => $usuario->correo ?? '',
                    'rfc' => $usuario->rfc ?? '',
                    'pagina_web' => $usuario->paginaWeb ?? '',
                    'tipo_entidad' => $usuario->tipo ?? '',
                    'rol' => ['nombre' => ucfirst($usuario->rol_nombre ?? '')],
                    'aprobacion' => (bool) ($usuario->aprobacion ?? false),
                    'estatus' => ['nombre' => ucfirst($usuario->estatus_nombre ?? '')],
                    'created_at' => $usuario->fundacion ?? 'No disponible'
                ];
                
                Log::info('Datos del donante obtenidos de BD: ' . $usuario->correo);
                return view('perfilDonante', compact('userData'));
            }
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo datos de BD: ' . $e->getMessage());
        }
        
        // Datos simulados como último recurso
        $userData = [
            'nombre' => 'Juan Carlos',
            'apellido_paterno' => 'Mendoza',
            'apellido_materno' => 'Reyes',
            'edad' => 42,
            'telefono' => '5559876543',
            'rfc' => 'MERJ820915ABC',
            'pagina_web' => 'https://fundacion-esperanza.org',
            'correo' => 'juan.mendoza@fundacion.org',
            'tipo_entidad' => 'Persona',
            'rol' => ['nombre' => 'Donante'],
            'aprobacion' => true,
            'estatus' => ['nombre' => 'Activo'],
            'created_at' => '2024-01-01'
        ];
        
        return view('perfilDonante', compact('userData'));
    }
}