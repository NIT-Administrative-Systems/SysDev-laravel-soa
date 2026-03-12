<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier\Contract;

use Laravel\Socialite\Two\InvalidStateException;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Validator;
use Northwestern\SysDev\SOA\Auth\OAuth2\Key\KeyToConstraintAdapter;

abstract class AbstractAzureTokenVerifier
{
    public const KEYS_URL = 'https://login.microsoftonline.com/common/discovery/v2.0/keys';

    /**
     * The list of additional constraints to validate the token.
     *
     * {@see SignedWith} (for valid Microsoft keys) and {@see LooseValidAt} will always be applied.
     *
     * @return Constraint[]
     */
    abstract protected function additionalTokenConstraints(): array;

    /**
     * Parses the ID token, validates it with Microsoft's signing keys, and returns it.
     *
     * This method will download Microsoft's signing keys and cache them briefly.
     *
     * @throws InvalidStateException
     */
    public function parseAndVerify(string $jwt): UnencryptedToken
    {
        $parser = new Parser(new JoseEncoder());
        $validator = new Validator();

        $token = $parser->parse($jwt);
        if (! ($token instanceof UnencryptedToken)) {
            $type = get_class($token);
            throw new InvalidStateException("Expected an UnencryptedToken, got {$type} instead.");
        }

        $constraints = [
            $this->getKeySignerConstraint(),
            new LooseValidAt(SystemClock::fromSystemTimezone()),
            ...$this->additionalTokenConstraints(),
        ];

        try {
            $validator->assert($token, ...$constraints);

            return $token;
        } catch (RequiredConstraintsViolated $e) {
            throw new InvalidStateException($e->getMessage());
        }

        /**
         * This is NOT unreachable -- if the assert code throws an unexpected exception, we'd get here.
         *
         * @phpstan-ignore deadCode.unreachable
         */
        throw new InvalidStateException('Invalid JWT Signature');
    }

    private function getKeySignerConstraint(): Constraint\SignedWithOneInSet
    {
        /** @var KeyToConstraintAdapter $keyAdapter */
        $keyAdapter = resolve(KeyToConstraintAdapter::class);
        $constraintContainer = $keyAdapter->configToConstraints(self::KEYS_URL);

        // If we can find ANY valid keys, the check can proceed. The way SignedWithOneInSet is implemented SHOULD
        // protect against 0 keys passing validation, but I want to do an explicit check to proof against changes
        // to the upstream implementation in the future.
        if ($constraintContainer->constraints === null) {
            throw new InvalidStateException('Could not load any keys for signature validation');
        }

        // If some keys could not be converted (likely due to a missing Signer implementation for a new algorithm),
        // report that in a way we can log/detect so somebody can look at it *before* it becomes a crisis.
        foreach ($constraintContainer->failedKeys as $failedKey) {
            report("Non-fatal SSO problem: {$failedKey->summary} ({$failedKey->e})");
        }

        return $constraintContainer->constraints;
    }
}
