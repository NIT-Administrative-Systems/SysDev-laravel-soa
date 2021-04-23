<?php

namespace Northwestern\SysDev\SOA\Tests\Exceptions;

use Northwestern\SysDev\SOA\Exceptions\ApigeeAuthenticationError;
use Orchestra\Testbench\TestCase;

class ApigeeAuthenticationErrorTest extends TestCase
{
    /** @test */
    public function throwable()
    {
        $this->expectExceptionMessageMatches('/WEBSSO_API_KEY/i');

        throw new ApigeeAuthenticationError('https://apigee.example.org');
    }
}