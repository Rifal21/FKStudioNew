<?php

use App\Http\Controllers\Api\BlogApiController;
use App\Http\Controllers\DuitkuController;
use Illuminate\Support\Facades\Route;

// ─── Payment Webhook (no auth) ────────────────────────────────────────────────
Route::post('/payment/duitku/callback', [DuitkuController::class, 'callback'])
    ->name('payment.duitku.callback');

// ─── Blog API ─────────────────────────────────────────────────────────────────

// Auth: Login & Logout
Route::prefix('auth')->group(function () {
    Route::post('/login',  [BlogApiController::class, 'login'])->name('api.auth.login');
    Route::post('/logout', [BlogApiController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('api.auth.logout');
});

// Public: anyone can read published posts
Route::prefix('blogs')->group(function () {
    Route::get('/',        [BlogApiController::class, 'index'])->name('api.blogs.index');
    Route::get('/{slug}',  [BlogApiController::class, 'show'])->name('api.blogs.show');
});

// Protected: super_admin only
Route::middleware(['auth:sanctum', 'ability:blog:manage'])->prefix('admin/blogs')->group(function () {
    Route::get('/',                      [BlogApiController::class, 'adminIndex'])->name('api.admin.blogs.index');
    Route::post('/',                     [BlogApiController::class, 'store'])->name('api.admin.blogs.store');
    Route::post('/upload-image',         [BlogApiController::class, 'uploadImage'])->name('api.admin.blogs.upload_image');
    Route::patch('/{blog}',              [BlogApiController::class, 'update'])->name('api.admin.blogs.update');
    Route::delete('/{blog}',             [BlogApiController::class, 'destroy'])->name('api.admin.blogs.destroy');
});

