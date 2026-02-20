<?php

namespace Creem\Laravel\Tests\Unit;

use Creem\Laravel\WebhookEventType;
use PHPUnit\Framework\TestCase;

class WebhookEventTypeTest extends TestCase
{
    public function test_all_contains_twelve_event_types(): void
    {
        $this->assertCount(12, WebhookEventType::ALL);
    }

    public function test_grant_access_events(): void
    {
        $this->assertTrue(WebhookEventType::shouldGrantAccess('checkout.completed'));
        $this->assertTrue(WebhookEventType::shouldGrantAccess('subscription.active'));
        $this->assertTrue(WebhookEventType::shouldGrantAccess('subscription.paid'));
        $this->assertFalse(WebhookEventType::shouldGrantAccess('subscription.canceled'));
        $this->assertFalse(WebhookEventType::shouldGrantAccess('unknown.event'));
    }

    public function test_revoke_access_events(): void
    {
        $this->assertTrue(WebhookEventType::shouldRevokeAccess('subscription.canceled'));
        $this->assertTrue(WebhookEventType::shouldRevokeAccess('subscription.expired'));
        $this->assertFalse(WebhookEventType::shouldRevokeAccess('checkout.completed'));
        $this->assertFalse(WebhookEventType::shouldRevokeAccess('subscription.active'));
    }

    public function test_constants_match_creem_api_values(): void
    {
        $this->assertEquals('checkout.completed', WebhookEventType::CHECKOUT_COMPLETED);
        $this->assertEquals('subscription.active', WebhookEventType::SUBSCRIPTION_ACTIVE);
        $this->assertEquals('subscription.paid', WebhookEventType::SUBSCRIPTION_PAID);
        $this->assertEquals('subscription.canceled', WebhookEventType::SUBSCRIPTION_CANCELED);
        $this->assertEquals('subscription.scheduled_cancel', WebhookEventType::SUBSCRIPTION_SCHEDULED_CANCEL);
        $this->assertEquals('subscription.past_due', WebhookEventType::SUBSCRIPTION_PAST_DUE);
        $this->assertEquals('subscription.expired', WebhookEventType::SUBSCRIPTION_EXPIRED);
        $this->assertEquals('subscription.trialing', WebhookEventType::SUBSCRIPTION_TRIALING);
        $this->assertEquals('subscription.paused', WebhookEventType::SUBSCRIPTION_PAUSED);
        $this->assertEquals('subscription.update', WebhookEventType::SUBSCRIPTION_UPDATE);
        $this->assertEquals('refund.created', WebhookEventType::REFUND_CREATED);
        $this->assertEquals('dispute.created', WebhookEventType::DISPUTE_CREATED);
    }
}
