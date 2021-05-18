<?php

namespace Northwestern\SysDev\SOA\Providers;

use Illuminate\Routing\Route;
use Northwestern\SysDev\SOA\EventHub;
use Illuminate\Support\ServiceProvider;
use Northwestern\SysDev\SOA\Auth\Strategy\OpenAM11;
use Northwestern\SysDev\SOA\Auth\Strategy\WebSSOStrategy;
use Northwestern\SysDev\SOA\Console\Commands;
use Northwestern\SysDev\SOA\Http\Middleware\VerifyEventHubHMAC;
use Northwestern\SysDev\SOA\Routing\EventHubWebhookRegistration;
use Northwestern\SysDev\SOA\DirectorySearch;
use Northwestern\SysDev\SOA\WebSSO;
use Northwestern\SysDev\SOA\WebSSOImpl\ApigeeAgentless;
use Northwestern\SysDev\SOA\WebSSOImpl\OpenAM11Api;
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Azure\AzureExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;

class NuSoaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/nusoa.php', 'nusoa');
    } // end register

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/nusoa.php' => config_path('nusoa.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\EventHub\QueueOverview::class,
                Commands\EventHub\TopicOverview::class,
                Commands\EventHub\WebhookStatus::class,
                Commands\EventHub\WebhookToggle::class,
                Commands\EventHub\WebhookConfiguration::class,

                Commands\MakeWebSSO::class,
            ]);
        }

        $this->bootEventHub();
        $this->bootWebSSO();
        $this->bootAzureAdSSO();

        $ds = new DirectorySearch(EventHub\Guzzle\RetryClient::make());
        $this->app->instance(DirectorySearch::class, $ds);
    } // end boot

    private function bootWebSSO()
    {
        $http = EventHub\Guzzle\RetryClient::make();
        $url = (string) config('app.url');
        $sso_config = (array) config('nusoa.sso');

        switch (config('nusoa.sso.strategy')) {
            case 'forgerock-direct':
                $sso = new OpenAM11Api($http, $url, $sso_config);
                $auth_strategy = new OpenAM11($sso);
            break;

            default:
            case 'apigee':
                $sso = new ApigeeAgentless($http, $url, $sso_config);
                $auth_strategy = new OpenAM11($sso);
            break;
        }
        
        $this->app->instance(WebSSO::class, $sso);
        $this->app->instance(WebSSOStrategy::class, $auth_strategy);
    }

    private function bootAzureAdSSO()
    {
        Event::listen(SocialiteWasCalled::class, AzureExtendSocialite::class);

        // If someone has set it manually, respect that.
        if (config('services.azure.redirect')) {
            return;
        }

        //dd(route('login-oauth-callback'));
        //config(['services.azure.redirect' => 'foozball']);
    }

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
            (string) config('nusoa.eventHub.baseUrl'),
            (string) config('nusoa.eventHub.apiKey'),
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
