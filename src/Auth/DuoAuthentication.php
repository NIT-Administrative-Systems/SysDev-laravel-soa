<?php

namespace Northwestern\SysDev\SOA\Auth;

use Duo;
use Illuminate\Http\Request;

trait DuoAuthentication
{
    public function index(Request $request)
    {
        $netid = $request->session()->get('mfa_netid');
        abort_unless($netid !== null, 404, 'User to send through MFA not specified.');

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
        return redirect(route('login'));
    }
}
