<?php

namespace App\Providers;

use App\Services\Elevador;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra os bindings no container de injeção de dependência do Laravel.
     *
     * A classe Elevador é registrada via bind() para que o framework
     * possa resolvê-la automaticamente quando injetada nos Commands,
     * Controllers ou qualquer outro lugar da aplicação.
     */
    public function register(): void
    {
        $this->app->bind(Elevador::class, function () {
            return new Elevador(capacidade: 10);
        });
    }

    public function boot(): void {}
}
