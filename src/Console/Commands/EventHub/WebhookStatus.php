<?php

namespace Northwestern\SysDev\SOA\Console\Commands\EventHub;

use Illuminate\Console\Command;
use Northwestern\SysDev\SOA\EventHub;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class WebhookStatus extends Command
{
    protected $signature = 'eventhub:webhook:status';

    protected $description = 'Display information about webhook setup for any queues you can read from';

    public function __construct(
        protected EventHub\Webhook $webhook_api
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $registered_hooks = spin(
            fn () => $this->webhook_api->listAll(),
            'Fetching webhooks...'
        );

        if (count($registered_hooks['webhooks']) === 0) {
            $this->components->error('You do not have any webhooks registered.');

            return self::FAILURE;
        }

        $hooks = [];
        foreach ($registered_hooks['webhooks'] as $possible_hook) {
            $queue_name = $possible_hook['topicName'];

            $details = spin(
                fn () => $this->webhook_api->getInfo($queue_name),
                "Fetching details for {$queue_name}..."
            );

            $hooks[$queue_name] = [
                'queue' => $queue_name,
                'endpoint' => $details['endpoint'],
                'active' => $details['active'] === false ? '<fg=red>Paused</>' : '<fg=green>Active</>',
            ];
        }

        $this->newLine();
        table(
            headers: ['Queue', 'Endpoint', 'Status'],
            rows: array_values(array_map(fn ($h) => array_values($h), $hooks))
        );

        return self::SUCCESS;
    }
}
