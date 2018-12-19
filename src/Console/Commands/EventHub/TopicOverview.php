<?php

namespace Northwestern\SysDev\SOA\Console\Commands\EventHub;

use Illuminate\Console\Command;
use Northwestern\SysDev\SOA\EventHub;

class TopicOverview extends Command
{
    protected $signature = 'eventhub:topic:status {duration?}';
    protected $description = 'Display statistics & information about any topics available for publishing';

    protected $topic_api;

    public function __construct(EventHub\Topic $topic_api)
    {
        parent::__construct();

        $this->topic_api = $topic_api;
    } // end __construct

    public function handle()
    {
        $duration = $this->argument('duration');
        if ($duration !== null) {
            $duration = (int)$duration;
        }

        $topics = $this->topic_api->listAll($duration);
        $topic_count = sizeof($topics);

        if ($topic_count === 0) {
            $this->error('You have no topics available.');
            return 1;
        }

        foreach ($topics as $topic_detail) {
            $this->info(vsprintf('<fg=yellow;options=bold,underscore>Topic %s</>', [$topic_detail['topicName']]));
            $this->line('');

            $fields = collect($topic_detail)->only(['timeToLive', 'enqueueCount']);
            foreach ($fields as $key => $value) {
                $this->line("<fg=yellow>$key</>: $value");
            }

            $this->line('');
            $this->comment('Subscribers');

            $subscriber_data = collect($topic_detail['subscribers'])->map(function ($sub) {
                return collect($sub)->only(['eventHubAccount', 'name', 'alertAddress']);
            });
            $this->table(['Subscriber', 'Contact', 'Queue Name'], $subscriber_data);

            $this->line('');
        }

        return 0;
    } // end handle

} // end TopicOverview
