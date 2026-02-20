<?php

namespace Creem\Laravel\Tests\Unit;

use Creem\Laravel\Events\CheckoutCompleted;
use Creem\Laravel\Events\DisputeCreated;
use Creem\Laravel\Events\RefundCreated;
use Creem\Laravel\Events\SubscriptionActive;
use Creem\Laravel\Events\SubscriptionCanceled;
use Creem\Laravel\Events\SubscriptionExpired;
use Creem\Laravel\Events\SubscriptionPaid;
use Creem\Laravel\Events\SubscriptionPastDue;
use Creem\Laravel\Events\SubscriptionPaused;
use Creem\Laravel\Events\SubscriptionScheduledCancel;
use Creem\Laravel\Events\SubscriptionTrialing;
use Creem\Laravel\Events\SubscriptionUpdated;
use Creem\Laravel\Http\Controllers\WebhookController;
use PHPUnit\Framework\TestCase;

class WebhookControllerTest extends TestCase
{
    public function test_event_map_contains_all_creem_events(): void
    {
        $eventMap = WebhookController::getEventMap();

        $this->assertArrayHasKey('checkout.completed', $eventMap);
        $this->assertArrayHasKey('subscription.active', $eventMap);
        $this->assertArrayHasKey('subscription.paid', $eventMap);
        $this->assertArrayHasKey('subscription.canceled', $eventMap);
        $this->assertArrayHasKey('subscription.scheduled_cancel', $eventMap);
        $this->assertArrayHasKey('subscription.past_due', $eventMap);
        $this->assertArrayHasKey('subscription.expired', $eventMap);
        $this->assertArrayHasKey('subscription.trialing', $eventMap);
        $this->assertArrayHasKey('subscription.paused', $eventMap);
        $this->assertArrayHasKey('subscription.update', $eventMap);
        $this->assertArrayHasKey('refund.created', $eventMap);
        $this->assertArrayHasKey('dispute.created', $eventMap);
    }

    public function test_event_map_points_to_correct_classes(): void
    {
        $eventMap = WebhookController::getEventMap();

        $this->assertEquals(CheckoutCompleted::class, $eventMap['checkout.completed']);
        $this->assertEquals(SubscriptionActive::class, $eventMap['subscription.active']);
        $this->assertEquals(SubscriptionPaid::class, $eventMap['subscription.paid']);
        $this->assertEquals(SubscriptionCanceled::class, $eventMap['subscription.canceled']);
        $this->assertEquals(SubscriptionScheduledCancel::class, $eventMap['subscription.scheduled_cancel']);
        $this->assertEquals(SubscriptionPastDue::class, $eventMap['subscription.past_due']);
        $this->assertEquals(SubscriptionExpired::class, $eventMap['subscription.expired']);
        $this->assertEquals(SubscriptionTrialing::class, $eventMap['subscription.trialing']);
        $this->assertEquals(SubscriptionPaused::class, $eventMap['subscription.paused']);
        $this->assertEquals(SubscriptionUpdated::class, $eventMap['subscription.update']);
        $this->assertEquals(RefundCreated::class, $eventMap['refund.created']);
        $this->assertEquals(DisputeCreated::class, $eventMap['dispute.created']);
    }

    public function test_event_map_has_twelve_entries(): void
    {
        $this->assertCount(12, WebhookController::getEventMap());
    }
}
