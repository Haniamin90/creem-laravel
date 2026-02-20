<?php

namespace Creem\Laravel\Http\Controllers;

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
use Creem\Laravel\WebhookEventType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WebhookController extends Controller
{
    /**
     * Map of CREEM event types to Laravel event classes.
     *
     * @var array<string, class-string>
     */
    protected static array $eventMap = [
        'checkout.completed' => CheckoutCompleted::class,
        'subscription.active' => SubscriptionActive::class,
        'subscription.paid' => SubscriptionPaid::class,
        'subscription.canceled' => SubscriptionCanceled::class,
        'subscription.scheduled_cancel' => SubscriptionScheduledCancel::class,
        'subscription.past_due' => SubscriptionPastDue::class,
        'subscription.expired' => SubscriptionExpired::class,
        'subscription.trialing' => SubscriptionTrialing::class,
        'subscription.paused' => SubscriptionPaused::class,
        'subscription.update' => SubscriptionUpdated::class,
        'refund.created' => RefundCreated::class,
        'dispute.created' => DisputeCreated::class,
    ];

    /**
     * Handle an incoming CREEM webhook.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $eventType = $payload['eventType'] ?? null;

        if (! $eventType) {
            return response()->json(['error' => 'Missing eventType'], 400);
        }

        // Dispatch the generic webhook event
        CreemWebhookReceived::dispatch($eventType, $payload);

        // Dispatch the specific event if mapped
        $objectData = $payload['object'] ?? $payload;

        if (isset(static::$eventMap[$eventType])) {
            $eventClass = static::$eventMap[$eventType];
            $eventClass::dispatch($objectData);
        }

        // Dispatch convenience access events (mirrors TypeScript SDK onGrantAccess/onRevokeAccess)
        if (WebhookEventType::shouldGrantAccess($eventType)) {
            AccessGranted::dispatch($eventType, $objectData);
        } elseif (WebhookEventType::shouldRevokeAccess($eventType)) {
            AccessRevoked::dispatch($eventType, $objectData);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Get the event map for testing or customization.
     *
     * @return array<string, class-string>
     */
    public static function getEventMap(): array
    {
        return static::$eventMap;
    }
}
