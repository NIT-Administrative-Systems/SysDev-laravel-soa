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

    public function __construct()
    {
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

    protected function getRequestMessage($event_type)
    {
        $this->lastRequestMessage = vsprintf('<MQConsumer> <Topic>%s</Topic> </MQConsumer>', [$event_type]);

        return $this->lastRequestMessage;
    } // end getRequestMessage

    protected function checkForMessage($topic)
    {
        $client = new GuzzleHttp\Client();
        $request = $client->request('POST', $this->getApiUrl(), [
            'auth' => [$this->username, $this->password],
            'headers' => [
                'apikey' => $this->apiKey,
            ],
            'http_errors' => false, // don't throw exceptions
            'content-type' => 'application/xml',
            'body' => $this->getRequestMessage($topic),
        ]);

        if ($request === null) {
            $this->lastError('Request failed. Verify connectivity to ' . $this->baseUrl . ' from the server.');
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

        return $request->getBody()->getContents();
    } // end publishJson

    private function getApiUrl()
    {
        $this->lastUrl = vsprintf('%s/%s', [$this->baseUrl, $this->endpointPath]);
        return $this->lastUrl;
    } // end getApiUrl

} // end Publisher
