<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdmin
{
    /**
     * Tolak pengguna yang bukan Super Admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isSuperAdmin()) {
            abort(403, 'Halaman ini hanya dapat diakses oleh Super Admin.');
        }

        return $next($request);
    }
}
