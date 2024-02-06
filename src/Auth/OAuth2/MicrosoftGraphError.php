<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2;

use Exception;
use GuzzleHttp\Exception\ClientException;

/**
 * Unpacks the Microsoft Graph API response JSON into the full message.
 *
 * These errors would typically be truncated in a logger since the response body is
 * cut off after X characters. This exception wraps a Guzzle error and exposes the full
 * JSON response as the error message instead of truncating it.
 */
class MicrosoftGraphError extends Exception
{
    public function __construct(ClientException $previous)
    {
        parent::__construct($previous->getResponse()->getBody()->getContents(), $previous->getCode(), $previous);
    }
}
