<?php

namespace Northwestern\SysDev\SOA\Console\Commands\EventHub;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Northwestern\SysDev\SOA\Console\Commands\Concerns\ConfirmsEventHubChanges;
use Northwestern\SysDev\SOA\EventHub;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\spin;

class WebhookToggle extends Command
{
    use ConfirmableTrait, ConfirmsEventHubChanges;

    protected $signature = 'eventhub:webhook:toggle {status : pause or unpause} {queues?* : one or more queues to toggle. if unspecified, all queues will be updated.}';

    protected $description = 'Pause or unpause webhook deliveries.';

    public function __construct(
        protected EventHub\Webhook $webhook_api
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->confirmWebhookToggle()) {
            $this->components->error('Command aborted.');

            return self::FAILURE;
        }

        $queues = [];

        $status = strtolower($this->argument('status'));
        if (in_array($status, ['pause', 'unpause']) === false) {
            $this->components->error('Invalid status. Please specify pause or unpause.');

            return self::FAILURE;
        }

        $specific_queues = $this->argument('queues');
        if (count($specific_queues) > 0) {
            $queues = $specific_queues;
        } else {
            $hooks = spin(
                fn () => $this->webhook_api->listAll(),
                'Fetching webhooks...'
            );

            $queues = collect($hooks['webhooks'])->pluck('topicName')->all();

            if (empty($queues)) {
                $this->components->warn('No webhooks found.');

                return self::SUCCESS;
            }
        }

        foreach ($queues as $queue) {
            try {
                spin(
                    fn () => $this->webhook_api->$status($queue),
                    ucfirst($status)."ing {$queue}..."
                );

                $this->components->info(ucfirst($status)."d <bg=magenta;fg=white;options=bold> {$queue} </>");
            } catch (EventHub\Exception\EventHubError $e) {
                $this->components->error("Unable to change status of <bg=magenta;fg=white;options=bold> {$queue} </>: {$e->getMessage()}");
                $this->newLine();
            }
        }

        // Display status after the changes are made.
        $this->call('eventhub:webhook:status');

        return self::SUCCESS;
    }
}
