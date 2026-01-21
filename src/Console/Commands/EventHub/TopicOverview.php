<?php

namespace Northwestern\SysDev\SOA\Console\Commands\EventHub;

use Illuminate\Console\Command;
use Northwestern\SysDev\SOA\Console\Commands\Concerns\FormatsCommandOutput;
use Northwestern\SysDev\SOA\EventHub;

use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class TopicOverview extends Command
{
    use FormatsCommandOutput;

    protected $signature = 'eventhub:topic:status {duration?}';

    protected $description = 'Display statistics & information about any topics available for publishing';

    public function __construct(protected EventHub\Topic $topic_api)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $duration = $this->argument('duration');
        $duration = $duration !== null ? (int) $duration : null;

        $topics = spin(
            fn () => $this->topic_api->listAll($duration),
            'Fetching topic information...'
        );

        if (count($topics) === 0) {
            $this->components->error('You have no topics available.');

            return self::FAILURE;
        }

        foreach ($topics as $topic_detail) {
            $this->divider();
            $this->newLine();
            $this->line(vsprintf(' <fg=white>Topic:</> <bg=magenta;fg=white;options=bold> %s </>', [$topic_detail['topicName']]));
            $this->newLine();

            $fields = collect($topic_detail)->only(['timeToLive', 'enqueueCount']);
            foreach ($fields as $key => $value) {
                $this->styledDetail($key, $value);
            }

            $this->newLine();
            note('Subscribers');

            $subscriber_data = collect($topic_detail['subscribers'])->map(
                fn ($sub) => array_values(collect($sub)->only(['eventHubAccount', 'name', 'alertAddress'])->all())
            );

            table(
                headers: ['Subscriber', 'Contact', 'Queue Name'],
                rows: $subscriber_data->values()->all()
            );
        }

        return self::SUCCESS;
    }
}
