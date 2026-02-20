<?php

namespace Creem\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionPaused
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload  The webhook payload object data.
     */
    public function __construct(
        public readonly array $payload,
    ) {}
}
