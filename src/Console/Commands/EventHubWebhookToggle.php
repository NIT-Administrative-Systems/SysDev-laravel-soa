<?php

namespace Northwestern\SysDev\SOA\Console\Commands;

use Illuminate\Console\Command;
use Northwestern\SysDev\SOA\EventHub;
use Illuminate\Console\ConfirmableTrait;

class EventHubWebhookToggle extends Command
{
    use ConfirmableTrait;

    protected $signature = 'eventhub:webhook:toggle {status : pause or unpause} {queues?* : one or more queues to toggle. if unspecified, all queues will be updated.}';
    protected $description = 'Pause or unpause webhook deliveries.';

    protected $webhook_api;

    public function __construct(EventHub\Webhook $webhook_api)
    {
        parent::__construct();

        $this->webhook_api = $webhook_api;
    } // end __construct

    public function handle()
    {
        $queues = [];

        $status = strtolower($this->argument('status'));
        if (in_array($status, ['pause', 'unpause']) === false) {
            $this->error('Invalid status. Please specify pause or unpause.');
            return 1;
        }

        $specific_queues = $this->argument('queues');
        if (sizeof($specific_queues) > 0) {
            $queues = $specific_queues;
        } else {
            $hooks = $this->webhook_api->listAll();
            $queues = collect($hooks['webhooks'])->map(function ($hook) {
                return $hook['topicName'];
            })->all();
        }

        foreach ($queues as $queue) {
            try {
                $this->webhook_api->$status($queue);
            } catch (EventHub\Exception\EventHubError $e) {
                $this->error(vsprintf('Unable to change status of "%s".', [$queue]));
                $this->line('');
                continue;
            }
        }

        // Display status after the changes are made.
        $this->call('eventhub:webhook:status');
    } // end handle

} // end EventHubWebhookToggle
