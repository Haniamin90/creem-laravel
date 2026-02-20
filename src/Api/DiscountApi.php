<?php

namespace Creem\Laravel\Api;

use Creem\Laravel\CreemClient;

class DiscountApi
{
    protected CreemClient $client;

    public function __construct(CreemClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new discount code.
     *
     * @param  array<string, mixed>  $params  Discount attributes:
     *                                        - name (string, required): Discount name.
     *                                        - type (string, required): 'percentage' or 'fixed'.
     *                                        - duration (string, required): 'forever', 'once', or 'repeating'.
     *                                        - applies_to_products (array, required): Array of product IDs.
     *                                        - code (string): Custom code (auto-generated if omitted).
     *                                        - percentage (int): Percentage value (for percentage type).
     *                                        - amount (int): Fixed discount in cents (for fixed type).
     *                                        - currency (string): Required for fixed discounts.
     *                                        - expiry_date (string): ISO 8601 expiration date.
     *                                        - max_redemptions (int): Maximum number of uses.
     *                                        - duration_in_months (int): For repeating duration.
     * @return array<string, mixed>
     */
    public function create(array $params): array
    {
        return $this->client->post('v1/discounts', $params);
    }

    /**
     * Retrieve a discount by ID or code.
     *
     * @param  array<string, mixed>  $params  Query parameters (discount_id or discount_code).
     * @return array<string, mixed>
     */
    public function get(array $params): array
    {
        return $this->client->get('v1/discounts', $params);
    }

    /**
     * Delete a discount by ID.
     *
     * @param  string  $discountId  The discount ID.
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     */
    public function delete(string $discountId): array
    {
        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $discountId)) {
            throw new \InvalidArgumentException('Invalid discount ID format.');
        }

        return $this->client->delete("v1/discounts/{$discountId}/delete");
    }
}
