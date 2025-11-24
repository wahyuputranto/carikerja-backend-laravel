<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share all permission names for the authenticated user
        \Inertia\Inertia::share('auth.permissions', function () {
            return auth()->user()
                ? auth()->user()->getAllPermissions()->pluck('name')
                : collect();
        });

        // Share the current user's role slug (or null if not logged in)
        \Inertia\Inertia::share('auth.role', function () {
            return auth()->user() ? optional(auth()->user()->role)->slug : null;
        });
    }
}
