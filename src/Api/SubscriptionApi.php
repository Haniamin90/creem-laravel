<?php

namespace Creem\Laravel\Api;

use Creem\Laravel\CreemClient;

class SubscriptionApi
{
    protected CreemClient $client;

    public function __construct(CreemClient $client)
    {
        $this->client = $client;
    }

    /**
     * Validate that a resource ID contains only safe characters.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateId(string $id, string $label = 'ID'): void
    {
        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
            throw new \InvalidArgumentException("Invalid {$label} format.");
        }
    }

    /**
     * Retrieve a subscription by ID.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @return array<string, mixed>
     */
    public function get(string $subscriptionId): array
    {
        return $this->client->get('v1/subscriptions', ['subscription_id' => $subscriptionId]);
    }

    /**
     * Search subscriptions with optional filters.
     *
     * @param  array<string, mixed>  $params  Search/filter parameters.
     * @return array<string, mixed>
     */
    public function search(array $params = []): array
    {
        return $this->client->get('v1/subscriptions/search', $params);
    }

    /**
     * Update a subscription (units, seats, add-ons).
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @param  array<string, mixed>  $params  Update parameters.
     * @return array<string, mixed>
     */
    public function update(string $subscriptionId, array $params): array
    {
        $this->validateId($subscriptionId, 'subscription ID');

        return $this->client->post("v1/subscriptions/{$subscriptionId}", $params);
    }

    /**
     * Upgrade a subscription to a different product.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @param  string  $newProductId  The product ID to upgrade to.
     * @return array<string, mixed>
     */
    public function upgrade(string $subscriptionId, string $newProductId): array
    {
        $this->validateId($subscriptionId, 'subscription ID');

        return $this->client->post("v1/subscriptions/{$subscriptionId}/upgrade", [
            'product_id' => $newProductId,
        ]);
    }

    /**
     * Cancel a subscription.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @param  string  $mode  Cancel mode: 'immediate' or 'scheduled'.
     * @return array<string, mixed>
     */
    public function cancel(string $subscriptionId, string $mode = 'scheduled'): array
    {
        $this->validateId($subscriptionId, 'subscription ID');

        return $this->client->post("v1/subscriptions/{$subscriptionId}/cancel", [
            'mode' => $mode,
        ]);
    }

    /**
     * Pause a subscription.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @return array<string, mixed>
     */
    public function pause(string $subscriptionId): array
    {
        $this->validateId($subscriptionId, 'subscription ID');

        return $this->client->post("v1/subscriptions/{$subscriptionId}/pause");
    }

    /**
     * Resume a paused subscription.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @return array<string, mixed>
     */
    public function resume(string $subscriptionId): array
    {
        $this->validateId($subscriptionId, 'subscription ID');

        return $this->client->post("v1/subscriptions/{$subscriptionId}/resume");
    }
}
