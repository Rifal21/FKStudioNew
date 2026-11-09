<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;
use App\Services\Shop\ShopException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            SetLocale::class,
        ]);
        $middleware->alias([
            'role'       => RoleMiddleware::class,
            'shop'       => \App\Http\Middleware\EnsureShopEnabled::class,
            'ability'    => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'abilities'  => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API clients always get JSON errors (validation, 401/403/404), even without an Accept header.
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson());

        // Shop rule violations (e.g. an invalid variant combination) are client errors, not 500s.
        $exceptions->render(function (ShopException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
        });
    })->create();
