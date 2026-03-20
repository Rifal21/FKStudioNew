<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('switch-language/{locale}', [LandingController::class, 'switchLanguage'])->name('switch-language');
Route::post('/chat', [ChatController::class, 'sendMessage'])->name('chat.send');

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('/', [CmsController::class, 'dashboard'])->name('dashboard');
    
    Route::name('dashboard.')->group(function() {
        // Site Settings
    Route::get('/settings', [CmsController::class, 'editSettings'])->name('settings.edit');
    Route::post('/settings', [CmsController::class, 'updateSettings'])->name('settings.update');

    // Hero
    Route::get('/hero', [CmsController::class, 'editHero'])->name('hero.edit');
    Route::post('/hero', [CmsController::class, 'updateHero'])->name('hero.update');
    Route::post('/hero/slides', [CmsController::class, 'storeHeroSlide'])->name('hero.slides.store');
    Route::delete('/hero/slides/{slide}', [CmsController::class, 'deleteHeroSlide'])->name('hero.slides.destroy');

    // About
    Route::get('/about', [CmsController::class, 'editAbout'])->name('about.edit');
    Route::post('/about', [CmsController::class, 'updateAbout'])->name('about.update');
    Route::post('/about/slides', [CmsController::class, 'storeAboutSlide'])->name('about.slides.store');
    Route::delete('/about/slides/{slide}', [CmsController::class, 'deleteAboutSlide'])->name('about.slides.destroy');

    // Services
    Route::get('/services', [CmsController::class, 'servicesIndex'])->name('services.index');
    Route::post('/services', [CmsController::class, 'storeService'])->name('services.store');
    Route::patch('/services/{service}', [CmsController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [CmsController::class, 'deleteService'])->name('services.destroy');

    // Projects
    Route::get('/projects', [CmsController::class, 'projectsIndex'])->name('projects.index');
    Route::post('/projects', [CmsController::class, 'storeProject'])->name('projects.store');
    Route::delete('/projects/{project}', [CmsController::class, 'deleteProject'])->name('projects.destroy');

    // Testimonials
    Route::get('/testimonials', [CmsController::class, 'testimonialsIndex'])->name('testimonials.index');
    Route::post('/testimonials', [CmsController::class, 'storeTestimonial'])->name('testimonials.store');
    Route::patch('/testimonials/{testimonial}', [CmsController::class, 'updateTestimonial'])->name('testimonials.update');
    Route::delete('/testimonials/{testimonial}', [CmsController::class, 'deleteTestimonial'])->name('testimonials.destroy');

    // Clients
    Route::get('/clients', [CmsController::class, 'clientsIndex'])->name('clients.index');
    Route::post('/clients', [CmsController::class, 'storeClient'])->name('clients.store');
    Route::delete('/clients/{client}', [CmsController::class, 'deleteClient'])->name('clients.destroy');

    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
