<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier\Contract;

use Lcobucci\JWT\UnencryptedToken;

interface TokenVerifierInterface
{
    public function parseAndVerify(string $jwt): UnencryptedToken;
}