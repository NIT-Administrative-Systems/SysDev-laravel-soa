<?php

namespace Northwestern\SysDev\SOA\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Northwestern\SysDev\SOA\Providers\NuSoaServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $service = null;

    protected $api;

    protected function setUp(): void
    {
        parent::setUp();
        $this->api = @$this->app->make($this->service);
    } // end setUp

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
