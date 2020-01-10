<?php

namespace Northwestern\SysDev\SOA\WebSSOImpl;

use GuzzleHttp;
use Northwestern\SysDev\SOA\WebSSO;

class ApigeeAgentless implements WebSSO
{
    protected $app_url;
    protected $api_base_url;
    protected $api_key;
    protected $http_client;
    protected $cookie_name;

    protected $login_url = null;

    public function __construct(GuzzleHttp\Client $client, string $app_url, array $config)
    {
        $this->app_url = $app_url;
        $this->http_client = $client;

        $this->api_base_url = $config['apigeeBaseUrl'];
        $this->api_key = $config['apiKey'];
        $this->mfa_required = $config['mfaRequired'];
        $this->cookie_name = $config['cookieName'];
    }

    public function getUser(string $token): ?User
    {
        $endpoint_url = sprintf('%s/session-info', $this->api_base_url);
        $response = $this->http_client->post($endpoint_url, [
            // No exceptions, we do our own error handling
            'http_errors' => false,
            'headers' => [
                'apikey' => $this->api_key,
                'webssotoken' => $token,
                'requiresMFA' => $this->mfa_required,
                'goto' => null, // @TODO
            ],
        ]);

        if ($response === null) {
            throw new \Exception(vsprintf('Unable to reach webSSO service. Verify connectivity to %s', [$endpoint_url]));
        }

        if ($response->getStatusCode() === 407) {
            $payload = json_decode($response->getBody()->getContents(), JSON_OBJECT_AS_ARRAY);
            $this->login_url = $payload['redirecturl'];

            return null;
        }

        if ($response->getStatusCode() != 200) {
            return null;
        }

        $session = json_decode($response->getBody()->getContents(), JSON_OBJECT_AS_ARRAY);
        return new User($session['username'], $session['properties']['isDuoAuthenticated']);
    }

    /** @deprecated 4.0.0 */
    public function getNetID(string $token): ?string
    {
        $user = $this->getUser($token);

        if ($user !== null) {
            return $user->getNetid();
        }

        return null;
    }

    public function getLoginUrl(string $redirect_path = null): string
    {
        return $this->login_url;
    }

    
    public function getLogoutUrl(): string
    {
        $endpoint_url = sprintf('%s/logout', $this->api_base_url);
        $response = $this->http_client->post($endpoint_url, [
            // No exceptions, we do our own error handling
            'http_errors' => false,
            'headers' => [
                'apikey' => $this->api_key,
            ],
        ]);

        if ($response->getStatusCode() != 200) {
            throw new \Exception(sprintf('Unable to determine logout URL from %s', [$endpoint_url]));
        }

        $json = json_decode($response->getBody()->getContents(), JSON_OBJECT_AS_ARRAY);
        return $json['url'];
    }

    public function getCookieName(): string
    {
        return $this->cookie_name;
    }

    public function setHttpClient(GuzzleHttp\Client $client)
    {
        $this->http_client = $client;
    }
}
