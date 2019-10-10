<?php

namespace Northwestern\SysDev\SOA\Auth;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Northwestern\SysDev\SOA\WebSSO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\RedirectsUsers;

trait WebSSOAuthentication
{
    use RedirectsUsers;

    public function login(Request $request, WebSSO $sso)
    {
        $login_url_w_redirect = $sso->getLoginUrl(route('login', [], false));

        // Laravel nulls out cookies that are not encrypted w/ its key.
        if (array_key_exists('openAMssoToken', $_COOKIE) === false) {
            return redirect($login_url_w_redirect);
        }

        $netid = $sso->getNetId($_COOKIE['openAMssoToken']);
        if ($netid == false) {
            return redirect($login_url_w_redirect);
        }

        if (config('duo.enabled') !== true || $request->session()->get('mfa_passed') === true) {
            $user = $this->findUserByNetId($netid);
            throw_if($user === null, new AuthenticationException);

            Auth::login($user);
        } else {
            $request->session()->put('mfa_netid', $netid);
            return redirect(route('mfa.index'));
        }

        return $this->authenticated($request, $user) ?: redirect()->intended($this->redirectPath());
    }

    public function logout(WebSSO $sso)
    {
        Auth::logout();

        return redirect($sso->getLogoutUrl());
    }

    /**
     * Retrieve a user model for a given netID.
     * 
     * This is an opportunity to create a user in your DB, if needed.
     *
     * If you do not have a user store, a plain-old PHP object implementing
     * the Illuminate\Contracts\Auth\Authenticatable interface is sufficient.
     */
    abstract protected function findUserByNetID(string $netid): ?Authenticatable;

    /**
     * Post-authentication hook.
     * 
     * You may return a response here, e.g. a redirect() somewhere,
     * and it will be respected.
     */
    protected function authenticated(Request $request, $user)
    {
        // 
    }
}
