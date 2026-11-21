<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\NeedsTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Confiar en todos los proxies (Easypanel/Traefik)
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO |
                     Request::HEADER_X_FORWARDED_AWS_ELB
        );

        // Activa la empresa (tenant) del usuario en cada request web
        $middleware->web(append: [
            NeedsTenant::class,
        ]);

        // IMPORTANTE: la empresa debe activarse ANTES de resolver los modelos
        // de la URL (route-model-binding); si no, {cliente}, {categoria}, etc.
        // podrían cargarse sin el filtro de empresa.
        $middleware->prependToPriorityList(
            before: SubstituteBindings::class,
            prepend: NeedsTenant::class,
        );

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'rol' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
