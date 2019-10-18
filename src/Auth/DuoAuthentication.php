<?php

namespace Northwestern\SysDev\SOA\Auth;

use Duo;
use Illuminate\Http\Request;

trait DuoAuthentication
{
    use WebSSORoutes;

    public function index(Request $request)
    {
        $netid = $request->session()->get('mfa_netid');

        // I've seen browsers remember the MFA URL and auto-suggest it.
        // Send the user somewhere useful if there's no in-flight login session instead of dead-ending w/ an error.
        if ($netid === null) {
            return redirect(route($this->login_route_name));
        }

        $signed_request = Duo\Web::signRequest(config('duo.ikey'), config('duo.skey'), config('duo.akey'), $netid);

        return view('auth.mfa', [
            'page_title' => 'Login',
            'duo_url' => config('duo.url'),
            'signed_request' => $signed_request,
        ]);
    }

    public function store(Request $request)
    {
        $verified = Duo\Web::verifyResponse(config('duo.ikey'), config('duo.skey'), config('duo.akey'), $request->input('sig_response'));
        abort_unless($verified, 401, 'MFA verification failed.');

        $request->session()->put('mfa_passed', true);
        return redirect(route($this->login_route_name));
    }
}
