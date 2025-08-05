<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class EventoController extends Controller
{
    private $apiBaseUrl = 'http://127.0.0.1:5001';

    // Menú principal
    public function menu()
    {
        return view('admin.eventos.menu_eventos');
    }

    // Formulario para crear evento
    public function create()
    {
        return view('admin.eventos.crear_evento');
    }

    // Guardar evento en la API
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

        $data = $request->only(['nombre', 'fechaIn', 'fechaTer', 'descripcion', 'estatus_id']);

        try {
            $response = Http::withToken($token)->post("{$this->apiBaseUrl}/eventos", $data);

            if ($response->successful()) {
                return redirect()->route('admin.eventos.create')->with('success', 'Evento creado correctamente.');
            } else {
                $error = $response->json()['detail'] ?? 'Error desconocido';
                return back()->withErrors(['api_error' => "API: $error"])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['exception' => 'Error al conectar con la API: ' . $e->getMessage()])->withInput();
        }
    }

    // Mostrar vista de gestión de eventos (listar todos)
    public function manage()
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            $response = Http::withToken($token)->get("{$this->apiBaseUrl}/eventos");
            $eventos = $response->json();

            return view('admin.eventos.eventos_manage', compact('eventos'));
        } catch (\Exception $e) {
            return redirect()->route('admin.eventos.menu')->with('error', 'No se pudieron obtener los eventos.');
        }
    }

    // Eliminar un evento
    public function destroy($id)
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        $response = Http::withToken($token)->delete("{$this->apiBaseUrl}/eventos/{$id}");

        if ($response->successful()) {
            return redirect()->route('admin.eventos.manage')->with('success', 'Evento eliminado correctamente.');
        }

        return redirect()->route('admin.eventos.manage')->with('error', 'No se pudo eliminar el evento.');
    }

    // Formulario para editar evento
    public function edit($id)
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        $response = Http::withToken($token)->get("{$this->apiBaseUrl}/eventos");

        $evento = collect($response->json())->firstWhere('id', $id);

        if (!$evento) {
            return redirect()->route('admin.eventos.manage')->with('error', 'Evento no encontrado.');
        }

        return view('admin.eventos.editar_evento', compact('evento'));
    }

    // Actualizar evento en la API
    public function update(Request $request, $id)
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'fechaIn' => 'required|date',
            'fechaTer' => 'required|date|after_or_equal:fechaIn',
            'descripcion' => 'required|string|max:255',
            'estatus_id' => 'required|integer',
        ]);

        $response = Http::withToken($token)
            ->put("{$this->apiBaseUrl}/eventos/{$id}", $data);

        if ($response->successful()) {
            return redirect()->route('admin.eventos.manage')->with('success', 'Evento actualizado correctamente.');
        }

        return back()->withErrors(['error' => 'Error actualizando evento.'])->withInput();
    }

    // CORREGIDO: Mostrar eventos disponibles para usuarios beneficiarios
    public function verEventosDisponibles()
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            // Obtener todos los eventos
            $response = Http::withToken($token)->get("{$this->apiBaseUrl}/eventos");
            $eventos = $response->json();

            // Obtener los eventos donde el usuario actual está como beneficiario
            $misEventosResponse = Http::withToken($token)->get("{$this->apiBaseUrl}/eventos/me/como_beneficiario");
            $misEventos = [];
            
            if ($misEventosResponse->successful()) {
                $misEventosData = $misEventosResponse->json();
                // Crear un array con los IDs de eventos donde ya está unido
                foreach ($misEventosData as $evento) {
                    $misEventos[$evento['evento_id']] = true;
                }
            }

            // Marcar los eventos donde ya está unido
            foreach ($eventos as &$evento) {
                $evento['ya_unido'] = isset($misEventos[$evento['id']]);
            }

            return view('usuario.eventos_disponibles', ['eventos' => $eventos]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'No se pudieron cargar los eventos: ' . $e->getMessage()]);
        }
    }

    // Unirse como beneficiario a un evento
    public function unirseEvento($id)
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            $response = Http::withToken($token)->post("{$this->apiBaseUrl}/eventos/{$id}/unirse_como_beneficiario");

            if ($response->successful()) {
                return redirect()->route('eventos.usuario')->with('success', 'Te uniste correctamente al evento.');
            } else {
                $detalle = $response->json()['detail'] ?? 'Error';

                // Para evitar mostrar el mensaje cuando ya está unido (409)
                if ($response->status() === 409) {
                    return redirect()->route('eventos.usuario')->with('info', 'Ya estás unido a este evento.');
                }

                return back()->withErrors(['error' => "No se pudo unir al evento: $detalle"]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al conectar con la API: ' . $e->getMessage()]);
        }
    }

    // CORREGIDO: Mostrar eventos disponibles para donantes
    public function verEventosDisponiblesDonante()
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            // Obtener todos los eventos
            $response = Http::withToken($token)->get("{$this->apiBaseUrl}/eventos");
            $eventos = $response->json();

            // Obtener los eventos donde el usuario actual está como donante
            $misEventosResponse = Http::withToken($token)->get("{$this->apiBaseUrl}/eventos/me/como_donante");
            $misEventos = [];
            
            if ($misEventosResponse->successful()) {
                $misEventosData = $misEventosResponse->json();
                // Crear un array con los IDs de eventos donde ya está unido
                foreach ($misEventosData as $evento) {
                    $misEventos[$evento['evento_id']] = true;
                }
            }

            // Marcar los eventos donde ya está unido
            foreach ($eventos as &$evento) {
                $evento['ya_unido'] = isset($misEventos[$evento['id']]);
            }

            return view('donaciones.eventos_disponibles_donante', ['eventos' => $eventos]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'No se pudieron cargar los eventos: ' . $e->getMessage()]);
        }
    }

    // Unirse como donante a un evento
    public function unirseEventoDonante($id)
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            $response = Http::withToken($token)->post("{$this->apiBaseUrl}/eventos/{$id}/unirse_como_donante");

            if ($response->successful()) {
                return redirect()->route('eventos.donante')->with('success', 'Te uniste correctamente al evento.');
            } else {
                $detalle = $response->json()['detail'] ?? 'Error';

                // Evitar mostrar mensaje si ya está unido (409)
                if ($response->status() === 409) {
                    return redirect()->route('eventos.donante')->with('info', 'Ya estás unido a este evento.');
                }

                return back()->withErrors(['error' => "No se pudo unir al evento: $detalle"]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al conectar con la API: ' . $e->getMessage()]);
        }
    }

    // Método para verificar participación en un evento específico (alternativo)
    public function verificarParticipacion($id)
    {
        $token = Session::get('access_token');
        if (!$token) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        try {
            $response = Http::withToken($token)->get("{$this->apiBaseUrl}/eventos/{$id}/participacion");
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'Error al verificar participación'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error de conexión'], 500);
        }
    }

    // NUEVO: Capacidad por evento (beneficiarios y donantes)
    public function capacidad()
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            $response = Http::withToken($token)->get("{$this->apiBaseUrl}/eventos/capacidad");

            if ($response->successful()) {
                $capacidades = $response->json();
                return view('admin.eventos.capacidad_eventos', compact('capacidades'));
            } else {
                return redirect()->route('admin.eventos.menu')->with('error', 'Error obteniendo la capacidad de eventos.');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.eventos.menu')->with('error', 'No se pudo conectar con la API.');
        }
    }

    // NUEVO: Salir de un evento como beneficiario
    public function salirEventoBeneficiario($id)
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            $response = Http::withToken($token)->delete("{$this->apiBaseUrl}/eventos/{$id}/salir_como_beneficiario");

            if ($response->successful()) {
                return redirect()->route('eventos.usuario')->with('success', 'Has salido del evento correctamente.');
            } else {
                $detalle = $response->json()['detail'] ?? 'Error';
                return back()->withErrors(['error' => "No se pudo salir del evento: $detalle"]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al conectar con la API: ' . $e->getMessage()]);
        }
    }

    // NUEVO: Salir de un evento como donante
    public function salirEventoDonante($id)
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            $response = Http::withToken($token)->delete("{$this->apiBaseUrl}/eventos/{$id}/salir_como_donante");

            if ($response->successful()) {
                return redirect()->route('eventos.donante')->with('success', 'Has salido del evento correctamente.');
            } else {
                $detalle = $response->json()['detail'] ?? 'Error';
                return back()->withErrors(['error' => "No se pudo salir del evento: $detalle"]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al conectar con la API: ' . $e->getMessage()]);
        }
    }
}