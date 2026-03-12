<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2\Key;

use Firebase\JWT\Key;
use Throwable;

/**
 * Represents a key that could not be converted into a constraint by {@see KeyToConstraintAdapter}.
 */
readonly class FailedKey
{
    public function __construct(
        public string     $summary,
        public Key        $key,
        public ?Throwable $e,
    ) {
        //
    }
}