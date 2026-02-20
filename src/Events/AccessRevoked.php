<?php

namespace Creem\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a webhook indicates the customer's access should be revoked.
 *
 * Triggered by: subscription.canceled, subscription.expired
 *
 * This mirrors the onRevokeAccess pattern from the CREEM TypeScript SDK,
 * providing a single event to listen for when revoking product access.
 */
class AccessRevoked
{
    use Dispatchable, SerializesModels;

    /**
     * @param  string  $reason  The original event type that triggered the access revocation.
     * @param  array<string, mixed>  $payload  The webhook payload object data.
     */
    public function __construct(
        public readonly string $reason,
        public readonly array $payload,
    ) {}
}
