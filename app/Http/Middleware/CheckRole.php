<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  ...$roles Allowed role IDs
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/login');
        }

        // Convert string role parameters to integers
        $allowedRoles = array_map('intval', $roles);

        if (!in_array(intval($user->id_rol), $allowedRoles, true)) {
            return redirect('/dashboard')->with('error', 'Acceso restringido. No tienes permisos para ingresar a este módulo.');
        }

        return $next($request);
    }
}
