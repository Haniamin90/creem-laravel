<?php

namespace Creem\Laravel\Api;

use Creem\Laravel\CreemClient;

class CustomerApi
{
    protected CreemClient $client;

    public function __construct(CreemClient $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve a customer by ID or email.
     *
     * @param  array<string, mixed>  $params  Query parameters:
     *                                        - id (string): Customer ID.
     *                                        - email (string): Customer email address.
     * @return array<string, mixed>
     */
    public function get(array $params): array
    {
        return $this->client->get('v1/customers', $params);
    }

    /**
     * List all customers with pagination.
     *
     * @param  array<string, mixed>  $params  Pagination parameters.
     * @return array<string, mixed>
     */
    public function list(array $params = []): array
    {
        return $this->client->get('v1/customers/list', $params);
    }

    /**
     * Generate a billing portal link for a customer.
     *
     * @param  string  $customerId  The customer ID.
     * @return array<string, mixed>
     */
    public function billingPortal(string $customerId): array
    {
        return $this->client->post('v1/customers/billing', [
            'customer_id' => $customerId,
        ]);
    }
}
