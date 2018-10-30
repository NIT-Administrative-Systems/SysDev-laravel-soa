<?php

namespace Northwestern\SysDev\SOA\EventHub;

class Webhook extends EventHubBase
{
    /**
     * Return information about webhooks you have registered
     *
     * @see https://apiserviceregistry.northwestern.edu/#/webhook/getWebhookList
     */
    public function listAll(): array
    {
        return $this->call('get', '/v1/event-hub/webhook');
    } // end listAll

    /**
     * Retrieve information about a webhook
     *
     * @param  string $topic_name The topic name
     * @see https://apiserviceregistry.northwestern.edu/#/webhook/getWebhookInfo
     */
    public function getInfo(string $topic_name): array
    {
        return $this->call('get', vsprintf('/v1/event-hub/webhook/%s', [$topic_name]));
    } // end getInfo

    /**
     * Deletes a webhook
     *
     * @param  string $topic_name The topic name
     * @see https://apiserviceregistry.northwestern.edu/#/webhook/deleteWebhook
     */
    public function delete(string $topic_name): bool
    {
        return $this->call('delete', vsprintf('/v1/event-hub/webhook/%s', [$topic_name]));
    } // end delete

    /**
     * Register a new webhook
     *
     * @param  string $topic_name The topic name
     * @param  array  $config     Settings for your new webhook
     * @see https://apiserviceregistry.northwestern.edu/#/webhook/registerWebhook
     */
    public function create(string $topic_name, array $config): bool
    {
        return $this->call('post', vsprintf('/v1/event-hub/webhook/%s', [$topic_name]), [], json_encode($config));
    } // end create

    /**
     * Update Webhook information
     *
     * @param  string $topic_name The topic name
     * @param  array  $config     Some settings to change on your webhook
     * @see https://apiserviceregistry.northwestern.edu/#/webhook/updateWebhookDefaults
     */
    public function updateConfig(string $topic_name, array $config): array
    {
        return $this->call('patch', vsprintf('/v1/event-hub/webhook/%s', [$topic_name]), [], json_encode($config));
    } // end updateConfig

    /**
     * Sends an update w/ active = false to pause the webhook.
     *
     * @param  string $topic_name The topic name
     * @see https://apiserviceregistry.northwestern.edu/#/webhook/updateWebhookDefaults
     */
    public function pause(string $topic_name): array
    {
        return $this->updateConfig($topic_name, ['active' => false]);
    } // end pause

    /**
     * Sends an update w/ active = true to un-pause the webhook.
     *
     * @param  string $topic_name The topic name
     * @see https://apiserviceregistry.northwestern.edu/#/webhook/updateWebhookDefaults
     */
    public function unpause(string $topic_name): array
    {
        return $this->updateConfig($topic_name, ['active' => true]);
    } // end unpause

} // end Webhook
