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
     * Procesar registro del usuario - ACTUALIZADO PARA FORMULARIO EXTENDIDO
     */
    public function register(Request $request)
    {
        try {
            Log::info('Intento de registro extendido', [
                'correo' => $request->correo,
                'nombre' => $request->nombre,
                'campos_recibidos' => array_keys($request->all()),
                'tipo_entidad_recibido' => $request->tipo_entidad,
                'request_all' => $request->all()
            ]);

            // Validar datos de entrada - REGLAS EXTENDIDAS
            $validator = Validator::make($request->all(), [
                // Campos obligatorios
                'nombre' => 'required|string|max:255',
                'apellido_paterno' => 'required|string|max:255',
                'correo' => 'required|email|max:255',
                'contraseña' => 'required|min:6',
                'confirmar_contraseña' => 'required|same:contraseña',
                'tipo_entidad' => 'required|in:persona,organizacion',
                'perfil' => 'required|in:donante,beneficiario',
                'rol_id' => 'required|integer',
                
                // Campos opcionales
                'apellido_materno' => 'nullable|string|max:255',
                'pagina_web' => 'nullable|url|max:255',
                'edad' => 'nullable|integer|min:18|max:120',
                'telefono' => 'nullable|string|max:15|regex:/^[0-9]+$/',
                'rfc' => 'nullable|string|min:10|max:13|regex:/^[A-Z0-9]+$/',
            ], [
                // Mensajes personalizados
                'nombre.required' => 'El nombre es obligatorio',
                'apellido_paterno.required' => 'El apellido paterno es obligatorio',
                'correo.required' => 'El correo electrónico es obligatorio',
                'correo.email' => 'El correo electrónico debe tener un formato válido',
                'correo.unique' => 'Este correo electrónico ya está registrado',
                'contraseña.required' => 'La contraseña es obligatoria',
                'contraseña.min' => 'La contraseña debe tener al menos 6 caracteres',
                'confirmar_contraseña.required' => 'Debes confirmar tu contraseña',
                'confirmar_contraseña.same' => 'Las contraseñas no coinciden',
                'tipo_entidad.required' => 'Debes seleccionar un tipo de entidad',
                'tipo_entidad.in' => 'El tipo de entidad debe ser Persona u Organización',
                'perfil.required' => 'Debes seleccionar un tipo de perfil',
                'perfil.in' => 'El tipo de perfil seleccionado no es válido',
                'pagina_web.url' => 'La página web debe tener un formato válido (https://ejemplo.com)',
                'edad.integer' => 'La edad debe ser un número',
                'edad.min' => 'Debes ser mayor de 18 años',
                'edad.max' => 'La edad no puede ser mayor a 120 años',
                'telefono.regex' => 'El teléfono solo debe contener números',
                'telefono.max' => 'El teléfono no puede tener más de 15 dígitos',
                'rfc.min' => 'El RFC debe tener al menos 10 caracteres',
                'rfc.max' => 'El RFC no puede tener más de 13 caracteres',
                'rfc.regex' => 'El RFC solo debe contener letras y números',
            ]);

            if ($validator->fails()) {
                Log::warning('Validación fallida en registro', [
                    'errors' => $validator->errors()->toArray()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Preparar datos para enviar a FastAPI (mapear campos)
            $registrationData = [
                // Campos obligatorios
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'correo' => $request->correo,
                'contraseña' => $request->contraseña,
                'tipo' => $request->tipo_entidad, // Mapear para FastAPI
                'perfil' => $request->perfil,
                'rol_id' => $request->rol_id,
                'estatus_id' => 1, // Valor por defecto
                
                // Campos opcionales
                'apellido_materno' => $request->apellido_materno,
                'pagina_web' => $request->pagina_web,
                'edad' => $request->edad ? (int)$request->edad : null,
                'telefono' => $request->telefono,
                'rfc' => $request->rfc ? strtoupper($request->rfc) : null,
            ];

            // Remover campos nulos para no enviarlos a la API
            $registrationData = array_filter($registrationData, function($value) {
                return $value !== null && $value !== '';
            });

            Log::info('Datos preparados para FastAPI', [
                'campos_enviados' => array_keys($registrationData),
                'correo' => $registrationData['correo']
            ]);

            // Hacer petición a la API FastAPI
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($this->apiBaseUrl . '/auth/register', $registrationData);

            Log::info('Respuesta de registro API', [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('Registro exitoso', [
                    'correo' => $request->correo,
                    'nombre' => $request->nombre
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
                
                // Manejar errores específicos de la API
                $errorMessage = 'Error en el registro';
                if (isset($errorData['detail'])) {
                    $errorMessage = $errorData['detail'];
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
                'message' => 'No se puede conectar al servidor de autenticación. Verifica que FastAPI esté funcionando.'
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

    public function showIndex() {
        return view('index');
    }
}