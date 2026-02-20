<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WebhookLog;
use Creem\Laravel\Exceptions\CreemApiException;
use Creem\Laravel\Facades\Creem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DemoController extends Controller
{
    /**
     * Home page — dashboard showing package features.
     */
    public function index()
    {
        $webhookLogs = WebhookLog::orderBy('created_at', 'desc')->take(20)->get();
        $users = User::all();

        return view('dashboard', [
            'webhookLogs' => $webhookLogs,
            'users' => $users,
            'isSandbox' => Creem::client()->isSandbox(),
            'baseUrl' => Creem::client()->getBaseUrl(),
        ]);
    }

    /**
     * List products from CREEM API.
     */
    public function products()
    {
        try {
            $products = Creem::searchProducts(['page_size' => 20]);

            return view('products', [
                'products' => $products['items'] ?? [],
                'error' => null,
            ]);
        } catch (CreemApiException $e) {
            return view('products', [
                'products' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create a checkout session for a product.
     */
    public function checkout(Request $request)
    {
        $productId = $request->input('product_id');
        $email = $request->input('email', 'demo@creem-laravel.test');

        // Create or find user
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => 'Demo User']
        );

        try {
            $checkout = $user->checkout($productId, [
                'success_url' => url('/success'),
            ]);

            return redirect($checkout['checkout_url']);
        } catch (CreemApiException $e) {
            return back()->with('error', 'Checkout failed: '.$e->getMessage());
        }
    }

    /**
     * Success page after checkout.
     */
    public function success(Request $request)
    {
        return view('success', [
            'checkoutId' => $request->query('checkout_id'),
            'orderId' => $request->query('order_id'),
            'customerId' => $request->query('customer_id'),
        ]);
    }

    /**
     * Test: Sync products via artisan command.
     */
    public function syncProducts()
    {
        try {
            $products = Creem::searchProducts(['page_size' => 50]);

            return response()->json([
                'status' => 'ok',
                'count' => count($products['items'] ?? []),
                'products' => $products['items'] ?? [],
            ]);
        } catch (CreemApiException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace_id' => $e->getTraceId(),
            ], 500);
        }
    }

    /**
     * View webhook logs as JSON.
     */
    public function webhookLogs()
    {
        $logs = WebhookLog::orderBy('created_at', 'desc')->take(50)->get();

        return response()->json($logs);
    }
}
