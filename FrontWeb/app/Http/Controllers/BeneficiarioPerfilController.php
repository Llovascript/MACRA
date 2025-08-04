<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BeneficiarioPerfilController extends Controller
{
    public function index(Request $request)
    {
        $token = Session::get('access_token');
        
        if ($token) {
            try {
                Log::info('Intentando obtener datos del usuario desde API FastAPI...');
                
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
                            Log::info('Datos obtenidos exitosamente de API: ' . $url);
                            
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
                            
                            return view('perfilBeneficiario', compact('userData'));
                        }
                        
                    } catch (\Exception $e) {
                        Log::warning("Error conectando con API {$url}: " . $e->getMessage());
                        continue;
                    }
                }
                
            } catch (\Exception $e) {
                Log::error('Error general obteniendo datos de API: ' . $e->getMessage());
            }
        }

        // Fallback a base de datos si no hay token o API falla
        try {
            Log::info('Intentando obtener datos del usuario desde base de datos...');
            
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
                ->where('roles.nombre', 'beneficiario')
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
                
                Log::info('Datos obtenidos exitosamente de la base de datos para usuario: ' . $usuario->correo);
                return view('perfilBeneficiario', compact('userData'));
            } else {
                Log::warning('No se encontró ningún usuario beneficiario en la base de datos');
            }
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo datos de la base de datos: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
        
        // Datos simulados como último recurso
        Log::info('Usando datos simulados - No se encontraron datos reales');
        
        $userData = [
            'nombre' => 'Usuario',
            'apellido_paterno' => 'de',
            'apellido_materno' => 'Prueba',
            'edad' => 25,
            'telefono' => '1234567890',
            'rfc' => 'ABCD123456XYZ',
            'pagina_web' => 'https://ejemplo.com',
            'correo' => 'usuario@ejemplo.com',
            'tipo_entidad' => 'Persona Física',
            'rol' => ['nombre' => 'Beneficiario'],
            'aprobacion' => true,
            'estatus' => ['nombre' => 'Activo'],
            'created_at' => '2024-01-01'
        ];
        
        return view('perfilBeneficiario', compact('userData'));
    }
}