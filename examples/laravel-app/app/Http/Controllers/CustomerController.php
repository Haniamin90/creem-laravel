<?php

namespace App\Http\Controllers;

use Creem\Laravel\Exceptions\CreemApiException;
use Creem\Laravel\Facades\Creem;
use Illuminate\Http\Request;

/**
 * Customer lookup and management.
 *
 * Customers are automatically created in CREEM when they complete a checkout.
 * Use these endpoints to look up customer data, list customers,
 * and generate billing portal links.
 */
class CustomerController extends Controller
{
    /**
     * List all customers with pagination.
     *
     * Demonstrates: Creem::listCustomers()
     */
    public function index(Request $request)
    {
        try {
            $result = Creem::listCustomers([
                'page_size' => 20,
                'page_number' => $request->query('page', 1),
            ]);

            return view('customers.index', [
                'customers' => $result['items'] ?? [],
                'pagination' => $result['pagination'] ?? null,
            ]);
        } catch (CreemApiException $e) {
            return view('customers.index', [
                'customers' => [],
                'pagination' => null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get a customer by ID or email.
     *
     * Demonstrates: Creem::getCustomer()
     *
     * The getCustomer() method accepts either:
     *   - ['customer_id' => 'cus_...'] — Look up by CREEM customer ID
     *   - ['email' => 'user@example.com'] — Look up by email address
     */
    public function show(Request $request)
    {
        $request->validate([
            'customer_id' => 'required_without:email|nullable|string',
            'email' => 'required_without:customer_id|nullable|email',
        ]);

        try {
            $customer = Creem::getCustomer(array_filter([
                'customer_id' => $request->customer_id,
                'email' => $request->email,
            ]));

            return response()->json($customer);
        } catch (CreemApiException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace_id' => $e->getTraceId(),
            ], 404);
        }
    }

    /**
     * Redirect to the billing portal for a specific customer.
     *
     * Demonstrates: Creem::customerBillingPortal()
     */
    public function billingPortal(string $customerId)
    {
        try {
            $portal = Creem::customerBillingPortal($customerId);

            return redirect($portal['url']);
        } catch (CreemApiException $e) {
            return back()->with('error', "Unable to open billing portal: {$e->getMessage()}");
        }
    }
}
