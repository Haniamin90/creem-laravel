<?php

namespace App\Listeners;

use App\Models\User;
use Creem\Laravel\Events\RefundCreated;
use Illuminate\Support\Facades\Log;

/**
 * Handle the refund.created webhook event.
 *
 * Fires when a refund is issued for a transaction. You should:
 *   - Revoke access if it was a one-time purchase
 *   - Update internal order records
 *   - Notify the customer
 *
 * Payload includes:
 *   - id: Refund ID
 *   - transaction_id: The original transaction
 *   - amount: Refund amount in cents
 *   - reason: Refund reason
 *   - customer_id: The customer
 */
class HandleRefundCreated
{
    public function handle(RefundCreated $event): void
    {
        $data = $event->payload;

        Log::info('CREEM: Refund created', [
            'refund_id' => $data['id'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? null,
            'amount' => $data['amount'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
        ]);

        // Find the affected user
        $customerId = $data['customer_id'] ?? null;
        $user = $customerId
            ? User::where('creem_customer_id', $customerId)->first()
            : null;

        if ($user) {
            // Revoke access or downgrade for one-time purchases
            // For subscriptions, the subscription events handle access
            //
            // $order = Order::where('creem_transaction_id', $data['transaction_id'])->first();
            // if ($order && $order->type === 'one_time') {
            //     $user->update(['plan' => 'free']);
            // }
        }
    }
}
