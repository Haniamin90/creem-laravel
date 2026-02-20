<?php

namespace App\Http\Controllers;

use Creem\Laravel\Exceptions\CreemApiException;
use Creem\Laravel\Facades\Creem;
use Illuminate\Http\Request;

/**
 * Product management via the CREEM API.
 *
 * Products are the core billable items in CREEM. They can be
 * one-time payments or recurring subscriptions.
 */
class ProductController extends Controller
{
    /**
     * List all products with pagination.
     *
     * Demonstrates: Creem::searchProducts()
     */
    public function index(Request $request)
    {
        try {
            $result = Creem::searchProducts([
                'page_size' => 20,
                'page_number' => $request->query('page', 1),
            ]);

            return view('products.index', [
                'products' => $result['items'] ?? [],
                'pagination' => $result['pagination'] ?? null,
            ]);
        } catch (CreemApiException $e) {
            return view('products.index', [
                'products' => [],
                'pagination' => null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create a new product.
     *
     * Demonstrates: Creem::createProduct()
     *
     * Product parameters:
     *   - name (string, required)
     *   - description (string, required)
     *   - price (int, cents — 1999 = $19.99)
     *   - currency (string — 'USD')
     *   - billing_type ('onetime' | 'recurring')
     *   - billing_period ('every-month' | 'every-three-months' | 'every-six-months' | 'every-year')
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer|min:100', // Minimum $1.00
            'currency' => 'required|string|size:3',
            'billing_type' => 'required|in:onetime,recurring',
            'billing_period' => 'required_if:billing_type,recurring|nullable|in:every-month,every-three-months,every-six-months,every-year',
        ]);

        try {
            $product = Creem::createProduct(array_filter([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'currency' => strtoupper($request->currency),
                'billing_type' => $request->billing_type,
                'billing_period' => $request->billing_period,
            ]));

            return back()->with('success', "Product '{$product['name']}' created (ID: {$product['id']}).");
        } catch (CreemApiException $e) {
            return back()->with('error', "Failed to create product: {$e->getMessage()}");
        }
    }

    /**
     * Get a single product by ID.
     *
     * Demonstrates: Creem::getProduct()
     */
    public function show(string $productId)
    {
        try {
            $product = Creem::getProduct($productId);

            return response()->json($product);
        } catch (CreemApiException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace_id' => $e->getTraceId(),
            ], 404);
        }
    }
}
