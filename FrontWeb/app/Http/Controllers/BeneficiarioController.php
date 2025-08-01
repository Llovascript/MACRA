<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BeneficiarioController extends Controller
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
     * Mostrar el formulario para crear un nuevo beneficiario
     */
    public function create()
    {
        return view('agregarBeneficiario');
    }

    /**
     * Almacenar un nuevo beneficiario
     */
    public function store(Request $request)
    {
        try {
            // Validar datos
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'aP' => 'nullable|string|max:255',
                'aM' => 'nullable|string|max:255',
                'correo' => 'required|email|max:255',
                'contraseña' => 'required|string|min:8',
                'telefono' => 'required|string|max:20',
                'edad' => 'nullable|integer|min:1|max:120',
                'rfc' => 'nullable|string|max:13',
                'paginaWeb' => 'nullable|url|max:255',
                'tipo' => 'required|in:persona,organizacion',
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
                'rol_id' => 3, // Beneficiario
                'estatus_id' => 1, // Activo por defecto
                'aprobacion' => true // Auto-aprobar por ahora
            ];

            Log::info('Enviando datos a FastAPI para crear beneficiario:', $userData);

            // Enviar datos a FastAPI
            $response = Http::timeout(30)->post($this->apiUrl . '/auth/register', $userData);

            Log::info('Respuesta de FastAPI:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return redirect()->route('adminBeneficiarios')
                    ->with('success', 'Beneficiario agregado exitosamente');
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['detail'] ?? 'Error al conectar con el servidor';
                
                return back()->withErrors(['api' => $errorMessage])
                    ->withInput($request->except('contraseña'));
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error al crear beneficiario:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Error interno del servidor'])
                ->withInput($request->except('contraseña'));
        }
    }

    /**
     * Mostrar formulario de búsqueda para actualizar
     */
    public function updateForm()
    {
        return view('actualizarBeneficiario');
    }

    /**
     * Buscar beneficiarios
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            Log::info('Realizando búsqueda de beneficiarios:', [
                'query' => $query,
                'url' => $this->apiUrl . '/admin/usuarios/search'
            ]);

            // Usar el nuevo endpoint de admin con headers
            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->get($this->apiUrl . '/admin/usuarios/search', [
                    'rol_id' => 3,
                    'search' => $query,
                    'limit' => 10
                ]);

            Log::info('Respuesta de búsqueda de beneficiarios:', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 200)
            ]);

            if ($response->successful()) {
                $usuarios = $response->json();
                
                // Formatear los resultados
                $resultados = collect($usuarios)->map(function($usuario) {
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

                Log::info('Beneficiarios encontrados:', ['count' => $resultados->count()]);
                return response()->json($resultados);
            } else {
                Log::error('Error en búsqueda de beneficiarios:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Error en la búsqueda'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error en búsqueda de beneficiarios:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Obtener un beneficiario específico
     */
    public function show($id)
    {
        try {
            Log::info('Obteniendo beneficiario:', ['id' => $id]);

            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->get($this->apiUrl . "/admin/usuarios/{$id}");
            
            Log::info('Respuesta al obtener beneficiario:', [
                'status' => $response->status(),
                'id' => $id
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                Log::error('Beneficiario no encontrado:', [
                    'id' => $id,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Beneficiario no encontrado'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error al obtener beneficiario:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Actualizar un beneficiario específico
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
                'tipo' => 'required|in:persona,organizacion',
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
            ], function($value) {
                return $value !== null && $value !== '';
            });

            // Solo incluir contraseña si se proporcionó
            if (!empty($validated['contraseña'])) {
                $updateData['contraseña'] = $validated['contraseña'];
            }

            Log::info('Actualizando beneficiario:', [
                'id' => $id,
                'data' => $updateData
            ]);

            // Enviar actualización a FastAPI usando el endpoint de admin
            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->put($this->apiUrl . "/admin/usuarios/{$id}", $updateData);

            Log::info('Respuesta de actualización:', [
                'status' => $response->status(),
                'id' => $id
            ]);

            if ($response->successful()) {
                return redirect()->route('adminBeneficiarios')
                    ->with('success', 'Beneficiario actualizado exitosamente');
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['detail'] ?? 'Error al actualizar el beneficiario';
                
                Log::error('Error al actualizar beneficiario:', [
                    'id' => $id,
                    'status' => $response->status(),
                    'error' => $errorMessage
                ]);

                return back()->withErrors(['api' => $errorMessage])
                    ->withInput($request->except('contraseña'));
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error al actualizar beneficiario:', [
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
        return view('eliminarBeneficiario');
    }

    /**
     * Eliminar un beneficiario específico (soft delete)
     */
    public function destroy($id)
    {
        try {
            Log::info('Eliminando beneficiario:', ['id' => $id]);

            $response = Http::timeout(30)
                ->withHeaders($this->getAdminHeaders())
                ->delete($this->apiUrl . "/admin/usuarios/{$id}");

            Log::info('Respuesta de eliminación:', [
                'status' => $response->status(),
                'id' => $id
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Beneficiario eliminado exitosamente'
                ]);
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['detail'] ?? 'Error al eliminar el beneficiario';
                
                Log::error('Error al eliminar beneficiario:', [
                    'id' => $id,
                    'status' => $response->status(),
                    'error' => $errorMessage
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar beneficiario:', [
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
     * Métodos del código base existente (si los necesitas)
     */
    public function profile()
    {
        return view('perfilBeneficiario');
    }

    public function menu()
    {
        return view('menuBeneficiario');
    }
}