<?php

namespace Northwestern\SysDev\SOA\Console\Commands\EventHub;

use Illuminate\Console\Command;
use Northwestern\SysDev\SOA\Console\Commands\Concerns\FormatsCommandOutput;
use Northwestern\SysDev\SOA\EventHub;

use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class QueueOverview extends Command
{
    use FormatsCommandOutput;

    protected $signature = 'eventhub:queue:status {duration?}';

    protected $description = 'Display statistics & information about any queues available for reading';

    public function __construct(
        protected EventHub\Queue $queue_api,
        protected EventHub\DeadLetterQueue $dlq_api
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $duration = $this->argument('duration');
        $duration = $duration !== null ? (int) $duration : null;

        $queues = spin(
            fn () => $this->queue_api->listAll($duration),
            'Fetching queue information...'
        );

        if (count($queues) === 0) {
            $this->components->error('You have no queues available.');

            return self::FAILURE;
        }

        foreach ($queues as $queue_detail) {
            $stats = collect($queue_detail['queueStatistics']);
            $stat_headers = collect($stats->first())->keys();

            $dlq_info = spin(
                fn () => $this->dlq_api->getInfo($queue_detail['topicName'], $duration),
                "Fetching DLQ info for {$queue_detail['topicName']}..."
            );
            $dlq_stats = collect($dlq_info['queueStatistics']);

            // Remove some of the less exciting information to cut down on visual clutter
            $info_to_display = collect($queue_detail)->except(['queueStatistics', 'topicName', 'name', 'eventHubAccount']);

            $this->divider();
            $this->newLine();
            $this->line(vsprintf(' <fg=white>Queue:</> <bg=magenta;fg=white;options=bold> %s </>', [$queue_detail['topicName']]));
            $this->newLine();

            foreach ($info_to_display as $key => $value) {
                $this->styledDetail($key, $value);
            }

            $this->newLine();
            note('Queue Statistics');
            table(headers: $stat_headers->all(), rows: $stats->all());
            $this->newLine();

            note('Dead Letter Queue Statistics');
            table(headers: $stat_headers->all(), rows: $dlq_stats->all());
        }

        return self::SUCCESS;
    }
}
