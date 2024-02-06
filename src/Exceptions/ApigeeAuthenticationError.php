<?php

namespace Northwestern\SysDev\SOA\Exceptions;

class ApigeeAuthenticationError extends \Exception
{
    public function __construct(string $apigeeEndpoint)
    {
        parent::__construct(sprintf('The configured Apigee API key (WEBSSO_API_KEY) is invalid for %s', $apigeeEndpoint));
    }
}
