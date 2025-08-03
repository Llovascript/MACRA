<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PerfilController extends Controller
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
     * Obtener rol_id basado en el tipo de perfil
     */
    private function getRolId($tipoPerfil)
    {
        return $tipoPerfil === 'donante' ? 2 : 3; // 2=Donante, 3=Beneficiario
    }

    /**
     * Obtener nombre del perfil
     */
    private function getNombrePerfil($tipoPerfil)
    {
        return $tipoPerfil === 'donante' ? 'Donante' : 'Beneficiario';
    }

    /**
     * Mostrar menú principal de administración de perfiles
     */
    public function index()
    {
        return view('adminPerfiles');
    }

    /**
     * Mostrar el formulario para crear un nuevo perfil
     */
    public function create()
    {
        return view('agregarPerfil');
    }

    /**
     * Almacenar un nuevo perfil (beneficiario o donante)
     */
    public function store(Request $request)
    {
        try {
            // Validar datos
            $validated = $request->validate([
                'tipo_perfil' => 'required|in:beneficiario,donante',
                'nombre' => 'required|string|max:255',
                'aP' => 'nullable|string|max:255',
                'aM' => 'nullable|string|max:255',
                'correo' => 'required|email|max:255',
                'contraseña' => 'required|string|min:8',
                'telefono' => 'required|string|max:20',
                'edad' => 'nullable|integer|min:1|max:120',
                'rfc' => 'nullable|string|max:13',
                'paginaWeb' => 'nullable|url|max:255',
                'tipo' => 'required|in:persona,institucion',
            ]);

            // Preparar datos para FastAPI
            $userData = [
                'tipo' => $validated['tipo'],
                'nombre' => $validated['nombre'],
                'aP' => $validated['aP'] ?? null,
                'aM' => $validated['aM'] ?? null,
                'edad' => $validated['edad'] ?? null,
                'telefono' => $validated['telefono'],
                'correo' => $validated['correo'],
                'contraseña' => $validated['contraseña'],
                'rfc' => $validated['rfc'] ?? null,
                'paginaWeb' => $validated['paginaWeb'] ?? null,
                'rol_id' => $this->getRolId($validated['tipo_perfil']),
                'estatus_id' => 1, // Activo
                'aprobacion' => true
            ];

            // Filtrar valores null
            $userData = array_filter($userData, function ($value) {
                return $value !== null && $value !== '';
            });

            // Asegurar campos obligatorios
            $userData['rol_id'] = $this->getRolId($validated['tipo_perfil']);
            $userData['estatus_id'] = 1;
            $userData['aprobacion'] = true;

            Log::info('🚀 Creando perfil', [
                'tipo_perfil' => $validated['tipo_perfil'],
                'datos' => $userData
            ]);

            // Enviar a FastAPI
            $response = Http::timeout(30)
                ->post($this->apiUrl . '/auth/register', $userData);

            Log::info('📥 Respuesta recibida:', [
                'status' => $response->status(),
                'tipo_perfil' => $validated['tipo_perfil']
            ]);

            if ($response->successful()) {
                $nombrePerfil = $this->getNombrePerfil($validated['tipo_perfil']);
                return redirect()->route('adminPerfiles')
                    ->with('success', "{$nombrePerfil} agregado exitosamente");
            } else {
                $statusCode = $response->status();
                $responseBody = $response->body();

                Log::error('❌ Error de FastAPI:', [
                    'status' => $statusCode,
                    'body' => $responseBody
                ]);

                try {
                    $errorData = $response->json();
                } catch (\Exception $e) {
                    $errorData = ['detail' => 'Respuesta inválida del servidor'];
                }

                $errorMessage = $this->processApiError($statusCode, $errorData);

                return back()
                    ->withErrors(['api' => $errorMessage])
                    ->withInput($request->except(['contraseña', 'confirmar_contraseña']));
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Error de validación:', $e->errors());
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('❌ Error inesperado:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return back()
                ->withErrors(['error' => 'Error interno del servidor. Por favor, inténtelo nuevamente.'])
                ->withInput($request->except(['contraseña', 'confirmar_contraseña']));
        }
    }

    /**
     * Mostrar formulario de búsqueda para actualizar
     */
    public function updateForm()
    {
        return view('actualizarPerfil');
    }

    /**
     * Buscar perfiles (beneficiarios o donantes)
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $tipoPerfil = $request->get('tipo_perfil', 'beneficiario');
            $rolId = $this->getRolId($tipoPerfil);

            Log::info('Realizando búsqueda de perfiles:', [
                'query' => $query,
                'tipo_perfil' => $tipoPerfil,
                'rol_id' => $rolId
            ]);

            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->get($this->apiUrl . '/admin/usuarios/search', [
                    'rol_id' => $rolId,
                    'search' => $query,
                    'limit' => 10
                ]);

            if ($response->successful()) {
                $usuarios = $response->json();

                $resultados = collect($usuarios)->map(function ($usuario) {
                    return [
                        'id' => $usuario['id'],
                        'nombre' => $usuario['nombre'],
                        'aP' => $usuario['aP'] ?? '',
                        'aM' => $usuario['aM'] ?? '',
                        'correo' => $usuario['correo'],
                        'telefono' => $usuario['telefono'],
                        'edad' => $usuario['edad'],
                        'rfc' => $usuario['rfc'] ?? '',
                        'paginaWeb' => $usuario['paginaWeb'] ?? '',
                        'tipo' => $usuario['tipo']
                    ];
                });

                Log::info('Perfiles encontrados:', ['count' => $resultados->count()]);
                return response()->json($resultados);
            } else {
                Log::error('Error en búsqueda:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Error en la búsqueda'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error en búsqueda:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Obtener un perfil específico
     */
    public function show($id)
    {
        try {
            Log::info('Obteniendo perfil:', ['id' => $id]);

            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->get($this->apiUrl . "/admin/usuarios/{$id}");

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                Log::error('Perfil no encontrado:', [
                    'id' => $id,
                    'status' => $response->status()
                ]);
                return response()->json(['error' => 'Perfil no encontrado'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error al obtener perfil:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Actualizar un perfil específico
     */
    public function update(Request $request, $id)
    {
        try {
            // Validar datos
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'aP' => 'nullable|string|max:255',
                'aM' => 'nullable|string|max:255',
                'correo' => 'required|email|max:255',
                'contraseña' => 'nullable|string|min:8',
                'telefono' => 'required|string|max:20',
                'edad' => 'nullable|integer|min:1|max:120',
                'rfc' => 'nullable|string|max:13',
                'paginaWeb' => 'nullable|url|max:255',
                'tipo' => 'required|in:persona,institucion',
            ]);

            // Preparar datos para actualización
            $updateData = array_filter([
                'tipo' => $validated['tipo'],
                'nombre' => $validated['nombre'],
                'aP' => $validated['aP'],
                'aM' => $validated['aM'],
                'edad' => $validated['edad'],
                'telefono' => $validated['telefono'],
                'correo' => $validated['correo'],
                'rfc' => $validated['rfc'],
                'paginaWeb' => $validated['paginaWeb'],
            ], function ($value) {
                return $value !== null && $value !== '';
            });

            // Solo incluir contraseña si se proporcionó
            if (!empty($validated['contraseña'])) {
                $updateData['contraseña'] = $validated['contraseña'];
            }

            Log::info('Actualizando perfil:', [
                'id' => $id,
                'data' => $updateData
            ]);

            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->put($this->apiUrl . "/admin/usuarios/{$id}", $updateData);

            if ($response->successful()) {
                return redirect()->route('adminPerfiles')
                    ->with('success', 'Perfil actualizado exitosamente');
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['detail'] ?? 'Error al actualizar el perfil';

                return back()->withErrors(['api' => $errorMessage])
                    ->withInput($request->except('contraseña'));
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error al actualizar perfil:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Error interno del servidor'])
                ->withInput($request->except('contraseña'));
        }
    }

    /**
     * Mostrar formulario de búsqueda para eliminar
     */
    public function deleteForm()
    {
        return view('eliminarPerfil');
    }

    /**
     * Eliminar un perfil específico
     */
    public function destroy($id)
    {
        try {
            Log::info('Eliminando perfil:', ['id' => $id]);

            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->delete($this->apiUrl . "/admin/usuarios/{$id}");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Perfil eliminado exitosamente'
                ]);
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['detail'] ?? 'Error al eliminar el perfil';

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar perfil:', [
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
     * Procesar errores de la API
     */
    private function processApiError($statusCode, $errorData)
    {
        switch ($statusCode) {
            case 400:
                if (isset($errorData['detail']) && strpos($errorData['detail'], 'Email already registered') !== false) {
                    return 'El correo electrónico ya está registrado. Por favor, use otro correo.';
                }
                return $errorData['detail'] ?? 'Error de solicitud. Verifique los datos ingresados.';

            case 422:
                if (isset($errorData['detail']) && is_array($errorData['detail'])) {
                    $errors = [];
                    foreach ($errorData['detail'] as $error) {
                        if (isset($error['loc']) && isset($error['msg'])) {
                            $field = end($error['loc']);
                            $message = $error['msg'];

                            if (strpos($message, 'Input should be') !== false && strpos($message, 'persona') !== false) {
                                $errors[] = "El tipo debe ser 'persona' o 'institucion'";
                            } elseif (strpos($message, 'value is not a valid email') !== false) {
                                $errors[] = "El formato del correo electrónico no es válido";
                            } else {
                                $errors[] = "Campo {$field}: {$message}";
                            }
                        }
                    }
                    return implode('. ', $errors);
                }
                return $errorData['detail'] ?? 'Error de validación de datos.';

            case 401:
                return 'Error de autenticación. Por favor, contacte al administrador.';

            case 500:
                return 'Error interno del servidor. Por favor, inténtelo más tarde.';

            default:
                return $errorData['detail'] ?? "Error del servidor (código {$statusCode}).";
        }
    }
}