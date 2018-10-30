<?php

namespace Northwestern\SysDev\SOA\EventHub;

use GuzzleHttp;
use Northwestern\SysDev\SOA\EventHub\Exception;
use Northwestern\SysDev\SOA\EventHub\Model\DeliveredMessage;

abstract class EventHubBase
{
    protected $base_url;
    protected $api_key;
    protected $last_req_url = null;
    protected $last_req_method = null;
    protected $last_req_body = null;
    protected $last_req_error = null;
    protected $last_req_response_code = null;
    protected $http_client;

    /**
     * Set up API class
     *
     * @param string           $base_url Base URL for Apigee, e.g. https://northwestern-dev.apigee.net
     * @param string           $api_key Apigeee API key
     * @param GuzzleHttpClient $client  A new GuzzleHttp\Client() is suitable, but you can customize it w/ failure retry middleware or what-have-you.
     */
    public function __construct(string $base_url, string $api_key, GuzzleHttp\Client $client)
    {
        $this->base_url = $base_url;
        $this->api_key = $api_key;
        $this->http_client = $client;
    } // end __construct

    protected function call($method, $url, $url_query_params = [], $body = null, $headers = [])
    {
        // Great for debugging w/ `print_r($my_event_hub_object)`.
        $this->last_req_url = $this->makeRequestUrl($url, $url_query_params);
        $this->last_req_method = $method;
        $this->last_req_error = null;
        $this->last_req_response_code = null;
        $this->last_req_body = $body;

        $headers = array_merge([
            'apikey' => $this->api_key,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ], $headers);

        $response = null;
        try {
            $response = $this->http_client->request($this->last_req_method, $this->last_req_url, [
                'headers' => $headers,
                'body' => $this->last_req_body,

                // Don't throw Guzzle exceptions for non-success HTTP codes, we'll read the responses ourselves
                'http_errors' => false,
            ]);
        } catch (GuzzleHttp\Exception\RequestException $e) {
            $this->last_req_error = vsprintf('Network connectivity failure, unable to %s %s: %s', [$this->last_req_method, $this->last_req_url, $e->getMessage()]);
            throw new Exception\EventHubDown($this->last_req_error, 999, $e);
        }

        $this->last_req_response_code = $response->getStatusCode();

        // Normal success code w/ body (but 204 is handled above)
        if ($this->last_req_response_code >= 200 && $this->last_req_response_code <= 299) {
            $response_content = $response->getBody()->getContents();
            $message_id = $this->extractMessageId($response);

            // If there is a body, return that.
            if (strlen($response_content) > 0) {
                if ($message_id !== null) {
                    return new DeliveredMessage($response->getHeader('X-message-id')[0], $response_content);
                }

                return json_decode($response_content, JSON_OBJECT_AS_ARRAY);
            }

            // If there is no body, see if we have a message ID header.
            if ($message_id !== null) {
                return $message_id;
            }

            // If we have no body & no header, just give us a true -- this is a 200-series code, after all.
            return true;
        }

        $this->last_req_error = vsprintf('Failed to %s %s: %s', [$this->last_req_method, $this->last_req_url, $response->getBody()->getContents()]);
        throw new Exception\EventHubError($this->last_req_error, $this->last_req_response_code);
    } // end call

    /**
     * [stringifyBool description]
     *
     * @param  bool   $flag
     * @return string The string "true" or "false", which is what the Event Hub seems to prefer in query parameters
     */
    protected function stringifyBool(bool $flag): string
    {
        return $flag === true ? "true" : "false";
    } // end stringifyBool

    private function makeRequestUrl($url, $query_params)
    {
        $url = vsprintf('%s/%s', [$this->base_url, $url]);

        $prepared_params = [];
        foreach ($query_params as $key => $value) {
            $prepared_params[] = vsprintf('%s=%s', [urlencode($key), urlencode($value)]);
        }

        if (sizeof($prepared_params) > 0) {
            $url = $url . "?" . implode('&', $prepared_params);
        }

        return $url;
    } // end makeRequestUrl

    /**
     * @internal
     */
    private function extractMessageId($response)
    {
        if ($response->hasHeader('X-message-id') === false) {
            return null;
        }

        $msg_id_header = $response->getHeader('X-message-id');
        return $msg_id_header[0];
    } // end extractMessageId

    /**
     * Replaces the HTTP client. Useful for unit testing in combination w/ GuzzleHttp\Handler\MockHandler.
     *
     * @internal
     */
    public function setHttpClient(GuzzleHttp\Client $client)
    {
        $this->http_client = $client;
    } // end setHttpClient

    /**
     * @internal
     */
    public function __debugInfo()
    {
        $dump = get_object_vars($this);
        unset($dump['http_client']);

        return $dump;
    } // end __debugInfo

} // end EventHubBase
