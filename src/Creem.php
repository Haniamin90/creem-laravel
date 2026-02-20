<?php

namespace Creem\Laravel;

use Creem\Laravel\Api\CheckoutApi;
use Creem\Laravel\Api\CustomerApi;
use Creem\Laravel\Api\DiscountApi;
use Creem\Laravel\Api\LicenseApi;
use Creem\Laravel\Api\ProductApi;
use Creem\Laravel\Api\SubscriptionApi;
use Creem\Laravel\Api\TransactionApi;

class Creem
{
    protected CreemClient $client;

    public function __construct(CreemClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get the underlying HTTP client.
     */
    public function client(): CreemClient
    {
        return $this->client;
    }

    // -------------------------------------------------------------------------
    // Checkout
    // -------------------------------------------------------------------------

    /**
     * Create a new checkout session.
     *
     * @param  string  $productId  The product ID to create a checkout for.
     * @param  array<string, mixed>  $params  Additional checkout parameters.
     * @return array<string, mixed>
     */
    public function createCheckout(string $productId, array $params = []): array
    {
        return (new CheckoutApi($this->client))->create($productId, $params);
    }

    /**
     * Retrieve a checkout session by ID.
     *
     * @param  string  $checkoutId  The checkout session ID.
     * @return array<string, mixed>
     */
    public function getCheckout(string $checkoutId): array
    {
        return (new CheckoutApi($this->client))->get($checkoutId);
    }

    // -------------------------------------------------------------------------
    // Products
    // -------------------------------------------------------------------------

    /**
     * Create a new product.
     *
     * @param  array<string, mixed>  $params  Product attributes.
     * @return array<string, mixed>
     */
    public function createProduct(array $params): array
    {
        return (new ProductApi($this->client))->create($params);
    }

    /**
     * Retrieve a product by ID.
     *
     * @param  string  $productId  The product ID.
     * @return array<string, mixed>
     */
    public function getProduct(string $productId): array
    {
        return (new ProductApi($this->client))->get($productId);
    }

    /**
     * Search products with optional filters.
     *
     * @param  array<string, mixed>  $params  Search/filter parameters.
     * @return array<string, mixed>
     */
    public function searchProducts(array $params = []): array
    {
        return (new ProductApi($this->client))->search($params);
    }

    // -------------------------------------------------------------------------
    // Customers
    // -------------------------------------------------------------------------

    /**
     * Retrieve a customer by ID or email.
     *
     * @param  array<string, mixed>  $params  Query parameters (id or email).
     * @return array<string, mixed>
     */
    public function getCustomer(array $params): array
    {
        return (new CustomerApi($this->client))->get($params);
    }

    /**
     * List all customers with pagination.
     *
     * @param  array<string, mixed>  $params  Pagination parameters.
     * @return array<string, mixed>
     */
    public function listCustomers(array $params = []): array
    {
        return (new CustomerApi($this->client))->list($params);
    }

    /**
     * Generate a billing portal link for a customer.
     *
     * @param  string  $customerId  The customer ID.
     * @return array<string, mixed>
     */
    public function customerBillingPortal(string $customerId): array
    {
        return (new CustomerApi($this->client))->billingPortal($customerId);
    }

    // -------------------------------------------------------------------------
    // Subscriptions
    // -------------------------------------------------------------------------

    /**
     * Retrieve a subscription by ID.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @return array<string, mixed>
     */
    public function getSubscription(string $subscriptionId): array
    {
        return (new SubscriptionApi($this->client))->get($subscriptionId);
    }

    /**
     * Search subscriptions with optional filters.
     *
     * @param  array<string, mixed>  $params  Search/filter parameters.
     * @return array<string, mixed>
     */
    public function searchSubscriptions(array $params = []): array
    {
        return (new SubscriptionApi($this->client))->search($params);
    }

    /**
     * Update a subscription (units, seats, add-ons).
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @param  array<string, mixed>  $params  Update parameters.
     * @return array<string, mixed>
     */
    public function updateSubscription(string $subscriptionId, array $params): array
    {
        return (new SubscriptionApi($this->client))->update($subscriptionId, $params);
    }

    /**
     * Upgrade a subscription to a different product.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @param  string  $newProductId  The product ID to upgrade to.
     * @return array<string, mixed>
     */
    public function upgradeSubscription(string $subscriptionId, string $newProductId): array
    {
        return (new SubscriptionApi($this->client))->upgrade($subscriptionId, $newProductId);
    }

    /**
     * Cancel a subscription.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @param  string  $mode  Cancel mode: 'immediate' or 'scheduled'.
     * @return array<string, mixed>
     */
    public function cancelSubscription(string $subscriptionId, string $mode = 'scheduled'): array
    {
        return (new SubscriptionApi($this->client))->cancel($subscriptionId, $mode);
    }

    /**
     * Pause a subscription.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @return array<string, mixed>
     */
    public function pauseSubscription(string $subscriptionId): array
    {
        return (new SubscriptionApi($this->client))->pause($subscriptionId);
    }

    /**
     * Resume a paused subscription.
     *
     * @param  string  $subscriptionId  The subscription ID.
     * @return array<string, mixed>
     */
    public function resumeSubscription(string $subscriptionId): array
    {
        return (new SubscriptionApi($this->client))->resume($subscriptionId);
    }

    // -------------------------------------------------------------------------
    // Transactions
    // -------------------------------------------------------------------------

    /**
     * Retrieve a transaction by ID.
     *
     * @param  string  $transactionId  The transaction ID.
     * @return array<string, mixed>
     */
    public function getTransaction(string $transactionId): array
    {
        return (new TransactionApi($this->client))->get($transactionId);
    }

    /**
     * Search transactions with optional filters.
     *
     * @param  array<string, mixed>  $params  Search/filter parameters.
     * @return array<string, mixed>
     */
    public function searchTransactions(array $params = []): array
    {
        return (new TransactionApi($this->client))->search($params);
    }

    // -------------------------------------------------------------------------
    // Licenses
    // -------------------------------------------------------------------------

    /**
     * Activate a license key for a device/instance.
     *
     * @param  string  $key  The license key.
     * @param  string  $instanceName  The device/instance name.
     * @return array<string, mixed>
     */
    public function activateLicense(string $key, string $instanceName): array
    {
        return (new LicenseApi($this->client))->activate($key, $instanceName);
    }

    /**
     * Validate a license key for a specific instance.
     *
     * @param  string  $key  The license key.
     * @param  string  $instanceId  The instance ID from activation.
     * @return array<string, mixed>
     */
    public function validateLicense(string $key, string $instanceId): array
    {
        return (new LicenseApi($this->client))->validate($key, $instanceId);
    }

    /**
     * Deactivate a license key for a device/instance.
     *
     * @param  string  $key  The license key.
     * @param  string  $instanceId  The instance ID from activation.
     * @return array<string, mixed>
     */
    public function deactivateLicense(string $key, string $instanceId): array
    {
        return (new LicenseApi($this->client))->deactivate($key, $instanceId);
    }

    // -------------------------------------------------------------------------
    // Discounts
    // -------------------------------------------------------------------------

    /**
     * Create a new discount code.
     *
     * @param  array<string, mixed>  $params  Discount attributes.
     * @return array<string, mixed>
     */
    public function createDiscount(array $params): array
    {
        return (new DiscountApi($this->client))->create($params);
    }

    /**
     * Retrieve a discount by ID or code.
     *
     * @param  array<string, mixed>  $params  Query parameters (id or code).
     * @return array<string, mixed>
     */
    public function getDiscount(array $params): array
    {
        return (new DiscountApi($this->client))->get($params);
    }

    /**
     * Delete a discount by ID.
     *
     * @param  string  $discountId  The discount ID.
     * @return array<string, mixed>
     */
    public function deleteDiscount(string $discountId): array
    {
        return (new DiscountApi($this->client))->delete($discountId);
    }
}
