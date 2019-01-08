<?php

namespace Northwestern\SysDev\SOA\Tests;

use Exception;
use Illuminate\Encryption\Encrypter;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Northwestern\SysDev\SOA\Providers\NuSoaServiceProvider;
use Northwestern\SysDev\SOA\Http\Middleware\VerifyEventHubHMAC;

class VerifyEventHubHMACTest extends BaseTestCase
{
    const HMAC_VERIFICATION_MIDDLEWARE = 'eventhub_hmac';
    protected $header_name;

    protected function getPackageProviders($application)
    {
        return [NuSoaServiceProvider::class];
    } // end getPackageProviders

    protected function getEnvironmentSetUp($app)
    {
        // Testbench seems to re-run this in setUp(), so the settings are
        // forced back to whatever is in this method for each test case.
        $app['config']->set('app.debug', true);
        $app['config']->set('nusoa.eventHub.hmacVerificationSharedSecret', 'qbmbR6Vgqlw5vF8vncSRGJXWw9a5JpEGA11ZRqVuaN4=');

        $this->header_name = $app['config']->get('nusoa.eventHub.hmacVerificationHeader');
    } // end getEnvironmentSetUp

    public function test_valid_signature_pass_through()
    {
        $success_msg = 'hmac is ok';
        $this->app['router']->post(__METHOD__, ['middleware' => self::HMAC_VERIFICATION_MIDDLEWARE, 'uses' => function () use ($success_msg) {
            return $success_msg;
        }]);

        $response = $this->postJson(__METHOD__, ['application_id' => 12345], [$this->header_name => base64_encode('f194cd675cc3ebb9f8bf60396a372860e4efcb7c578b8eb76deb3c222bac902b')]);
        $response->assertOk();
        $response->assertSeeText($success_msg);
    } // end test_valid_signature_pass_through

    public function test_invalid_signature_401_unauthorized()
    {
        $this->app['router']->post(__METHOD__, ['middleware' => self::HMAC_VERIFICATION_MIDDLEWARE, 'uses' => function () {
            return 'middleware passed';
        }]);

        $response = $this->postJson(__METHOD__, ['application_id' => 12345], [$this->header_name => base64_encode('c29973c4e84c0edf5b4e601506ff63ce95baffc9ecc9e287cb0580cfbb243b7a')]);
        $response->assertStatus(401);
        $response->assertSeeText('HMAC Validation Failure');
    } // end test_invalid_signature_401_unauthorized

    public function test_no_header_401_unauthorized()
    {
        $this->app['router']->post(__METHOD__, ['middleware' => self::HMAC_VERIFICATION_MIDDLEWARE, 'uses' => function () {
            return 'middleware passed';
        }]);

        $response = $this->postJson(__METHOD__, ['application_id' => 12345]);
        $response->assertStatus(401);
        $response->assertSeeText('No HMAC Signature Sent');
    } // end test_no_header_401_unauthorized

    public function test_bad_hmac_algorithm()
    {
        $this->app['config']->set('nusoa.eventHub.hmacVerificationAlgorithmForPHPHashHmac', 'a very invalid algorithm');

        $this->app['router']->post(__METHOD__, ['middleware' => self::HMAC_VERIFICATION_MIDDLEWARE, 'uses' => function () {
            return 'middleware passed';
        }]);

        $response = $this->postJson(__METHOD__, ['application_id' => 12345], [$this->header_name => base64_encode('f194cd675cc3ebb9f8bf60396a372860e4efcb7c578b8eb76deb3c222bac902b')]);
        $response->assertStatus(500);
        $response->assertSeeText('Invalid hash algorithm');
    } // end test_bad_hmac_algorithm

} // end VerifyEventHubHMACTest
