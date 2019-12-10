<?php

namespace Northwestern\SysDev\SOA\Auth\Strategy;

use Exception;

class NoSsoSession extends Exception
{
    protected $redirect_url;

    public function __construct($redirect_url)
    {
        parent::__construct('No active SSO session, redirect needed');
        $this->redirect_url = $redirect_url;
    }

    public function getRedirectUrl()
    {
        return $this->redirect_url;
    }
}
