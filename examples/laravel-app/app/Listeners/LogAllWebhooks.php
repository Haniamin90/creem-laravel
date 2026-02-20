<?php

namespace App\Listeners;

use Creem\Laravel\Events\CreemWebhookReceived;
use Illuminate\Support\Facades\Log;

/**
 * Log all incoming CREEM webhook events.
 *
 * Listens to CreemWebhookReceived, which fires for EVERY webhook
 * regardless of event type. Use this for:
 *   - Debugging during development
 *   - Audit logging in production
 *   - Storing webhook history in the database
 *
 * TIP: This event fires in addition to the specific event.
 * For example, a checkout.completed webhook dispatches both:
 *   1. CreemWebhookReceived (with eventType = 'checkout.completed')
 *   2. CheckoutCompleted (with the same payload)
 */
class LogAllWebhooks
{
    public function handle(CreemWebhookReceived $event): void
    {
        Log::info("CREEM Webhook: {$event->eventType}", [
            'event_type' => $event->eventType,
            'payload_keys' => array_keys($event->payload),
        ]);

        // Optional: Store in database for a webhook log dashboard
        //
        // \App\Models\WebhookLog::create([
        //     'event_type' => $event->eventType,
        //     'payload' => $event->payload,
        //     'processed_at' => now(),
        // ]);
    }
}
