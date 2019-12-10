<?php

namespace Northwestern\SysDev\SOA\WebSSOImpl;

class User
{
    private $netid;
    private $mfa_verified;

    public function __construct(string $netid, bool $mfa_verified)
    {
        $this->netid = $netid;
        $this->mfa_verified = $mfa_verified;
    }

    public function getNetid(): string
    {
        return $this->netid;
    }

    public function getMfaVerified(): bool
    {
        return $this->mfa_verified;
    }
}
