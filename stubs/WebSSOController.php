<?php

namespace DummyNamespace;

use App\Http\Controllers\Controller;
use Northwestern\SysDev\SOA\Auth\WebSSOAuthentication;

class WebSSOController extends Controller
{
    use WebSSOAuthentication;

    protected function findUserByNetID(string $netid): ?Authenticatable
    {
        // Retrieve a user model for a given netID.

        // This is an opportunity to create a user in your DB, if needed.

        // If you do not have a user store, a plain-old PHP object implementing
        // the Illuminate\Contracts\Auth\Authenticatable interface is sufficient.
    }

    /*
    protected function authenticated(Request $request, $user)
    {
        // Post-authentication hook. You are not required to implement anything here.

        // If you want, you can return a redirect() here & it will be respected.
    }
    */
}
