<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('tymon.jwt.expired', function () {
            return \Response::make(['error' => true, 'success' => false, 'log' => ['Sessão expirada']], 401);
        });

        Event::listen('tymon.jwt.absent', function () {
            return \Response::make(['error' => true, 'success' => false, 'log' => ['O token informado não existe']], 401);
        });

        Event::listen('tymon.jwt.invalid', function () {
            return \Response::make(['error' => true, 'success' => false, 'log' => ['Token inválido']], 401);
        });

        Event::listen('tymon.jwt.user_not_found', function () {
            return \Response::make(['error' => true, 'success' => false, 'log' => ['Usuário de aplicativo não encontrado']], 401);
        });
    }
}
