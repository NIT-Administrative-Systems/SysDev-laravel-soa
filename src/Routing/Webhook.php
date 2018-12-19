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
        $default_settings = array_merge([
            'topicName' => $this->queue,
            'endpoint' => $this->delivery_url,
            'contentType' => 'application/json',
            // 'active' => true,
        ], $this->getDefaultSecurity());

        return array_merge_recursive($default_settings, $this->additional_settings);
    } // end toArray

    protected function getDefaultSecurity()
    {
        if ($this->hmac_secret !== null) {
            return [
                'securityTypes' => ['HMAC'],
                'webhookSecurity' => [
                    [
                        'securityType' => 'HMAC',
                        'topicName' => $this->queue,
                        'secretKey' => $this->hmac_secret,
                        'headerName' => $this->hmac_header_name,
                        'algorithm' => $this->hmac_algorithm,
                    ],
                ],
            ];
        }

        return [
            'securityTypes' => ['NONE'],
            'webhookSecurity' => [['securityType' => 'NONE']],
        ];
    } // end getDefaultSecurity

} // end EventHubWebhookRegistration
