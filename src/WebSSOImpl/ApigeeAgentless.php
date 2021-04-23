<?php

namespace Northwestern\SysDev\SOA\WebSSOImpl;

use GuzzleHttp;
use Northwestern\SysDev\SOA\Exceptions\ApigeeAuthenticationError;

class ApigeeAgentless extends OpenAM11Api
{
    protected $apigee_base_url;
    protected $apigee_key;

    const APIGEE_KEY_INVALID_RESP_CODE = 401;

    public function __construct(GuzzleHttp\Client $client, string $app_url, array $config)
    {
        parent::__construct($client, $app_url, $config);

        $this->apigee_base_url = $config['apigeeBaseUrl'];
        $this->apigee_key = $config['apigeeApiKey'];
    }

    /**
     * Get information about the SSO token from the Apigee service.
     *
     * If the Apigee API key is wrong, a 401 Unauthorized response is returned.
     * If the SSO token is wrong (invalid/expired), a 407 response is returned.
     *
     * The Apigee error will be detected and raised as an exception to provide the
     * Dev/Ops folks with a clear error message about their config.
     */
    protected function getSessionInfo(string $endpoint_url, string $token)
    {
        $response = $this->http_client->post($endpoint_url, [
            // No exceptions, we do our own error handling
            'http_errors' => false,
            'headers' => [
                'apikey' => $this->apigee_key,
                'webssotoken' => $token,
                'requiresMFA' => $this->tree === 'ldap-and-duo',
                'goto' => null, // not using this functionality
            ],
        ]);

        if ($response->getStatusCode() === self::APIGEE_KEY_INVALID_RESP_CODE) {
            throw new ApigeeAuthenticationError($this->getEndpointUrl());
        }

        return $response;
    }

    protected function getEndpointUrl(): string
    {
        return sprintf('%s/session-info', $this->apigee_base_url);
    }
}
