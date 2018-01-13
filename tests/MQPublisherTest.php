<?php

namespace Northwestern\SysDev\SOA\Tests;

use Northwestern\SysDev\SOA\MQ\Publisher;
use Northwestern\SysDev\SOA\Tests\TestCase;

class MQPublisherTest extends TestCase
{
    protected $service = Publisher::class;

    public function testPublish()
    {
        $this->api->setHttpClient($this->mockedResponse(200, 'MD " MQSTR'));

        $this->assertTrue($this->api->queueText('msg', 'topic'));
    } // end testPublish

    public function testBadTopic()
    {
        // 200 is indeed correct
        $this->api->setHttpClient($this->mockedResponse(200, '<ErrorMessage>An error occurred processing your request, please contact the IT department for assistance.</ErrorMessage>'));

        $this->assertFalse($this->api->queueText('msg', 'topic-that-doesnt-exist'));
    } // end testBadTopic

    public function testBadApiKey()
    {
        $this->api->setHttpClient($this->mockedResponse(401, '{"fault":{"faultstring":"Invalid ApiKey","detail":{"errorcode":"oauth.v2.InvalidApiKey"}}}'));

        $this->assertFalse($this->api->queueText('msg', 'topic'));
    } // end testBadApiKey

    public function testBadUser()
    {
        $this->api->setHttpClient($this->mockedResponse(401, '<html> <head> <META http-equiv="Content-Type" content="text/html; charset=UTF-8"/> <title>401 Authorization Required</title> </head> <body> <h1>Thing</h1> <h1>401 Authorization Required</h1> This server could not verify that you are authorized to access the document requested.<br/> </body> </html>'));

        $this->assertFalse($this->api->queueText('msg', 'topic'));
    } // end testBadUser

    public function testConnectionFailure()
    {
        $this->api->setHttpClient($this->mockedConnError());

        $this->assertFalse($this->api->queueText('msg', 'topic'));
    } // end testConnectionFailure

} // end MQPublisherTest
