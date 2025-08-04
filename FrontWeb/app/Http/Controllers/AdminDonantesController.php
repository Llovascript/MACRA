<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminDonantesController extends Controller
{
    private $apiBaseUrl;
    
    public function __construct()
    {
        $this->apiBaseUrl = env('API_BASE_URL', 'http://127.0.0.1:5001');
    }

    /**
     * Obtener token de sesión del usuario autenticado
     */
    private function getAuthToken()
    {
        return Session::get('access_token');
    }

    /**
     * Mostrar la vista principal de administración de donantes
     */
    public function showAdminDonantes()
    {
        return view('adminDonantes');
    }

    /**
     * Mostrar la vista de agregar donante
     */
    public function showAgregarDonante()
    {
        return view('agregarDonante');
    }

    /**
     * Crear un nuevo donante
     */
    public function agregarDonante(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido_paterno' => 'required|string|max:255',
                'apellido_materno' => 'required|string|max:255',
                'email' => 'required|email',
                'password' => 'required|string|min:6|confirmed',
                'edad' => 'required|integer|min:18|max:100',
                'telefono' => 'required|string',
                'tipo_entidad' => 'required|in:persona,institucion',
                'tipo_perfil' => 'required|string',
                'rfc' => 'nullable|string|max:13',
                'sitio_web' => 'nullable|url'
            ]);

            $token = $this->getAuthToken();
            if (!$token) {
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => 'Sesión Expirada',
                    'message' => 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.'
                ]);
            }

            $userData = [
                'tipo' => $request->tipo_entidad,
                'nombre' => $request->nombre,
                'aP' => $request->apellido_paterno,
                'aM' => $request->apellido_materno,
                'correo' => $request->email,
                'contraseña' => $request->password,
                'edad' => (int)$request->edad,
                'telefono' => $request->telefono,
                'rfc' => $request->rfc,
                'paginaWeb' => $request->sitio_web,
                'rol_id' => 2,
                'estatus_id' => 1,
                'aprobacion' => true,
                'del_flag' => false
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->apiBaseUrl . '/usuarios/', $userData);

            if ($response->successful()) {
                return back()->with('notification', [
                    'type' => 'success',
                    'title' => 'Donante Agregado',
                    'message' => 'El donante ' . $request->nombre . ' ' . $request->apellido_paterno . ' ha sido agregado exitosamente.',
                    'redirect' => route('adminDonantes')
                ]);
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['detail'] ?? 'Error desconocido al agregar donante';
                
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => 'Error al Agregar Donante',
                    'message' => $errorMessage
                ])->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = collect($e->errors())->flatten()->implode(', ');
            return back()->with('notification', [
                'type' => 'error',
                'title' => 'Error de Validación',
                'message' => $errors
            ])->withInput();
        } catch (\Exception $e) {
            return back()->with('notification', [
                'type' => 'error',
                'title' => 'Error del Servidor',
                'message' => 'Ha ocurrido un error interno. Por favor intenta nuevamente.'
            ])->withInput();
        }
    }

    /**
     * Mostrar la vista de actualizar donante
     */
    public function showActualizarDonante()
    {
        return view('actualizarDonante');
    }

    /**
     * Buscar donante por nombre para actualizar
     */
    public function buscarDonanteParaActualizar(Request $request)
    {
        try {
            $request->validate(['buscar_usuario' => 'required|string']);

            $token = $this->getAuthToken();
            if (!$token) {
                return response()->json([
                    'error' => true,
                    'type' => 'error',
                    'title' => 'Sesión Expirada',
                    'message' => 'Tu sesión ha expirado'
                ], 401);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($this->apiBaseUrl . '/usuarios/');

            if ($response->successful()) {
                $usuarios = $response->json();
                $termino = strtolower($request->buscar_usuario);
                
                $donantesEncontrados = array_filter($usuarios, function($usuario) use ($termino) {
                    $esRolCorrecto = isset($usuario['rol_id']) && $usuario['rol_id'] == 2;
                    $noEliminado = isset($usuario['del_flag']) && $usuario['del_flag'] == false;
                    
                    if (!$esRolCorrecto || !$noEliminado) {
                        return false;
                    }
                    
                    $coincideNombre = isset($usuario['nombre']) && stripos($usuario['nombre'], $termino) !== false;
                    $coincidePaterno = isset($usuario['aP']) && stripos($usuario['aP'], $termino) !== false;
                    $coincideMaterno = isset($usuario['aM']) && stripos($usuario['aM'], $termino) !== false;
                    $coincideEmail = isset($usuario['correo']) && stripos($usuario['correo'], $termino) !== false;
                    
                    return $coincideNombre || $coincidePaterno || $coincideMaterno || $coincideEmail;
                });

                if (empty($donantesEncontrados)) {
                    return response()->json([
                        'error' => true,
                        'type' => 'warning',
                        'title' => 'Sin Resultados',
                        'message' => 'No se encontró ningún donante con el término: "' . $request->buscar_usuario . '"'
                    ], 404);
                }

                $donante = array_values($donantesEncontrados)[0];
                return response()->json(['donante' => $donante]);

            } else {
                return response()->json([
                    'error' => true,
                    'type' => 'error',
                    'title' => 'Error de Conexión',
                    'message' => 'No se pudo conectar con el servidor'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'type' => 'error',
                'title' => 'Error del Servidor',
                'message' => 'Ha ocurrido un error interno'
            ], 500);
        }
    }

    /**
     * Actualizar donante
     */
    public function actualizarDonante(Request $request)
    {
        try {
            $request->validate([
                'usuario_id' => 'required|integer',
                'nombre' => 'required|string|max:255',
                'apellido_paterno' => 'required|string|max:255',
                'apellido_materno' => 'required|string|max:255',
                'email' => 'required|email',
                'edad' => 'required|integer|min:18|max:100',
                'telefono' => 'required|string',
                'tipo_entidad' => 'required|in:persona,institucion',
                'rfc' => 'nullable|string|max:13',
                'sitio_web' => 'nullable|url'
            ]);

            $token = $this->getAuthToken();
            if (!$token) {
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => 'Sesión Expirada',
                    'message' => 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.'
                ]);
            }

            $updateData = [
                'tipo' => $request->tipo_entidad,
                'nombre' => $request->nombre,
                'aP' => $request->apellido_paterno,
                'aM' => $request->apellido_materno,
                'correo' => $request->email,
                'edad' => (int)$request->edad,
                'telefono' => $request->telefono,
                'rfc' => $request->rfc,
                'paginaWeb' => $request->sitio_web
            ];

            if ($request->filled('password')) {
                $updateData['contraseña'] = $request->password;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->put($this->apiBaseUrl . '/usuarios/' . $request->usuario_id, $updateData);

            if ($response->successful()) {
                return back()->with('notification', [
                    'type' => 'success',
                    'title' => 'Donante Actualizado',
                    'message' => 'Los datos del donante ' . $request->nombre . ' ' . $request->apellido_paterno . ' han sido actualizados exitosamente.',
                    'redirect' => route('adminDonantes')
                ]);
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['detail'] ?? 'Error desconocido al actualizar donante';
                
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => 'Error al Actualizar',
                    'message' => $errorMessage
                ])->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = collect($e->errors())->flatten()->implode(', ');
            return back()->with('notification', [
                'type' => 'error',
                'title' => 'Error de Validación',
                'message' => $errors
            ])->withInput();
        } catch (\Exception $e) {
            return back()->with('notification', [
                'type' => 'error',
                'title' => 'Error del Servidor',
                'message' => 'Ha ocurrido un error interno. Por favor intenta nuevamente.'
            ])->withInput();
        }
    }

    /**
     * Mostrar la vista de eliminar donante
     */
    public function showEliminarDonante()
    {
        return view('eliminarDonante');
    }

    /**
     * Buscar donante por nombre para eliminar
     */
    public function buscarDonanteParaEliminar(Request $request)
    {
        try {
            $request->validate(['buscar_usuario' => 'required|string']);

            $token = $this->getAuthToken();
            if (!$token) {
                return response()->json([
                    'error' => true,
                    'type' => 'error',
                    'title' => 'Sesión Expirada',
                    'message' => 'Tu sesión ha expirado'
                ], 401);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($this->apiBaseUrl . '/usuarios/');

            if ($response->successful()) {
                $usuarios = $response->json();
                $termino = strtolower($request->buscar_usuario);
                
                $donantesEncontrados = array_filter($usuarios, function($usuario) use ($termino) {
                    $esRolCorrecto = isset($usuario['rol_id']) && $usuario['rol_id'] == 2;
                    $noEliminado = isset($usuario['del_flag']) && $usuario['del_flag'] == false;
                    
                    if (!$esRolCorrecto || !$noEliminado) {
                        return false;
                    }
                    
                    $coincideNombre = isset($usuario['nombre']) && stripos($usuario['nombre'], $termino) !== false;
                    $coincidePaterno = isset($usuario['aP']) && stripos($usuario['aP'], $termino) !== false;
                    $coincideMaterno = isset($usuario['aM']) && stripos($usuario['aM'], $termino) !== false;
                    $coincideEmail = isset($usuario['correo']) && stripos($usuario['correo'], $termino) !== false;
                    
                    return $coincideNombre || $coincidePaterno || $coincideMaterno || $coincideEmail;
                });

                if (empty($donantesEncontrados)) {
                    return response()->json([
                        'error' => true,
                        'type' => 'warning',
                        'title' => 'Sin Resultados',
                        'message' => 'No se encontró ningún donante con el término: "' . $request->buscar_usuario . '"'
                    ], 404);
                }

                $donante = array_values($donantesEncontrados)[0];
                $donante['contraseña'] = '••••••••••••';
                
                return response()->json(['donante' => $donante]);

            } else {
                return response()->json([
                    'error' => true,
                    'type' => 'error',
                    'title' => 'Error de Conexión',
                    'message' => 'No se pudo conectar con el servidor'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'type' => 'error',
                'title' => 'Error del Servidor',
                'message' => 'Ha ocurrido un error interno'
            ], 500);
        }
    }

    /**
     * Eliminar donante
     */
    public function eliminarDonante(Request $request)
    {
        try {
            $request->validate(['usuario_id' => 'required|integer']);

            $token = $this->getAuthToken();
            if (!$token) {
                return response()->json([
                    'error' => true,
                    'type' => 'error',
                    'title' => 'Sesión Expirada',
                    'message' => 'Tu sesión ha expirado'
                ], 401);
            }

            // Obtener datos del donante antes de eliminarlo
            $getUserResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($this->apiBaseUrl . '/usuarios/' . $request->usuario_id);

            $nombreDonante = 'el donante';
            if ($getUserResponse->successful()) {
                $userData = $getUserResponse->json();
                $nombreDonante = ($userData['nombre'] ?? '') . ' ' . ($userData['aP'] ?? '');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->delete($this->apiBaseUrl . '/usuarios/' . $request->usuario_id);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'type' => 'success',
                    'title' => 'Donante Eliminado',
                    'message' => $nombreDonante . ' ha sido eliminado exitosamente del sistema.',
                    'redirect' => route('adminDonantes')
                ]);
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['detail'] ?? 'Error desconocido al eliminar donante';
                
                return response()->json([
                    'error' => true,
                    'type' => 'error',
                    'title' => 'Error al Eliminar',
                    'message' => $errorMessage
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'type' => 'error',
                'title' => 'Error del Servidor',
                'message' => 'Ha ocurrido un error interno. Por favor intenta nuevamente.'
            ], 500);
        }
    }
}