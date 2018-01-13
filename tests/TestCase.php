<?php

namespace Northwestern\SysDev\SOA\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Northwestern\SysDev\SOA\Laravel\NuSoaServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($application)
    {
        return [NuSoaServiceProvider::class];
    } // end getPackageProviders

} // end DirectoySearchTest
