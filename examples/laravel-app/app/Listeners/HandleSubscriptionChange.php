<?php

namespace App\Listeners;

use App\Models\User;
use Creem\Laravel\Events\SubscriptionActive;
use Creem\Laravel\Events\SubscriptionCanceled;
use Creem\Laravel\Events\SubscriptionExpired;
use Creem\Laravel\Events\SubscriptionPaid;
use Creem\Laravel\Events\SubscriptionPastDue;
use Creem\Laravel\Events\SubscriptionPaused;
use Creem\Laravel\Events\SubscriptionScheduledCancel;
use Creem\Laravel\Events\SubscriptionTrialing;
use Creem\Laravel\Events\SubscriptionUpdated;
use Illuminate\Support\Facades\Log;

/**
 * Handle all subscription lifecycle webhook events.
 *
 * This single listener handles 9 subscription events. Register it
 * for each event type in EventServiceProvider.
 *
 * Subscription lifecycle:
 *   trialing → active → paid (each period) → scheduled_cancel → canceled
 *                  ↕                                                ↑
 *               paused                            expired ──────────┘
 *                  ↕                                ↑
 *               active                         past_due
 *
 * All subscription payloads include:
 *   - id: Subscription ID
 *   - status: Current status
 *   - product_id: The product
 *   - customer_id: The CREEM customer
 *   - current_period_start / current_period_end: Billing period
 */
class HandleSubscriptionChange
{
    public function handle(
        SubscriptionActive|SubscriptionPaid|SubscriptionCanceled|SubscriptionExpired|SubscriptionPastDue|SubscriptionPaused|SubscriptionScheduledCancel|SubscriptionTrialing|SubscriptionUpdated $event
    ): void {
        $data = $event->payload;
        $eventClass = class_basename($event);

        Log::info("CREEM: {$eventClass}", [
            'subscription_id' => $data['id'] ?? null,
            'status' => $data['status'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
        ]);

        $user = $this->resolveUser($data);

        if (! $user) {
            Log::warning("CREEM: {$eventClass} — user not found", [
                'customer_id' => $data['customer_id'] ?? null,
            ]);

            return;
        }

        // ── Route to the appropriate handler ─────────────────────────
        match (true) {
            $event instanceof SubscriptionActive => $this->onActive($user, $data),
            $event instanceof SubscriptionPaid => $this->onPaid($user, $data),
            $event instanceof SubscriptionTrialing => $this->onTrialing($user, $data),
            $event instanceof SubscriptionPaused => $this->onPaused($user, $data),
            $event instanceof SubscriptionScheduledCancel => $this->onScheduledCancel($user, $data),
            $event instanceof SubscriptionCanceled => $this->onCanceled($user, $data),
            $event instanceof SubscriptionExpired => $this->onExpired($user, $data),
            $event instanceof SubscriptionPastDue => $this->onPastDue($user, $data),
            $event instanceof SubscriptionUpdated => $this->onUpdated($user, $data),
        };
    }

    // ── Event Handlers ───────────────────────────────────────────────

    private function onActive(User $user, array $data): void
    {
        // Subscription is active and paid. Grant/maintain access.
        // $user->update(['plan' => 'premium', 'plan_expires_at' => $data['current_period_end']]);
    }

    private function onPaid(User $user, array $data): void
    {
        // A recurring payment was successfully collected.
        // Extend access period, send receipt.
        // $user->update(['plan_expires_at' => $data['current_period_end']]);
    }

    private function onTrialing(User $user, array $data): void
    {
        // User started a trial. Grant access with trial flag.
        // $user->update(['plan' => 'premium', 'is_trial' => true]);
    }

    private function onPaused(User $user, array $data): void
    {
        // Subscription paused by user or admin. Optionally limit access.
        // $user->update(['plan_status' => 'paused']);
    }

    private function onScheduledCancel(User $user, array $data): void
    {
        // User requested cancellation but still has access until period ends.
        // Show "canceling" status in UI. Don't revoke yet.
        // $user->update(['plan_status' => 'canceling']);
    }

    private function onCanceled(User $user, array $data): void
    {
        // Subscription fully canceled. Revoke access.
        // $user->update(['plan' => 'free', 'plan_status' => 'canceled']);
    }

    private function onExpired(User $user, array $data): void
    {
        // Subscription expired (e.g., after failed payments). Revoke access.
        // $user->update(['plan' => 'free', 'plan_status' => 'expired']);
    }

    private function onPastDue(User $user, array $data): void
    {
        // Payment failed. Consider grace period before revoking access.
        // $user->update(['plan_status' => 'past_due']);
        // Mail::to($user)->send(new PaymentFailedNotification());
    }

    private function onUpdated(User $user, array $data): void
    {
        // Subscription was modified (units, seats, add-ons).
        // Sync local records with updated subscription data.
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function resolveUser(array $data): ?User
    {
        $customerId = $data['customer_id'] ?? null;

        if ($customerId) {
            return User::where('creem_customer_id', $customerId)->first();
        }

        return null;
    }
}
