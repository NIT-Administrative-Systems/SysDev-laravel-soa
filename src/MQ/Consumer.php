<?php

namespace Northwestern\SysDev\SOA\MQ;

use GuzzleHttp;

class Consumer
{
    protected $baseUrl;
    protected $endpointPath;
    protected $username;
    protected $password;
    protected $apiKey;

    protected $lastUrl;
    protected $lastError;
    protected $lastRequestMessage;

    private $http_client;

    public function __construct(GuzzleHttp\Client $client)
    {
        $this->http_client = $client;
        $this->baseUrl = config('nusoa.messageQueue.baseUrl');
        $this->endpointPath = config('nusoa.messageQueue.consumePath');
        $this->username = config('nusoa.messageQueue.username');
        $this->password = config('nusoa.messageQueue.password');
        $this->apiKey = config('nusoa.messageQueue.apiKey');
    } // end __construct

    /**
     * Pulls one message off the queue.
     *
     * @param  [type] $event_type [description]
     * @return string|false|null  You'll get back the JSON string if there was a message, false on error, and null if there are no messages.
     */
    public function pullMessage($topic)
    {
        return $this->checkForMessage($topic);
    } // end pullMessage

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

    protected function getRequestMessage($event_type)
    {
        $this->lastRequestMessage = vsprintf('<MQConsumer> <Topic>%s</Topic> </MQConsumer>', [$event_type]);

        return $this->lastRequestMessage;
    } // end getRequestMessage

    protected function checkForMessage($topic)
    {
        try {
            $request = $this->http_client->request('POST', $this->getApiUrl(), [
                'auth' => [$this->username, $this->password],
                'headers' => [
                    'apikey' => $this->apiKey,
                ],
                'http_errors' => false, // don't throw exceptions
                'content-type' => 'application/xml',
                'body' => $this->getRequestMessage($topic),
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
            // We get a 500 for no messages & need to check the body
            if ($request->getBody() != null && substr($request->getBody(), 0, 11) == 'no messages') {
                return null;
            }

            $message = 'The request got an error code. HTTP code was ' . $request->getStatusCode();
            if ($request->getBody() != null) {
                $message .= "\nBody was\n\n" . $request->getBody();
            }

            $this->lastError = $message;
            return false;
        }

        // I&A fixed it to do no messages for a 200 too.
        // Can be either 'no messages' or '<Result>no messages</Result>', depending on service acct config.
        $payload = $request->getBody()->getContents();
        if (substr($payload, 0, 11) == 'no messages' || substr($payload, 8, 11) == 'no messages') {
            return null;
        }

        return $payload;
    } // end publishJson

    private function getApiUrl()
    {
        $this->lastUrl = vsprintf('%s/%s', [$this->baseUrl, $this->endpointPath]);
        return $this->lastUrl;
    } // end getApiUrl

} // end Publisher
