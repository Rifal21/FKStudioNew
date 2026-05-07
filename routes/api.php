<?php

use App\Http\Controllers\DuitkuController;
use Illuminate\Support\Facades\Route;

Route::post('/payment/duitku/callback', [DuitkuController::class, 'callback'])->name('payment.duitku.callback');
