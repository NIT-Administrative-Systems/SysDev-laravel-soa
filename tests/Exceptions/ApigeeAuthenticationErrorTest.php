<?php

namespace Northwestern\SysDev\SOA\Tests\Exceptions;

use PHPUnit\Framework\Attributes\Test;
use Northwestern\SysDev\SOA\Exceptions\ApigeeAuthenticationError;
use Orchestra\Testbench\TestCase;

class ApigeeAuthenticationErrorTest extends TestCase
{
    #[Test]
    public function throwable(): void
    {
        $this->expectExceptionMessageMatches('/WEBSSO_API_KEY/i');

        throw new ApigeeAuthenticationError('https://apigee.example.org');
    }
}
