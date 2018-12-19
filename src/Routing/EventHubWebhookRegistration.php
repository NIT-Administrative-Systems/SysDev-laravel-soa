<?php

namespace Northwestern\SysDev\SOA\Routing;

class EventHubWebhookRegistration
{
    protected $hooks = [];
    protected $use_hmac = false;

    public function __construct()
    {
        $this->hmac_secret = config('nusoa.eventHub.hmacVerificationSharedSecret');
        $this->hmac_algorithm = config('nusoa.eventHub.hmacVerificationAlgorithmForRegistration');
        $this->hmac_header_name = config('nusoa.eventHub.hmacVerificationHeader');

        $this->use_hmac = $this->hmac_secret !== null;
    } // end __construct

    public function registerHookToRoute($queue, $url, $additional_settings = [])
    {
        $hook = new Webhook($queue, $url);

        if ($this->use_hmac === true) {
            $hook->setHmac($this->hmac_secret, $this->hmac_algorithm, $this->hmac_header_name);
        }

        $hook->setAdditionalSettings($additional_settings);

        $this->hooks[] = $hook;
    } // end registerHookToRoute

    public function getHooks()
    {
        return $this->hooks;
    } // end getHooks

} // end EventHubWebhookRegistration
