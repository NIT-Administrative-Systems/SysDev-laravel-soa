<?php

namespace Northwestern\SysDev\SOA\Tests\WebSSO;

use PHPUnit\Framework\Attributes\Test;
use GuzzleHttp\Client;
use Northwestern\SysDev\SOA\Exceptions\ApigeeAuthenticationError;
use Northwestern\SysDev\SOA\Tests\Concerns\TestsOpenAM11;
use Northwestern\SysDev\SOA\Tests\TestCase;
use Northwestern\SysDev\SOA\WebSSO;
use Northwestern\SysDev\SOA\WebSSOImpl\ApigeeAgentless;

final class OpenAM11Test extends TestCase
{
    use TestsOpenAM11;

    protected $service = WebSSO::class;

    protected function setUp(): void
    {
        parent::setUp();

        // Test sets this up too early for the impls to swap, so make it explicitly.
        $this->api = new ApigeeAgentless(resolve(Client::class), config('app.url'), config('nusoa.sso'));
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('nusoa.sso.strategy', 'apigee');
        $app['config']->set('duo.enabled', true);
    }

    #[Test]
    public function valid_session(): void
    {
        $netid = 'netid123';
        $this->api->setHttpClient($this->mockedResponse(200, $this->ssoResponseJson($netid)));

        $user = $this->api->getUser('test-token');
        $this->assertEquals($netid, $user->getNetid());
    }

    #[Test]
    public function invalid_session(): void
    {
        $this->api->setHttpClient($this->mockedResponse(407, ''));

        $user = $this->api->getUser('test-token');
        $this->assertNull($user);
    }

    #[Test]
    public function invalid_apigee_key(): void
    {
        $this->expectException(ApigeeAuthenticationError::class);

        $this->api->setHttpClient($this->mockedResponse(401, ''));

        $this->api->getUser('test-token');
    }

    #[Test]
    public function connectivity_error(): void
    {
        $this->api->setHttpClient($this->mockedConnError());

        $this->expectException(\Exception::class);
        $this->api->getUser('random');
    }

    #[Test]
    public function login_url(): void
    {
        $this->assertNotEmpty($this->api->getLoginUrl());
        $this->assertNotEmpty($this->api->getLoginUrl('/foobar'));
    }
}
