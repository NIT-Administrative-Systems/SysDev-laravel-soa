<?php

namespace Northwestern\SysDev\SOA\Tests\Auth;

use PHPUnit\Framework\Attributes\DataProvider;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User;
use Northwestern\SysDev\SOA\Auth\Entity\OAuthUser;
use Northwestern\SysDev\SOA\Auth\OAuth2\NorthwesternAzureProvider as AzureDriver;
use Northwestern\SysDev\SOA\Auth\WebSSOAuthentication;
use Northwestern\SysDev\SOA\Providers\NuSoaServiceProvider;
use Orchestra\Testbench\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class OAuthAuthenticationTest extends TestCase
{
    const OAUTH_DUMMY_PROVIDER_URL = 'https://oauth.example.org';

    public function test_redirects_to_oauth_provider()
    {
        $this->app['router']->get(__METHOD__, function (Request $request) {
            return $this->mock_controller()->oauthRedirect($request);
        });

        $response = $this->get(__METHOD__);
        $response->assertRedirect(self::OAUTH_DUMMY_PROVIDER_URL);
    }

    public function test_callback_success()
    {
        $this->app['router']->get(__METHOD__, function (Request $request) {
            $oauthUser = $this->createStub(User::class);
            $oauthUser->token = 'a';
            $oauthUser->method('getRaw')->willReturn([
                'mailNickname' => 'foo',
            ]);

            $driver = $this->createStub(AzureDriver::class);
            $driver->method('user')->willReturn($oauthUser);

            return $this->mock_controller($driver)->oauthCallback($request);
        });

        $response = $this->get(__METHOD__);
        $response->assertRedirect('/logged-in');
        $this->assertAuthenticated();
    }

    #[DataProvider('restartableExceptionProvider')]
    public function test_exceptions_restart_flow($exception)
    {
        $this->app['router']->get('/login-oauth-redirect', function () {
        })->name('login-oauth-redirect');

        $this->app['router']->get(__METHOD__, function (Request $request) use ($exception) {
            $driver = $this->createStub(AzureDriver::class);
            $driver->method('user')->willThrowException($exception);

            return $this->mock_controller($driver)->oauthCallback($request);
        });

        $response = $this->get(__METHOD__);
        $response->assertRedirect('/login-oauth-redirect');
    }

    public function restartableExceptionProvider()
    {
        $errorResponse = $this->createStub(ResponseInterface::class);
        $errorResponse->method('getStatusCode')->willReturn(400);

        return [
            'invalid state' => [new InvalidStateException],
            'guzzle 400 w/ message' => [
                new ClientException(
                    'OAuth2 Authorization code was already redeemed',
                    $this->createStub(RequestInterface::class),
                    $errorResponse
                ),
            ],
        ];
    }

    public function test_unhandled_exceptions_are_rethrown()
    {
        $this->app['router']->get(__METHOD__, function (Request $request) {
            $driver = $this->createStub(AzureDriver::class);
            $driver->method('user')->willThrowException(new \Exception('Unhandled, yay!'));

            return $this->mock_controller($driver)->oauthCallback($request);
        });

        $response = $this->get(__METHOD__);
        $this->assertEquals('Unhandled, yay!', $response->exception->getMessage());
    }

    public function test_logout()
    {
        $this->app['router']->post(__METHOD__, function (Request $request) {
            $driver = $this->createStub(AzureDriver::class);
            $driver->method('getLogoutUrl')->willReturn('/oauth2/v2.0/logout');

            return $this->mock_controller($driver)->oauthLogout();
        });

        Auth::shouldReceive('logout')->once();
        $response = $this->post(__METHOD__);
        $response->assertRedirect();
        $this->assertStringContainsString('/oauth2/v2.0/logout', $response->headers->get('Location'));
    }

    public function test_logout_with_redirect()
    {
        $this->app['router']->post(__METHOD__, function (Request $request) {
            $driver = $this->createStub(AzureDriver::class);
            $driver->method('getLogoutUrl')->willReturn('/oauth2/v2.0/logout');

            return $this->mock_controller($driver)->oauthLogout('https://google.com?foo=1&bar=2');
        });

        Auth::shouldReceive('logout')->once();
        $response = $this->post(__METHOD__);
        $response->assertRedirect();
        $this->assertStringContainsString('/oauth2/v2.0/logout', $response->headers->get('Location'));
        $this->assertStringContainsString('?post_logout_redirect_uri=https%3A%2F%2Fgoogle.com%3Ffoo%3D1%26bar%3D2', $response->headers->get('Location'));
    }

    private function mock_controller($driver = null)
    {
        if (! $driver) {
            $driver = $this->createStub(AzureDriver::class);
            $driver->method('redirectUrl')->willReturn(null);
            $driver->method('redirect')->willReturn(redirect(self::OAUTH_DUMMY_PROVIDER_URL));
        }

        return new class($driver)
        {
            use WebSSOAuthentication;

            protected $driver;

            public function __construct($driver)
            {
                $this->driver = $driver;
            }

            public function oauthDriver()
            {
                return $this->driver;
            }

            protected function redirectPath()
            {
                return '/logged-in';
            }

            protected function findUserByOAuthUser(Request $request, OAuthUser $oauthUser): ?AuthenticatableContract
            {
                // Ensures the service container is resolving requested dependencies (like Request)
                if ($request === null) {
                    throw \Exception('Injection failed');
                }

                $user = new class implements AuthenticatableContract
                {
                    use Authenticatable;

                    private $netid;

                    public function getKeyName()
                    {
                        return 'netid';
                    }
                };

                return $user;
            }
        };
    }

    protected function getEnvironmentSetUp($app)
    {
        // Since the controller manipulates cookies, it needs a key set to work.
        $app['config']->set('app.key', 'base64:'.base64_encode(Encrypter::generateKey($app['config']['app.cipher'])));
    }

    protected function getPackageProviders($application)
    {
        return [NuSoaServiceProvider::class];
    }
}
