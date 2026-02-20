<?php

namespace App\Http\Controllers;

use Creem\Laravel\Exceptions\CreemApiException;
use Creem\Laravel\Facades\Creem;
use Illuminate\Http\Request;

/**
 * Discount code management.
 *
 * Create and manage discount codes that can be applied during checkout.
 * Discounts can be percentage-based or fixed-amount, with optional
 * product restrictions and duration limits.
 */
class DiscountController extends Controller
{
    /**
     * Create a new discount code.
     *
     * Demonstrates: Creem::createDiscount()
     *
     * @see https://docs.creem.io/api#create-a-discount
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'percentage' => 'required_if:type,percentage|nullable|numeric|min:1|max:100',
            'amount' => 'required_if:type,fixed|nullable|integer|min:1',
            'duration' => 'required|in:once,forever,repeating',
            'duration_in_months' => 'required_if:duration,repeating|nullable|integer|min:1',
            'applies_to_products' => 'nullable|array',
            'applies_to_products.*' => 'string',
            'max_redemptions' => 'nullable|integer|min:1',
        ]);

        try {
            $params = array_filter([
                'name' => $request->name,
                'type' => $request->type,
                'percentage' => $request->percentage,
                'amount' => $request->amount,
                'duration' => $request->duration,
                'duration_in_months' => $request->duration_in_months,
                'applies_to_products' => $request->applies_to_products,
                'max_redemptions' => $request->max_redemptions,
            ]);

            $discount = Creem::createDiscount($params);

            return back()->with('success', "Discount '{$discount['name']}' created (code: {$discount['code']}).");
        } catch (CreemApiException $e) {
            return back()->with('error', "Failed to create discount: {$e->getMessage()}");
        }
    }

    /**
     * Look up a discount by ID or code.
     *
     * Demonstrates: Creem::getDiscount()
     */
    public function show(Request $request)
    {
        $request->validate([
            'discount_id' => 'required_without:discount_code|nullable|string',
            'discount_code' => 'required_without:discount_id|nullable|string',
        ]);

        try {
            $discount = Creem::getDiscount(array_filter([
                'discount_id' => $request->discount_id,
                'discount_code' => $request->discount_code,
            ]));

            return response()->json($discount);
        } catch (CreemApiException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace_id' => $e->getTraceId(),
            ], 404);
        }
    }

    /**
     * Delete a discount code.
     *
     * Demonstrates: Creem::deleteDiscount()
     */
    public function destroy(string $discountId)
    {
        try {
            Creem::deleteDiscount($discountId);

            return back()->with('success', 'Discount deleted.');
        } catch (CreemApiException $e) {
            return back()->with('error', "Failed to delete discount: {$e->getMessage()}");
        }
    }
}
