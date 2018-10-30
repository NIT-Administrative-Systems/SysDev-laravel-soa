<?php

namespace Northwestern\SysDev\SOA\EventHub;

class Message extends EventHubBase
{
    /*
    public function readOldestJson(string $topic_name, bool $acknowledge = null): array
    {
        return json_decode($this->readOldest($topic_name, $acknowledge), );
    } // end readOldestJson

    public function readJson(string $topic_name, string $message_id): array
    {

    } // end readJson
    */

    /**
     * Reads a message from a queue you are authorized to access.
     *
     * @param  string $topic_name The topic name
     * @param  bool   $acknowledge Auto-ack (auto-delete), true or false
     * @see https://apiserviceregistry.northwestern.edu/#/message/getMessages
     */
    public function readOldest(string $topic_name, bool $acknowledge = null): ?string
    {
        $params = ($acknowledge === null ? [] : ['acknowledge' => $acknowledge]);

        $message = $this->call('get', vsprintf('/v1/event-hub/queue/%s/message', [$topic_name]), $params);

        // No messages, yay!
        if ($message === true) {
            return null;
        }

        return $message;
    } // end readOldest

    /**
     * Acknowledges (Deletes) the oldest message from a queue
     *
     * @param  string $topic_name The topic name
     * @see https://apiserviceregistry.northwestern.edu/#/message/acknowledgeOldestMessage
     */
    public function acknowledgeOldest(string $topic_name): bool
    {
        return $this->call('delete', vsprintf('/v1/event-hub/queue/%s/message', [$topic_name]));
    } // end acknowledgeOldest

    /**
     * Retrieve a specific message from a queue
     *
     * @param  string $topic_name The topic name
     * @param  string $message_id The message ID
     * @see https://apiserviceregistry.northwestern.edu/#/message/getSpecificMessage
     */
    public function read(string $topic_name, string $message_id): string
    {
        return $this->call('get', vsprintf('/v1/event-hub/queue/%s/message/%s', [$topic_name, $message_id]));
    } // end read

    /**
     * Acknowledge (Delete) a message or group of message from a queue
     *
     * @param  string $topic_name   The topic name
     * @param  string $message_id   The message ID
     * @param  bool   $fast_forward If set to true the message represented by the message identifier and all older messages in the queue will be acknowledged.
     * @see https://apiserviceregistry.northwestern.edu/#/message/acknowledgeMessage
     */
    public function acknowledge(string $topic_name, string $message_id, bool $fast_forward = null): bool
    {
        $params = ($fast_forward === null ? [] : ['fast_forward' => $fast_forward]);

        return $this->call('delete', vsprintf('/v1/event-hub/queue/%s/message/%s', [$topic_name, $message_id]), $params);
    } // end acknowledge

    /**
     * Allows you to move a message from a queue you own to another queue/topic you own. This could be done via a get and subsequent write call. It also allows for the moving of a message to the queues corresponding DLQ (dead letter queue), or to redeliver the message to the same queue but with a delivery delay.
     *
     * @param  string $source_topic_name      Source topic name
     * @param  string $message_id             The message ID to move
     * @param  string $destination_type       Type of destination you want to move this message to {QUEUE | TOPIC}
     * @param  string $destination_topic_name Name of the destination you are moving this message to
     * @param  int    $delay                  Delay in milliseconds to wait before delivering this message
     * @see https://apiserviceregistry.northwestern.edu/#/message/moveMessage1
     */
    public function move(string $source_topic_name, string $message_id, string $destination_type, string $destination_topic_name, int $delay = null): bool
    {
        $params = ($delay === null ? [] : ['delay' => $delay]);

        // This one is kinda long & was pretty unreadable on one line, so...
        $url = vsprintf('/v1/event-hub/queue/%s/message/%s/%s/%s', [
            $source_topic_name,
            $message_id,
            $destination_type,
            $destination_topic_name,
        ]);

        return $this->call('post', $url, $params);
    } // end move

} // end Message
