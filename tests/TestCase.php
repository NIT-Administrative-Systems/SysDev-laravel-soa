<?php

namespace Northwestern\SysDev\SOA\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Exception\RequestException;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Northwestern\SysDev\SOA\Laravel\NuSoaServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($application)
    {
        return [NuSoaServiceProvider::class];
    } // end getPackageProviders

    protected function mockedResponse($status_code, $body, $headers = [])
    {
        $headers = array_merge(['Content-Type' => 'application/json'], $headers);

        $mock = new MockHandler([
            new Response($status_code, $headers, $body),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    } // end mockedResponse

    protected function mockedConnError()
    {
        $mock = new MockHandler([
            new RequestException('Connection timed out', new Request('GET', 'dummy')),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    } // end mockedConnError

} // end DirectoySearchTest
