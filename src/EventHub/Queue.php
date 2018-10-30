<?php

namespace Northwestern\SysDev\SOA\EventHub;

class Queue extends EventHubBase
{
    /**
     * Return information about queues you are authorized to access.
     *
     * @param  int $duration Period to pull queue statistics for
     * @see https://apiserviceregistry.northwestern.edu/#/queue/getQueueList
     */
    public function listAll(int $duration = null): array
    {
        $params = ($duration === null ? [] : ['duration' => $duration]);

        return $this->call('get', '/v1/event-hub/queue', $params);
    } // end listAll

    /**
     * Retrieve information about a specific queue.
     *
     * @param  string $topic_name  The topic name
     * @param  int    $duration   Period to pull queue statistics for
     * @see https://apiserviceregistry.northwestern.edu/#/queue/getQueueInfo
     */
    public function getInfo(string $topic_name, int $duration = null): array
    {
        $params = ($duration === null ? [] : ['duration' => $duration]);

        return $this->call('get', vsprintf('/v1/event-hub/queue/%s', [$topic_name]), $params);
    } // end getInfo

    /**
     * Clear any messages in a queue.
     *
     * @param  string  $topic_name  The topic name
     * @see https://apiserviceregistry.northwestern.edu/#/queue/clearQueue
     */
    public function clearAllMessages(string $topic_name): bool
    {
        return $this->call('delete', vsprintf('/v1/event-hub/queue/%s', [$topic_name]));
    } // end clearAllMessages

    /**
     * Update information about this queue.
     *
     * @param  string $topic_name The topic name
     * @param  array  $params     Set of parameters to send, e.g. ['autoAcknowledge' => true]
     * @see https://apiserviceregistry.northwestern.edu/#/queue/clearQueue
     */
    public function configure(string $topic_name, array $params): array
    {
        $params = sizeof($params) === 0 ? '{}' : json_encode($params);

        return $this->call('patch', vsprintf('/v1/event-hub/queue/%s', [$topic_name]), [], $params);
    } // end configure

    /**
     * Handy method for submitting a PHP assoc array as a JSON message
     *
     * @param  string $topic_name   The topic name
     * @param  array  $message      Your message, as a PHP associative array e.g. `['cat' => 'dog']`
     * @see https://apiserviceregistry.northwestern.edu/#/queue/writeToQueue
     */
    public function sendTestJsonMessage(string $topic_name, array $message) : string
    {
        return $this->sendTestMessage($topic_name, json_encode($message), 'application/json');
    } // end sendTestJsonMessage

    /**
     * Posts a message to the queue (mostly intended for testing purposes).
     *
     * @param  string $topic_name   The topic name
     * @param  string $message      The message you want to post
     * @param  string $content_type The HTTP Content-Type header value, e.g. application/json
     * @see https://apiserviceregistry.northwestern.edu/#/queue/writeToQueue
     */
    public function sendTestMessage(string $topic_name, string $message, string $content_type): string
    {
        return $this->call('post', vsprintf('/v1/event-hub/queue/%s', [$topic_name]), [], $message, ['Content-Type' => $content_type]);
    } // end sendTestMessage

} // end Queue
