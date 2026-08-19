<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;  // TAMBAHKAN INI!

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // TAMBAHKAN INI UNTUK NGROK!
        $middleware->trustProxies(at: '*');

        // Kalau masih redirect, tambahkan ini juga:
        // $middleware->trustProxies(
        //     headers: Request::HEADER_X_FORWARDED_ALL
        // );

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'auto.logout' => \App\Http\Middleware\AutoLogoutInactiveUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException $e) {
            $retry = $e->getHeaders()['Retry-After'] ?? 60;
            return back()->with('error', "Terlalu sering meminta kirim ulang. Coba lagi dalam {$retry} detik.");
        });
    })->create();
