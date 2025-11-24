<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Protected routes (auth + verified)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Master Data Routes (Superadmin only)
    Route::middleware('role:superadmin')->prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/', fn () => Inertia::render('MasterData/Index'))->name('index');
        
        // Document Types
        Route::resource('document-types', \App\Http\Controllers\DocumentTypeController::class)->except(['create', 'edit', 'show']);
        
        // Users
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['create', 'edit', 'show']);

        // Job Categories
        Route::resource('job-categories', \App\Http\Controllers\MasterData\JobCategoryController::class)->except(['create', 'edit', 'show']);

        // Skills
        Route::resource('skills', \App\Http\Controllers\MasterData\SkillController::class)->except(['create', 'edit', 'show']);

        // Locations
        Route::resource('locations', \App\Http\Controllers\MasterData\LocationController::class)->except(['create', 'edit', 'show']);
    });

    // Job Posting Routes
    Route::resource('jobs', \App\Http\Controllers\JobController::class);

    // Talent Pool Routes
    Route::prefix('talent-pool')->name('talent-pool.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TalentPoolController::class, 'index'])->name('index');
        Route::get('/{candidate}', [\App\Http\Controllers\TalentPoolController::class, 'show'])->name('show');
        Route::patch('/{candidate}/status', [\App\Http\Controllers\TalentPoolController::class, 'updateStatus'])->name('update-status');
    });

    // Client Routes
    Route::resource('clients', \App\Http\Controllers\ClientController::class)
        ->except(['show'])
        ->middleware('role:superadmin');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
