<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // URL base de tu API FastAPI
    private $apiBaseUrl = 'http://127.0.0.1:5001';

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
            Log::info('Intento de login', ['correo' => $request->correo]);

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

            // Hacer petición a la API FastAPI
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
                
                Session::put('access_token', $data['access_token']);
                Session::put('token_type', $data['token_type']);
                
                $userInfo = $this->getUserInfo($data['access_token']);
                
                if ($userInfo) {
                    Session::put('user', $userInfo);
                    
                    // Determinar la URL de redirección basada en el rol
                    $redirectUrl = $this->getRedirectUrlByRole($userInfo);
                    
                    Log::info('Login exitoso', [
                        'correo' => $request->correo,
                        'rol_id' => $userInfo['rol_id'] ?? 'no definido',
                        'redirect_url' => $redirectUrl
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Login exitoso',
                        'access_token' => $data['access_token'],
                        'redirect_url' => $redirectUrl,
                        'user_role' => $userInfo['rol_id'] ?? null
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Login exitoso',
                        'access_token' => $data['access_token'],
                        'redirect_url' => route('dashboard')
                    ]);
                }
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
                'message' => 'No se puede conectar al servidor de autenticación. Verifica que FastAPI esté ejecutándose.'
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
     * Determinar URL de redirección basada en el rol del usuario
     */
    private function getRedirectUrlByRole($userInfo)
    {
        $rolId = $userInfo['rol_id'] ?? null;

        switch ($rolId) {
            case 1: // Administrador
                return route('admin.menu');
            case 2: // Donante
                return route('donante.menu');
            case 3: // Beneficiario
                return route('beneficiario.menu');
            default:
                return route('dashboard');
        }
    }

    /**
     * Procesar registro del usuario
     */
    public function register(Request $request)
    {
        try {
            Log::info('Intento de registro', [
                'correo' => $request->correo,
                'nombre' => $request->nombre,
                'perfil_seleccionado' => $request->perfil
            ]);

            $validator = Validator::make($request->all(), [
                // Campos obligatorios
                'nombre' => 'required|string|max:255',
                'apellido_paterno' => 'required|string|max:255',
                'correo' => 'required|email|max:255',
                'contraseña' => 'required|min:6',
                'confirmar_contraseña' => 'required|same:contraseña',
                'tipo_entidad' => 'required|in:individual,organizacion,persona',
                'perfil' => 'required|in:donante,beneficiario',
                
                // Campos opcionales
                'apellido_materno' => 'nullable|string|max:255',
                'pagina_web' => 'nullable|url|max:255',
                'edad' => 'nullable|integer|min:18|max:120',
                'telefono' => 'nullable|string|max:15|regex:/^[0-9]+$/',
                'rfc' => 'nullable|string|min:10|max:13|regex:/^[A-Z0-9]+$/',
            ], [
                'nombre.required' => 'El nombre es obligatorio',
                'apellido_paterno.required' => 'El apellido paterno es obligatorio',
                'correo.required' => 'El correo electrónico es obligatorio',
                'correo.email' => 'El correo electrónico debe tener un formato válido',
                'contraseña.required' => 'La contraseña es obligatoria',
                'contraseña.min' => 'La contraseña debe tener al menos 6 caracteres',
                'confirmar_contraseña.required' => 'Debes confirmar tu contraseña',
                'confirmar_contraseña.same' => 'Las contraseñas no coinciden',
                'tipo_entidad.required' => 'Debes seleccionar un tipo de entidad',
                'perfil.required' => 'Debes seleccionar un tipo de perfil',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Preparar datos para enviar a FastAPI
            $registrationData = [
                'nombre' => $request->nombre,
                'aP' => $request->apellido_paterno,
                'aM' => $request->apellido_materno,
                'correo' => $request->correo,
                'contraseña' => $request->contraseña,
                'tipo' => $request->tipo_entidad,
                'rol_id' => $this->mapPerfilToRolId($request->perfil),
                'estatus_id' => 1,
                'aprobacion' => 1,
                'del' => 0,
                'paginaWeb' => $request->pagina_web,
                'edad' => $request->edad ? (int)$request->edad : null,
                'telefono' => $request->telefono,
                'rfc' => $request->rfc ? strtoupper($request->rfc) : null,
                'fundacion' => null,
                'direccion_id' => null
            ];

            // Remover campos nulos excepto los permitidos
            $filteredData = [];
            foreach ($registrationData as $key => $value) {
                if ($value !== null && $value !== '') {
                    $filteredData[$key] = $value;
                } elseif (in_array($key, ['aM', 'paginaWeb', 'edad', 'telefono', 'rfc', 'fundacion', 'direccion_id'])) {
                    $filteredData[$key] = $value;
                }
            }

            // Hacer petición a la API FastAPI
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($this->apiBaseUrl . '/auth/register', $filteredData);

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('Registro exitoso', [
                    'correo' => $request->correo,
                    'nombre' => $request->nombre,
                    'rol_asignado' => $rolId,
                    'perfil' => $request->perfil
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Usuario registrado exitosamente. Ya puedes iniciar sesión.',
                    'data' => $responseData
                ]);
            } else {
                $errorData = $response->json();
                Log::error('Error en registro API', [
                    'status' => $response->status(),
                    'error' => $errorData
                ]);
                
                $errorMessage = 'Error en el registro';
                if (isset($errorData['detail'])) {
                    if (is_array($errorData['detail'])) {
                        $errorMessage = $errorData['detail'][0]['msg'] ?? 'Error de validación';
                    } else {
                        $errorMessage = $errorData['detail'];
                    }
                } elseif (isset($errorData['message'])) {
                    $errorMessage = $errorData['message'];
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], $response->status());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Error de conexión en registro', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'No se puede conectar al servidor de autenticación.'
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
     * Mapear el perfil seleccionado al rol_id correspondiente
     */
    private function mapPerfilToRolId($perfil)
    {
        switch (strtolower(trim($perfil))) {
            case 'donante':
                return 2;
            case 'beneficiario':
                return 3;
            default:
                return 1;
        }
    }

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

    public function logout(Request $request)
    {
        Session::forget(['access_token', 'token_type', 'user']);
        Session::flush();
        
        return redirect()->route('login')->with('message', 'Sesión cerrada exitosamente');
    }

    /**
     * Método alternativo de logout por GET
     */
    public function logoutGet(Request $request)
    {
        return $this->logout($request);
    }

    /**
     * Mostrar dashboard genérico
     */
    public function dashboard()
    {
        if (!Session::has('access_token')) {
            return redirect()->route('login');
        }

        $user = Session::get('user');
        return view('dashboard', compact('user'));
    }

    /**
     * Mostrar menú de administrador
     */
    public function menuAdmin()
    {
        if (!Session::has('access_token')) {
            return redirect()->route('login.register');
        }

        $user = Session::get('user');
        
        if (!$user || ($user['rol_id'] ?? null) !== 1) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección');
        }

        return view('menuAdmin', compact('user'));
    }

    /**
     * Mostrar menú de donante
     */
    public function menuDonantes()
    {
        if (!Session::has('access_token')) {
            return redirect()->route('login.register');
        }

        $user = Session::get('user');
        
        if (!$user || ($user['rol_id'] ?? null) !== 2) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección');
        }

        return view('menuDonantes', compact('user'));
    }

    /**
     * Mostrar menú de beneficiario
     */
    public function menuBeneficiario()
    {
        if (!Session::has('access_token')) {
            return redirect()->route('login.register');
        }

        $user = Session::get('user');
        
        if (!$user || ($user['rol_id'] ?? null) !== 3) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección');
        }

        return view('menuBeneficiario', compact('user'));
    }

    /**
     * Mostrar página de inicio
     */
    public function showIndex()
    {
        return view('index');
    }
}