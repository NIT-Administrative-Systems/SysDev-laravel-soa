<?php

namespace Northwestern\SysDev\SOA\Tests\WebSSO;

use GuzzleHttp\Client;
use Northwestern\SysDev\SOA\Exceptions\ApigeeAuthenticationError;
use Northwestern\SysDev\SOA\WebSSO;
use Northwestern\SysDev\SOA\Tests\TestCase;
use Northwestern\SysDev\SOA\Tests\Concerns\TestsOpenAM11;
use Northwestern\SysDev\SOA\WebSSOImpl\ApigeeAgentless;

class OpenAM11Test extends TestCase
{
    use TestsOpenAM11;

    protected $service = WebSSO::class;

    public function setUp(): void
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

    /** @test */
    public function valid_session()
    {
        $netid = 'netid123';
        $this->api->setHttpClient($this->mockedResponse(200, $this->ssoResponseJson($netid)));

        $user = $this->api->getUser('test-token');
        $this->assertEquals($netid, $user->getNetid());
    }

    /** @test */
    public function invalid_session()
    {
        $this->api->setHttpClient($this->mockedResponse(407, ''));

        $user = $this->api->getUser('test-token');
        $this->assertNull($user);
    }

    /** @test */
    public function invalid_apigee_key()
    {
        $this->expectException(ApigeeAuthenticationError::class);

        $this->api->setHttpClient($this->mockedResponse(401, ''));

        $this->api->getUser('test-token');
    }

    /** @test */
    public function connectivity_error()
    {
        $this->api->setHttpClient($this->mockedConnError());

        $this->expectException(\Exception::class);
        $this->api->getUser('random');
    }

    /** @test */
    public function login_url()
    {
        $this->assertNotEmpty($this->api->getLoginUrl());
        $this->assertNotEmpty($this->api->getLoginUrl('/foobar'));
    }
}
