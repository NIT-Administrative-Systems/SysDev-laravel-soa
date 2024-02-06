<?php

namespace Northwestern\SysDev\SOA\Tests\Exceptions;

use Northwestern\SysDev\SOA\Exceptions\InsecureSsoError;
use Orchestra\Testbench\TestCase;

class InsecureSsoErrorTest extends TestCase
{
    /** @test */
    public function throwable()
    {
        $this->expectExceptionMessageMatches('/https/');

        throw new InsecureSsoError;
    }
}
