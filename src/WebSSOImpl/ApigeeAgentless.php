<?php

namespace Northwestern\SysDev\SOA\WebSSOImpl;

use GuzzleHttp;

class ApigeeAgentless extends OpenAM11Api
{
    protected $apigee_base_url;
    protected $apigee_key;

    public function __construct(GuzzleHttp\Client $client, string $app_url, array $config)
    {
        parent::__construct($client, $app_url, $config);

        $this->apigee_base_url = $config['apigeeBaseUrl'];
        $this->apigee_key = $config['apigeeApiKey'];
    }

    protected function getSessionInfo(string $endpoint_url, string $token)
    {
        return $this->http_client->post($endpoint_url, [
            // No exceptions, we do our own error handling
            'http_errors' => false,
            'headers' => [
                'apikey' => $this->apigee_key,
                'webssotoken' => $token,
                'requiresMFA' => $this->tree === 'ldap-and-duo',
                'goto' => null, // not using this functionality
            ],
        ]);
    }

    protected function getEndpointUrl(): string
    {
        return sprintf('%s/session-info', $this->apigee_base_url);
    }
}
