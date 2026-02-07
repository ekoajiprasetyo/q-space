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
        // Fix untuk Hosting (dimana folder public dipisah ke public_html/space.q-link)
        // Cek jika folder ../public_html/space.q-link ada, maka jadikan itu sebagai public path
        $customPublicPath = base_path('../public_html/space.q-link');
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
