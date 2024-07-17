<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier;

use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier\Contract\AbstractAzureTokenVerifier;
use Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier\Contract\TokenVerifierInterface;

class NorthwesternAzureTokenVerifier extends AbstractAzureTokenVerifier implements TokenVerifierInterface
{
    /** @var string UUID for the Northwestern Azure tenant */
    public const ISSUER = 'https://login.microsoftonline.com/7d76d361-8277-4708-a477-64e8366cd1bc/v2.0';

    /**
     * {@inheritDoc}
     *
     * Checks the token was issued by the Northwestern Azure tenant.
     */
    protected function additionalTokenConstraints(): array
    {
        return [
            new IssuedBy(self::ISSUER),
        ];
    }
}
