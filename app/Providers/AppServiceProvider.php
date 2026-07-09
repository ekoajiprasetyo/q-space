<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $customPublicPath = env('APP_PUBLIC_PATH');

        if (!$customPublicPath) {
            // Legacy fallback for the older cPanel layout.
            $legacyPublicPath = base_path('../public_html/space.q-link');
            if (file_exists($legacyPublicPath)) {
                $customPublicPath = $legacyPublicPath;
            }
        }

        if (file_exists($customPublicPath)) {
            $this->app->usePublicPath($customPublicPath);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS unconditionally on hosting environment
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
