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
        $this->loadRoutesFrom(__DIR__ . '/../routes/testing.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'suspicion');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('suspicion.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views/errors' => resource_path('views/errors'),
            ], 'views');
        }
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
