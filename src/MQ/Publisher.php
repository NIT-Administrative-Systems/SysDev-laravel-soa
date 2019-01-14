<?php

namespace Northwestern\SysDev\SOA\MQ;

use GuzzleHttp;

class Publisher
{
    protected $baseUrl;
    protected $endpointPath;
    protected $username;
    protected $password;
    protected $apiKey;

    protected $lastUrl;
    protected $lastError;

    private $http_client;

    public function __construct(GuzzleHttp\Client $client)
    {
        // trigger_error('The Generic Publisher service is deprecated. Please upgrade to EventHub.', E_USER_DEPRECATED);

        $this->http_client = $client;
        $this->baseUrl = config('nusoa.messageQueue.baseUrl');
        $this->endpointPath = config('nusoa.messageQueue.publishPath');
        $this->username = config('nusoa.messageQueue.username');
        $this->password = config('nusoa.messageQueue.password');
        $this->apiKey = config('nusoa.messageQueue.apiKey');
    } // end __construct

    public function queue($array, $topic)
    {
        return $this->publishJson(json_encode($array), $topic);
    } // end queueRaw

    public function queueText($text, $topic) {
        return $this->publishJson($text, $topic);
    } // end queueJson

    public function getLastError()
    {
        return $this->lastError;
    } // end getlastError

    public function getLastUrl()
    {
        return $this->lastUrl;
    } // end getLastUrl

    public function setHttpClient(GuzzleHttp\Client $client)
    {
        $this->http_client = $client;
    } // end setHttpClient

    protected function publishJson($message, $topic)
    {
        $url = $this->getPostUrl($topic);

        try {
            $request = $this->http_client->request('POST', $url, [
                'auth' => [$this->username, $this->password],
                'headers' => [
                    'apikey' => $this->apiKey,
                ],
                'http_errors' => false, // don't throw exceptions
                'content-type' => 'text/plain',
                'body' => $message,
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->lastError = vsprintf('Verify connectivity to %s from the server: %s', [$this->baseUrl, $e->getMessage()]);
            return false;
        }

        if ($request === null) {
            $this->lastError = vsprintf('Request failed. Verify connectivity to %s from the server.', [$this->baseUrl]);
            return false;
        }

        if ($request->getStatusCode() != 200) {
            $message = 'The request got an error code. HTTP code was ' . $request->getStatusCode();
            if ($request->getBody() != null) {
                $message .= "\nBody was\n\n" . $request->getBody();
            }

            $this->lastError = $message;
            return false;
        }

        if (strpos($request->getBody(), '<ErrorMessage>') !== false) {
            $this->lastError = "The request got an HTTP 200, but the body has an error message.\n\n" . $request->getBody();
            return false;
        }

        return true;
    } // end publishJson

    private function getPostUrl($topic)
    {
        $this->lastUrl = vsprintf('%s/%s/%s', [$this->baseUrl, $this->endpointPath, $topic]);
        return $this->lastUrl;
    } // end getPostUrl
} // end Publisher
