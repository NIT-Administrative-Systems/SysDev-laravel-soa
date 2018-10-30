<?php

namespace Northwestern\SysDev\SOA\EventHub;

class DeadLetterQueue extends EventHubBase
{
    /**
     * Retrieve information about the dead letter queue for a specific queue
     *
     * @param  string $topic_name The topic name
     * @param  int    $duration   Period to pull DLQ statistics for
     * @see https://apiserviceregistry.northwestern.edu/#/DLQ/getDeadLetterQueueInfo
     */
    public function getInfo(string $topic_name, int $duration = null): array
    {
        $params = ($duration === null ? [] : ['duration' => $duration]);

        return $this->call('get', vsprintf('/v1/event-hub/queue/%s/dlq', [$topic_name]), $params);
    } // end getInfo

    /**
     * Moves a message to the DLQ (dead letter queue) for the specified queue.
     *
     * @param  string $destination_topic_name The topic name whose DLQ you want to move a message into
     * @param  string $message_id             ID of the message you are moving to the DLQ
     * @see https://apiserviceregistry.northwestern.edu/#/DLQ/moveMessage2
     */
    public function moveToDLQ(string $destination_topic_name, string $message_id): bool
    {
        /*
        * @TODO - Brent is looking at why this is 404ing
        *
        * Brent: This was how I was calling it, 
        *   https://northwestern-{{env}}.apigee.net/v1/event-hub/dlq/BAB/message/ID:b137ad914b75-44883-1540240224517-1:21:1:1:1/QUEUE/BAB
        */
        return $this->call('post', vsprintf('/v1/event-hub/queue/%s/message/%s/dlq', [$destination_topic_name, $message_id]));
    } // end moveToDLQ

} // end DeadLetterQueue
