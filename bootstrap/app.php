<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RestrictAdminToBarangKeluar;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->trustProxies(at: '*');


        // 2. Daftarkan alias middleware 'prevent-back'
        $middleware->alias([
            'prevent-back' => PreventBackHistory::class,
            'admin-barang-keluar' => RestrictAdminToBarangKeluar::class,
        ]);

        // 3. Atur redirect untuk user yang belum login ke '/signin'
        $middleware->redirectTo(
            guests: '/signin',
            users: '/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
