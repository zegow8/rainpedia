<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Jika belum login (misal dari Landing Page ngubah URL ke /admin), munculkan 401 UNAUTHORIZED
        if (!auth()->check()) {
            abort(401, '401 UNAUTHORIZED: Silakan login terlebih dahulu untuk mengakses area Admin.');
        }

        // 2. Jika sudah login tapi role-nya bukan admin (misal User pembaca iseng ngubah URL ke /admin), munculkan 403 FORBIDDEN
        if (auth()->user()->role !== 'admin') {
            abort(403, '403 FORBIDDEN: Anda tidak memiliki hak akses Administrator untuk halaman ini.');
        }

        return $next($request);
    }
}
