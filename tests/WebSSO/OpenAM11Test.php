<?php

namespace Northwestern\SysDev\SOA\Tests\WebSSO;

use GuzzleHttp\Client;
use Northwestern\SysDev\SOA\WebSSO;
use GuzzleHttp\Exception\RequestException;
use Northwestern\SysDev\SOA\Tests\TestCase;
use Northwestern\SysDev\SOA\WebSSOImpl\OpenAM11Api;

class OpenAM11Test extends TestCase
{
    protected $service = WebSSO::class;

    public function setUp(): void
    {
        parent::setUp();

        // Test sets this up too early for the impls to swap, so make it explicitly.
        $this->api = new OpenAM11Api(resolve(Client::class), config('app.url'), config('nusoa.sso'));
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('nusoa.sso.enableForgerock', true);
        $app['config']->set('duo.enabled', true);
    }

    /** @test */
    public function valid_session()
    {
        $netid = 'netid123';
        $this->api->setHttpClient($this->mockedResponse(200, $this->ssoJson($netid)));

        $user = $this->api->getUser('test-token');
        $this->assertEquals($netid, $user->getNetid());
    }

    /** @test */
    public function invalid_session()
    {
        $this->api->setHttpClient($this->mockedResponse(401, ''));

        $user = $this->api->getUser('test-token');
        $this->assertNull($user);
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

    private function ssoJson(string $netid = 'dog1234', bool $isDuoAuthed = false): string
    {
        return json_encode([
            'username' => $netid,
            'universalId' => sprintf('id=%s,ou=user,ou=am-config,dc=northwestern,dc=edu', $netid),
            'realm' => '/',
            'latestAccessTime' => '2019-11-18T14:17:03Z',
            'maxIdleExpirationTime' => '2019-11-18T18:17:03Z',
            'maxSessionExpirationTime' => '2019-11-19T02:17:02Z',
            'properties' => [
                'AMCtxId' => 'faa9b02e-df32-4c3b-9775-9c1e310d7265-91217',
                'isDuoAuthenticated' => $isDuoAuthed,
            ],
        ]);
    }
}
