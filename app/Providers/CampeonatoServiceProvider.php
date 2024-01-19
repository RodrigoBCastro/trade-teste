<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CampeonatoService;

class CampeonatoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CampeonatoService::class, function ($app) {
            return new CampeonatoService();
        });
    }

    public function boot()
    {
        //
    }
}
