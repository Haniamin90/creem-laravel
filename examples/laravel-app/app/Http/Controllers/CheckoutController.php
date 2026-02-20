<?php

namespace App\Http\Controllers;

use Creem\Laravel\Exceptions\CreemApiException;
use Creem\Laravel\Exceptions\CreemAuthenticationException;
use Creem\Laravel\Exceptions\CreemRateLimitException;
use Creem\Laravel\Facades\Creem;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Show the pricing page with products fetched from CREEM.
     *
     * Demonstrates: Creem::searchProducts()
     */
    public function pricing()
    {
        try {
            $products = Creem::searchProducts([
                'page_size' => 20,
            ]);

            return view('pricing', [
                'products' => $products['items'] ?? [],
                'pagination' => $products['pagination'] ?? null,
                'error' => null,
            ]);
        } catch (CreemAuthenticationException $e) {
            return view('pricing', [
                'products' => [],
                'pagination' => null,
                'error' => 'Invalid API key. Check your CREEM_API_KEY in .env',
            ]);
        } catch (CreemApiException $e) {
            return view('pricing', [
                'products' => [],
                'pagination' => null,
                'error' => "Failed to load products: {$e->getMessage()} (Trace: {$e->getTraceId()})",
            ]);
        }
    }

    /**
     * Create a checkout session and redirect to CREEM's hosted payment page.
     *
     * Demonstrates two approaches:
     *   1. Using the Creem Facade directly
     *   2. Using the Billable trait on the User model
     *
     * Demonstrates: Creem::createCheckout(), $user->checkout()
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            'discount_code' => 'nullable|string',
            'method' => 'nullable|in:facade,billable',
        ]);

        $user = $request->user();
        $method = $request->input('method', 'billable');

        try {
            if ($method === 'facade') {
                // ── Approach 1: Using the Facade directly ────────────────
                // Gives you full control over all parameters.
                $checkout = Creem::createCheckout($request->product_id, [
                    'success_url' => route('checkout.success'),
                    'customer' => [
                        'email' => $user->email,
                    ],
                    'metadata' => [
                        'user_id' => (string) $user->id,
                        'source' => 'pricing_page',
                    ],
                    // Apply discount code if provided
                    ...($request->discount_code ? ['discount_code' => $request->discount_code] : []),
                ]);
            } else {
                // ── Approach 2: Using the Billable trait ──────────────────
                // Auto-fills email from $user->email and adds metadata with
                // model_type, model_id, and creem_customer_id (if available).
                $checkout = $user->checkout($request->product_id, [
                    'success_url' => route('checkout.success'),
                    ...($request->discount_code ? ['discount_code' => $request->discount_code] : []),
                ]);
            }

            return redirect($checkout['checkout_url']);
        } catch (CreemRateLimitException $e) {
            return back()->with('error', 'Too many requests. Please try again in a moment.');
        } catch (CreemAuthenticationException $e) {
            return back()->with('error', 'Payment system configuration error. Please contact support.');
        } catch (CreemApiException $e) {
            return back()->with('error', "Unable to create checkout: {$e->getMessage()}");
        }
    }

    /**
     * Handle the return from CREEM after a successful checkout.
     *
     * CREEM appends query parameters: checkout_id, order_id, customer_id.
     *
     * Demonstrates: Creem::getCheckout()
     */
    public function success(Request $request)
    {
        $checkoutId = $request->query('checkout_id');
        $checkoutDetails = null;

        // Optionally fetch full checkout details for display
        if ($checkoutId) {
            try {
                $checkoutDetails = Creem::getCheckout($checkoutId);
            } catch (CreemApiException $e) {
                // Gracefully degrade — show success page without details
            }
        }

        return view('checkout.success', [
            'checkoutId' => $checkoutId,
            'orderId' => $request->query('order_id'),
            'customerId' => $request->query('customer_id'),
            'checkout' => $checkoutDetails,
        ]);
    }
}
