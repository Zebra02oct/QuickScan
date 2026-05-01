<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
       
        if (!auth()->check()) {
            return redirect()->route('login');
        }

       
        if (auth()->user()->role !== $role) {
            abort(403, 'Akses Ditolak! Anda tidak memiliki izin untuk masuk ke halaman ini.');
        }

        // Kalau aman, silakan lewat!
        return $next($request);
    }
}