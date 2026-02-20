<?php

namespace App\Listeners;

use App\Models\User;
use Creem\Laravel\Events\DisputeCreated;
use Illuminate\Support\Facades\Log;

/**
 * Handle the dispute.created webhook event.
 *
 * Fires when a customer initiates a chargeback/dispute.
 * This is critical — you should:
 *   - Flag the account for review
 *   - Collect evidence for dispute response
 *   - Optionally restrict access during investigation
 *   - Notify your support team
 *
 * Payload includes:
 *   - id: Dispute ID
 *   - transaction_id: The disputed transaction
 *   - amount: Disputed amount
 *   - reason: Dispute reason
 *   - customer_id: The customer
 */
class HandleDisputeCreated
{
    public function handle(DisputeCreated $event): void
    {
        $data = $event->payload;

        Log::warning('CREEM: Dispute created — requires attention', [
            'dispute_id' => $data['id'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? null,
            'amount' => $data['amount'] ?? null,
            'reason' => $data['reason'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
        ]);

        $customerId = $data['customer_id'] ?? null;
        $user = $customerId
            ? User::where('creem_customer_id', $customerId)->first()
            : null;

        if ($user) {
            // Flag the account for manual review
            // $user->update(['account_status' => 'under_review']);

            // Notify your team
            // Notification::route('slack', config('services.slack.disputes_channel'))
            //     ->notify(new DisputeReceived($data, $user));
        }
    }
}
