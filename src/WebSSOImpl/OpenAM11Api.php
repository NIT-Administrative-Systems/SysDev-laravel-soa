<?php

namespace Northwestern\SysDev\SOA\WebSSOImpl;

use GuzzleHttp;
use Northwestern\SysDev\SOA\WebSSO;

class OpenAM11Api implements WebSSO
{
    protected $app_url;

    protected $http_client;

    protected $sso_server;

    protected $realm;

    protected $tree;

    protected $cookie_name;

    public function __construct(GuzzleHttp\Client $client, string $app_url, array $config)
    {
        $this->app_url = $app_url;
        $this->http_client = $client;

        $this->sso_server = $config['openAmBaseUrl'];
        $this->realm = $config['realm'];
        $this->tree = $config['authTree'];
        $this->cookie_name = $config['cookieName'];
    }

    public function getUser(string $token): ?User
    {
        $endpoint_url = $this->getEndpointUrl();

        $response = $this->getSessionInfo($endpoint_url, $token);
        if ($response === null) {
            throw new \Exception(vsprintf('Unable to reach webSSO service. Verify connectivity to %s', [$this->getEndpointUrl()]));
        }

        if ($response->getStatusCode() != 200) {
            return null;
        }

        $session = json_decode($response->getBody()->getContents(), associative: true);

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

    public function getLoginUrl(?string $redirect_path = null): string
    {
        $redirect_to = $this->prepareRedirect($redirect_path);

        return sprintf('%s/nusso/XUI/?realm=%s#login&authIndexType=service&authIndexValue=%s&goto=%s', $this->sso_server, $this->realm, $this->tree, $redirect_to);
    }

    public function getLogoutUrl(?string $redirect_path = null): string
    {
        /*
        * If the redirect for logout is left blank, the default OpenAM XUI behaviour is used.
        * It will display a logged out page with a "Return to Login" link, and the return to login will
        * have looked up the original login goto for that session & send you there.
        *
        * OTOH, if it is set, the logout page will BRIEFLY display a logged out message, then auto-redirect
        * to the specified route.
        */

        $redirect_fragment = '';
        if ($redirect_path !== null) {
            $redirect_fragment = sprintf('&goto=%s', $this->prepareRedirect($redirect_path));
        }

        return sprintf('%s/nusso/XUI/?realm=/%s#logout%s', $this->sso_server, $this->realm, $redirect_fragment);
    }

    public function getCookieName(): string
    {
        return $this->cookie_name;
    }

    protected function getSessionInfo(string $endpoint_url, string $token)
    {
        return $this->http_client->post($endpoint_url, [
            // No exceptions, we do our own error handling
            'http_errors' => false,
            'headers' => [
                'Accept-API-Version' => 'resource=3',
            ],
            'json' => [
                'tokenId' => $token,
                'realm' => '/',
            ],
        ]);
    }

    protected function getEndpointUrl(): string
    {
        return sprintf('%s/nusso/json/realms/root/realms/%s/sessions?_action=getSessionInfo', $this->sso_server, $this->realm);
    }

    private function prepareRedirect(?string $redirect_path = null): string
    {
        $redirect_to = $this->app_url;
        if ($redirect_path != null) {
            $redirect_to = vsprintf('%s/%s', [trim($redirect_to, '/'), trim($redirect_path, '/')]);
        }

        return urlencode($redirect_to);
    }

    public function setHttpClient(GuzzleHttp\Client $client)
    {
        $this->http_client = $client;
    }
}
