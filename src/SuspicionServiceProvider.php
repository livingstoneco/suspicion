<?php
namespace Livingstoneco\Suspicion;

use Illuminate\Support\ServiceProvider;

class SuspicionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/testing.php');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'suspicion');

        // Register the main class to use with the facade
        $this->app->singleton('suspicion', function () {
            return new Suspicion;
        });
    }
}
