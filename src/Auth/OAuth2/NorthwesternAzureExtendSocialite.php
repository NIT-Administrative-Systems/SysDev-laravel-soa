<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2;

use SocialiteProviders\Manager\SocialiteWasCalled;

class NorthwesternAzureExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('northwestern-azure', NorthwesternAzureProvider::class);
    }
}