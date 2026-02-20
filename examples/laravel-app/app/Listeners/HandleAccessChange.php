<?php

namespace App\Listeners;

use App\Models\User;
use Creem\Laravel\Events\AccessGranted;
use Creem\Laravel\Events\AccessRevoked;
use Illuminate\Support\Facades\Log;

/**
 * Handle the convenience AccessGranted and AccessRevoked events.
 *
 * These events simplify access control by abstracting the specific
 * webhook events into two clear actions: grant or revoke.
 *
 * AccessGranted fires on:
 *   - checkout.completed
 *   - subscription.active
 *   - subscription.paid
 *
 * AccessRevoked fires on:
 *   - subscription.canceled
 *   - subscription.expired
 *
 * This mirrors the TypeScript SDK's onGrantAccess/onRevokeAccess pattern.
 *
 * TIP: If you only need simple grant/revoke logic, use these events
 * instead of listening to each individual webhook event type.
 */
class HandleAccessChange
{
    public function handle(AccessGranted|AccessRevoked $event): void
    {
        $data = $event->payload;
        $reason = $event->reason; // The original event type, e.g., 'checkout.completed'

        Log::info('CREEM: Access change', [
            'action' => $event instanceof AccessGranted ? 'granted' : 'revoked',
            'reason' => $reason,
            'customer_id' => $data['customer_id'] ?? $data['customer']['id'] ?? null,
        ]);

        $user = $this->resolveUser($data);

        if (! $user) {
            return;
        }

        if ($event instanceof AccessGranted) {
            $this->grantAccess($user, $data, $reason);
        } else {
            $this->revokeAccess($user, $data, $reason);
        }
    }

    /**
     * Grant access to paid features.
     *
     * Called when a checkout completes or a subscription is active/paid.
     */
    private function grantAccess(User $user, array $data, string $reason): void
    {
        // Link CREEM customer if not already linked
        $customerId = $data['customer_id'] ?? $data['customer']['id'] ?? null;
        if ($customerId && ! $user->hasCreemCustomerId()) {
            $user->setCreemCustomerId($customerId);
        }

        // Example: Grant premium access
        // $user->update(['plan' => 'premium']);

        Log::info("CREEM: Access granted to user {$user->id} (reason: {$reason})");
    }

    /**
     * Revoke access to paid features.
     *
     * Called when a subscription is canceled or expires.
     */
    private function revokeAccess(User $user, array $data, string $reason): void
    {
        // Example: Downgrade to free plan
        // $user->update(['plan' => 'free']);

        Log::info("CREEM: Access revoked for user {$user->id} (reason: {$reason})");
    }

    private function resolveUser(array $data): ?User
    {
        // Try customer_id (subscription events)
        $customerId = $data['customer_id'] ?? null;
        if ($customerId) {
            $user = User::where('creem_customer_id', $customerId)->first();
            if ($user) {
                return $user;
            }
        }

        // Try customer object (checkout events)
        $customerObj = $data['customer'] ?? null;
        if ($customerObj) {
            if (isset($customerObj['id'])) {
                $user = User::where('creem_customer_id', $customerObj['id'])->first();
                if ($user) {
                    return $user;
                }
            }
            if (isset($customerObj['email'])) {
                return User::where('email', $customerObj['email'])->first();
            }
        }

        // Try metadata
        $metadata = $data['metadata'] ?? [];
        if (isset($metadata['model_id'])) {
            return User::find($metadata['model_id']);
        }

        return null;
    }
}
