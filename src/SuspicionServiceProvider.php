<?php

namespace Livingstoneco\Suspicion;

use Illuminate\Support\ServiceProvider;
use Livingstoneco\Suspicion\Http\Middleware\IsRequestSuspicious;

class SuspicionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    { 
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'suspicion');

        // Register the main class to use with the facade
        $this->app->singleton('suspicion', function () {
            return new Suspicion;
        });
    }
}
