<?php

namespace Northwestern\SysDev\SOA\Laravel;

class NuSoaServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/nusoa.php', 'nusoa');
    } // end register

    public function boot()
    {
        $this->publishes([__DIR__.'/config/nusoa.php' => config_path('nusoa.php')]);
    } // end boot
} // end NuSoaServiceProvider
