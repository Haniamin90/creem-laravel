<?php

namespace App\Listeners;

use Creem\Laravel\Events\AccessRevoked;
use Illuminate\Support\Facades\Log;

class HandleAccessRevoked
{
    public function handle(AccessRevoked $event): void
    {
        Log::info("ACCESS REVOKED via {$event->reason}", $event->payload);
    }
}
