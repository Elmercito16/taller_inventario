<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // ← IMPORTANTE: Añadir esta línea

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
        // Forzar HTTPS en producción
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            
            // Opcional: asegurar que el request también lo detecte
            $this->app['request']->server->set('HTTPS', 'on');
        }
    }
}
