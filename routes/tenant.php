<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', [\App\Http\Controllers\Tenant\PublicSiteController::class, 'index'])->name('tenant.home');

    // Tenant Authentication Routes
    Route::middleware('guest')->group(function () {
        Route::get('login', [\App\Http\Controllers\Tenant\AuthController::class, 'showLoginForm'])->name('tenant.login');
        Route::post('login', [\App\Http\Controllers\Tenant\AuthController::class, 'login'])->name('tenant.login.submit');
    });

    // Public E-commerce Routes
    Route::post('/cart/add/{product}', [\App\Http\Controllers\Tenant\CartController::class, 'add'])->name('tenant.cart.add');
    Route::get('/cart', [\App\Http\Controllers\Tenant\CartController::class, 'index'])->name('tenant.cart');
    Route::get('/cart/remove/{id}', [\App\Http\Controllers\Tenant\CartController::class, 'remove'])->name('tenant.cart.remove');
    Route::get('/checkout', [\App\Http\Controllers\Tenant\CartController::class, 'checkout'])->name('tenant.checkout');
    Route::post('/checkout', [\App\Http\Controllers\Tenant\CartController::class, 'process'])->name('tenant.checkout.process');
    Route::get('/order/success/{order}', [\App\Http\Controllers\Tenant\CartController::class, 'success'])->name('tenant.order.success');
    Route::get('/order/{order}/invoice', [\App\Http\Controllers\Tenant\CartController::class, 'downloadInvoice'])->name('tenant.order.invoice');

    Route::post('logout', [\App\Http\Controllers\Tenant\AuthController::class, 'logout'])->name('tenant.logout');

    // Tenant Protected Routes
    Route::middleware('auth')->group(function () {
        
        // Onboarding (Cannot be accessed if already onboarded)
        Route::get('/onboarding', [\App\Http\Controllers\Tenant\OnboardingController::class, 'index'])->name('tenant.onboarding');
        Route::post('/onboarding', [\App\Http\Controllers\Tenant\OnboardingController::class, 'store'])->name('tenant.onboarding.store');

        // Dashboard (Must be onboarded)
        Route::middleware('tenant.onboarded')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Tenant\DashboardController::class, 'index'])->name('tenant.dashboard');

            // Customization (Live Builder)
            Route::get('/dashboard/builder', [\App\Http\Controllers\Tenant\BuilderController::class, 'index'])->name('tenant.builder');
            Route::post('/dashboard/builder/save', [\App\Http\Controllers\Tenant\BuilderController::class, 'save'])->name('tenant.builder.save');

            // Product Management
            Route::resource('/dashboard/products', \App\Http\Controllers\Tenant\ProductController::class)->names('tenant.products');
            
            // Order Management
            Route::get('/dashboard/orders', [\App\Http\Controllers\Tenant\OrderController::class, 'index'])->name('tenant.orders');
            Route::get('/dashboard/orders/{order}', [\App\Http\Controllers\Tenant\OrderController::class, 'show'])->name('tenant.orders.show');
            Route::get('/dashboard/orders/{order}/invoice', [\App\Http\Controllers\Tenant\OrderController::class, 'downloadInvoice'])->name('tenant.orders.invoice');
            Route::patch('/dashboard/orders/{order}/status', [\App\Http\Controllers\Tenant\OrderController::class, 'updateStatus'])->name('tenant.orders.status');
        });
    });
});
