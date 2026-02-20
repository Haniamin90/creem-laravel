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
     * Methods: Creem::searchProducts(), Creem::getProduct()
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
     * Get a single product detail (JSON).
     * Method: Creem::getProduct()
     */
    public function getProduct(string $id)
    {
        try {
            $product = Creem::getProduct($id);

            return response()->json(['status' => 'ok', 'product' => $product]);
        } catch (CreemApiException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a checkout session for a product.
     * Method: $user->checkout() (Billable trait → Creem::createCheckout())
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
     * Get checkout status (JSON).
     * Method: Creem::getCheckout()
     */
    public function getCheckout(string $id)
    {
        try {
            $checkout = Creem::getCheckout($id);

            return response()->json(['status' => 'ok', 'checkout' => $checkout]);
        } catch (CreemApiException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
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
     * Customers page — list all customers.
     * Methods: Creem::listCustomers(), Creem::getCustomer(), Creem::customerBillingPortal()
     */
    public function customers()
    {
        try {
            $customers = Creem::listCustomers(['page_size' => 20]);

            return view('customers', [
                'customers' => $customers['items'] ?? [],
                'error' => null,
            ]);
        } catch (CreemApiException $e) {
            return view('customers', [
                'customers' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get a single customer detail (JSON).
     * Method: Creem::getCustomer()
     */
    public function getCustomerApi(Request $request)
    {
        try {
            $params = [];
            if ($request->query('customer_id')) {
                $params['customer_id'] = $request->query('customer_id');
            } elseif ($request->query('email')) {
                $params['email'] = $request->query('email');
            }
            $customer = Creem::getCustomer($params);

            return response()->json(['status' => 'ok', 'customer' => $customer]);
        } catch (CreemApiException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get billing portal URL for a customer (JSON).
     * Method: Creem::customerBillingPortal()
     */
    public function billingPortal(string $customerId)
    {
        try {
            $portal = Creem::customerBillingPortal($customerId);

            return response()->json(['status' => 'ok', 'portal' => $portal]);
        } catch (CreemApiException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Subscriptions page — list all subscriptions.
     * Methods: Creem::searchSubscriptions(), Creem::getSubscription(),
     *          Creem::cancelSubscription(), Creem::pauseSubscription(), Creem::resumeSubscription(),
     *          Creem::updateSubscription(), Creem::upgradeSubscription()
     */
    public function subscriptions(Request $request)
    {
        $subscription = null;
        $error = null;
        $lookupId = $request->query('id');

        if ($lookupId) {
            try {
                $subscription = Creem::getSubscription($lookupId);
            } catch (CreemApiException $e) {
                $error = $e->getMessage();
            }
        }

        // Also try searchSubscriptions (may not be available on all API plans)
        $subscriptions = [];
        try {
            $result = Creem::searchSubscriptions(['page_size' => 20]);
            $subscriptions = $result['items'] ?? [];
        } catch (\Exception $e) {
            // Search endpoint may not exist — that's OK, we still have lookup
        }

        return view('subscriptions', [
            'subscriptions' => $subscriptions,
            'subscription' => $subscription,
            'lookupId' => $lookupId,
            'error' => $error,
        ]);
    }

    /**
     * Get a single subscription detail (JSON).
     * Method: Creem::getSubscription()
     */
    public function getSubscriptionApi(string $id)
    {
        try {
            $subscription = Creem::getSubscription($id);

            return response()->json(['status' => 'ok', 'subscription' => $subscription]);
        } catch (CreemApiException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancel a subscription.
     * Method: Creem::cancelSubscription()
     */
    public function cancelSubscription(Request $request)
    {
        try {
            $mode = $request->input('mode', 'scheduled');
            $result = Creem::cancelSubscription($request->input('subscription_id'), $mode);

            return redirect('/subscriptions')->with('success', 'Subscription cancellation initiated ('.$mode.')');
        } catch (CreemApiException $e) {
            return redirect('/subscriptions')->with('error', 'Cancel failed: '.$e->getMessage());
        }
    }

    /**
     * Pause a subscription.
     * Method: Creem::pauseSubscription()
     */
    public function pauseSubscription(Request $request)
    {
        try {
            Creem::pauseSubscription($request->input('subscription_id'));

            return redirect('/subscriptions')->with('success', 'Subscription paused');
        } catch (CreemApiException $e) {
            return redirect('/subscriptions')->with('error', 'Pause failed: '.$e->getMessage());
        }
    }

    /**
     * Resume a subscription.
     * Method: Creem::resumeSubscription()
     */
    public function resumeSubscription(Request $request)
    {
        try {
            Creem::resumeSubscription($request->input('subscription_id'));

            return redirect('/subscriptions')->with('success', 'Subscription resumed');
        } catch (CreemApiException $e) {
            return redirect('/subscriptions')->with('error', 'Resume failed: '.$e->getMessage());
        }
    }

    /**
     * Upgrade a subscription to a new product.
     * Method: Creem::upgradeSubscription()
     */
    public function upgradeSubscription(Request $request)
    {
        try {
            Creem::upgradeSubscription(
                $request->input('subscription_id'),
                $request->input('new_product_id')
            );

            return redirect('/subscriptions')->with('success', 'Subscription upgraded');
        } catch (CreemApiException $e) {
            return redirect('/subscriptions')->with('error', 'Upgrade failed: '.$e->getMessage());
        }
    }

    /**
     * Transactions page — list all transactions.
     * Methods: Creem::searchTransactions(), Creem::getTransaction()
     */
    public function transactions()
    {
        try {
            $transactions = Creem::searchTransactions(['page_size' => 20]);

            return view('transactions', [
                'transactions' => $transactions['items'] ?? [],
                'error' => null,
            ]);
        } catch (CreemApiException $e) {
            return view('transactions', [
                'transactions' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get a single transaction detail (JSON).
     * Method: Creem::getTransaction()
     */
    public function getTransactionApi(string $id)
    {
        try {
            $transaction = Creem::getTransaction($id);

            return response()->json(['status' => 'ok', 'transaction' => $transaction]);
        } catch (CreemApiException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Licenses page — activate, validate, deactivate.
     * Methods: Creem::activateLicense(), Creem::validateLicense(), Creem::deactivateLicense()
     */
    public function licenses()
    {
        return view('licenses', [
            'result' => session('license_result'),
            'error' => null,
        ]);
    }

    /**
     * Activate a license.
     * Method: Creem::activateLicense()
     */
    public function activateLicense(Request $request)
    {
        try {
            $result = Creem::activateLicense(
                $request->input('license_key'),
                $request->input('instance_name', 'demo-instance')
            );

            return redirect('/licenses')->with('license_result', [
                'action' => 'activate',
                'data' => $result,
            ]);
        } catch (CreemApiException $e) {
            return redirect('/licenses')->with('error', 'Activation failed: '.$e->getMessage());
        }
    }

    /**
     * Validate a license.
     * Method: Creem::validateLicense()
     */
    public function validateLicense(Request $request)
    {
        try {
            $result = Creem::validateLicense(
                $request->input('license_key'),
                $request->input('instance_id')
            );

            return redirect('/licenses')->with('license_result', [
                'action' => 'validate',
                'data' => $result,
            ]);
        } catch (CreemApiException $e) {
            return redirect('/licenses')->with('error', 'Validation failed: '.$e->getMessage());
        }
    }

    /**
     * Deactivate a license.
     * Method: Creem::deactivateLicense()
     */
    public function deactivateLicense(Request $request)
    {
        try {
            $result = Creem::deactivateLicense(
                $request->input('license_key'),
                $request->input('instance_id')
            );

            return redirect('/licenses')->with('license_result', [
                'action' => 'deactivate',
                'data' => $result,
            ]);
        } catch (CreemApiException $e) {
            return redirect('/licenses')->with('error', 'Deactivation failed: '.$e->getMessage());
        }
    }

    /**
     * Discounts page — create, lookup, delete.
     * Methods: Creem::createDiscount(), Creem::getDiscount(), Creem::deleteDiscount()
     */
    public function discounts()
    {
        return view('discounts', [
            'result' => session('discount_result'),
            'error' => session('error'),
        ]);
    }

    /**
     * Create a discount.
     * Method: Creem::createDiscount()
     */
    public function createDiscount(Request $request)
    {
        try {
            $params = [
                'product_id' => $request->input('product_id'),
                'type' => $request->input('type', 'percentage'),
                'amount' => (int) $request->input('amount', 10),
            ];

            if ($request->input('code')) {
                $params['code'] = $request->input('code');
            }
            if ($request->input('duration')) {
                $params['duration'] = $request->input('duration');
            }
            if ($request->input('duration_in_months')) {
                $params['duration_in_months'] = (int) $request->input('duration_in_months');
            }

            $result = Creem::createDiscount($params);

            return redirect('/discounts')->with('discount_result', [
                'action' => 'create',
                'data' => $result,
            ]);
        } catch (CreemApiException $e) {
            return redirect('/discounts')->with('error', 'Create failed: '.$e->getMessage());
        }
    }

    /**
     * Lookup a discount.
     * Method: Creem::getDiscount()
     */
    public function getDiscountApi(Request $request)
    {
        try {
            $params = [];
            if ($request->query('id')) {
                $params['id'] = $request->query('id');
            } elseif ($request->query('code')) {
                $params['code'] = $request->query('code');
            }
            $result = Creem::getDiscount($params);

            return response()->json(['status' => 'ok', 'discount' => $result]);
        } catch (CreemApiException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a discount.
     * Method: Creem::deleteDiscount()
     */
    public function deleteDiscount(Request $request)
    {
        try {
            Creem::deleteDiscount($request->input('discount_id'));

            return redirect('/discounts')->with('discount_result', [
                'action' => 'delete',
                'data' => ['message' => 'Discount deleted successfully'],
            ]);
        } catch (CreemApiException $e) {
            return redirect('/discounts')->with('error', 'Delete failed: '.$e->getMessage());
        }
    }

    /**
     * Seed sample products via CREEM API.
     * Method: Creem::createProduct()
     */
    public function seedProducts()
    {
        $sampleProducts = [
            [
                'name' => 'Starter Plan',
                'description' => 'Perfect for individuals and small projects. Includes core features and email support.',
                'price' => 999,
                'currency' => 'USD',
                'billing_type' => 'recurring',
                'billing_period' => 'every-month',
            ],
            [
                'name' => 'Pro Plan',
                'description' => 'For growing teams. Unlimited projects, priority support, and advanced analytics.',
                'price' => 2999,
                'currency' => 'USD',
                'billing_type' => 'recurring',
                'billing_period' => 'every-month',
            ],
            [
                'name' => 'Enterprise Annual',
                'description' => 'Best value — annual billing with 2 months free. Everything in Pro plus custom integrations.',
                'price' => 29900,
                'currency' => 'USD',
                'billing_type' => 'recurring',
                'billing_period' => 'every-year',
            ],
            [
                'name' => 'Lifetime License',
                'description' => 'One-time purchase. Lifetime access to all current and future features. No recurring fees.',
                'price' => 9900,
                'currency' => 'USD',
                'billing_type' => 'onetime',
            ],
        ];

        $created = 0;
        $errors = [];

        foreach ($sampleProducts as $product) {
            try {
                Creem::createProduct($product);
                $created++;
            } catch (CreemApiException $e) {
                $errors[] = $product['name'].': '.$e->getMessage();
            }
        }

        if ($created > 0) {
            session()->flash('success', "Created {$created} sample product(s) via Creem::createProduct()");
        }
        if (! empty($errors)) {
            session()->flash('error', 'Some products failed: '.implode('; ', $errors));
        }

        return redirect()->route('products');
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
