<?php

namespace Northwestern\SysDev\SOA\Tests\Exceptions;

use PHPUnit\Framework\Attributes\Test;
use Northwestern\SysDev\SOA\Exceptions\InsecureSsoError;
use Orchestra\Testbench\TestCase;

class InsecureSsoErrorTest extends TestCase
{
    #[Test]
    public function throwable()
    {
        $this->expectExceptionMessageMatches('/https/');

        throw new InsecureSsoError;
    }
}
