<?php

namespace TunnelConflux\DevCrud;

use Illuminate\Support\ServiceProvider;

class DevCrudServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //$this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/views', "easy-crud");
        //$this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/easy-crud'),
        ]);
    }

    public function register()
    {
    }
}