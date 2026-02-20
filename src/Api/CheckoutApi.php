<?php

namespace Creem\Laravel\Api;

use Creem\Laravel\CreemClient;

class CheckoutApi
{
    protected CreemClient $client;

    public function __construct(CreemClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new checkout session.
     *
     * @param  string  $productId  The product ID.
     * @param  array<string, mixed>  $params  Additional parameters:
     *                                        - success_url (string): URL to redirect after successful payment.
     *                                        - request_id (string): Idempotency key.
     *                                        - customer (array): Customer data with 'email' key.
     *                                        - metadata (array): Custom metadata key-value pairs.
     *                                        - discount_code (string): Apply a discount code.
     * @return array<string, mixed>
     */
    public function create(string $productId, array $params = []): array
    {
        return $this->client->post('v1/checkouts', array_merge(
            ['product_id' => $productId],
            $params
        ));
    }

    /**
     * Retrieve a checkout session by ID.
     *
     * @param  string  $checkoutId  The checkout session ID.
     * @return array<string, mixed>
     */
    public function get(string $checkoutId): array
    {
        return $this->client->get('v1/checkouts', ['checkout_id' => $checkoutId]);
    }
}
