<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminToBarangKeluar
{
    /**
     * Batasi admin agar hanya dapat mengakses halaman Barang Keluar.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->user()?->role === 'admin'
            && !$request->routeIs(['barang-keluar.*', 'logout'])
        ) {
            return redirect()->route('barang-keluar.index');
        }

        return $next($request);
    }
}
