<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2\TokenVerifier\Contract;

use Firebase\JWT\JWK;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Two\InvalidStateException;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

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
        $jwtContainer = Configuration::forUnsecuredSigner();
        $token = $jwtContainer->parser()->parse($jwt);

        $data = $this->loadKeys();

        /**
         * This is kind of jank, but the `alg` claim in the JWK is not required by the spec, so Microsoft has opted
         * not to include it.
         *
         * As of v6, the JWT library requires either the alg to be provided -or- a default given, to mitigate
         * CVE-2021-46743, a key type confusion attack. The CVE is probably broadly applicable to any implementation
         * dealing with these keys missing their `alg` claims.
         *
         * If Microsoft updates in the future, they will hopefully start providing the `alg` claim on the new keys in
         * the keyring. In that case, this will continue to work just fine, since the `alg` claim has priority over
         * this default.
         *
         * @see https://github.com/firebase/php-jwt/issues/498
         * @see https://github.com/advisories/GHSA-8xf4-w7qw-pjjw
         * @see https://github.com/firebase/php-jwt/issues/351
         */
        $defaultAlgorithm = 'RS256';

        $publicKeys = JWK::parseKeySet($data, $defaultAlgorithm);
        $kid = $token->headers()->get('kid');

        if (isset($publicKeys[$kid])) {
            $publicKey = openssl_pkey_get_details($publicKeys[$kid]->getKeyMaterial());
            $constraints = [
                new SignedWith(new Sha256(), InMemory::plainText($publicKey['key'])),
                new LooseValidAt(SystemClock::fromSystemTimezone()),
                ...$this->additionalTokenConstraints(),
            ];

            try {
                $jwtContainer->validator()->assert($token, ...$constraints);

                if (! ($token instanceof UnencryptedToken)) {
                    $type = get_class($token);
                    throw new InvalidStateException("Expected an UnencryptedToken, got {$type} instead.");
                }

                return $token;
            } catch (RequiredConstraintsViolated $e) {
                throw new InvalidStateException($e->getMessage());
            }
        }

        throw new InvalidStateException('Invalid JWT Signature');
    }

    private function loadKeys()
    {
        return Cache::remember('socialite:Azure-JWKSet', 5 * 60, function () {
            $response = (new Client())->get(self::KEYS_URL);

            return json_decode($response->getBody()->getContents(), true);
        });
    }
}