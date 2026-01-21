<?php

namespace Northwestern\SysDev\SOA\Console\Commands\EventHub;

use Illuminate\Console\Command;
use Northwestern\SysDev\SOA\EventHub\DeadLetterQueue;

use function Laravel\Prompts\progress;

class RestoreMessagesFromDLQ extends Command
{
    protected $signature = 'eventhub:dlq:restore-messages {dlqName} {maxNumber}';

    protected $description = 'Move messages from DLQ back to the active queue for re-processing.';

    public function __construct(
        protected DeadLetterQueue $dlqApi,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dlqName = $this->argument('dlqName');
        $max = (int) $this->argument('maxNumber');

        $processedIds = [];

        progress(
            label: 'Restoring messages from DLQ',
            steps: range(1, $max),
            callback: function () use ($dlqName, &$processedIds) {
                $id = $this->returnOldestMessage($dlqName);
                $processedIds[] = $id;
            }
        );

        $this->newLine();
        $this->components->info("Completed processing {$max} messages!");
        $this->newLine();

        $this->components->bulletList($processedIds);

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
