<?php

namespace Creem\Laravel;

/**
 * Constants for all CREEM webhook event types.
 */
final class WebhookEventType
{
    public const CHECKOUT_COMPLETED = 'checkout.completed';

    public const SUBSCRIPTION_ACTIVE = 'subscription.active';

    public const SUBSCRIPTION_PAID = 'subscription.paid';

    public const SUBSCRIPTION_CANCELED = 'subscription.canceled';

    public const SUBSCRIPTION_SCHEDULED_CANCEL = 'subscription.scheduled_cancel';

    public const SUBSCRIPTION_PAST_DUE = 'subscription.past_due';

    public const SUBSCRIPTION_EXPIRED = 'subscription.expired';

    public const SUBSCRIPTION_TRIALING = 'subscription.trialing';

    public const SUBSCRIPTION_PAUSED = 'subscription.paused';

    public const SUBSCRIPTION_UPDATE = 'subscription.update';

    public const REFUND_CREATED = 'refund.created';

    public const DISPUTE_CREATED = 'dispute.created';

    /**
     * Events that indicate the user should be granted access.
     *
     * @var string[]
     */
    public const GRANT_ACCESS = [
        self::CHECKOUT_COMPLETED,
        self::SUBSCRIPTION_ACTIVE,
        self::SUBSCRIPTION_PAID,
    ];

    /**
     * Events that indicate the user's access should be revoked.
     *
     * @var string[]
     */
    public const REVOKE_ACCESS = [
        self::SUBSCRIPTION_CANCELED,
        self::SUBSCRIPTION_EXPIRED,
    ];

    /**
     * All supported event types.
     *
     * @var string[]
     */
    public const ALL = [
        self::CHECKOUT_COMPLETED,
        self::SUBSCRIPTION_ACTIVE,
        self::SUBSCRIPTION_PAID,
        self::SUBSCRIPTION_CANCELED,
        self::SUBSCRIPTION_SCHEDULED_CANCEL,
        self::SUBSCRIPTION_PAST_DUE,
        self::SUBSCRIPTION_EXPIRED,
        self::SUBSCRIPTION_TRIALING,
        self::SUBSCRIPTION_PAUSED,
        self::SUBSCRIPTION_UPDATE,
        self::REFUND_CREATED,
        self::DISPUTE_CREATED,
    ];

    /**
     * Determine if the event type should grant access.
     */
    public static function shouldGrantAccess(string $eventType): bool
    {
        return in_array($eventType, self::GRANT_ACCESS, true);
    }

    /**
     * Determine if the event type should revoke access.
     */
    public static function shouldRevokeAccess(string $eventType): bool
    {
        return in_array($eventType, self::REVOKE_ACCESS, true);
    }
}
