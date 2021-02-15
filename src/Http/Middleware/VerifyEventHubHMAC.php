<?php

namespace Northwestern\SysDev\SOA\Http\Middleware;

use Closure;

class VerifyEventHubHMAC
{
    protected $shared_secret;
    protected $hmac_algorithm;
    protected $hmac_header_name;

    public function __construct()
    {
        $this->shared_secret = config('nusoa.eventHub.hmacVerificationSharedSecret');
        $this->hmac_algorithm = config('nusoa.eventHub.hmacVerificationAlgorithmForPHPHashHmac');
        $this->hmac_header_name = config('nusoa.eventHub.hmacVerificationHeader');
    } // end __construct

    public function handle($request, Closure $next)
    {
        if ($this->shared_secret === null) {
            throw new \Exception('Env setting "EVENT_HUB_HMAC_VERIFICATION_SHARED_SECRET" is not set. This is required to use the HMAC verification middleware.');
        }

        $proffered_signature = $request->header($this->hmac_header_name);
        if ($proffered_signature === null) {
            return response('Unauthorized - No HMAC Signature Sent', 401);
        }

        $calculated_signature = null;
        try {
            $calculated_signature = @hash_hmac($this->hmac_algorithm, $request->getContent(), $this->shared_secret, true);
        } catch (\Error $e) {
            // In PHP 8, this will give us a ValueError instead of returning false. 
            // But this supports PHP 7.4+ and ValueError is new, so the new check is being done in a way that is backwards-compatible.
            $calculated_signature = false;
        }

        if ($calculated_signature === false) {
            throw new \Exception(vsprintf('Invalid hash algorithm "%s" detected. Valid values can be determined by running `hash_algos()`.', [$this->hmac_algorithm]));
        }

        $match = hash_equals(base64_encode($calculated_signature), $proffered_signature);
        if ($match === false) {
            return response('Unauthorized - HMAC Validation Failure', 401);
        }

        return $next($request);
    } // end handle
} // end VerifyEventHubHMAC
