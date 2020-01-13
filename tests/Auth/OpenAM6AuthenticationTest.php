<?php

namespace Northwestern\SysDev\SOA\Tests;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Northwestern\SysDev\SOA\Auth\WebSSOAuthentication;
use Northwestern\SysDev\SOA\Providers\NuSoaServiceProvider;
use Northwestern\SysDev\SOA\WebSSO;
use Northwestern\SysDev\SOA\Tests\TestCase;
use Northwestern\SysDev\SOA\Auth\Strategy\WebSSOStrategy;

class OpenAM6AuthenticationTest extends TestCase
{
    protected $service = WebSSO::class;
    protected $strategy;

    public function setUp(): void
    {
        parent::setUp();
        $this->strategy = resolve(WebSSOStrategy::class);
    }

    protected function getPackageProviders($application)
    {
        return [NuSoaServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Since the controller manipulates cookies, it needs a key set to work.
        $app['config']->set('app.key', 'base64:'.base64_encode(Encrypter::generateKey($app['config']['app.cipher'])));
        $app['config']->set('nusoa.sso.strategy', 'classic');
        $app['config']->set('duo.enabled', false);
    } // end getEnvironmentSetUp

    public function test_successful_login_no_mfa()
    {
        $this->app['router']->get(__METHOD__, function (Request $request) {
            // Doing withCookie() on the get won't work cuz that only injects into the Request,
            // but the controller must access this via the $_COOKIE array to avoid Laravel "decrypting"
            // the value and exploding.
            $_COOKIE['openAMssoToken'] = 'dummy-token';

            // Successful openAM token lookup
            $this->api->setHttpClient($this->mockedResponse(200, "userdetails.token.id=test-token\nuserdetails.attribute.name=UserToken\nuserdetails.attribute.value=test-id"));

            return $this->mock_controller()->login($request, $this->strategy);
        })->name('login');

        $response = $this->get(__METHOD__);
        $response->assertRedirect('/logged-in');
        $this->assertAuthenticated();
    }

    public function test_redirects_when_no_cookie()
    {
        $this->app['router']->get(__METHOD__, function (Request $request) {
            unset($_COOKIE['openAMssoToken']);

            return $this->mock_controller()->login($request, $this->strategy);
        })->name('login');

        $response = $this->get(__METHOD__)->assertRedirect();
        $this->assertSsoRedirect($response);
    }

    public function test_redirects_when_cookie_is_invalid()
    {
        $this->app['router']->get(__METHOD__, function (Request $request) {
            $_COOKIE['openAMssoToken'] = 'dummy-token';

            $this->api->setHttpClient($this->mockedResponse(200, "userdetails.attribute.name=UserToken"));

            return $this->mock_controller()->login($request, $this->strategy);
        })->name('login');

        $response = $this->get(__METHOD__)->assertRedirect();
        $this->assertSsoRedirect($response);
    }

    public function test_sends_to_mfa()
    {
        $this->app['config']->set('duo.enabled', true);

        $mfa_url = Str::slug(sprintf('%s/mfa', __METHOD__));
        $this->app['router']->get($mfa_url, function () {
            // do nothing, just need the route to exist
        })->name('mfa.index');

        $this->app['router']->get(__METHOD__, ['middleware' => 'web', 'uses' => function (Request $request) {
            $_COOKIE['openAMssoToken'] = 'dummy-token';
            $this->api->setHttpClient($this->mockedResponse(200, "userdetails.token.id=test-token\nuserdetails.attribute.name=UserToken\nuserdetails.attribute.value=test-id"));

            return $this->mock_controller()->login($request, $this->strategy);
        }])->name('login');

        $response = $this->withSession([])->get(__METHOD__)
            ->assertRedirect($mfa_url)
            ->assertSessionHas('mfa_netid');
    }

    private function mock_controller()
    {
        return new class {
            use WebSSOAuthentication;

            protected function redirectPath()
            {
                return '/logged-in';
            }

            protected function findUserByNetID(Request $request, string $netid): ?AuthenticatableContract
            {
                // Ensures the service container is resolsing requested dependencies (like Request)
                if ($request === null) {
                    throw \Exception('Injection failed');
                }

                $user = new class implements AuthenticatableContract {
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
}
