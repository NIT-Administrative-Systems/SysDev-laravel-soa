<?php

namespace Northwestern\SysDev\SOA\Auth;

trait WebSSORoutes
{
    /** Route name for your login page */
    protected $login_route_name = 'login';

    /** Route name for the logout page */
    protected $logout_route_name = 'logout';

    /** Optional name for where you want webSSO to return you to when you've logged out. */
    protected $logout_return_to_route = null;

    /** @var string Route name for the OAuth (Azure AD) login route */
    protected $oauth_redirect_route_name = 'login-oauth-redirect';

    /** @var string Route name for the OAuth callback, which the provider returns users to after authentication */
    protected $oauth_callback_route_name = 'login-oauth-callback';

    /** @var string Route name for the OAuth logout */
    protected $oauth_logout_route_name = 'login-oauth-logout';

}
