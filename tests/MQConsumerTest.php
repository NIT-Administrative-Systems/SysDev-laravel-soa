<?php

namespace Northwestern\SysDev\SOA\Tests;

use Northwestern\SysDev\SOA\MQ\Consumer;
use Northwestern\SysDev\SOA\Tests\TestCase;

class MQConsumerTest extends TestCase
{
    protected $service = Consumer::class;

    public function testReceive()
    {
        $message = 'message text';
        $this->api->setHttpClient($this->mockedResponse(200, $message));

        $this->assertEquals($message, $this->api->pullMessage('topic'));
    } // end testReceive

    public function testEmptyQueue200()
    {
        // When configured as a plain-text service acct
        $this->api->setHttpClient($this->mockedResponse(200, 'no messages'));
        $this->assertNull($this->api->pullMessage('topic'));

        // When configured as an XML service acct
        $this->api->setHttpClient($this->mockedResponse(200, '<Result>no messages</Result>'));
        $this->assertNull($this->api->pullMessage('topic'));
    } // end testEmptyQueue

    public function testEmptyQueue500()
    {
        $this->api->setHttpClient($this->mockedResponse(500, 'no messages'));

        $this->assertNull($this->api->pullMessage('topic'));
    } // end testEmptyQueue


    public function testNoSuchQueue()
    {
        $this->api->setHttpClient($this->mockedResponse(403, '<ErrorMessage><Text>Error!</Text><ExceptionNumber>1234</ExceptionNumber><ExceptionText>User generated exception</ExceptionText><ExceptionDetail><File>ThrowException.cpp</File><Line>1</Line><Function>SqlThrowExceptionStatement::execute</Function><Type>Thing</Type><Name>Flow#Composite</Name><Label>Flow</Label><Catalog>Thing</Catalog><Severity>1</Severity><Number>1234</Number><Text>User generated exception</Text><Insert><Type>1</Type><Text>Destination Queue not found for Topic: topic</Text></Insert></ExceptionDetail></ErrorMessage>'));

        $this->assertFalse($this->api->pullMessage('topic'));
    } // end testError

    public function testBadApiKey()
    {
        $this->api->setHttpClient($this->mockedResponse(401, '{"fault":{"faultstring":"Invalid ApiKey","detail":{"errorcode":"oauth.v2.InvalidApiKey"}}}'));

        $this->assertFalse($this->api->pullMessage('topic'));
    } // end testBadApiKey

    public function testBadUser()
    {
        $this->api->setHttpClient($this->mockedResponse(401, '<html> <head> <META http-equiv="Content-Type" content="text/html; charset=UTF-8"/> <title>401 Authorization Required</title> </head> <body> <h1>Thing</h1> <h1>401 Authorization Required</h1> This server could not verify that you are authorized to access the document requested.<br/> </body> </html>'));

        $this->assertFalse($this->api->pullMessage('topic'));
    } // end testBadUser

    public function testConnectionFailure()
    {
        $this->api->setHttpClient($this->mockedConnError());

        $this->assertFalse($this->api->pullMessage('topic'));
    } // end testConnectionFailure

} // end DirectoySearchTest
