<?php

namespace Northwestern\SysDev\SOA\Tests\Feature\Exceptions;

use Northwestern\SysDev\SOA\Exceptions\InsecureSsoError;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class InsecureSsoErrorTest extends TestCase
{
    #[Test]
    public function throwable(): void
    {
        $this->expectExceptionMessageMatches('/https/');

        throw new InsecureSsoError;
    }
}
