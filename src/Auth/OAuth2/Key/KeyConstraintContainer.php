<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2\Key;

use Lcobucci\JWT\Validation\Constraint;

readonly class KeyConstraintContainer
{
    /**
     * @param Constraint\SignedWithOneInSet|null $constraints Null when an empty array would have been generated, indicating no constraint is possible. When this is null, that is an exception condition.
     * @param FailedKey[] $failedKeys
     */
    public function __construct(
        public ?Constraint\SignedWithOneInSet $constraints,
        public array $failedKeys,
    ) {
        //
    }
}