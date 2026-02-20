<?php

namespace Creem\Laravel\Api;

use Creem\Laravel\CreemClient;

class TransactionApi
{
    protected CreemClient $client;

    public function __construct(CreemClient $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve a transaction by ID.
     *
     * @param  string  $transactionId  The transaction ID.
     * @return array<string, mixed>
     */
    public function get(string $transactionId): array
    {
        return $this->client->get('v1/transactions', ['transaction_id' => $transactionId]);
    }

    /**
     * Search transactions with optional filters.
     *
     * @param  array<string, mixed>  $params  Search/filter parameters:
     *                                        - customer_id (string): Filter by customer ID.
     *                                        - order_id (string): Filter by order ID.
     *                                        - product_id (string): Filter by product ID.
     *                                        - page_number (int): Page number.
     *                                        - page_size (int): Items per page.
     * @return array<string, mixed>
     */
    public function search(array $params = []): array
    {
        return $this->client->get('v1/transactions/search', $params);
    }
}
