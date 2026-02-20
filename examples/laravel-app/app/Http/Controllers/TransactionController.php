<?php

namespace App\Http\Controllers;

use Creem\Laravel\Exceptions\CreemApiException;
use Creem\Laravel\Facades\Creem;
use Illuminate\Http\Request;

/**
 * Transaction lookup and search.
 *
 * Transactions represent completed payments. Use these endpoints
 * to build order history, generate receipts, or reconcile payments.
 */
class TransactionController extends Controller
{
    /**
     * Search transactions with filters.
     *
     * Demonstrates: Creem::searchTransactions()
     *
     * Supported filters:
     *   - customer_id: Filter by CREEM customer
     *   - order_id: Filter by specific order
     *   - product_id: Filter by product
     *   - page_number / page_size: Pagination
     */
    public function index(Request $request)
    {
        try {
            $result = Creem::searchTransactions(array_filter([
                'customer_id' => $request->query('customer_id'),
                'order_id' => $request->query('order_id'),
                'product_id' => $request->query('product_id'),
                'page_number' => $request->query('page', 1),
                'page_size' => 20,
            ]));

            return view('transactions.index', [
                'transactions' => $result['items'] ?? [],
                'pagination' => $result['pagination'] ?? null,
            ]);
        } catch (CreemApiException $e) {
            return view('transactions.index', [
                'transactions' => [],
                'pagination' => null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get a single transaction by ID.
     *
     * Demonstrates: Creem::getTransaction()
     */
    public function show(string $transactionId)
    {
        try {
            $transaction = Creem::getTransaction($transactionId);

            return response()->json($transaction);
        } catch (CreemApiException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace_id' => $e->getTraceId(),
            ], 404);
        }
    }
}
