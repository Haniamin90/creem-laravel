<?php

namespace App\Listeners;

use App\Models\User;
use Creem\Laravel\Events\AccessGranted;
use Illuminate\Support\Facades\Log;

class HandleAccessGranted
{
    public function handle(AccessGranted $event): void
    {
        Log::info("ACCESS GRANTED via {$event->reason}", $event->payload);

        // Try to link CREEM customer to our user
        $customerEmail = $event->payload['customer']['email'] ?? null;
        $customerId = $event->payload['customer']['id'] ?? null;

        if ($customerEmail) {
            $user = User::where('email', $customerEmail)->first();
            if ($user && $customerId && ! $user->hasCreemCustomerId()) {
                $user->setCreemCustomerId($customerId);
                Log::info("Linked CREEM customer {$customerId} to user {$user->id}");
            }
        }
    }
}
