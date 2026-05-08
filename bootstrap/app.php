<?php

use App\Http\Middleware\EnsureTenantOnboarded;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

return Application::configure(basePath: dirname(__DIR__))
    // ->withRouting(
    //     commands: __DIR__.'/../routes/console.php',
    //     api: __DIR__.'/../routes/api.php',
    //     health: '/up',
    //     then: function () {
    //         $centralDomains = config('tenancy.central_domains', []);
    //         $currentHost = request()->getHost();
    //         $isCentral = in_array($currentHost, $centralDomains);

    //         if ($isCentral || empty($centralDomains)) {
    //             Route::middleware('web')->group(base_path('routes/web.php'));
    //         } else {
    //             Route::middleware('web')->group(base_path('routes/tenant.php'));
    //         }
    //     }
    // )
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {

            Route::middleware([
                'web',
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
            ])->group(base_path('routes/tenant.php'));

        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            SetLocale::class,
        ]);
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'tenant.onboarded' => EnsureTenantOnboarded::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
