<?php

namespace App\Providers;

use App\Listeners\HandleAccessChange;
use App\Listeners\HandleCheckoutCompleted;
use App\Listeners\HandleDisputeCreated;
use App\Listeners\HandleRefundCreated;
use App\Listeners\HandleSubscriptionChange;
use App\Listeners\LogAllWebhooks;
use Creem\Laravel\Events\AccessGranted;
use Creem\Laravel\Events\AccessRevoked;
use Creem\Laravel\Events\CheckoutCompleted;
use Creem\Laravel\Events\CreemWebhookReceived;
use Creem\Laravel\Events\DisputeCreated;
use Creem\Laravel\Events\RefundCreated;
use Creem\Laravel\Events\SubscriptionActive;
use Creem\Laravel\Events\SubscriptionCanceled;
use Creem\Laravel\Events\SubscriptionExpired;
use Creem\Laravel\Events\SubscriptionPaid;
use Creem\Laravel\Events\SubscriptionPastDue;
use Creem\Laravel\Events\SubscriptionPaused;
use Creem\Laravel\Events\SubscriptionScheduledCancel;
use Creem\Laravel\Events\SubscriptionTrialing;
use Creem\Laravel\Events\SubscriptionUpdated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Event-to-listener mappings for CREEM webhook events.
 *
 * The CREEM package dispatches 15 different event types. This provider
 * maps each one to the appropriate listener(s) in your application.
 *
 * Event types:
 *   - 12 webhook-mapped events (one per CREEM event type)
 *   -  2 convenience events (AccessGranted, AccessRevoked)
 *   -  1 generic event (CreemWebhookReceived — fires for ALL webhooks)
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [

        // ── Generic: Fires for ALL webhook events ────────────────────
        // Use for logging, auditing, or debugging.
        CreemWebhookReceived::class => [
            LogAllWebhooks::class,
        ],

        // ── Checkout ─────────────────────────────────────────────────
        CheckoutCompleted::class => [
            HandleCheckoutCompleted::class,
        ],

        // ── Subscription Lifecycle (all 9 events) ────────────────────
        SubscriptionActive::class => [
            HandleSubscriptionChange::class,
        ],
        SubscriptionPaid::class => [
            HandleSubscriptionChange::class,
        ],
        SubscriptionTrialing::class => [
            HandleSubscriptionChange::class,
        ],
        SubscriptionPaused::class => [
            HandleSubscriptionChange::class,
        ],
        SubscriptionScheduledCancel::class => [
            HandleSubscriptionChange::class,
        ],
        SubscriptionCanceled::class => [
            HandleSubscriptionChange::class,
        ],
        SubscriptionExpired::class => [
            HandleSubscriptionChange::class,
        ],
        SubscriptionPastDue::class => [
            HandleSubscriptionChange::class,
        ],
        SubscriptionUpdated::class => [
            HandleSubscriptionChange::class,
        ],

        // ── Refunds & Disputes ───────────────────────────────────────
        RefundCreated::class => [
            HandleRefundCreated::class,
        ],
        DisputeCreated::class => [
            HandleDisputeCreated::class,
        ],

        // ── Convenience: Simplified Access Control ───────────────────
        // These fire IN ADDITION to the specific events above.
        // Use these if you only need grant/revoke logic.
        AccessGranted::class => [
            HandleAccessChange::class,
        ],
        AccessRevoked::class => [
            HandleAccessChange::class,
        ],
    ];
}
