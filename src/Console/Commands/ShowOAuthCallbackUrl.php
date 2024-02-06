<?php

namespace Northwestern\SysDev\SOA\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ShowOAuthCallbackUrl extends Command
{
    protected $signature = 'websso:callback';

    protected $description = 'Displays the OAuth callback URI, which must be added to an allow list in Azure AD';

    public function handle()
    {
        $callbackUrl = route('login-oauth-callback', [], true);

        $this->line("You must add the OAuth callback URLs to Azure AD's list of web redirect URIs.");
        $this->line('Shown below is the callback URI for this application:');
        $this->newLine();

        $this->info($callbackUrl);
        $this->newLine();

        if (
            Str::startsWith($callbackUrl, 'http://')
            && ! Str::contains($callbackUrl, 'localhost')
        ) {
            $this->error('WARNING: This callback URI is not served over HTTPS. Azure AD will not accept it.');
        }
    }
}
