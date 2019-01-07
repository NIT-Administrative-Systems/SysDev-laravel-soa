<?php

namespace Northwestern\SysDev\SOA\Routing;

class Webhook
{
    protected $queue;
    protected $delivery_url;
    protected $hmac_secret;
    protected $hmac_algorithm;
    protected $hmac_header_name;
    protected $additional_settings = [];

    public function __construct(string $queue, string $delivery_url)
    {
        $this->queue = $queue;
        $this->delivery_url = $delivery_url;
    } // end __construct

    public function setHmac(string $secret, string $algorithm, string $header_name)
    {
        $this->hmac_secret = $secret;
        $this->hmac_algorithm = $algorithm;
        $this->hmac_header_name = $header_name;
    } // end setHmac

    public function setAdditionalSettings(array $settings)
    {
        $this->additional_settings = array_merge_recursive($this->additional_settings, $settings);
    } // end setAdditionalSettings

    public function toArray()
    {
        $additional_settings = $this->additional_settings;

        // Default to securityType NONE, unless the user has specified a security type in the custom settings.
        $default_settings = $this->getNoSecurity();
        if (array_key_exists('securityTypes', $this->additional_settings) && sizeof($this->additional_settings) > 0) {
            $default_settings = [];
        }

        if ($this->hmac_secret !== null) {
            $default_settings = $this->getHmacSecurity();
        }

        $final_settings = array_merge([
            'topicName' => $this->queue,
            'endpoint' => $this->delivery_url,
            'contentType' => 'application/json',
            'securityTypes' => [],
            'webhookSecurity' => [],
            // 'active' => true,
        ], $default_settings);

        // Additive instead of replacing
        if (array_key_exists('securityTypes', $additional_settings) === true) {
            $final_settings['securityTypes'] = array_merge($final_settings['securityTypes'], $additional_settings['securityTypes']);
            unset($additional_settings['securityTypes']);
        }

        // Additive instead of replacing
        if (array_key_exists('webhookSecurity', $additional_settings) === true) {
            $final_settings['webhookSecurity'] = array_merge($final_settings['webhookSecurity'], $additional_settings['webhookSecurity']);
            unset($additional_settings['webhookSecurity']);
        }

        return array_merge($final_settings, $additional_settings);
    } // end toArray

    protected function getHmacSecurity()
    {
        return [
            'securityTypes' => ['HMAC'],
            'webhookSecurity' => [
                [
                    'securityTypes' => 'HMAC',
                    'topicName' => $this->queue,
                    'secretKey' => $this->hmac_secret,
                    'headerName' => $this->hmac_header_name,
                    'algorithm' => $this->hmac_algorithm,
                ],
            ],
        ];
    } // end getHmacSecurity

    protected function getNoSecurity()
    {
        return [
            'securityTypes' => ['NONE'],
            'webhookSecurity' => [['securityTypes' => 'NONE']],
        ];
    } // end getNoSecurity

} // end EventHubWebhookRegistration
