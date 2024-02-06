<?php

namespace Northwestern\SysDev\SOA\Exceptions;

class InsecureSsoError extends \Exception
{
    public function __construct()
    {
        parent::__construct('The webSSO connection is insecure (http://). The SSO cookie is only available on a secure (https://) connection.');
    }
}
