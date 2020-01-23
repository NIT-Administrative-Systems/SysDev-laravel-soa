<?php

namespace Northwestern\SysDev\SOA\WebSSOImpl;

use GuzzleHttp;
use Northwestern\SysDev\SOA\WebSSO;

/**
 * OpenAM webSSO API bindings for agentless authentication.
 *
 * @deprecated 4.0.0
 */
class OpenAM6Api implements WebSSO
{
    protected $app_url;
    protected $sso_server;
    protected $api_path = '/amserver/identity/attributes';
    protected $http_client;

    public function __construct(GuzzleHttp\Client $client, $app_url, $sso_base_url)
    {
        $this->http_client = $client;
        $this->app_url = $app_url;
        $this->sso_server = $sso_base_url;
    } // end __construct

    /**
     * Fetches user profile information from WebSSO for a given session token.
     *
     * @param string $token
     * @return User|null
     */
    public function getUser(string $token): ?User
    {
        $request = $this->http_client->request('POST', $this->getEndpointUrl(), [
            'http_errors' => false, // don't throw exceptions
            'form_params' => [
                'subjectid' => $token,
                'attributenames' => 'UserToken',
            ],
        ]);

        if ($request === null) {
            throw new \Exception(vsprintf('Unable to reach webSSO service. Verify connectivity to %s', [$this->getEndpointUrl()]));
        }

        if ($request->getStatusCode() != 200) {
            return null;
        }

        $netid = $this->extractNetid($request->getBody());
        if ($netid === null) {
            return null;
        }

        // MFA bit will always be false here, since
        // the old OpenAM API does not expose any info about it.
        return new User($netid, false);
    }

    /**
     * Turns an openAMssoToken cookie into a netID.
     *
     * @deprecated 4.0.0
     * @param  string value of openAMssoToken cookie
     * @return string   the netID, or null for an invalid token.
     */
    public function getNetID(string $token): ?string
    {
        $user = $this->getUser($token);

        return optional($user)->getNetID();
    }

    public function setHttpClient(GuzzleHttp\Client $client)
    {
        $this->http_client = $client;
    }

    protected function extractNetid($payload)
    {
        $seek = 'userdetails.attribute.value=';
        foreach (explode("\n", $payload) as $line) {
            if (substr($line, 0, strlen($seek)) == $seek) {
                return trim(substr($line, strlen($seek)));
            }
        }

        return null;
    } // end extractNetid

    protected function getEndpointUrl()
    {
        return $this->sso_server . $this->api_path;
    }

    /**
     * [getLoginUrl description]
     * @param  [type] $redirect_to [description]
     * @return [type]              [description]
     */
    public function getLoginUrl(string $redirect_path = null): string
    {
        $redirect_to = $this->app_url;
        if ($redirect_path != null) {
            $redirect_to = vsprintf('%s/%s', [trim($redirect_to, '/'), trim($redirect_path, '/')]);
        }

        $redirect_to = urlencode($redirect_to);
        return $this->sso_server . "/amserver/UI/Login?goto=$redirect_to&ForceAuthn=false&IsPassive=false&Federate=false";
    }

    public function getLogoutUrl(?string $redirect_path = null): string
    {
        // The SSO server does not have a link back to the app, so this is unused.
        return $this->sso_server . "/amserver/UI/Logout.jsp";
    }

    public function getCookieName(): string
    {
        return 'openAMssoToken';
    }
}
