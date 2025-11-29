<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminNotificationController;

// Public webhook endpoint untuk Go backend
// Tidak menggunakan middleware auth karena ini internal service-to-service call
Route::post('/webhook/admin-notifications', [AdminNotificationController::class, 'store'])
    ->withoutMiddleware(['auth', 'verified']);
