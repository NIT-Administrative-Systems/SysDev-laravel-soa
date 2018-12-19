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

} // end WebhookRouteRegistrationTest
