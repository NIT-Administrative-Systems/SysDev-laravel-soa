<?php

namespace Northwestern\SysDev\SOA\Console\Commands\EventHub;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Northwestern\SysDev\SOA\Console\Commands\Concerns\ConfirmsEventHubChanges;
use Northwestern\SysDev\SOA\EventHub;
use Northwestern\SysDev\SOA\Routing\EventHubWebhookRegistration;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\spin;

class WebhookConfiguration extends Command
{
    use ConfirmableTrait, ConfirmsEventHubChanges;

    protected $signature = 'eventhub:webhook:configure {--force}';

    protected $description = 'Configure webhook routes in EventHub';

    public function __construct(
        protected EventHub\Webhook $webhook_api,
        protected EventHubWebhookRegistration $hook_registry
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->confirmWebhookConfiguration()) {
            $this->components->error('Command aborted.');

            return self::FAILURE;
        }

        $force_delete = $this->option('force');

        $registered_hooks = spin(
            fn () => collect($this->webhook_api->listAll()['webhooks']),
            'Fetching existing webhooks...'
        );

        $registered_hooks = $registered_hooks->map(fn ($hook) => $hook['topicName'])->all();

        // We can remove stuff from this one as we touch them -- that we we know which still exist.
        $existing_hooks_updated = $registered_hooks;

        $desired_hooks = $this->hook_registry->getHooks();
        foreach ($desired_hooks as $hook) {
            $hook = $hook->toArray();

            // Remove from unmanaged list
            $existing_hooks_updated = array_diff($existing_hooks_updated, [$hook['topicName']]);

            $this->createOrUpdate($hook, $registered_hooks);
        }

        if (count($existing_hooks_updated) > 0) {
            $this->newLine();
            $this->components->error('The following webhooks are configured in EventHub but do not have a corresponding entry in the routes file:');
            $this->newLine();
            $this->components->bulletList($existing_hooks_updated);
            $this->newLine();

            $delete_unmanaged = $force_delete ?: confirm(
                label: 'Would you like to delete these unmanaged webhooks?',
                default: false,
                hint: 'This will remove webhooks not defined in your routes file.'
            );

            if ($delete_unmanaged === true) {
                foreach ($existing_hooks_updated as $hook_to_delete) {
                    $this->delete($hook_to_delete);
                }
            }
        }

        // Display status after the changes are made.
        $this->call('eventhub:webhook:status');

        return self::SUCCESS;
    }

    protected function createOrUpdate($hook, $registered_hooks)
    {
        $topicName = $hook['topicName'];

        try {
            if (in_array($hook['topicName'], $registered_hooks) === false) {
                // For new webhooks, use the active state from the
                // DTO if set, otherwise default to true.
                if (! array_key_exists('active', $hook)) {
                    $hook['active'] = true;
                }

                // Not allowed in the POST/PUT body
                unset($hook['topicName']);

                spin(
                    fn () => $this->webhook_api->create($topicName, $hook),
                    "Creating webhook for {$topicName}..."
                );

                $this->components->info("Created webhook for <bg=magenta;fg=white;options=bold> {$topicName} </>");
            } else {
                // For existing webhooks, enforce the active state if specified.
                // This allows `eventHubWebhookActiveWhen()` to control state.

                // Not allowed in the POST/PUT body
                unset($hook['topicName']);

                spin(
                    fn () => $this->webhook_api->updateConfig($topicName, $hook),
                    "Updating webhook for {$topicName}..."
                );

                $this->components->info("Updated webhook for <bg=magenta;fg=white;options=bold> {$topicName} </>");
            }
        } catch (EventHub\Exception\EventHubError $e) {
            $this->newLine();
            $this->components->error("Failed to update {$topicName}: {$e->getMessage()}");
            $this->newLine();
        }
    }

    protected function delete($queue_name)
    {
        try {
            spin(
                fn () => $this->webhook_api->delete($queue_name),
                "Deleting webhook for {$queue_name}..."
            );

            $this->components->info("Deleted webhook for <bg=magenta;fg=white;options=bold> {$queue_name} </>");
        } catch (EventHub\Exception\EventHubError $e) {
            $this->newLine();
            $this->components->error("Failed to delete {$queue_name}: {$e->getMessage()}");
            $this->newLine();
        }
    }
}
