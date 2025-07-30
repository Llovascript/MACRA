<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class EventoController extends Controller
{
    private $apiBaseUrl = 'http://127.0.0.1:5001';

    // Mostrar menú principal de eventos
    public function menu()
    {
        return view('admin.eventos.menu_eventos');
    }

    // Mostrar formulario para crear evento
    public function create()
    {
        return view('admin.eventos.crear_evento');
    }

    // Guardar evento enviándolo a la API FastAPI
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'fechaIn' => 'required|date',
            'fechaTer' => 'required|date|after_or_equal:fechaIn',
            'descripcion' => 'required|string|max:255',
            'estatus_id' => 'required|integer',
        ]);

        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['error' => 'No autenticado. Por favor, inicia sesión.']);
        }

        $data = [
            'nombre' => $request->input('nombre'),
            'fechaIn' => $request->input('fechaIn'),
            'fechaTer' => $request->input('fechaTer'),
            'descripcion' => $request->input('descripcion'),
            'estatus_id' => $request->input('estatus_id'),
        ];

        try {
            $response = Http::withToken($token)
                ->post($this->apiBaseUrl . '/eventos', $data);

            if ($response->successful()) {
                return redirect()->route('admin.eventos.create')->with('success', 'Evento creado correctamente.');
            } else {
                $error = $response->json()['detail'] ?? 'Error desconocido';

                if (is_array($error)) {
                    $error = json_encode($error);
                }

                return back()->withErrors(['api_error' => "API: $error"])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['exception' => 'Error al conectar con la API: ' . $e->getMessage()])->withInput();
        }
    }

    // Menú para actualizar eventos (vista básica por ahora)
    public function menuActualizar()
    {
        return view('admin.eventos.menu_actualizar');
    }

    // Menú para eliminar eventos (vista básica por ahora)
    public function menuEliminar()
    {
        return view('admin.eventos.menu_eliminar');
    }
}
