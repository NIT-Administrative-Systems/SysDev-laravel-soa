<?php

namespace Northwestern\SysDev\SOA\Tests\Traits;

trait TestMQCommon
{
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

} // end MQCommon
