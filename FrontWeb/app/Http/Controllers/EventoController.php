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

    // NUEVO: Mostrar eventos disponibles para usuarios beneficiarios
    public function verEventosDisponibles()
    {
        $token = Session::get('access_token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            $response = Http::withToken($token)->get("{$this->apiBaseUrl}/eventos");
            $eventos = $response->json();

            return view('usuario.eventos_disponibles', compact('eventos'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'No se pudieron cargar los eventos.']);
        }
    }

    // NUEVO: Unirse como beneficiario a un evento
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
                return back()->withErrors(['error' => "No se pudo unir al evento: $detalle"]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al conectar con la API.']);
        }
    }
}
