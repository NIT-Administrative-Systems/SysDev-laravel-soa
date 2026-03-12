<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2\Key;

use Firebase\JWT\JWK;
use Firebase\JWT\Key;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Two\InvalidStateException;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;

use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\Validator;

class KeyToConstraintAdapter
{
    public function configToConstraints(string $keysUrl): KeyConstraintContainer
    {
        $rawData = $this->loadKeys($keysUrl);
        $keyset = $this->parsePrivateKeys($rawData);

        return $this->keysToConstraint($keyset);
    }

    /**
     * @return Key[]
     */
    private function parsePrivateKeys(array $data): array
    {
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

        return JWK::parseKeySet($data, $defaultAlgorithm);
    }

    /**
     * Generates key constraints from a JWKS endpoint for use with {@see Validator}, to ensure it's signed by a valid
     * key.
     *
     * The keys will be cached for five minutes instead of re-fetching them every request.
     *
     * The returned constraint MUST be used immediately. It MUST NOT be serialized and stored. The JWKS key revocation
     * mechanism is "delete the key from the JSON file", so old constraints CANNOT be kept around and reused.
     *
     * The constraint WILL expire and return invalid after the `$secondsForValidConstraint` period.
     *
     * @param Key[] $keySet
     */
    private function keysToConstraint(array $keySet, int $secondsForValidConstraint = 60): KeyConstraintContainer
    {
        $constraints = [];
        $failedKeys = [];

        foreach ($keySet as $key) {
            try {
                $signer = $this->jwksAlgorithmToSignerImplementationFactory($key);
            } catch (InvalidStateException $e) {
                $failedKeys[] = new FailedKey('Algorithm unsupported', $key, $e);
                continue;
            }

            // The key can return a string or one of these two OpenSSL objects.
            $keyString = $key->getKeyMaterial();
            if ($keyString instanceof \OpenSSLAsymmetricKey || $keyString instanceof \OpenSSLCertificate) {
                $unpackedKey = openssl_pkey_get_details($key->getKeyMaterial());
                $keyString = $unpackedKey['key'];
            }

            // The valid-until was meant to be a key rotation mechanism, allowing overlap with a built-in drop-dead date.
            // We're loading the keys from JWKS instead of supplying them in a config file for the app, so this isn't
            // applicable. The constraint should be used IMMEDIATELY, so it will be valid for the next minute only.
            $validUntil = now()->addSeconds($secondsForValidConstraint);

            $constraints[] = new Constraint\SignedWithUntilDate(
                signer: $signer,
                key: InMemory::plainText($keyString),
                validUntil: $validUntil->toDateTimeImmutable(),
            );
        }

        $constraint = null;
        if (count($constraints) > 0) {
            $constraint = new Constraint\SignedWithOneInSet(...$constraints);
        }

        return new KeyConstraintContainer($constraint, $failedKeys);
    }

    private function jwksAlgorithmToSignerImplementationFactory(Key $key): Signer
    {
        return match ($key->getAlgorithm()) {
            'HS256' => new Signer\Hmac\Sha256(),
            'HS384' => new Signer\Hmac\Sha384(),
            'HS512' => new Signer\Hmac\Sha512(),
            'RS256' => new Signer\Rsa\Sha256(),
            'RS384' => new Signer\Rsa\Sha384(),
            'RS512' => new Signer\Rsa\Sha512(),
            'ES256' => new Signer\Ecdsa\Sha256(),
            'ES384' => new Signer\Ecdsa\Sha384(),
            'ES512' => new Signer\Ecdsa\Sha512(),
            'EdDSA' => new Signer\Eddsa(),
            default => throw new InvalidStateException("Unsupported signed algorithm type {$key->getAlgorithm()}"),
        };
    }

    private function loadKeys(string $keysUrl): array
    {
        $cacheKeyHash = hash('sha256', $keysUrl);

        return Cache::remember("socialite:Azure-JWKSet:{$cacheKeyHash}", 5 * 60, function () use ($keysUrl) {
            $response = (new Client())->get($keysUrl);

            return json_decode($response->getBody()->getContents(), true);
        });
    }
}