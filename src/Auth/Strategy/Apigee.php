<?php

namespace Northwestern\SysDev\SOA\Auth\Strategy;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Northwestern\SysDev\SOA\WebSSO;

/**
 * Apigee AgentlessSSO strategy
 */
class Apigee implements WebSSOStrategy
{
    private $sso;

    public function __construct(WebSSO $sso_api)
    {
        $this->sso = $sso_api;
    }

    public function login(Request $request, string $login_route_name, string $mfa_route_name)
    {
        $login_callback_url = route($login_route_name, [], false);

        $token = Arr::get($_COOKIE, $this->sso_api->getCookieName(), '');
        
        $user = $this->sso_api->getUser($token);
        if ($user === null) {
            throw new NoSsoSession($this->sso_api->getLoginUrl());
        }

        if (config('duo.enabled') === true && $user->getMfaVerified() === false) {
            // The Apigee API should be enforcing MFA-ness for us, but if it doesn't for some reason,
            // don't let them into the app.
            throw new \Exception('MFA is required, but this user has not completed MFA.');
        }

        return $user->getNetid();
    }

    public function logout()
    {
        return redirect($this->sso->getLogoutUrl());
    }
}
