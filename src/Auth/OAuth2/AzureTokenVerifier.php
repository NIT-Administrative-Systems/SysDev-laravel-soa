<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2;

use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Two\InvalidStateException;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Firebase\JWT\JWK;
use GuzzleHttp\Client;

class AzureTokenVerifier
{
    public const KEYS_URL = 'https://login.microsoftonline.com/common/discovery/v2.0/keys';
    public const ISSUER = 'https://login.microsoftonline.com/7d76d361-8277-4708-a477-64e8366cd1bc/v2.0'; // UUID is our tenant ID

    /**
     * Parses the ID token, validates it with Microsoft's signing keys, and returns it.
     *
     * This method will download Microsoft's signing keys and cache them briefly.
     *
     * @throws InvalidStateException
     * @return \Lcobucci\JWT\UnencryptedToken
     */
    public static function parseAndVerify(string $jwt)
    {
        $jwtContainer = Configuration::forUnsecuredSigner();
        $token = $jwtContainer->parser()->parse($jwt);

        $data = self::loadKeys();

        $publicKeys = JWK::parseKeySet($data);
        $kid = $token->headers()->get('kid');

        if (isset($publicKeys[$kid])) {
            $publicKey = openssl_pkey_get_details($publicKeys[$kid]);
            $constraints = [
                new SignedWith(new Sha256(), InMemory::plainText($publicKey['key'])),
                new IssuedBy(self::ISSUER),
                new LooseValidAt(SystemClock::fromSystemTimezone()),
            ];

            try {
                $jwtContainer->validator()->assert($token, ...$constraints);

                return $token;
            } catch (RequiredConstraintsViolated $e) {
                throw new InvalidStateException($e->getMessage());
            }
        }

        throw new InvalidStateException('Invalid JWT Signature');
    }

    protected static function loadKeys()
    {
        return Cache::remember('socialite:Azure-JWKSet', 5 * 60, function () {
            $response = (new Client())->get(self::KEYS_URL);

            return json_decode($response->getBody()->getContents(), true);
        });
    }
}