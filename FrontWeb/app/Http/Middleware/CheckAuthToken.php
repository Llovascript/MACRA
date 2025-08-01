<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckAuthToken
{
    /**
     * Maneja la petición HTTP y verifica que el token JWT esté en sesión.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('access_token')) {
            // Redirige a login si no hay token
            return redirect()->route('login.register')->withErrors(['error' => 'Debes iniciar sesión primero']);
        }

        return $next($request);
    }
}
