<?php

namespace Northwestern\SysDev\SOA\Console\Commands\EventHub;

use Illuminate\Console\Command;
use Northwestern\SysDev\SOA\EventHub;

class WebhookStatus extends Command
{
    protected $signature = 'eventhub:webhook:status';
    protected $description = 'Display information about webhook setup for any queues you can read from';

    protected $webhook_api;

    public function __construct(EventHub\Webhook $webhook_api)
    {
        parent::__construct();

        $this->webhook_api = $webhook_api;
    } // end __construct

    public function handle()
    {
        $registered_hooks = $this->webhook_api->listAll();
        if (sizeof($registered_hooks['webhooks']) === 0) {
            $this->error('You do not have any webhooks registered.');
            return 1;
        }

        $hooks = [];
        foreach ($registered_hooks['webhooks'] as $possible_hook) {
            $queue_name = $possible_hook['topicName'];

            $details = $this->webhook_api->getInfo($queue_name);
            $hooks[$queue_name] = [
                'queue' => $queue_name,
                'endpoint' => $details['endpoint'],
                'active' => $details['active'] === false ? 'Paused' : 'Active',
            ];
        }

        $this->table(['Queue', 'Endpoint', 'Active'], $hooks);

        return 0;
    } // end handle

} // end WebhookStatus
