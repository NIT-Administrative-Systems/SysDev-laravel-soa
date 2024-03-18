<?php

namespace Northwestern\SysDev\SOA\Tests\Feature;

use Northwestern\SysDev\SOA\DirectorySearch;
use Northwestern\SysDev\SOA\Tests\TestCase;

final class DirectoySearchTest extends TestCase
{
    protected $service = DirectorySearch::class;

    public function testGoodLookup(): void
    {
        $this->api->setHttpClient($this->mockedResponse(200, '{"results":[{ "displayName" : [ "Test E User" ], "givenName" : [ "Test" ], "sn" : [ "User" ], "eduPersonNickname" : [ "test" ], "mail" : "test@example.org", "nuStudentEmail" : "", "title" : [ "Tester" ], "telephoneNumber" : "123 1231234", "nuTelephoneNumber2" : "", "nuTelephoneNumber3" : "", "nuOtherTitle" : "" }]}'));

        $info = $this->api->lookupByNetId('test', 'public');
        $this->assertArrayHasKey('mail', $info);
    } // end testGoodLookup

    public function testBadLookup(): void
    {
        $this->api->setHttpClient($this->mockedResponse(404, '{"errorCode":404,"errorMessage":"No Data Found for = uid=test"}'));

        $this->assertFalse($this->api->lookupByNetId('test', 'public'));
        $this->assertNotEmpty($this->api->getLastError());
    } // end testBadLookup

    public function testBadPerms(): void
    {
        $this->api->setHttpClient($this->mockedResponse(401, '{"fault":{"faultstring":"Invalid ApiKey for given resource","detail":{"errorcode":"oauth.v2.InvalidApiKeyForGivenResource"}}}'));

        $this->assertFalse($this->api->lookupByNetId('test', 'public'));
        $this->assertNotEmpty($this->api->getLastError());
    } // end testBadPerms

    public function testBadApiKey(): void
    {
        $this->api->setHttpClient($this->mockedResponse(401, '{"fault":{"faultstring":"Failed to resolve API Key variable request.header.apikey","detail":{"errorcode":"steps.oauth.v2.FailedToResolveAPIKey"}}}'));

        $this->assertFalse($this->api->lookupByNetId('test', 'public'));
        $this->assertNotEmpty($this->api->getLastError());
    } // end testBadApiKey

    public function testConnectionFailure(): void
    {
        $this->api->setHttpClient($this->mockedConnError());

        $this->assertFalse($this->api->lookupByNetId('test', 'public'));
        $this->assertNotEmpty($this->api->getLastError());
    } // end testConnectionFailure

} // end DirectoySearchTest
