<?php

namespace Creem\Laravel\Traits;

use Creem\Laravel\Facades\Creem;

/**
 * Add CREEM billing capabilities to an Eloquent model.
 *
 * This trait provides convenient methods for managing checkouts,
 * subscriptions, and billing portals for a user model.
 */
trait Billable
{
    /**
     * Get the CREEM customer ID stored on this model.
     */
    public function creemCustomerId(): ?string
    {
        return $this->creem_customer_id;
    }

    /**
     * Determine if the model has a CREEM customer ID.
     */
    public function hasCreemCustomerId(): bool
    {
        return ! is_null($this->creem_customer_id);
    }

    /**
     * Set the CREEM customer ID on this model.
     */
    public function setCreemCustomerId(string $customerId): self
    {
        $this->creem_customer_id = $customerId;
        $this->save();

        return $this;
    }

    /**
     * Create a checkout session for this customer.
     *
     * @param  string  $productId  The product ID.
     * @param  array<string, mixed>  $params  Additional checkout parameters.
     * @return array<string, mixed>
     */
    public function checkout(string $productId, array $params = []): array
    {
        $params['customer'] = $params['customer'] ?? [];
        $params['customer']['email'] = $params['customer']['email'] ?? $this->email;

        if ($this->hasCreemCustomerId()) {
            $params['metadata'] = array_merge($params['metadata'] ?? [], [
                'creem_customer_id' => $this->creem_customer_id,
                'model_type' => class_basename($this),
                'model_id' => $this->getKey(),
            ]);
        } else {
            $params['metadata'] = array_merge($params['metadata'] ?? [], [
                'model_type' => class_basename($this),
                'model_id' => $this->getKey(),
            ]);
        }

        return Creem::createCheckout($productId, $params);
    }

    /**
     * Get the billing portal URL for this customer.
     *
     * @return array<string, mixed>
     */
    public function billingPortalUrl(): array
    {
        if (! $this->hasCreemCustomerId()) {
            throw new \RuntimeException(
                'This model does not have a CREEM customer ID. Complete a checkout first.'
            );
        }

        return Creem::customerBillingPortal($this->creem_customer_id);
    }

    /**
     * Retrieve the CREEM customer data.
     *
     * @return array<string, mixed>
     */
    public function creemCustomer(): array
    {
        if ($this->hasCreemCustomerId()) {
            return Creem::getCustomer(['id' => $this->creem_customer_id]);
        }

        return Creem::getCustomer(['email' => $this->email]);
    }

    /**
     * Get all subscriptions for this customer from CREEM.
     *
     * @param  array<string, mixed>  $params  Additional search parameters.
     * @return array<string, mixed>
     */
    public function creemSubscriptions(array $params = []): array
    {
        if ($this->hasCreemCustomerId()) {
            $params['customer'] = $this->creem_customer_id;
        }

        return Creem::searchSubscriptions($params);
    }

    /**
     * Get all transactions for this customer from CREEM.
     *
     * @param  array<string, mixed>  $params  Additional search parameters.
     * @return array<string, mixed>
     */
    public function creemTransactions(array $params = []): array
    {
        if ($this->hasCreemCustomerId()) {
            $params['customer'] = $this->creem_customer_id;
        }

        return Creem::searchTransactions($params);
    }

    /**
     * Cancel a subscription for this customer.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @param  string  $mode  Cancel mode: 'immediate' or 'scheduled'.
     * @return array<string, mixed>
     */
    public function cancelSubscription(string $subscriptionId, string $mode = 'scheduled'): array
    {
        return Creem::cancelSubscription($subscriptionId, $mode);
    }

    /**
     * Pause a subscription for this customer.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @return array<string, mixed>
     */
    public function pauseSubscription(string $subscriptionId): array
    {
        return Creem::pauseSubscription($subscriptionId);
    }

    /**
     * Resume a paused subscription for this customer.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @return array<string, mixed>
     */
    public function resumeSubscription(string $subscriptionId): array
    {
        return Creem::resumeSubscription($subscriptionId);
    }
}
