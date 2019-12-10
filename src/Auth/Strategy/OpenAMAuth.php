<?php

namespace Northwestern\SysDev\SOA\Auth\Strategy;

use Illuminate\Http\Request;

interface OpenAMAuth
{
    public function login(Request $request, string $login_route_name, string $mfa_route_name);
    public function logout();
}
