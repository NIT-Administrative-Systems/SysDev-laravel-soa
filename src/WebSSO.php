<?php

namespace Northwestern\SysDev\SOA;

use Northwestern\SysDev\SOA\WebSSOImpl\User;

interface WebSSO
{
    public function getUser(string $token): ?User;
    public function getLoginUrl(string $redirect_path): string;
    public function getLogoutUrl(?string $redirect_path = null): string;
    public function getCookieName(): string;

    /**
     * Gets the netID for a token.
     *
     * Deprecated -- use getUser() instead; it returns additional information.
     *
     * @deprecated 4.0.0
     */
    public function getNetID(string $token): ?string;
}
