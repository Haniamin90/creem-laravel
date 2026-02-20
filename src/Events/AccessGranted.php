<?php

namespace Creem\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a webhook indicates the customer should be granted access.
 *
 * Triggered by: checkout.completed, subscription.active, subscription.paid
 *
 * This mirrors the onGrantAccess pattern from the CREEM TypeScript SDK,
 * providing a single event to listen for when granting product access.
 */
class AccessGranted
{
    use Dispatchable, SerializesModels;

    /**
     * @param  string  $reason  The original event type that triggered the access grant.
     * @param  array<string, mixed>  $payload  The webhook payload object data.
     */
    public function __construct(
        public readonly string $reason,
        public readonly array $payload,
    ) {}
}
