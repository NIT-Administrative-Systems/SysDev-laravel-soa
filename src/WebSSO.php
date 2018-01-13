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

    public function __construct()
    {
        $this->sso_server = config('sso.openAmBaseUrl');
    } // end __construct

    /**
     * Turns an openAMssoToken cookie into a netID.
     *
     * @param  string value of openAMssoToken cookie
     * @return string   the netID, or false for an invalid token.
     */
    public function getNetID($token)
    {
        $client = new GuzzleHttp\Client();
        $request = $client->request('POST', $this->getEndpointUrl(), [
            'http_errors' => false, // don't throw exceptions
            'form_params' => [
                'subjectid' => $token,
                'attributenames' => 'UserToken',
            ],
        ]);

        if ($request === null) {
            throw new \Exception('Unable to reach webSSO service. Verify connectivity to ' . $this->getEndpointUrl());
        }

        if ($request->getStatusCode() != 200) {
            return false;
        }

        return $this->extractNetid($request->getBody());
    } // end getInfo

    protected function extractNetid($payload)
    {
        $seek = 'userdetails.attribute.value=';
        foreach (explode("\n", $payload) as $line)
        {
            if (substr($line, 0, strlen($seek)) == $seek)
            {
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
    public function getLoginUrl($redirect_to = null)
    {
        if ($redirect_to == null) {
            $redirect_to = vsprintf('%s/auth/sso/login', [config('app.url')]);
        }

        $redirect_to = urlencode($redirect_to);
        return $this->sso_server . "/amserver/UI/Login?goto=$redirect_to&ForceAuthn=false&IsPassive=false&Federate=false";
    } // end getLoginUrl

    public function getLogoutUrl()
    {
        return $this->sso_server . "/amserver/UI/Logout.jsp";
    } // end getLogoutUrl
} // end WebSSO
