<?php

namespace Lackone\LaravelGatewayWorker\Providers;

use Illuminate\Support\ServiceProvider;
use Lackone\LaravelGatewayWorker\Commands\GatewayWorkerCommand;

class GatewayWorkerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (app()->environment() == 'local' || app()->environment() == 'testing') {
            error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
        } else {
            error_reporting(0);
        }

        $this->mergeConfigFrom(__DIR__ . '/../../config/gw.php', 'gw');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GatewayWorkerCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/gw.php' => config_path('gw.php'),
        ], 'config');
    }
}