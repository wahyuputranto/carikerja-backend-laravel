<?php

use App\Http\Controllers\AdminNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
|
| These routes are for internal service-to-service communication.
| They do not use web middleware (no session, CSRF, etc).
|
*/

Route::post('/webhook/admin-notifications', [AdminNotificationController::class, 'store']);

Route::get('/webhook/test', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/webhook/test', function () {
    return response()->json(['status' => 'ok (post)']);
});
