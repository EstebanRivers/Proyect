<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HandleAjaxRequests
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Si es una petición AJAX y el usuario no está autenticado
        if ($request->ajax() && !Auth::check()) {
            return response()->json([
                'error' => 'Unauthenticated',
                'redirect' => route('login')
            ], 401);
        }
        
        // Agregar headers de seguridad para peticiones AJAX
        if ($request->ajax()) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
        }
        
        return $response;
    }
}