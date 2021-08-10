<?php

namespace Northwestern\SysDev\SOA\Auth;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Northwestern\SysDev\SOA\Auth\Entity\ActiveDirectoryUser;
use Northwestern\SysDev\SOA\Auth\Entity\OAuthUser;
use Northwestern\SysDev\SOA\Auth\Strategy\NoSsoSession;
use Northwestern\SysDev\SOA\Auth\Strategy\WebSSOStrategy;

trait WebSSOAuthentication
{
    use RedirectsUsers, WebSSORoutes;

    /**
     * OpenAM WebSSO login action.
     */
    public function login(Request $request, WebSSOStrategy $sso_strategy)
    {
        try {
            $netid = strtolower($sso_strategy->login($request, $this->login_route_name));
        } catch (NoSsoSession $e) {
            return redirect($e->getRedirectUrl());
        }

        $user = app()->call(\Closure::fromCallable('static::findUserByNetId'), ['netid' => $netid]);
        throw_if($user === null, new AuthenticationException());

        Auth::login($user);

        return $this->authenticated($request, $user) ?: redirect()->intended($this->redirectPath());
    }

    /**
     * OpenAM WebSSO logout action.
     */
    public function logout(WebSSOStrategy $sso_strategy)
    {
        Auth::logout();
        return $sso_strategy->logout($this->logout_return_to_route);
    }

    public function oauthLogout()
    {
        Auth::logout();

        return redirect($this->oauthDriver()->getLogoutUrl());
    }

    /**
     * Azure AD OAuth initiator action
     */
    public function oauthRedirect()
    {
        return $this->oauthDriver()->redirect();
    }

    /**
     * OAuth callback URL, where users are sent when they're
     */
    public function oauthCallback(Request $request)
    {
        try {
            $userInfo = $this->oauthDriver()->user();
        } catch (InvalidStateException $e) {
            // Should be resolvable by starting the flow over
            return redirect(route($this->oauth_redirect_route_name));
        } catch (ClientException $e) {
            /**
            * Handle specific failures that we know can be resolved by re-starting the auth flow.
            * Anything more general from Guzzle should rethrow and be handled
            * by the app's exception handler.
            */
            if (
                $e->getCode() === 400
                && Str::contains($e->getMessage(), 'OAuth2 Authorization code was already redeemed')
            ) {
                return redirect(route($this->oauth_redirect_route_name));
            }

            throw $e;
        }

        $oauthUser = new ActiveDirectoryUser($userInfo->token, $userInfo->getRaw());

        $user = app()->call(
            \Closure::fromCallable('static::findUserByOAuthUser'),
            ['oauthUser' => $oauthUser]
        );
        throw_if($user === null, new AuthenticationException());

        Auth::login($user);

        return $this->authenticated($request, $user) ?: redirect()->intended($this->redirectPath());
    }

    /**
     * Retrieve a user model for a given OAuth profile.
     *
     * By default, this method will pass the netID through to ::findUserByNetId instead
     * of doing anything on its own. This is for backwards-compatibility -- if you've used
     * OpenAM SSO in the past (or plan to in the future), the two methods can be used interchangably.
     *
     * In cases where you wish to utilize data from the Azure AD profile (like email, name, phone, etc),
     * you can implement this method and return a Laravel user directly, without invoking the
     * ::findUserByNetID method.
     */
    protected function findUserByOAuthUser(OAuthUser $oauthUser): ?Authenticatable
    {
        return app()->call(
            \Closure::fromCallable('static::findUserByNetID'),
            ['netid' => $oauthUser->getNetid()]
        );
    }

    /**
     * Retrieve a user model for a given netID.
     *
     * This is an opportunity to create a user in your DB, if needed.
     *
     * If you do not have a user store, a plain-old PHP object implementing
     * the Illuminate\Contracts\Auth\Authenticatable interface is sufficient.
     */
    protected function findUserByNetID(string $netid): ?Authenticatable
    {
        throw new \Exception('findUserByNetID is not implemented, but must be implemented.');
    }

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

    /**
     * @return \Laravel\Socialite\Contracts\Provider
     */
    protected function oauthDriver()
    {
        $driver = Socialite::driver('northwestern-azure')->scopes($this->oauthScopes());

        if (! config('services.azure.redirect')) {
            $driver = $driver->redirectUrl(route($this->oauth_callback_route_name, [], true));
        }

        return $driver;
    }

    /**
     * Additional scopes for the user.
     *
     * @return array
     */
    protected function oauthScopes()
    {
        return ['https://graph.microsoft.com/User.Read'];
    }
}
