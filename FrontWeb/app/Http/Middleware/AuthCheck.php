<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('AuthCheck middleware ejecutado', [
            'url' => $request->url(),
            'has_token' => Session::has('access_token'),
            'session_id' => Session::getId()
        ]);

        // Verificar si existe un token de acceso en la sesión
        if (!Session::has('access_token')) {
            Log::warning('No hay token de acceso, redirigiendo a login');
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        $token = Session::get('access_token');
        Log::info('Token encontrado', ['token_preview' => substr($token, 0, 20) . '...']);

        // Verificar si el token no ha expirado haciendo una petición a FastAPI
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->timeout(10)->get('http://127.0.0.1:5001/auth/me');

            if (!$response->successful()) {
                Log::warning('Token inválido o expirado', ['status' => $response->status()]);
                
                // Limpiar sesión si el token es inválido
                Session::forget(['access_token', 'token_type', 'user']);
                
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Token expired'], 401);
                }
                
                return redirect()->route('login')->with('error', 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.');
            }

            Log::info('Token válido, permitiendo acceso');
            
        } catch (\Exception $e) {
            Log::error('Error verificando token', ['error' => $e->getMessage()]);
            // En caso de error de conexión, permitir el acceso (opcional)
            // return redirect()->route('login')->with('error', 'Error de conexión con el servidor.');
        }

        return $next($request);
    }
}
