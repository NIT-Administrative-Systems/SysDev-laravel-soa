<?php

namespace Northwestern\SysDev\SOA\Tests;

use GuzzleHttp\Client;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Northwestern\SysDev\SOA\Auth\Strategy\OpenAM11;
use Northwestern\SysDev\SOA\Auth\WebSSOAuthentication;
use Northwestern\SysDev\SOA\Exceptions\InsecureSsoError;
use Northwestern\SysDev\SOA\Providers\NuSoaServiceProvider;
use Northwestern\SysDev\SOA\Tests\Concerns\TestsOpenAM11;
use Northwestern\SysDev\SOA\WebSSO;
use Northwestern\SysDev\SOA\WebSSOImpl\ApigeeAgentless;

class OpenAM11AuthenticationTest extends TestCase
{
    use TestsOpenAM11;

    protected $useSecure;

    protected $service = WebSSO::class;

    protected $strategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useSecure = true; // resets to true for every test, since that's mostly what we're testing
        $this->api = new ApigeeAgentless(resolve(Client::class), config('app.url'), config('nusoa.sso'));
        $this->strategy = new OpenAM11($this->api);
    }

    protected function getPackageProviders($application)
    {
        return [NuSoaServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Since the controller manipulates cookies, it needs a key set to work.
        $app['config']->set('app.key', 'base64:'.base64_encode(Encrypter::generateKey($app['config']['app.cipher'])));
        $app['config']->set('nusoa.sso.strategy', 'apigee');
        $app['config']->set('duo.enabled', false);
    } // end getEnvironmentSetUp

    public function test_successful_login_no_mfa()
    {
        $this->app['router']->get(__METHOD__, function (Request $request) {
            // Doing withCookie() on the get won't work cuz that only injects into the Request,
            // but the controller must access this via the $_COOKIE array to avoid Laravel "decrypting"
            // the value and exploding.
            $_COOKIE['nusso'] = 'dummy-token';

            // Successful openAM token lookup
            $this->api->setHttpClient($this->mockedResponse(200, $this->ssoResponseJson('test-id', false)));

            return $this->mock_controller()->login($request, $this->strategy);
        })->name('login');

        $response = $this->get(__METHOD__);
        $response->assertRedirect('/logged-in');
        $this->assertAuthenticated();
    }

    public function test_redirects_when_no_cookie()
    {
        $this->app['router']->get(__METHOD__, function (Request $request) {
            unset($_COOKIE['nusso']);

            return $this->mock_controller()->login($request, $this->strategy);
        })->name('login');

        $response = $this->get(__METHOD__)->assertRedirect();
        $this->assertSsoRedirect($response);
    }

    public function test_redirects_when_cookie_is_invalid()
    {
        $this->app['router']->get(__METHOD__, function (Request $request) {
            $_COOKIE['nusso'] = 'dummy-token';

            $this->api->setHttpClient($this->mockedResponse(407, ''));

            return $this->mock_controller()->login($request, $this->strategy);
        })->name('login');

        $response = $this->get(__METHOD__)->assertRedirect();
        $this->assertSsoRedirect($response);
    }

    public function test_exception_when_apigee_key_is_invalid()
    {
        $this->app['router']->get(__METHOD__, function (Request $request) {
            $_COOKIE['nusso'] = 'dummy-token';

            $this->api->setHttpClient($this->mockedResponse(401, ''));

            return $this->mock_controller()->login($request, $this->strategy);
        })->name('login');

        $this->get(__METHOD__)->assertStatus(500);
    }

    public function test_sends_to_mfa()
    {
        $this->app['config']->set('nusoa.sso.authTree', 'ldap-and-duo');
        $this->app['config']->set('duo.enabled', true);

        $this->api = new ApigeeAgentless(resolve(Client::class), config('app.url'), config('nusoa.sso'));
        $this->strategy = new OpenAM11($this->api);

        $this->app['router']->get(__METHOD__, ['middleware' => 'web', 'uses' => function (Request $request) {
            $_COOKIE['openAMssoToken'] = 'dummy-token';
            $this->api->setHttpClient($this->mockedResponse(200, $this->ssoResponseJson('test-id', false)));

            return $this->mock_controller()->login($request, $this->strategy);
        }])->name('login');

        $response = $this->withSession([])->get(__METHOD__)->assertRedirect();

        $error = sprintf('SSO redirect URL %s should contain ldap-and-duo', $response->getTargetUrl());
        $this->assertGreaterThan(-1, strpos($response->getTargetUrl(), 'authIndexValue=ldap-and-duo'), $error);
    }

    public function tests_exception_when_insecure_connection_used()
    {
        // Disable the "force HTTPS" thing in ::prepareUrlForRequest
        $this->useSecure = false;

        $this->app['router']->get(__METHOD__, function (Request $request) {
            $_COOKIE['nusso'] = 'dummy-token';

            $this->api->setHttpClient($this->mockedResponse(407, ''));

            return $this->mock_controller()->login($request, $this->strategy);
        })->name('login');

        $response = $this->get(__METHOD__)->assertStatus(500);
        $this->assertInstanceOf(InsecureSsoError::class, $response->exception);
    }

    private function mock_controller()
    {
        return new class
        {
            use WebSSOAuthentication;

            protected function redirectPath()
            {
                return '/logged-in';
            }

            protected function findUserByNetID(Request $request, string $netid): ?AuthenticatableContract
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

    private function assertSsoRedirect($response)
    {
        $this->assertTrue(Str::contains($response->headers->get('Location'), config('nusoa.sso.openAmBaseUrl')));
    }

    /**
     * Forces URLs invoked in this test to be <https://> so the request will claim to be secure.
     *
     * The Symfony\Component\HttpFoundation\Request that underlies Laravel's own Request class
     * will check the URL to determine if it's a secure request when it's instantiated with ::create(),
     * which is what the MakesHttpRequests trait is doing to mock up the request for PHPUnit.
     *
     * The $_SERVER var HTTPS is ignored in favour of the protocol field in the URL, so replacing this method
     * with one that forces https is the only way to make this test pass.
     */
    protected function prepareUrlForRequest($uri)
    {
        if (Str::startsWith($uri, '/')) {
            $uri = substr($uri, 1);
        }

        return trim(url($uri, [], $this->useSecure), '/');
    }
}
