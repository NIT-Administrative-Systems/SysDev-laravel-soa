<?php

namespace Northwestern\SysDev\SOA\Laravel;

use Illuminate\Support\ServiceProvider;

class NuSoaServiceProvider extends ServiceProvider
{
    const CONFIG = __DIR__.'/../../config/nusoa.php';

    public function register()
    {
        $this->mergeConfigFrom(self::CONFIG, 'nusoa');
    } // end register

    public function boot()
    {
        $this->publishes([self::CONFIG => config_path('nusoa.php')]);
    } // end boot
} // end NuSoaServiceProvider
