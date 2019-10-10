<?php

namespace Northwestern\SysDev\SOA;

use GuzzleHttp;

/**
 * OpenAM webSSO API bindings for agentless authentication.
 */
class WebSSO
{
    protected $sso_server;
    protected $api_path = '/amserver/identity/attributes';
    protected $http_client;

    public function __construct(GuzzleHttp\Client $client)
    {
        $this->http_client = $client;
        $this->sso_server = config('nusoa.sso.openAmBaseUrl');
    } // end __construct

    /**
     * Turns an openAMssoToken cookie into a netID.
     *
     * @param  string value of openAMssoToken cookie
     * @return string   the netID, or false for an invalid token.
     */
    public function getNetID($token)
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
            return false;
        }

        return $this->extractNetid($request->getBody());
    } // end getInfo

    public function setHttpClient(GuzzleHttp\Client $client)
    {
        $this->http_client = $client;
    } //end setHttpClient

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
    } // end getEndpointUrl

    /**
     * [getLoginUrl description]
     * @param  [type] $redirect_to [description]
     * @return [type]              [description]
     */
    public function getLoginUrl($redirect_path = null)
    {
        $redirect_to = config('app.url');
        if ($redirect_path != null) {
            $redirect_to = vsprintf('%s/%s', [trim($redirect_to, '/'), trim($redirect_path, '/')]);
        }

        $redirect_to = urlencode($redirect_to);
        return $this->sso_server . "/amserver/UI/Login?goto=$redirect_to&ForceAuthn=false&IsPassive=false&Federate=false";
    } // end getLoginUrl

    public function getLogoutUrl()
    {
        return $this->sso_server . "/amserver/UI/Logout.jsp";
    } // end getLogoutUrl
} // end WebSSO
