<?php

namespace Northwestern\SysDev\SOA\Console\Commands\Concerns;

use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

/**
 * Prompts for confirmation when running EventHub commands in local environments.
 *
 * @mixin Command
 */
trait ConfirmsEventHubChanges
{
    /**
     * Confirm before reconfiguring webhook endpoints.
     */
    protected function confirmWebhookConfiguration(): bool
    {
        if (config('app.env') !== 'local') {
            return true;
        }

        $this->displayLocalEnvironmentWarning();

        $this->components->warn('This command will reconfigure EventHub webhooks to deliver messages to your local URL.');
        $this->components->warn('If this EventHub environment is shared with other applications, this may disrupt their webhook deliveries.');
        $this->newLine();

        return confirm(
            label: 'Do you want to continue?',
            default: false,
            yes: 'Yes, continue',
            no: 'No, cancel',
        );
    }

    /**
     * Confirm before pausing or unpausing webhook deliveries.
     */
    protected function confirmWebhookToggle(): bool
    {
        if (config('app.env') !== 'local') {
            return true;
        }

        $this->displayLocalEnvironmentWarning();

        $this->components->warn('This command will pause or unpause webhook deliveries.');
        $this->components->warn('If this EventHub environment is shared with other applications, this may affect their message processing.');
        $this->newLine();

        return confirm(
            label: 'Do you want to continue?',
            default: false,
            yes: 'Yes, continue',
            no: 'No, cancel',
        );
    }

    private function displayLocalEnvironmentWarning(): void
    {
        $appUrl = config('app.url') ?? '';
        $eventHubUrl = config('nusoa.eventHub.baseUrl') ?? '';

        $this->newLine();
        $this->components->error('Local Environment Detected');
        $this->newLine();

        $this->components->bulletList([
            "APP_ENV is set to <fg=yellow>local</>",
            "APP_URL is <fg=yellow>{$appUrl}</>",
            "EventHub URL is <fg=yellow>{$eventHubUrl}</>",
        ]);

        $this->newLine();
    }
}
