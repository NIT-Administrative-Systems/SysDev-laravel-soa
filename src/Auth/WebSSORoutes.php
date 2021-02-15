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
}
