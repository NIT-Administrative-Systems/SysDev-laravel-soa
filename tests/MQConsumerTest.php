<?php

namespace Northwestern\SysDev\SOA\Tests;

use Northwestern\SysDev\SOA\MQ\Consumer;
use Northwestern\SysDev\SOA\Tests\TestCase;
use Northwestern\SysDev\SOA\Tests\Traits\TestMQCommon;

class MQConsumerTest extends TestCase
{
    use TestMQCommon;
    protected $service = Consumer::class;

    public function testReceive()
    {
        $message = 'message text';
        $this->api->setHttpClient($this->mockedResponse(200, $message));

        $this->assertEquals($message, $this->api->pullMessage('topic'));
    } // end testReceive

    public function testEmptyQueue()
    {
        $this->api->setHttpClient($this->mockedResponse(500, 'no messages'));

        $this->assertNull($this->api->pullMessage('topic'));
    } // end testEmptyQueue

    public function testNoSuchQueue()
    {
        $this->api->setHttpClient($this->mockedResponse(403, '<ErrorMessage><Text>Error!</Text><ExceptionNumber>1234</ExceptionNumber><ExceptionText>User generated exception</ExceptionText><ExceptionDetail><File>ThrowException.cpp</File><Line>1</Line><Function>SqlThrowExceptionStatement::execute</Function><Type>Thing</Type><Name>Flow#Composite</Name><Label>Flow</Label><Catalog>Thing</Catalog><Severity>1</Severity><Number>1234</Number><Text>User generated exception</Text><Insert><Type>1</Type><Text>Destination Queue not found for Topic: topic</Text></Insert></ExceptionDetail></ErrorMessage>'));

        $this->assertFalse($this->api->pullMessage('topic'));
    } // end testError

} // end DirectoySearchTest
