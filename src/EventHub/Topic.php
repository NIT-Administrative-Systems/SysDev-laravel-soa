<?php

namespace Northwestern\SysDev\SOA\EventHub;

class Topic extends EventHubBase
{
    /**
     * Return information about topics you are authorized to write to.
     *
     * @param  int $duration Period to pull topic statistics for
     * @see https://apiserviceregistry.northwestern.edu/#/topic/getTopicList
     */
    public function listAll(int $duration = null): array
    {
        $params = ($duration === null ? [] : ['duration' => $duration]);

        return $this->call('get', '/v1/event-hub/topic', $params);
    } // end listAll

    /**
     * Retrieve information about a specific topic
     *
     * @param  string $topic_name  The topic name
     * @param  int    $duration   Period to pull topic statistics for
     * @see https://apiserviceregistry.northwestern.edu/#/topic/getTopicInfo
     */
    public function getInfo(string $topic_name, int $duration = null): array
    {
        $params = ($duration === null ? [] : ['duration' => $duration]);

        return $this->call('get', vsprintf('/v1/event-hub/topic/%s', [$topic_name]), $params);
    } // end getInfo

    /**
     * Update topic information
     *
     * @param  string $topic_name The topic name
     * @param  array  $params     Set of parameters to send, e.g. ['timeToLive' => 26208000]
     * @see https://apiserviceregistry.northwestern.edu/#/topic/updateTopicDefaults
     */
    public function configure(string $topic_name, array $params): array
    {
        $params = sizeof($params) === 0 ? '{}' : json_encode($params);

        return $this->call('patch', vsprintf('/v1/event-hub/topic/%s', [$topic_name]), [], $params);
    } // end configure

    /**
     * Handy method for submitting a PHP assoc array as a JSON message
     *
     * @param  string $topic_name   The topic name
     * @param  array  $message      Your message, as a PHP associative array e.g. `['cat' => 'dog']`
     * @see https://apiserviceregistry.northwestern.edu/#/topic/updateTopicDefaults
     */
    public function writeJsonMessage(string $topic_name, array $message): string
    {
        return $this->writeMessage($topic_name, json_encode($message), 'application/json');
    } // end writeJsonMessage

    /**
     * Write a message to a topic
     *
     * @param  string $topic_name   The topic name
     * @param  string $message      The message you want to post
     * @param  string $content_type The HTTP Content-Type header value, e.g. application/json
     * @see https://apiserviceregistry.northwestern.edu/#/topic/writeToTopic
     */
    public function writeMessage(string $topic_name, string $message, string $content_type): string
    {
        return $this->call('post', vsprintf('/v1/event-hub/topic/%s', [$topic_name]), [], $message, ['Content-Type' => $content_type]);
    } // end writeMessage

} // end Topic
