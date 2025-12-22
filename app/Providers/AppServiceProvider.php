<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // ← IMPORTANTE: Añadir esta línea
use App\Services\FacturacionService; // 👈 AÑADIDO

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 👇 NUEVO: Registrar FacturacionService como singleton
        $this->app->singleton(FacturacionService::class, function ($app) {
            return new FacturacionService();
        });
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
