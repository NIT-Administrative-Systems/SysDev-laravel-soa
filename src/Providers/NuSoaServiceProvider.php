<?php

namespace Northwestern\SysDev\SOA\Providers;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Northwestern\SysDev\SOA\Auth\OAuth2\NorthwesternAzureExtendSocialite;
use Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier\Contract\TokenVerifierInterface;
use Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier\MultiTenantAzureTokenVerifier;
use Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier\NorthwesternAzureTokenVerifier;
use Northwestern\SysDev\SOA\Auth\Strategy\OpenAM11;
use Northwestern\SysDev\SOA\Auth\Strategy\WebSSOStrategy;
use Northwestern\SysDev\SOA\Console\Commands;
use Northwestern\SysDev\SOA\DirectorySearch;
use Northwestern\SysDev\SOA\EventHub;
use Northwestern\SysDev\SOA\Http\Middleware\VerifyEventHubHMAC;
use Northwestern\SysDev\SOA\Routing\EventHubWebhookRegistration;
use Northwestern\SysDev\SOA\WebSSO;
use Northwestern\SysDev\SOA\WebSSOImpl\ApigeeAgentless;
use Northwestern\SysDev\SOA\WebSSOImpl\OpenAM11Api;
use SocialiteProviders\Manager\SocialiteWasCalled;

class NuSoaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/nusoa.php', 'nusoa');
    } // end register

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/nusoa.php' => config_path('nusoa.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\EventHub\QueueOverview::class,
                Commands\EventHub\TopicOverview::class,
                Commands\EventHub\WebhookStatus::class,
                Commands\EventHub\WebhookToggle::class,
                Commands\EventHub\WebhookConfiguration::class,
                Commands\EventHub\RestoreMessagesFromDLQ::class,

                Commands\MakeWebSSO::class,
                Commands\ShowOAuthCallbackUrl::class,
            ]);
        }

        $this->bootEventHub();
        $this->bootWebSSO();

        Event::listen(SocialiteWasCalled::class, NorthwesternAzureExtendSocialite::class);

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

        $this->bootAzureSSO();
    }

    private function bootAzureSSO(): void
    {
        $verifier = config('services.northwestern-azure.token_verifier', 'northwestern');
        $verifierClass = match ($verifier) {
            'common' => MultiTenantAzureTokenVerifier::class,
            'northwestern' => NorthwesternAzureTokenVerifier::class,
            default => Str::start($verifier, '\\'),
        };

        throw_unless(class_exists($verifierClass), new \InvalidArgumentException('Verifier for services.northwestern-azure.token-verifier not found'));

        $this->app->bind(TokenVerifierInterface::class, $verifierClass);
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

        /**
         * Register an EventHub webhook for this route.
         *
         * @param string $queue The EventHub queue name
         * @param array $additional_settings Optional webhook configuration overrides
         */
        Route::macro('eventHubWebhook', function ($queue, $additional_settings = []) {
            /** @var Route $this */
            $url = url($this->uri());

            $registry = resolve(EventHubWebhookRegistration::class);
            $registry->registerHookToRoute($queue, $url, $additional_settings);

            return $this;
        });

        /**
         * Register an EventHub webhook with conditional activation.
         *
         * The webhook is always registered, but the condition determines its active state.
         * When false, the webhook starts paused, which is useful for production-only
         * webhooks or safe testing scenarios.
         *
         * Running `eventhub:webhook:configure` enforces this state on execution by
         * automatically pausing webhooks that may have been manually unpaused for
         * testing purposes.
         *
         * @param bool $condition When true, webhook is active; when false, webhook is paused
         * @param string $queue The EventHub queue name
         * @param array $additional_settings Optional webhook configuration overrides
         */
        Route::macro('eventHubWebhookActiveWhen', function (bool $condition, string $queue, array $additional_settings = []) {
            /** @var Route $this */
            $url = url($this->uri());

            $registry = resolve(EventHubWebhookRegistration::class);
            $registry->registerHookToRoute($queue, $url, $additional_settings, active: $condition);

            return $this;
        });
    } // end bootEventHub
} // end NuSoaServiceProvider
