<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        //force https if env is production
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
        // Define Gate for permissions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasPermission($ability) ? true : null;
        });

        // Share all permission names for the authenticated user
        \Inertia\Inertia::share('auth.permissions', function () {
            if (!auth()->user() || !auth()->user()->role) {
                return [];
            }
            
            if (auth()->user()->role->slug === 'superadmin') {
                return ['*']; // Superadmin has all permissions
            }

            return auth()->user()->role->privileges->pluck('permission');
        });

        // Share the current user's role slug (or null if not logged in)
        \Inertia\Inertia::share('auth.role', function () {
            return auth()->user() ? optional(auth()->user()->role)->slug : null;
        });
    }
}
