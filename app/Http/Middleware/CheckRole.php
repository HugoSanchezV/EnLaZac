<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$admin): Response
    {
        //return dd($admin);
        try {
            if (Auth::check()) {
                // Verificar si el usuario tiene e  l rol requerido
                // dd("checo ");
                if (in_array(Auth::user()->admin, $admin)) {
                    return $next($request); // Continuar con la solicitud
                }
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
        //dd("eh: ".Auth::user()->admin.":".$admin);
        return redirect('/dashboard')->with('warning', 'No tienes acceso a esta sección.');
    }
}
