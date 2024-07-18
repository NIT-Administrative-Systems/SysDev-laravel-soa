<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier;

use Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier\Contract\AbstractAzureTokenVerifier;
use Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier\Contract\TokenVerifierInterface;

class MultiTenantAzureTokenVerifier extends AbstractAzureTokenVerifier implements TokenVerifierInterface
{
    /**
     * {@inheritDoc}
     *
     * No additional verifications are necessary.
     */
    protected function additionalTokenConstraints(): array
    {
        return [];
    }
}