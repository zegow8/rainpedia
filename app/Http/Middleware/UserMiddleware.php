<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Jika belum login (misal dari Landing Page ngubah URL ke /user), munculkan 401 UNAUTHORIZED
        if (!auth()->check()) {
            abort(401, '401 UNAUTHORIZED: Silakan login terlebih dahulu untuk mengakses area User.');
        }

        // 2. Jika sudah login tapi role-nya bukan user (misal Admin iseng ngubah URL ke /user), munculkan 403 FORBIDDEN
        if (auth()->user()->role !== 'user') {
            abort(403, '403 FORBIDDEN: Akun Admin tidak memiliki izin untuk mengakses area User.');
        }

        return $next($request);
    }
}
