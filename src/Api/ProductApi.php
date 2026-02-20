<?php

namespace Creem\Laravel\Api;

use Creem\Laravel\CreemClient;

class ProductApi
{
    protected CreemClient $client;

    public function __construct(CreemClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new product.
     *
     * @param  array<string, mixed>  $params  Product attributes:
     *                                        - name (string, required): Product name.
     *                                        - description (string): Product description.
     *                                        - price (int, required): Price in cents (e.g., 1000 = $10.00).
     *                                        - currency (string): ISO currency code (default: USD).
     *                                        - billing_type (string): 'onetime' or 'recurring'.
     *                                        - billing_period (string): 'every-month', 'every-three-months', 'every-six-months', 'every-year'.
     *                                        - tax_category (string): 'saas', 'digital-goods-service', 'ebooks'.
     *                                        - tax_mode (string): 'inclusive' or 'exclusive'.
     * @return array<string, mixed>
     */
    public function create(array $params): array
    {
        return $this->client->post('v1/products', $params);
    }

    /**
     * Retrieve a product by ID.
     *
     * @param  string  $productId  The product ID.
     * @return array<string, mixed>
     */
    public function get(string $productId): array
    {
        return $this->client->get('v1/products', ['product_id' => $productId]);
    }

    /**
     * Search products with optional filters and pagination.
     *
     * @param  array<string, mixed>  $params  Search/filter parameters.
     * @return array<string, mixed>
     */
    public function search(array $params = []): array
    {
        return $this->client->get('v1/products/search', $params);
    }
}
