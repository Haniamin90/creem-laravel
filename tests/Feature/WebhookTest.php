<?php

namespace Creem\Laravel\Tests\Feature;

use Creem\Laravel\Events\AccessGranted;
use Creem\Laravel\Events\AccessRevoked;
use Creem\Laravel\Events\CheckoutCompleted;
use Creem\Laravel\Events\CreemWebhookReceived;
use Creem\Laravel\Events\RefundCreated;
use Creem\Laravel\Events\SubscriptionActive;
use Creem\Laravel\Events\SubscriptionCanceled;
use Creem\Laravel\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class WebhookTest extends TestCase
{
    protected function sendWebhook(string $eventType, array $objectData = []): \Illuminate\Testing\TestResponse
    {
        $payload = json_encode([
            'eventType' => $eventType,
            'object' => $objectData,
            'timestamp' => now()->toISOString(),
        ]);

        $secret = config('creem.webhook_secret');
        $signature = hash_hmac('sha256', $payload, $secret);

        return $this->postJson(
            route('creem.webhook'),
            json_decode($payload, true),
            [
                'creem-signature' => $signature,
                'Content-Type' => 'application/json',
            ]
        );
    }

    public function test_webhook_accepts_valid_signature(): void
    {
        Event::fake();

        $response = $this->sendWebhook('checkout.completed', [
            'id' => 'txn_123',
            'customer' => ['email' => 'test@example.com'],
        ]);

        $response->assertOk();
        $response->assertJson(['received' => true]);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $payload = json_encode([
            'eventType' => 'checkout.completed',
            'object' => [],
        ]);

        $response = $this->postJson(
            route('creem.webhook'),
            json_decode($payload, true),
            [
                'creem-signature' => 'invalid_signature',
                'Content-Type' => 'application/json',
            ]
        );

        $response->assertStatus(403);
    }

    public function test_webhook_rejects_missing_signature(): void
    {
        $response = $this->postJson(
            route('creem.webhook'),
            ['eventType' => 'checkout.completed', 'object' => []]
        );

        $response->assertStatus(403);
    }

    public function test_checkout_completed_event_is_dispatched(): void
    {
        Event::fake([CheckoutCompleted::class, CreemWebhookReceived::class]);

        $this->sendWebhook('checkout.completed', [
            'id' => 'txn_123',
            'product_id' => 'prod_123',
        ]);

        Event::assertDispatched(CheckoutCompleted::class, function ($event) {
            return $event->payload['id'] === 'txn_123';
        });

        Event::assertDispatched(CreemWebhookReceived::class, function ($event) {
            return $event->eventType === 'checkout.completed';
        });
    }

    public function test_subscription_active_event_is_dispatched(): void
    {
        Event::fake([SubscriptionActive::class, CreemWebhookReceived::class]);

        $this->sendWebhook('subscription.active', [
            'id' => 'sub_123',
            'status' => 'active',
        ]);

        Event::assertDispatched(SubscriptionActive::class, function ($event) {
            return $event->payload['id'] === 'sub_123';
        });
    }

    public function test_subscription_canceled_event_is_dispatched(): void
    {
        Event::fake([SubscriptionCanceled::class, CreemWebhookReceived::class]);

        $this->sendWebhook('subscription.canceled', [
            'id' => 'sub_456',
            'status' => 'canceled',
        ]);

        Event::assertDispatched(SubscriptionCanceled::class);
    }

    public function test_refund_created_event_is_dispatched(): void
    {
        Event::fake([RefundCreated::class, CreemWebhookReceived::class]);

        $this->sendWebhook('refund.created', [
            'id' => 'ref_123',
            'amount' => 2999,
        ]);

        Event::assertDispatched(RefundCreated::class);
    }

    public function test_generic_webhook_event_always_dispatched(): void
    {
        Event::fake([CreemWebhookReceived::class]);

        $this->sendWebhook('some.unknown.event', ['id' => 'test']);

        Event::assertDispatched(CreemWebhookReceived::class, function ($event) {
            return $event->eventType === 'some.unknown.event';
        });
    }

    public function test_access_granted_event_on_checkout_completed(): void
    {
        Event::fake([AccessGranted::class, CreemWebhookReceived::class, CheckoutCompleted::class]);

        $this->sendWebhook('checkout.completed', [
            'id' => 'txn_123',
            'customer' => ['email' => 'test@example.com'],
        ]);

        Event::assertDispatched(AccessGranted::class, function ($event) {
            return $event->reason === 'checkout.completed'
                && $event->payload['id'] === 'txn_123';
        });
    }

    public function test_access_granted_event_on_subscription_paid(): void
    {
        Event::fake([AccessGranted::class, CreemWebhookReceived::class]);

        $this->sendWebhook('subscription.paid', ['id' => 'sub_123']);

        Event::assertDispatched(AccessGranted::class, function ($event) {
            return $event->reason === 'subscription.paid';
        });
    }

    public function test_access_revoked_event_on_subscription_canceled(): void
    {
        Event::fake([AccessRevoked::class, CreemWebhookReceived::class, SubscriptionCanceled::class]);

        $this->sendWebhook('subscription.canceled', ['id' => 'sub_456']);

        Event::assertDispatched(AccessRevoked::class, function ($event) {
            return $event->reason === 'subscription.canceled';
        });
    }

    public function test_access_revoked_event_on_subscription_expired(): void
    {
        Event::fake([AccessRevoked::class, CreemWebhookReceived::class]);

        $this->sendWebhook('subscription.expired', ['id' => 'sub_789']);

        Event::assertDispatched(AccessRevoked::class, function ($event) {
            return $event->reason === 'subscription.expired';
        });
    }

    public function test_no_access_event_on_refund(): void
    {
        Event::fake([AccessGranted::class, AccessRevoked::class, CreemWebhookReceived::class, RefundCreated::class]);

        $this->sendWebhook('refund.created', ['id' => 'ref_123']);

        Event::assertNotDispatched(AccessGranted::class);
        Event::assertNotDispatched(AccessRevoked::class);
    }

    public function test_webhook_returns_400_without_event_type(): void
    {
        $payload = json_encode(['object' => []]);
        $secret = config('creem.webhook_secret');
        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->postJson(
            route('creem.webhook'),
            json_decode($payload, true),
            ['creem-signature' => $signature]
        );

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Missing eventType']);
    }
}
