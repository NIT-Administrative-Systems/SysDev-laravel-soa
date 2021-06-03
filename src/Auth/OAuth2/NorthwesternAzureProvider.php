<?php

namespace Northwestern\SysDev\SOA\Auth\OAuth2;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SocialiteProviders\Manager\OAuth2\User;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;

class NorthwesternAzureProvider extends AbstractProvider
{
    public const IDENTIFIER = 'NU_AZURE';
    public const NU_TENANT_ID = 'northwestern.edu';

    protected $encodingType = PHP_QUERY_RFC3986;

    /**
     * The Microsoft Graph API endpoint for profile information.
     *
     * @var string
     */
    protected $graphUrl = 'https://graph.microsoft.com/v1.0/me/';

    /** @var string Scopes to request */
    protected $scopes = ['openid'];

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        // Throws if the token isn't signed properly
        $idToken = AzureTokenVerifier::parseAndVerify($this->request->input('id_token'));

        //Temporary fix to enable stateless
        $response = $this->getAccessTokenResponse($this->request->input('code'));

        $userToken = $this->getUserByToken(
            $token = Arr::get($response, 'access_token')
        );

        if ($this->usesState()) {
            $state = explode('.', $idToken->claims()->get('nonce'))[1];
            if ($state === $this->request->input('state')) {
                $this->request->session()->put('state', $state);
                $this->request->session()->put('state_verify', $state);
            }

            if ($this->hasInvalidState()) {
                throw new InvalidStateException();
            }
        }

        $user = $this->mapUserToObject($userToken);

        if ($user instanceof User) {
            $user->setAccessTokenResponseBody($response);
        }

        return $user->setToken($token)
            ->setRefreshToken(Arr::get($response, 'refresh_token'))
            ->setExpiresIn(Arr::get($response, 'expires_in'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://login.microsoftonline.com/'.($this->config['tenant'] ?: self::NU_TENANT_ID).'/oauth2/v2.0/authorize',
            $state
        );
    }

    protected function getTokenUrl()
    {
        return 'https://login.microsoftonline.com/'.($this->config['tenant'] ?: self::NU_TENANT_ID).'/oauth2/v2.0/token';
    }

    public function getAccessToken($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'form_params' => $this->getTokenFields($code),
        ]);

        $this->credentialsResponseBody = json_decode($response->getBody()->getContents(), true);

        return $this->parseAccessToken($response->getBody());
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->graphUrl, [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => null,
            'name' => $user['displayName'],
            'email' => $user['mail'],
            'avatar' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
            'scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getCodeFields($state = null)
    {
        $fields = [
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'scope'         => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
            'response_type' => 'id_token code',
            'response_mode' => 'form_post',
            'domain_hint'   => 'northwestern.edu',
        ];

        if ($this->usesState()) {
            $fields['state'] = $state;
            $fields['nonce'] = sprintf('%s.%s', Str::uuid(), $state);
        }

        return array_merge($fields, $this->parameters);
    }

    /**
     * {@inheritDoc}
     */
    protected function hasInvalidState()
    {
        if ($this->isStateless()) {
            return false;
        }

        $state = $this->request->session()->pull('state');

        return ! (strlen($state) > 0 && $this->request->input('state') === $state);
    }

    /**
     * Get the access token response for the given code.
     *
     * @param  string  $code
     * @return array
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'form_params' => $this->getTokenFields($code),
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Add the additional configuration key 'tenant' to enable the branded sign-in experience.
     *
     * @return array
     */
    public static function additionalConfigKeys()
    {
        return ['tenant'];
    }
}