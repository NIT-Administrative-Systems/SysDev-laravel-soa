<?php

namespace Northwestern\SysDev\SOA\Auth\Strategy;

use Illuminate\Http\Request;
use Northwestern\SysDev\SOA\Exceptions\InsecureSsoError;
use Northwestern\SysDev\SOA\WebSSO;

/**
 * OpenAM 11 ("new" webSSO) login strategy.
 */
class OpenAM11 implements WebSSOStrategy
{
    private $sso;

    public function __construct(WebSSO $sso_api)
    {
        $this->sso = $sso_api;
    }

    public function login(Request $request, string $login_route_name)
    {
        if (! $request->isSecure()) {
            throw new InsecureSsoError;
        }

        $login_url_w_redirect = $this->sso->getLoginUrl(route($login_route_name, [], false));

        // Laravel nulls out cookies that are not encrypted w/ its key.
        if (array_key_exists($this->sso->getCookieName(), $_COOKIE) === false) {
            throw new NoSsoSession($login_url_w_redirect);
        }

        $user = $this->sso->getUser($_COOKIE[$this->sso->getCookieName()]);
        if ($user === null) {
            throw new NoSsoSession($login_url_w_redirect);
        }

        // If we require MFA, make sure the user isn't just reusing an existing non-MFA'd login.
        if (config('duo.enabled') === true && $user->getMfaVerified() === false) {
            throw new NoSsoSession($login_url_w_redirect);
        }

        return $user->getNetid();
    }

    public function logout(?string $logout_return_to_route)
    {
        $redirect_to = null;
        if ($logout_return_to_route !== null) {
            $redirect_to = route($logout_return_to_route, [], false);
        }

        return redirect($this->sso->getLogoutUrl($redirect_to));
    }
}
