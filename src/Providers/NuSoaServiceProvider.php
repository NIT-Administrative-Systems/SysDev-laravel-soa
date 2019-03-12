<?php

namespace Northwestern\SysDev\SOA\Providers;

use Illuminate\Routing\Route;
use Northwestern\SysDev\SOA\EventHub;
use Illuminate\Support\ServiceProvider;
use Northwestern\SysDev\SOA\Console\Commands;
use Northwestern\SysDev\SOA\Http\Middleware\VerifyEventHubHMAC;
use Northwestern\SysDev\SOA\Routing\EventHubWebhookRegistration;
use Northwestern\SysDev\SOA\DirectorySearch;

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
            $this->commands([
                Commands\MakeCheckQueue::class,
                Commands\EventHub\QueueOverview::class,
                Commands\EventHub\TopicOverview::class,
                Commands\EventHub\WebhookStatus::class,
                Commands\EventHub\WebhookToggle::class,
                Commands\EventHub\WebhookConfiguration::class,
            ]);
        }

        $this->bootEventHub();

	$ds = new DirectorySearch(EventHub\Guzzle\RetryClient::make());
	$this->app->instance(DirectorySearch::class, $ds);

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

        // This singleton will hold all the routes registered as webhook endpoints
        $this->app->singleton(EventHubWebhookRegistration::class, function ($app) {
            return new EventHubWebhookRegistration;
        });

        Route::macro('eventHubWebhook', function ($queue, $additional_settings = []) {
            $url = url($this->uri());

            $registry = resolve(EventHubWebhookRegistration::class);
            $registry->registerHookToRoute($queue, $url, $additional_settings);

            return $this;
        });
    } // end bootEventHub

} // end NuSoaServiceProvider
