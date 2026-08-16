<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe el acceso a una ruta según el rol del usuario.
 *
 * Uso en las rutas:  ->middleware('role:soporte,admin')
 *
 * Antes esta verificación estaba copiada como un if dentro de cada
 * método de cada controlador. Ese enfoque es frágil: basta olvidarlo
 * una sola vez para dejar una ruta abierta (que fue justo lo que pasó
 * con todo el módulo de inventario). Centralizándolo aquí, la regla se
 * declara junto a la ruta y no depende de que alguien recuerde
 * escribirla en el controlador.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$rolesPermitidos): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!in_array(Auth::user()->role, $rolesPermitidos, true)) {
            // 403 en vez de redirect silencioso: deja claro que el acceso
            // fue denegado y no simula que la página no existe.
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
