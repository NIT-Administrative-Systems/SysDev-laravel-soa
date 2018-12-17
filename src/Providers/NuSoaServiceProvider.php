<?php

namespace Northwestern\SysDev\SOA\Providers;

use Northwestern\SysDev\SOA\EventHub;
use Illuminate\Support\ServiceProvider;
use Northwestern\SysDev\SOA\Console\Commands;
use Northwestern\SysDev\SOA\Http\Middleware\VerifyEventHubHMAC;

class NuSoaServiceProvider extends ServiceProvider
{
    const CONFIG = __DIR__.'/../../config/nusoa.php';

    public function register()
    {
        $this->mergeConfigFrom(self::CONFIG, 'nusoa');
    } // end register

    public function boot()
    {
        $this->publishes([self::CONFIG => config_path('nusoa.php')], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([Commands\MakeCheckQueue::class]);
        }

        $this->bootEventHub();
    } // end boot

    private function bootEventHub()
    {
        $classes = [
            EventHub\Queue::class,
            EventHub\DeadLetterQueue::class,
            EventHub\Topic::class,
            EventHub\Message::class,
            EventHub\Webhook::class,
        ];

        $args = [
            (string)config('nusoa.eventHub.baseUrl'),
            (string)config('nusoa.eventHub.apiKey'),
            EventHub\Guzzle\RetryClient::make(),
        ];

        foreach ($classes as $class) {
            $api = new $class(...$args);
            $this->app->instance($class, $api);
        }

        $router = $this->app['router'];
        $router->aliasMiddleware('eventhub_hmac', VerifyEventHubHMAC::class);
    } // end bootEventHub

} // end NuSoaServiceProvider
