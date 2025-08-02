<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DonacionController extends Controller
{
    private $apiBaseUrl = 'http://127.0.0.1:5001';

    public function historial()
    {
        $token = Session::get('access_token');

        if (!$token) {
            return redirect()->route('login')->withErrors(['error' => 'No autenticado.']);
        }

        try {
            $response = Http::withToken($token)->get("{$this->apiBaseUrl}/donaciones/me");

            if ($response->successful()) {
                $donaciones = $response->json();
                return view('usuario.historial_donaciones', compact('donaciones'));
            } else {
                return redirect()->route('dashboard')->withErrors(['error' => 'No se pudo obtener el historial.']);
            }
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Error conectando con la API.']);
        }
    }
}