<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tambahkan HandleCors di awal pipeline API
        // agar preflight OPTIONS selalu mendapat respons 200 dengan header CORS yang benar.
        // EnsureFrontendRequestsAreStateful DIHAPUS karena mobile pakai Bearer token,
        // bukan cookie/session — middleware ini justru menyebabkan preflight gagal.
        $middleware->api(prepend: [
            HandleCors::class,
        ]);

        $middleware->alias([
            'admin_jalan' => \App\Http\Middleware\PastikanAdminJalan::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

