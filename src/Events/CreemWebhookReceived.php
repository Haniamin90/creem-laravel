<?php

namespace Creem\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreemWebhookReceived
{
    use Dispatchable, SerializesModels;

    /**
     * @param  string  $eventType  The webhook event type (e.g., 'checkout.completed').
     * @param  array<string, mixed>  $payload  The full webhook payload.
     */
    public function __construct(
        public readonly string $eventType,
        public readonly array $payload,
    ) {}
}
