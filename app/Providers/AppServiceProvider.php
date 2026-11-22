<?php

namespace App\Providers;

use App\Services\FacturacionService;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FacturacionService::class, fn () => new FacturacionService());
    }

    public function boot(): void
    {
        // Fechas relativas en español ("hace 2 minutos")
        Carbon::setLocale(config('app.locale'));

        // Paginación con el estilo del sistema
        Paginator::defaultView('vendor.pagination.sistema');
        Paginator::defaultSimpleView('vendor.pagination.sistema');

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
