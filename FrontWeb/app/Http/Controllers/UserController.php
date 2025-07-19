<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // URL base de tu API FastAPI - CORREGIDA
    private $apiBaseUrl = 'http://127.0.0.1:5001'; // Cambiado a 127.0.0.1 y puerto 5001

    /**
     * Mostrar el formulario de login y registro
     */
    public function showLoginRegister()
    {
        return view('auth.login');
    }

    /**
     * Procesar login del usuario
     */
    public function login(Request $request)
    {
        try {
            // Log para debug
            Log::info('Intento de login', ['correo' => $request->correo]);

            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'correo' => 'required|email',
                'contraseña' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Log de la URL que se va a usar
            Log::info('Conectando a API', ['url' => $this->apiBaseUrl . '/auth/login']);

            // Hacer petición a la API FastAPI con más configuraciones
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($this->apiBaseUrl . '/auth/login', [
                    'correo' => $request->correo,
                    'contraseña' => $request->contraseña
                ]);

            Log::info('Respuesta de API', [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Guardar token en sesión
                Session::put('access_token', $data['access_token']);
                Session::put('token_type', $data['token_type']);
                
                // Obtener información del usuario
                $userInfo = $this->getUserInfo($data['access_token']);
                
                if ($userInfo) {
                    Session::put('user', $userInfo);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Login exitoso',
                    'access_token' => $data['access_token'],
                    'redirect_url' => route('dashboard')
                ]);
            } else {
                $errorData = $response->json();
                Log::error('Error en login API', ['error' => $errorData]);
                
                return response()->json([
                    'success' => false,
                    'message' => $errorData['detail'] ?? 'Error en el login'
                ], $response->status());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Error de conexión', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'No se puede conectar al servidor de autenticación. Verifica que FastAPI esté ejecutándose en el puerto 5001.'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error general', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Procesar registro del usuario
     */
    public function register(Request $request)
    {
        try {
            Log::info('Intento de registro', ['correo' => $request->correo]);

            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'correo' => 'required|email|max:255',
                'contraseña' => 'required|min:6',
                'rol_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Hacer petición a la API FastAPI
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($this->apiBaseUrl . '/auth/register', [
                    'nombre' => $request->nombre,
                    'correo' => $request->correo,
                    'contraseña' => $request->contraseña,
                    'rol_id' => $request->rol_id,
                    'estatus_id' => 1 // Valor por defecto
                ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuario registrado exitosamente'
                ]);
            } else {
                $errorData = $response->json();
                Log::error('Error en registro API', ['error' => $errorData]);
                
                return response()->json([
                    'success' => false,
                    'message' => $errorData['detail'] ?? 'Error en el registro'
                ], $response->status());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Error de conexión en registro', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'No se puede conectar al servidor de autenticación'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error general en registro', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Obtener información del usuario autenticado
     */
    private function getUserInfo($accessToken)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken
            ])->get($this->apiBaseUrl . '/auth/me');

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Error obteniendo info de usuario', ['error' => $e->getMessage()]);
        }
        
        return null;
    }

    /**
     * Cerrar sesión del usuario
     */
    public function logout(Request $request)
    {
        Session::forget(['access_token', 'token_type', 'user']);
        Session::flush();
        
        return redirect()->route('login.register')->with('message', 'Sesión cerrada exitosamente');
    }

    /**
     * Mostrar dashboard
     */
    public function dashboard()
    {
        // Verificar si el usuario está autenticado
        if (!Session::has('access_token')) {
            return redirect()->route('login.register');
        }

        $user = Session::get('user');
        return view('dashboard', compact('user'));
    }
}
