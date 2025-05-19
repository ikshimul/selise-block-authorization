<?php

namespace Inzamam\SeliseBlockAuthorization;

use Illuminate\Support\ServiceProvider;

class BlockAuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind the AuthService to the facade name
        $this->app->singleton('selise-block-auth-service', function () {
            return new AuthService();
        });

        // Merge package config with app config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/selise-block-authorization.php',
            'selise-block-authorization'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish config file
        $this->publishes([
            __DIR__ . '/../config/selise-block-authorization.php' => config_path('selise-block-authorization.php'),
        ], 'selise-block-authorization-config');

        // Publish migration files
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'selise-block-authorization-migrations');

        // Optionally allow publishing everything with a single tag
        $this->publishes([
            __DIR__ . '/../config/selise-block-authorization.php' => config_path('selise-block-authorization.php'),
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'selise-block-authorization');

        // Load migrations without publishing (auto-load from package)
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

    }
}