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

        $publicKeys = JWK::parseKeySet($data);
        $kid = $token->headers()->get('kid');

        if (isset($publicKeys[$kid])) {
            $publicKey = openssl_pkey_get_details($publicKeys[$kid]);
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