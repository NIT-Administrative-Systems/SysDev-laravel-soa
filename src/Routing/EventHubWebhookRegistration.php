<?php

namespace Northwestern\SysDev\SOA\Routing;

class EventHubWebhookRegistration
{
    protected array $hooks = [];

    protected bool $use_hmac = false;

    private ?string $hmac_secret;

    private ?string $hmac_algorithm;

    private ?string $hmac_header_name;

    public function __construct()
    {
        $this->hmac_secret = config('nusoa.eventHub.hmacVerificationSharedSecret');
        $this->hmac_algorithm = config('nusoa.eventHub.hmacVerificationAlgorithmForRegistration');
        $this->hmac_header_name = config('nusoa.eventHub.hmacVerificationHeader');

        $this->use_hmac = $this->hmac_secret !== null;
    }

    public function registerHookToRoute($queue, $url, $additional_settings = []): void
    {
        $hook = new Webhook($queue, $url);

        if ($this->use_hmac === true) {
            $hook->setHmac($this->hmac_secret, $this->hmac_algorithm, $this->hmac_header_name);
        }

        $hook->setAdditionalSettings($additional_settings);

        $this->hooks[] = $hook;
    }

    public function getHooks(): array
    {
        return $this->hooks;
    }
}
