<?php

namespace Northwestern\SysDev\SOA\Providers;

use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Northwestern\SysDev\SOA\Console\Commands;
use Northwestern\SysDev\SOA\DirectorySearch;
use Northwestern\SysDev\SOA\EventHub;
use Northwestern\SysDev\SOA\Http\Middleware\VerifyEventHubHMAC;
use Northwestern\SysDev\SOA\Routing\EventHubWebhookRegistration;

class NuSoaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/nusoa.php', 'nusoa');
        $this->mergeConfigFrom(__DIR__ . '/../../config/duo.php', 'duo');
    } // end register

    public function boot()
    {
        $this->app['router']->pushMiddlewareToGroup('web', \Northwestern\SysDev\SOA\Http\Middleware\SsoLogger::class);

        $this->publishes([
            __DIR__ . '/../../config/nusoa.php' => config_path('nusoa.php'),
            __DIR__ . '/../../config/duo.php' => config_path('duo.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../Models' => model_path(),
        ], 'models');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\EventHub\QueueOverview::class,
                Commands\EventHub\TopicOverview::class,
                Commands\EventHub\WebhookStatus::class,
                Commands\EventHub\WebhookToggle::class,
                Commands\EventHub\WebhookConfiguration::class,
                Commands\MakeWebSSO::class,
                Commands\MakeSsoLogMigration::class,
                Commands\MakeDuo::class,
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
