<?php

namespace Northwestern\SysDev\SOA\Console\Commands\EventHub;

use Illuminate\Console\Command;
use Northwestern\SysDev\SOA\EventHub\DeadLetterQueue;

class RestoreMessagesFromDLQ extends Command
{
    protected $signature = 'eventhub:dlq:restore-messages {dlqName} {maxNumber}';

    protected $description = 'Move messages from DLQ back to the active queue for re-processing.';

    public function __construct(
        protected DeadLetterQueue $dlqApi,
    )
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dlqName = $this->argument('dlqName');
        $max = (int) $this->argument('maxNumber');

        for ($i = 0; $i<$max; $i++) {
            $this->info("Processing message #{$i}...\n");

            $id = $this->returnOldestMessage($dlqName);
            $this->info("\tProcessed Message ID '{$id}'\n");
            $this->newLine();
        }

        $this->info("Completed processing!\n");
        return self::SUCCESS;
    }

    /**
     * Returns the oldest message in a DLQ back to its actual queue for redelivery.
     *
     * @return string|null Processed Message ID, or null if there's nothing left.
     */
    protected function returnOldestMessage(string $queueName): ?string
    {
        $message = $this->dlqApi->readOldest($queueName, acknowledge: false);

        $this->dlqApi->moveFromDLQ($queueName, $message->getId(), $queueName);

        return $message->getId();
    }
}