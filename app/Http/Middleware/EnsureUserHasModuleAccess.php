<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasModuleAccess
{
    /**
     * Tolak pengguna yang tidak punya akses ke salah satu modul yang disebut.
     * Contoh pemakaian di route: `module_access:berita` atau `module_access:berita,artikel`
     * (cukup salah satu yang boleh).
     */
    public function handle(Request $request, Closure $next, string ...$modules): Response
    {
        $user = $request->user();

        $allowed = $user !== null
            && collect($modules)->contains(fn (string $module): bool => $user->canAccessModule($module));

        if (! $allowed) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini. Hubungi Super Admin untuk meminta akses.');
        }

        return $next($request);
    }
}
