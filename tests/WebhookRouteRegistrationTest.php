<?php

namespace Northwestern\SysDev\SOA\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Northwestern\SysDev\SOA\Providers\NuSoaServiceProvider;
use Northwestern\SysDev\SOA\Routing\EventHubWebhookRegistration;

class WebhookRouteRegistrationTest extends BaseTestCase
{
    protected function getPackageProviders($application)
    {
        return [NuSoaServiceProvider::class];
    } // end getPackageProviders

    public function test_route_registration()
    {
        $route = app()->router->post('/webhook/foo')->eventHubWebhook('foo.my-queue');

        $registered_hooks = resolve(EventHubWebhookRegistration::class)->getHooks();
        $this->assertEquals(1, sizeof($registered_hooks));
    } // end test_route_registration

    public function test_uses_hmac_when_configured()
    {
        $secret = 'abcdefg';
        $this->app['config']->set('nusoa.eventHub.hmacVerificationSharedSecret', $secret);

        $route = app()->router->post('/webhook/foo')->eventHubWebhook('foo.my-queue');

        $registered_hooks = resolve(EventHubWebhookRegistration::class)->getHooks();
        $hook = $registered_hooks[0]->toArray();

        $this->assertEquals(1, sizeof($hook['securityTypes']));
        $this->assertEquals(1, sizeof($hook['webhookSecurity']));
        $this->assertEquals('HMAC', $hook['securityTypes'][0]);
        $this->assertEquals($secret, $hook['webhookSecurity'][0]['secretKey']);
    } // end test_uses_hmac_when_configured

    public function test_custom_security_setup()
    {
        $this->app['config']->set('nusoa.eventHub.hmacVerificationSharedSecret', null);

        $secret = 'my-very-good-api-key';
        $route = app()->router->post('/webhook/foo')->eventHubWebhook('foo.my-queue', $this->makeApiSecurityBlock($secret));

        $registered_hooks = resolve(EventHubWebhookRegistration::class)->getHooks();
        $hook = $registered_hooks[0]->toArray();

        $this->assertEquals(1, sizeof($hook['securityTypes']));
        $this->assertEquals(1, sizeof($hook['webhookSecurity']));
        $this->assertEquals('APIKEY', $hook['securityTypes'][0]);
        $this->assertEquals($secret, $hook['webhookSecurity'][0]['apiKey']);
    } // end test_custom_security_setup

    public function test_multiple_security_modes()
    {
        $this->app['config']->set('nusoa.eventHub.hmacVerificationSharedSecret', 'hmac-key');

        $secret = 'my-very-good-api-key';
        $route = app()->router->post('/webhook/foo')->eventHubWebhook('foo.my-queue', $this->makeApiSecurityBlock('api-key'));

        $registered_hooks = resolve(EventHubWebhookRegistration::class)->getHooks();
        $hook = $registered_hooks[0]->toArray();

        $this->assertEquals(2, sizeof($hook['securityTypes']));
        $this->assertEquals(2, sizeof($hook['webhookSecurity']));
    } // end test_multiple_security_modes

    public function test_change_content_type()
    {
        $content_type = 'application/xml';
        $route = app()->router->post('/webhook/foo')->eventHubWebhook('foo.my-queue', ['contentType' => $content_type]);

        $registered_hooks = resolve(EventHubWebhookRegistration::class)->getHooks();
        $hook = $registered_hooks[0]->toArray();

        $this->assertInternalType('string', $hook['contentType']);
        $this->assertEquals($content_type, $hook['contentType']);
    } // end test_change_content_type

    protected function makeApiSecurityBlock($secret)
    {
        return [
            'securityTypes' => ['APIKEY'],
            'webhookSecurity' => [
                [
                    'securityType' => 'APIKEY',
                    'eventHubAccount' => 'dogge',
                    'topicName' => 'foo.my-queue',
                    'apiKey' => $secret,
                    'headerName' => 'x-api-key',
                ],
            ],
        ];
    } // end makeApiSecurityBlock

} // end WebhookRouteRegistrationTest
