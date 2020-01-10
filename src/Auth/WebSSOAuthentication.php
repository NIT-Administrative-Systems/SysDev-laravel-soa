<?php

namespace Northwestern\SysDev\SOA\Auth;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Northwestern\SysDev\SOA\Auth\Strategy\NoSsoSession;
use Northwestern\SysDev\SOA\Auth\Strategy\WebSSOStrategy;

trait WebSSOAuthentication
{
    use RedirectsUsers, WebSSORoutes;

    public function login(Request $request, WebSSOStrategy $sso_strategy)
    {
        try {
            $netid = $sso_strategy->login($request, $this->login_route_name, $this->mfa_route_name);
        } catch (NoSsoSession $e) {
            return redirect($e->getRedirectUrl());
        }

        $user = app()->call(\Closure::fromCallable('static::findUserByNetId'), [$netid]);
        throw_if($user === null, new AuthenticationException());

        Auth::login($user);

        return $this->authenticated($request, $user) ?: redirect()->intended($this->redirectPath());
    }

    public function logout(WebSSOStrategy $sso_strategy)
    {
        Auth::logout();
        return $sso_strategy->logout();
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
