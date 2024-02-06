<?php

namespace Northwestern\SysDev\SOA\Console\Commands\EventHub;

use Illuminate\Console\Command;
use Northwestern\SysDev\SOA\EventHub;

class QueueOverview extends Command
{
    protected $signature = 'eventhub:queue:status {duration?}';

    protected $description = 'Display statistics & information about any queues available for reading';

    protected $queue_api;

    protected $dlq_api;

    public function __construct(EventHub\Queue $queue_api, EventHub\DeadLetterQueue $dlq_api)
    {
        parent::__construct();

        $this->queue_api = $queue_api;
        $this->dlq_api = $dlq_api;
    } // end __construct

    public function handle()
    {
        $duration = $this->argument('duration');
        if ($duration !== null) {
            $duration = (int) $duration;
        }

        $queues = $this->queue_api->listAll($duration);
        $queue_count = count($queues);

        if ($queue_count === 0) {
            $this->error('You have no queues available.');

            return 1;
        }

        foreach ($queues as $queue_detail) {
            $stats = collect($queue_detail['queueStatistics']);
            $stat_headers = collect($stats->first())->keys();

            $dlq_info = $this->dlq_api->getInfo($queue_detail['topicName'], $duration);
            $dlq_stats = collect($dlq_info['queueStatistics']);

            // Remove some of the less exciting information to cut down on visual clutter
            $info_to_display = collect($queue_detail)->except(['queueStatistics', 'topicName', 'name', 'eventHubAccount']);

            $this->info(vsprintf('<fg=yellow;options=bold,underscore>Queue %s</>', [$queue_detail['topicName']]));
            $this->line('');

            foreach ($info_to_display as $key => $value) {
                if (is_bool($value) === true) {
                    $value = ($value === true ? 'true' : 'false');
                }

                $this->line("<fg=yellow>$key</>: $value");
            }

            $this->line('');
            $this->comment('Queue Statistics');
            $this->table($stat_headers, $stats);
            $this->line('');

            $this->comment('Dead Letter Queue Statistics');
            $this->table($stat_headers, $dlq_stats);
            $this->line('');
        }

        return 0;
    } // end handle

} // end QueueOverview
