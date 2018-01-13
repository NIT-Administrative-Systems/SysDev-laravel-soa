<?php

namespace Northwestern\SysDev\SOA;

use GuzzleHttp;

/**
 * Bindings for NU's DirectorySearch SOA service.
 *
 * For documentation, see <https://northwestern-apiportal.apigee.io/apis/directory-search-expanded/index>.
 */
class DirectorySearch
{
    protected $baseUrl;
    protected $apiKey;

    protected $lastError;

    protected $lookupMethods = [
        'netid' => '/res/netid',
        'emplid' => '/res/emplid',
        'hremplid' => '/res/hremplid',
        'sesemplid' => '/res/sesemplid',
        'barcode' => '/res/barcode',
        'mail' => '/res/mail',
        'studentemail' => '/res/studentemail',
    ];

    protected $detailLevel = [
        'public' => '/pub/',
        'basic' => '/bas/',
        'expanded' => '/exp/',
    ];

    private $http_client;

    public function __construct(GuzzleHttp\Client $client)
    {
        $this->http_client = $client;
        $this->baseUrl = config('nusoa.directorySearch.baseUrl');
        $this->apiKey = config('nusoa.directorySearch.apiKey');
    } // end __constructg

    /**
     * Convenience method to get the expanded detail by netID.
     */
    public function lookupByNetId($netid, $level = 'expanded')
    {
        return $this->lookup($netid, 'netid', $level);
    } // end lookupByNetId

    /**
     * [lookup description]
     * @param  string $value    Value to search by.
     * @param  string $searchBy See the $lookupMethods property.
     * @param  string $level    public, basic, or expanded
     * @return []               NetID details. Fields depend on the level.
     */
    public function lookup($value, $searchBy, $level)
    {
        if (array_key_exists($searchBy, $this->lookupMethods) == false) {
            throw new \Exception("Invalid searchBy specified: '$searchBy'.");
        }

        if (array_key_exists($level, $this->detailLevel) == false) {
            throw new \Exception("Invalid $level specified: '$level'.");
        }

        $url = implode('', [$this->baseUrl, $this->lookupMethods[$searchBy], $this->detailLevel[$level], $value]);

        $result = $this->doGet($url);
        if ($result == false) {
            return $result;
        }

        $result = json_decode($result, true);

        // HTTP 200 can still have an error.
        if (array_key_exists('ErrorMessage', $result) == true) {
            $this->lastError = $result['ErrorMessage'];
            return false;
        }

        return $result['results'][0];
    } // end lookup

    public function getLastError()
    {
        return $this->lastError;
    } // end getLastError

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    } // end setBaseUrl

    public function setApiKey($key)
    {
        $this->apiKey = $key;
    } // end setApiKey

    public function setHttpClient(GuzzleHttp\Client $client)
    {
        $this->http_client = $client;
    } // end setHttpClient

    protected function doGet($url)
    {
        try {
            $request = $this->http_client->request('GET', $url, [
                'headers' => [
                    'apikey' => $this->apiKey,
                ],
                'http_errors' => false, // don't throw exceptions, I want to check the status code
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->lastError = vsprintf('Verify connectivity to %s from the server: %s', [$this->baseUrl, $e->getMessage()]);
            return false;
        }

        // Bad netID, service unavailable
        if ($request === null) {
            $this->lastError = vsprintf('Request failed. Verify connectivity to %s from the server.', [$this->baseUrl]);
            return false;
        }

        if ($request->getStatusCode() != 200) {
            $message = 'HTTP connection succeeded but no body. HTTP code was ' . $request->getStatusCode();
            if ($request->getBody() != null) {
                $error = json_decode($request->getBody(), true);

                // handle error based on returned format
                if (!empty($error['errorMessage'])) {
                    $message = $error['errorMessage'];
                } else if (!empty($error['fault']['faultstring'])) {
                    $message = $error['fault']['faultstring'];
                }
            }

            $this->lastError = $message;
            return false;
        }

        return $request->getBody();
    } // end doGet

} // end IdMapper
