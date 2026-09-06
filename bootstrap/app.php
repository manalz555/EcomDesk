<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureIsAdmin::class,
            'actif' => \App\Http\Middleware\EnsureIsActive::class,
            'manage-team' => \App\Http\Middleware\EnsureCanManageTeam::class,
        ]);

        // The widget posts from third-party sites that have no way to obtain
        // a CSRF token — the endpoint is protected by rate limiting and the
        // per-visitor token instead.
        $middleware->validateCsrfTokens(except: [
            'widget/messages',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
