<?php

namespace App\Listeners;

use App\Models\User;
use Creem\Laravel\Events\CheckoutCompleted;
use Illuminate\Support\Facades\Log;

/**
 * Handle the checkout.completed webhook event.
 *
 * This fires when a customer successfully completes a payment.
 * It's the primary place to:
 *   - Link the CREEM customer to your local user
 *   - Grant access to purchased features
 *   - Send confirmation emails
 *   - Create internal order records
 *
 * Webhook payload includes:
 *   - id: Checkout session ID
 *   - product: { id, name, ... }
 *   - customer: { id, email, ... }
 *   - order: { id, ... }
 *   - subscription: { id, ... } (if recurring)
 *   - metadata: Your custom data from checkout creation
 */
class HandleCheckoutCompleted
{
    public function handle(CheckoutCompleted $event): void
    {
        $data = $event->payload;

        Log::info('CREEM: Checkout completed', [
            'checkout_id' => $data['id'] ?? null,
            'product_id' => $data['product']['id'] ?? null,
            'customer_email' => $data['customer']['email'] ?? null,
        ]);

        // ── 1. Find the local user ──────────────────────────────────
        // The metadata was set during checkout creation. Fall back to email.
        $user = $this->resolveUser($data);

        if (! $user) {
            Log::warning('CREEM: Checkout completed but user not found', [
                'email' => $data['customer']['email'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ]);

            return;
        }

        // ── 2. Store the CREEM customer ID ──────────────────────────
        // This enables the Billable trait methods: creemSubscriptions(),
        // creemTransactions(), billingPortalUrl(), etc.
        $customerId = $data['customer']['id'] ?? null;
        if ($customerId && ! $user->hasCreemCustomerId()) {
            $user->setCreemCustomerId($customerId);
            Log::info('CREEM: Linked customer to user', [
                'user_id' => $user->id,
                'creem_customer_id' => $customerId,
            ]);
        }

        // ── 3. Grant access based on product ────────────────────────
        // Implement your business logic here. Examples:
        //
        // $user->update(['plan' => 'premium']);
        // $user->givePermissionTo('premium-features');
        // $user->subscription()->create([...]);
        // Mail::to($user)->send(new PurchaseConfirmation($data));
    }

    /**
     * Resolve the local user from webhook payload.
     *
     * Strategy: metadata.model_id → metadata.user_id → customer email
     */
    private function resolveUser(array $data): ?User
    {
        $metadata = $data['metadata'] ?? [];

        // The Billable trait sets model_type and model_id automatically
        if (isset($metadata['model_id'])) {
            $modelClass = $metadata['model_type'] ?? User::class;
            if ($user = $modelClass::find($metadata['model_id'])) {
                return $user;
            }
        }

        // Legacy: check for user_id in metadata
        if (isset($metadata['user_id'])) {
            if ($user = User::find($metadata['user_id'])) {
                return $user;
            }
        }

        // Fallback: match by email
        $email = $data['customer']['email'] ?? null;
        if ($email) {
            return User::where('email', $email)->first();
        }

        return null;
    }
}
