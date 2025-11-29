<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminNotificationController;

// Public webhook endpoint untuk Go backend
// Tidak menggunakan middleware auth karena ini internal service-to-service call
Route::post('/webhook/admin-notifications', [AdminNotificationController::class, 'store'])
    ->withoutMiddleware(['auth', 'verified']);

// Offering Letter Download endpoint (no auth required, just validates file path)
Route::post('/offering-letter/download', function(\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'file_path' => 'required|string',
    ]);

    try {
        // Generate temporary URL (valid for 5 minutes)
        $url = \Storage::disk('minio')->temporaryUrl(
            $validated['file_path'],
            now()->addMinutes(5)
        );

        return response()->json(['url' => $url]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to generate download URL'], 500);
    }
});
