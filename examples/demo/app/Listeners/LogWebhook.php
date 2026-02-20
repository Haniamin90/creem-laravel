<?php

namespace App\Listeners;

use App\Models\WebhookLog;
use Creem\Laravel\Events\CreemWebhookReceived;
use Illuminate\Support\Facades\Log;

class LogWebhook
{
    public function handle(CreemWebhookReceived $event): void
    {
        Log::info("CREEM Webhook: {$event->eventType}", $event->payload);

        WebhookLog::create([
            'event_type' => $event->eventType,
            'payload' => $event->payload,
        ]);
    }
}
