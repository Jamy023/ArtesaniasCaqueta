<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado con el guard 'web'
        if (!Auth::guard('web')->check()) {
            return response()->json([
                'message' => 'No autorizado. Debe iniciar sesión como administrador.',
                'authenticated' => false
            ], 401);
        }

        return $next($request);
    }
}