<?php

namespace App\Providers;

use App\Listeners\HandleAccessGranted;
use App\Listeners\HandleAccessRevoked;
use App\Listeners\LogWebhook;
use Creem\Laravel\Events\AccessGranted;
use Creem\Laravel\Events\AccessRevoked;
use Creem\Laravel\Events\CreemWebhookReceived;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(CreemWebhookReceived::class, LogWebhook::class);
        Event::listen(AccessGranted::class, HandleAccessGranted::class);
        Event::listen(AccessRevoked::class, HandleAccessRevoked::class);
    }
}
