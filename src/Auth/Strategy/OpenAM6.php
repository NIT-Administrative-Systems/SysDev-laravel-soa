<?php

namespace Northwestern\SysDev\SOA\Auth\Strategy;

use Illuminate\Http\Request;
use Northwestern\SysDev\SOA\WebSSO;

/**
 * OpenAM 6 ("old" webSSO) login strategy.
 */
class OpenAM6 implements WebSSOStrategy
{
    protected $sso;
    
    public function __construct(WebSSO $sso_api)
    {
        $this->sso = $sso_api;
    }

    public function login(Request $request, string $login_route_name, string $mfa_route_name)
    {
        $login_url_w_redirect = $this->sso->getLoginUrl(route($login_route_name, [], false));

        // Laravel nulls out cookies that are not encrypted w/ its key.
        if (array_key_exists($this->sso->getCookieName(), $_COOKIE) === false) {
            throw new NoSsoSession($login_url_w_redirect);
        }

        $netid = $this->sso->getNetId($_COOKIE[$this->sso->getCookieName()]);
        if ($netid == false) {
            throw new NoSsoSession($login_url_w_redirect);
        }

        if (config('duo.enabled') !== true || $request->session()->get('mfa_passed') === true) {
            return $netid;
        } else {
            $request->session()->put('mfa_netid', $netid);
            throw new NoSsoSession(route($mfa_route_name));
        }
    }

    public function logout(?string $logout_return_to_route)
    {
        return redirect($this->sso->getLogoutUrl(null));
    }
}
