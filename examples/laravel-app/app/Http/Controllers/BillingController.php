<?php

namespace App\Http\Controllers;

use Creem\Laravel\Exceptions\CreemApiException;
use Creem\Laravel\Facades\Creem;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    /**
     * Show the billing dashboard with subscriptions and transactions.
     *
     * Demonstrates: $user->creemSubscriptions(), $user->creemTransactions(),
     *               $user->hasCreemCustomerId(), $user->creemCustomer()
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $subscriptions = [];
        $transactions = [];
        $customer = null;
        $error = null;

        try {
            if ($user->hasCreemCustomerId()) {
                // Fetch customer profile from CREEM
                $customer = $user->creemCustomer();

                // Fetch active subscriptions via the Billable trait
                $subscriptionsResponse = $user->creemSubscriptions();
                $subscriptions = $subscriptionsResponse['items'] ?? [];

                // Fetch recent transactions with pagination
                $transactionsResponse = $user->creemTransactions([
                    'page_size' => 10,
                    'page_number' => $request->query('page', 1),
                ]);
                $transactions = $transactionsResponse['items'] ?? [];
            }
        } catch (CreemApiException $e) {
            $error = "Failed to load billing data: {$e->getMessage()}";
        }

        return view('billing.index', [
            'user' => $user,
            'subscriptions' => $subscriptions,
            'transactions' => $transactions,
            'customer' => $customer,
            'error' => $error,
        ]);
    }

    /**
     * Redirect to the CREEM-hosted billing portal.
     *
     * The billing portal lets customers manage their own subscriptions,
     * update payment methods, and view invoices.
     *
     * Demonstrates: $user->billingPortalUrl()
     */
    public function portal(Request $request)
    {
        $user = $request->user();

        try {
            $portal = $user->billingPortalUrl();

            return redirect($portal['url']);
        } catch (\RuntimeException $e) {
            // User has no creem_customer_id yet
            return back()->with('error', 'Complete a purchase first to access the billing portal.');
        } catch (CreemApiException $e) {
            return back()->with('error', 'Unable to open billing portal. Please try again.');
        }
    }

    /**
     * Cancel a subscription (scheduled end of billing period).
     *
     * Demonstrates: $user->cancelSubscription(), Creem::cancelSubscription()
     */
    public function cancelSubscription(Request $request, string $subscriptionId)
    {
        $user = $request->user();
        $mode = $request->input('mode', 'scheduled'); // 'scheduled' or 'immediate'

        try {
            // Using the Billable trait — delegates to Creem::cancelSubscription()
            $result = $user->cancelSubscription($subscriptionId, $mode);

            $message = $mode === 'immediate'
                ? 'Subscription canceled immediately.'
                : 'Subscription will cancel at the end of the billing period.';

            return back()->with('success', $message);
        } catch (CreemApiException $e) {
            return back()->with('error', "Unable to cancel subscription: {$e->getMessage()}");
        }
    }

    /**
     * Pause a subscription.
     *
     * Demonstrates: $user->pauseSubscription(), Creem::pauseSubscription()
     */
    public function pauseSubscription(Request $request, string $subscriptionId)
    {
        try {
            // Using Facade directly — either approach works
            Creem::pauseSubscription($subscriptionId);

            return back()->with('success', 'Subscription paused successfully.');
        } catch (CreemApiException $e) {
            return back()->with('error', "Unable to pause subscription: {$e->getMessage()}");
        }
    }

    /**
     * Resume a paused subscription.
     *
     * Demonstrates: $user->resumeSubscription(), Creem::resumeSubscription()
     */
    public function resumeSubscription(Request $request, string $subscriptionId)
    {
        try {
            $request->user()->resumeSubscription($subscriptionId);

            return back()->with('success', 'Subscription resumed successfully.');
        } catch (CreemApiException $e) {
            return back()->with('error', "Unable to resume subscription: {$e->getMessage()}");
        }
    }

    /**
     * Upgrade a subscription to a different product/plan.
     *
     * Demonstrates: Creem::upgradeSubscription()
     */
    public function upgradeSubscription(Request $request, string $subscriptionId)
    {
        $request->validate([
            'product_id' => 'required|string',
        ]);

        try {
            $result = Creem::upgradeSubscription($subscriptionId, $request->product_id);

            return back()->with('success', 'Subscription upgraded successfully.');
        } catch (CreemApiException $e) {
            return back()->with('error', "Unable to upgrade subscription: {$e->getMessage()}");
        }
    }

    /**
     * Update subscription (change units, seats, or add-ons).
     *
     * Demonstrates: Creem::updateSubscription()
     */
    public function updateSubscription(Request $request, string $subscriptionId)
    {
        $request->validate([
            'units' => 'nullable|integer|min:1',
        ]);

        try {
            $result = Creem::updateSubscription($subscriptionId, $request->only([
                'units',
            ]));

            return back()->with('success', 'Subscription updated successfully.');
        } catch (CreemApiException $e) {
            return back()->with('error', "Unable to update subscription: {$e->getMessage()}");
        }
    }

    /**
     * Look up a specific subscription by ID.
     *
     * Demonstrates: Creem::getSubscription()
     */
    public function showSubscription(string $subscriptionId)
    {
        try {
            $subscription = Creem::getSubscription($subscriptionId);

            return view('billing.subscription', [
                'subscription' => $subscription,
            ]);
        } catch (CreemApiException $e) {
            return back()->with('error', "Subscription not found: {$e->getMessage()}");
        }
    }

    /**
     * Search subscriptions with filters.
     *
     * Demonstrates: Creem::searchSubscriptions()
     */
    public function searchSubscriptions(Request $request)
    {
        try {
            $result = Creem::searchSubscriptions(array_filter([
                'customer_id' => $request->query('customer_id'),
                'product_id' => $request->query('product_id'),
                'status' => $request->query('status'),
                'page_number' => $request->query('page', 1),
                'page_size' => 20,
            ]));

            return view('billing.subscriptions', [
                'subscriptions' => $result['items'] ?? [],
                'pagination' => $result['pagination'] ?? null,
            ]);
        } catch (CreemApiException $e) {
            return back()->with('error', "Search failed: {$e->getMessage()}");
        }
    }
}
