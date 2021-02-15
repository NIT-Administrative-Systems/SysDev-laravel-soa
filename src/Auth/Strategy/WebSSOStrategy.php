<?php

namespace Northwestern\SysDev\SOA\Auth\Strategy;

use Illuminate\Http\Request;

interface WebSSOStrategy
{
    public function login(Request $request, string $login_route_name);
    public function logout(?string $logout_return_to_route);
}
