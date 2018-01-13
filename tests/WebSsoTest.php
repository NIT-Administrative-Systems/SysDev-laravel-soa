<?php

namespace Northwestern\SysDev\SOA\Tests;

use Northwestern\SysDev\SOA\WebSSO;
use GuzzleHttp\Exception\RequestException;
use Northwestern\SysDev\SOA\Tests\TestCase;

class WebSsoTest extends TestCase
{
    protected $service = WebSSO::class;

    public function testValidSession()
    {
        $this->api->setHttpClient($this->mockedResponse(200, "userdetails.token.id=test-token\nuserdetails.attribute.name=UserToken\nuserdetails.attribute.value=test-id"));

        $this->assertEquals('test-id', $this->api->getNetId('test-token'));
    } // end testValidSession

    public function testInvalidSession()
    {
        $this->api->setHttpClient($this->mockedResponse(401, 'exception.name=com.sun.identity.idsvcs.TokenExpired Token is NULL'));

        $this->assertFalse($this->api->getNetId('random token'));
    } // end testInvalidSession

    public function testConnectivityError()
    {
        $this->api->setHttpClient($this->mockedConnError());

        $this->expectException(\Exception::class);
        $this->api->getNetId('random');
    } // end testConnectivityError

    public function testLoginUrl()
    {
        $this->assertNotEmpty($this->api->getLoginUrl());
        $this->assertNotEmpty($this->api->getLoginUrl('/foobar'));
    } // end testLoginUrl

} // end WebSsoTest
