<?php

namespace Northwestern\SysDev\SOA\EventHub\Model;

class DeliveredMessage
{
    protected $id;
    protected $deserialized_message;
    protected $raw_message;

    public function __construct(string $id, string $message)
    {
        $this->id = $id;
        $this->raw_message = $message;
        $this->deserialized_message = json_decode($message, JSON_OBJECT_AS_ARRAY);
    } // end __construct

    public function getId(): string
    {
        return $this->id;
    } // end getMessageId

    public function getMessage(): array
    {
        return $this->deserialized_message;
    } // end getMessage

    public function getRawMessage(): string
    {
        return $this->raw_message;
    } // end getRawMessage

} // end DeliveredMessage
