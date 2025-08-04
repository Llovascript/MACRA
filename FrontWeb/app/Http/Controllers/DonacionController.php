<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DonacionController extends Controller
{
    private $apiBaseUrl = 'http://127.0.0.1:5001';

    private $presentaciones = [
        ['id' => 1, 'nombre' => 'Arroz', 'cantidad' => 1, 'unidad' => 'Kilogramo', 'categoria' => 'Alimentos'],
        ['id' => 2, 'nombre' => 'Frijoles', 'cantidad' => 1, 'unidad' => 'Kilogramo', 'categoria' => 'Alimentos'],
        ['id' => 3, 'nombre' => 'Aceite', 'cantidad' => 1, 'unidad' => 'Litro', 'categoria' => 'Alimentos'],
        ['id' => 4, 'nombre' => 'Leche', 'cantidad' => 1, 'unidad' => 'Litro', 'categoria' => 'Alimentos'],
        ['id' => 5, 'nombre' => 'Huevos', 'cantidad' => 12, 'unidad' => 'Piezas', 'categoria' => 'Alimentos'],
        ['id' => 6, 'nombre' => 'Pan', 'cantidad' => 1, 'unidad' => 'Pieza', 'categoria' => 'Alimentos'],
        ['id' => 7, 'nombre' => 'Azúcar', 'cantidad' => 1, 'unidad' => 'Kilogramo', 'categoria' => 'Alimentos'],
        ['id' => 8, 'nombre' => 'Sal', 'cantidad' => 1, 'unidad' => 'Kilogramo', 'categoria' => 'Alimentos'],
        ['id' => 9, 'nombre' => 'Pasta', 'cantidad' => 500, 'unidad' => 'Gramos', 'categoria' => 'Alimentos'],
        ['id' => 10, 'nombre' => 'Atún', 'cantidad' => 1, 'unidad' => 'Lata', 'categoria' => 'Alimentos'],
        ['id' => 11, 'nombre' => 'Jabón', 'cantidad' => 1, 'unidad' => 'Pieza', 'categoria' => 'Limpieza'],
        ['id' => 12, 'nombre' => 'Detergente', 'cantidad' => 1, 'unidad' => 'Litro', 'categoria' => 'Limpieza'],
        ['id' => 13, 'nombre' => 'Papel Higiénico', 'cantidad' => 4, 'unidad' => 'Rollos', 'categoria' => 'Higiene'],
        ['id' => 14, 'nombre' => 'Pasta de Dientes', 'cantidad' => 1, 'unidad' => 'Tubo', 'categoria' => 'Higiene'],
        ['id' => 15, 'nombre' => 'Shampoo', 'cantidad' => 1, 'unidad' => 'Botella', 'categoria' => 'Higiene']
    ];

    public function crear()
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión primero']);
        }

        try {
            $response = Http::withToken($token)->get("{$this->apiBaseUrl}/articulos/presentaciones");
            
            if ($response->successful()) {
                $presentacionesApi = $response->json();
                $this->presentaciones = array_map(function($item) {
                    return [
                        'id' => $item['id'],
                        'nombre' => $item['articulo']['nombre'],
                        'cantidad' => $item['cantidad'],
                        'unidad' => $item['unidad']['nombre'],
                        'categoria' => $item['articulo']['categoria']['nombre']
                    ];
                }, $presentacionesApi);
            }
        } catch (\Exception $e) {
            report($e);
        }

        return view('donaciones.donaciones', [
            'presentaciones' => $this->presentaciones
        ]);
    }

    public function guardar(Request $request)
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['error' => 'Sesión expirada']);
        }

        $validated = $request->validate([
            'articulo_id' => 'required|numeric',
            'cantidad' => 'required|numeric|min:1'
        ]);

        try {
            $userResponse = Http::withToken($token)->get("{$this->apiBaseUrl}/auth/me");
            if (!$userResponse->successful()) {
                throw new \Exception('No se pudo obtener información del usuario');
            }
            $userData = $userResponse->json();

            $donacionData = [
                'tipo_donante' => 'persona',
                'fecha' => now()->format('Y-m-d'),
                'cantidad' => (int)$request->cantidad,
                'usuario_id' => $userData['id'],
                'articuloP_id' => (int)$request->articulo_id,
                'estatus_id' => 3,
                'aprobacion' => null
            ];

            $response = Http::withToken($token)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->apiBaseUrl}/donaciones/", $donacionData);

            if ($response->successful()) {
                return redirect()->route('donaciones.historial')
                    ->with('success', '¡Donación registrada con éxito! Tu solicitud está pendiente de aprobación.');
            }

            $errorDetail = $response->json()['detail'] ?? 'Error desconocido al crear donación';
            return back()->withErrors(['error' => $errorDetail])->withInput();

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error de conexión: '.$e->getMessage()])->withInput();
        }
    }

    public function historial()
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            $response = Http::withToken($token)->get("{$this->apiBaseUrl}/donaciones/me");
            
            if (!$response->successful()) {
                throw new \Exception('Error al obtener donaciones: '.$response->body());
            }
            
            $donaciones = $response->json();

            // Forzar todas las donaciones como pendientes
            $donaciones = array_map(function($donacion) {
                $donacion['aprobacion'] = null;
                return $donacion;
            }, $donaciones);

            $presentacionesResponse = Http::withToken($token)
                ->get("{$this->apiBaseUrl}/articulos/presentaciones");
            
            $presentacionesMap = [];
            if ($presentacionesResponse->successful()) {
                $presentacionesApi = $presentacionesResponse->json();
                foreach ($presentacionesApi as $item) {
                    $presentacionesMap[$item['id']] = [
                        'nombre' => $item['articulo']['nombre'],
                        'unidad' => $item['unidad']['nombre'],
                        'categoria' => $item['articulo']['categoria']['nombre']
                    ];
                }
            } else {
                foreach ($this->presentaciones as $item) {
                    $presentacionesMap[$item['id']] = [
                        'nombre' => $item['nombre'],
                        'unidad' => $item['unidad'],
                        'categoria' => $item['categoria']
                    ];
                }
            }

            $donacionesConNombres = array_map(function($donacion) use ($presentacionesMap) {
                $articuloInfo = $presentacionesMap[$donacion['articuloP_id']] ?? [
                    'nombre' => 'Artículo '.$donacion['articuloP_id'],
                    'unidad' => 'unidades',
                    'categoria' => 'Sin categoría'
                ];
                
                return array_merge($donacion, [
                    'nombre_articulo' => $articuloInfo['nombre'],
                    'unidad_articulo' => $articuloInfo['unidad'],
                    'categoria_articulo' => $articuloInfo['categoria']
                ]);
            }, $donaciones);

            return view('donaciones.historial', [
                'donaciones' => $donacionesConNombres
            ]);

        } catch (\Exception $e) {
            return view('donaciones.historial', [
                'donaciones' => [],
                'error' => 'Error: '.$e->getMessage()
            ]);
        }
    }
}