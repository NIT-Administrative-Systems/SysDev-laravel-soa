<?php

namespace Northwestern\SysDev\SOA\Providers;

use Illuminate\Support\ServiceProvider;
use Northwestern\SysDev\SOA\Console\Commands;

class NuSoaServiceProvider extends ServiceProvider
{
    const CONFIG = __DIR__.'/../../config/nusoa.php';

    public function register()
    {
        $this->mergeConfigFrom(self::CONFIG, 'nusoa');
    } // end register

    public function boot()
    {
        $this->publishes([self::CONFIG => config_path('nusoa.php')], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([Commands\MakeCheckQueue::class]);
        }
    } // end boot
} // end NuSoaServiceProvider
