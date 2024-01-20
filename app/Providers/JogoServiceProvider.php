<?php

namespace App\Providers;

use App\Services\CampeonatoService;
use Illuminate\Support\ServiceProvider;
use App\Services\JogoService;

class JogoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(JogoService::class, function ($app) {
            return new JogoService($app->make(CampeonatoService::class));
        });
    }

    public function boot()
    {
        //
    }
}
