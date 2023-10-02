<?php

namespace Northwestern\SysDev\SOA\Console\Commands\EventHub;

use Illuminate\Console\Command;
use Northwestern\SysDev\SOA\EventHub;
use Illuminate\Console\ConfirmableTrait;
use Northwestern\SysDev\SOA\Routing\EventHubWebhookRegistration;

class WebhookConfiguration extends Command
{
    use ConfirmableTrait;

    protected $signature = 'eventhub:webhook:configure {--force}';
    protected $description = 'Configure webhook routes in EventHub';

    protected $webhook_api;
    protected $hook_registry;

    public function __construct(EventHub\Webhook $webhook_api, EventHubWebhookRegistration $hook_registry)
    {
        parent::__construct();

        $this->webhook_api = $webhook_api;
        $this->hook_registry = $hook_registry;
    } // end __construct

    public function handle()
    {
        $force_delete = $this->option('force');

        $registered_hooks = collect($this->webhook_api->listAll()['webhooks']);
        $registered_hooks = $registered_hooks->map(function ($hook) {
            return $hook['topicName'];
        })->all();

        // We can remove stuff from this one as we touch them -- that we we know which still exist.
        $existing_hooks_updated = $registered_hooks;

        $desired_hooks = $this->hook_registry->getHooks();
        foreach ($desired_hooks as $hook) {
            $hook = $hook->toArray();

            // Remove from unmanaged list
            $existing_hooks_updated = array_diff($existing_hooks_updated, [$hook['topicName']]);

            $this->createOrUpdate($hook, $registered_hooks);
        }

        if (sizeof($existing_hooks_updated) > 0) {
            $this->line('');
            $this->error('The following webhooks are configured in EventHub but do not have a corresponding entry in the routes file:');
            $this->line('');
            $this->error(implode(', ', $existing_hooks_updated));
            $this->line('');

            $delete_unmanaged = $force_delete ?: $this->confirm('Would you like to delete these unmanaged webhooks?');
            if ($delete_unmanaged === true) {
                foreach ($existing_hooks_updated as $hook_to_delete) {
                    $this->delete($hook_to_delete);
                }
            }
        }

        // Display status after the changes are made.
        $this->call('eventhub:webhook:status');

        return 0;
    } // end handle

    protected function createOrUpdate($hook, $registered_hooks)
    {
        $topicName = $hook['topicName'];

        try {
            // If the hook exists on EventHub already, we don't need to touch the 'active' status.
            // But, new ones will require it.
            if (in_array($hook['topicName'], $registered_hooks) === false) {
                // Not permitted in creates since Jan 2022 (but fine for updates)
                unset($hook['topicName']);

                $hook['active'] = true;

                $this->webhook_api->create($topicName, $hook);
            } else {
                $this->webhook_api->updateConfig($topicName, $hook);
            }
        } catch (EventHub\Exception\EventHubError $e) {
            $this->line('');
            $this->error(vsprintf('Failed to update %s: %s', [$topicName, $e->getMessage()]));
            $this->line('');
        }
    } // end createOrUpdate

    protected function delete($queue_name)
    {
        try {
            $this->webhook_api->delete($queue_name);
        } catch (EventHub\Exception\EventHubError $e) {
            $this->line('');
            $this->error(vsprintf('Failed to delete %s: %s', [$queue_name, $e->getMessage()]));
            $this->line('');
        }
    } // end delete
} // end WebhookConfiguration
