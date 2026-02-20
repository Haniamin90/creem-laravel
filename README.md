# CREEM for Laravel

The official Laravel package for [CREEM](https://creem.io) — accept payments globally with automatic tax handling, subscription management, and license key distribution.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/creem/laravel.svg)](https://packagist.org/packages/creem/laravel)
[![Tests](https://github.com/Haniamin90/creem-laravel/actions/workflows/tests.yml/badge.svg)](https://github.com/Haniamin90/creem-laravel/actions/workflows/tests.yml)
[![PHP](https://img.shields.io/badge/php-%3E%3D8.1-8892BF)](composer.json)
[![Laravel](https://img.shields.io/badge/laravel-10%2B-FF2D20)](composer.json)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

## Features

- **Facade API** — `Creem::createCheckout()`, `Creem::getProduct()`, and 24 more methods
- **Webhook handling** — Automatic signature verification with typed Laravel events
- **Billable trait** — Add checkout, subscription, and billing portal to any Eloquent model
- **Artisan commands** — `creem:webhook-secret`, `creem:sync-products`
- **Full API coverage** — Products, Checkouts, Subscriptions, Customers, Transactions, Licenses, Discounts
- **Auto sandbox detection** — Automatically routes to sandbox API when using test keys
- **Laravel 10, 11 & 12** support with PHP 8.1+

## Installation

```bash
composer require creem/laravel
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=creem-config
```

Add your API credentials to `.env`:

```env
CREEM_API_KEY=creem_test_your_api_key_here
CREEM_WEBHOOK_SECRET=your_webhook_secret_here
```

## Quick Start

### Create a Checkout Session

```php
use Creem\Laravel\Facades\Creem;

$checkout = Creem::createCheckout('prod_abc123', [
    'success_url' => route('checkout.success'),
    'customer' => ['email' => $user->email],
    'metadata' => ['user_id' => $user->id],
]);

return redirect($checkout['checkout_url']);
```

### Using the Billable Trait

Add the trait to your User model:

```php
use Creem\Laravel\Traits\Billable;

class User extends Authenticatable
{
    use Billable;
}
```

Run the migration to add the `creem_customer_id` column:

```bash
php artisan vendor:publish --tag=creem-migrations
php artisan migrate
```

Now you can use billing methods directly on your User model:

```php
// Create a checkout for this user
$checkout = $user->checkout('prod_abc123', [
    'success_url' => route('checkout.success'),
]);

// Get billing portal URL
$portal = $user->billingPortalUrl();

// Get all subscriptions
$subscriptions = $user->creemSubscriptions();

// Cancel a subscription
$user->cancelSubscription('sub_xyz', 'scheduled');
```

## API Reference

### Checkouts

```php
// Create a checkout session
Creem::createCheckout('prod_123', [
    'success_url' => 'https://yourapp.com/success',
    'customer' => ['email' => 'customer@example.com'],
    'metadata' => ['key' => 'value'],
    'discount_code' => 'LAUNCH20',
    'request_id' => 'idempotency-key',
]);

// Retrieve a checkout session
Creem::getCheckout('chk_abc123');
```

### Products

```php
// Create a product (price in cents)
Creem::createProduct([
    'name' => 'Pro Plan',
    'price' => 2999, // $29.99
    'currency' => 'USD',
    'billing_type' => 'recurring', // or 'onetime'
    'billing_period' => 'every-month',
    'tax_category' => 'saas',
]);

// Get a product
Creem::getProduct('prod_123');

// Search products
Creem::searchProducts(['limit' => 10]);
```

### Customers

```php
// Get customer by ID or email
Creem::getCustomer(['customer_id' => 'cus_123']);
Creem::getCustomer(['email' => 'user@example.com']);

// List all customers
Creem::listCustomers(['limit' => 50]);

// Generate billing portal link
Creem::customerBillingPortal('cus_123');
```

### Subscriptions

```php
// Get a subscription
Creem::getSubscription('sub_123');

// Search subscriptions
Creem::searchSubscriptions(['status' => 'active']);

// Update (seats, units, add-ons)
Creem::updateSubscription('sub_123', ['units' => 5]);

// Upgrade to different product
Creem::upgradeSubscription('sub_123', 'prod_pro');

// Cancel (immediate or at period end)
Creem::cancelSubscription('sub_123', 'scheduled');
Creem::cancelSubscription('sub_123', 'immediate');

// Pause & Resume
Creem::pauseSubscription('sub_123');
Creem::resumeSubscription('sub_123');
```

### Transactions

```php
Creem::getTransaction('txn_123');
Creem::searchTransactions([
    'customer_id' => 'cus_123',
    'page_size' => 20,
]);
```

### Licenses

```php
// Activate a license on a device
$result = Creem::activateLicense('ABCD-1234-EFGH', 'MacBook Pro');
$instanceId = $result['instance_id'];

// Validate a license
Creem::validateLicense('ABCD-1234-EFGH', $instanceId);

// Deactivate
Creem::deactivateLicense('ABCD-1234-EFGH', $instanceId);
```

### Discounts

```php
// Create a discount
Creem::createDiscount([
    'name' => 'Launch Sale',
    'code' => 'LAUNCH20',
    'type' => 'percentage', // or 'fixed'
    'percentage' => 20,
    'duration' => 'forever', // 'forever', 'once', or 'repeating'
    'applies_to_products' => ['prod_abc123'],
    'max_redemptions' => 100,
]);

// Get a discount
Creem::getDiscount(['code' => 'LAUNCH20']);

// Delete a discount
Creem::deleteDiscount('disc_123');
```

## Webhooks

CREEM webhooks are handled automatically. The package registers a POST route at `/creem/webhook` (configurable) and verifies signatures using HMAC-SHA256.

### Setup

1. Set your webhook secret in `.env`:
   ```env
   CREEM_WEBHOOK_SECRET=your_webhook_secret
   ```

2. Register your webhook URL in the [CREEM dashboard](https://creem.io) under **Developers > Webhook**:
   ```
   https://yourapp.com/creem/webhook
   ```

### Listening to Events

Register listeners in your `EventServiceProvider` or use Laravel's event discovery:

```php
use Creem\Laravel\Events\CheckoutCompleted;
use Creem\Laravel\Events\SubscriptionActive;
use Creem\Laravel\Events\SubscriptionCanceled;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CheckoutCompleted::class => [
            GrantAccessListener::class,
        ],
        SubscriptionCanceled::class => [
            RevokeAccessListener::class,
        ],
    ];
}
```

Create a listener:

```php
use Creem\Laravel\Events\CheckoutCompleted;

class GrantAccessListener
{
    public function handle(CheckoutCompleted $event): void
    {
        $data = $event->payload;

        // Grant access based on checkout data
        // $data contains customer, product, and transaction info
    }
}
```

### Available Events

| Event Class | Webhook Type | Description |
|---|---|---|
| `CheckoutCompleted` | `checkout.completed` | Payment successful, order created |
| `SubscriptionActive` | `subscription.active` | New subscription created |
| `SubscriptionPaid` | `subscription.paid` | Recurring payment processed |
| `SubscriptionCanceled` | `subscription.canceled` | Subscription ended |
| `SubscriptionScheduledCancel` | `subscription.scheduled_cancel` | Cancellation pending at period end |
| `SubscriptionPastDue` | `subscription.past_due` | Payment failed, retrying |
| `SubscriptionExpired` | `subscription.expired` | Period ended without payment |
| `SubscriptionTrialing` | `subscription.trialing` | Trial period started |
| `SubscriptionPaused` | `subscription.paused` | Subscription paused |
| `SubscriptionUpdated` | `subscription.update` | Subscription modified |
| `RefundCreated` | `refund.created` | Refund issued |
| `DisputeCreated` | `dispute.created` | Chargeback opened |

### Convenience Access Events

The package also dispatches `AccessGranted` and `AccessRevoked` events for simplified access management (inspired by the CREEM TypeScript SDK's `onGrantAccess`/`onRevokeAccess` pattern):

```php
use Creem\Laravel\Events\AccessGranted;
use Creem\Laravel\Events\AccessRevoked;

// AccessGranted fires on: checkout.completed, subscription.active, subscription.paid
// AccessRevoked fires on: subscription.canceled, subscription.expired

class ManageUserAccess
{
    public function handleGrant(AccessGranted $event): void
    {
        // $event->reason — the original event type (e.g., 'checkout.completed')
        // $event->payload — the webhook object data
    }

    public function handleRevoke(AccessRevoked $event): void
    {
        // Revoke the user's access
    }
}
```

You can also listen to the generic `CreemWebhookReceived` event to catch all webhook types:

```php
use Creem\Laravel\Events\CreemWebhookReceived;

class LogAllWebhooks
{
    public function handle(CreemWebhookReceived $event): void
    {
        logger()->info("CREEM webhook: {$event->eventType}", $event->payload);
    }
}
```

## Artisan Commands

### Generate Webhook Secret

```bash
# Generate a new webhook secret and add it to .env
php artisan creem:webhook-secret

# Show the current webhook secret
php artisan creem:webhook-secret --show

# Force overwrite an existing secret
php artisan creem:webhook-secret --force
```

### Sync Products

```bash
# List all products from your CREEM account
php artisan creem:sync-products

# Limit the number of products fetched
php artisan creem:sync-products --limit=50
```

## Configuration

The config file (`config/creem.php`) supports these options:

| Option | Env Variable | Default | Description |
|---|---|---|---|
| `api_key` | `CREEM_API_KEY` | `''` | Your CREEM API key |
| `webhook_secret` | `CREEM_WEBHOOK_SECRET` | `''` | Webhook signing secret |
| `api_url` | `CREEM_API_URL` | Auto-detected | Override the API base URL |
| `webhook_path` | `CREEM_WEBHOOK_PATH` | `creem/webhook` | Webhook route path |
| `webhook_tolerance` | `CREEM_WEBHOOK_TOLERANCE` | `300` | Max webhook age in seconds |
| `currency` | `CREEM_CURRENCY` | `USD` | Default currency |
| `customer_model` | `CREEM_CUSTOMER_MODEL` | `App\Models\User` | Billable model class |

### Sandbox vs Production

The package automatically detects sandbox mode based on your API key prefix:
- `creem_test_*` → Sandbox API (`https://test-api.creem.io`)
- `creem_*` → Production API (`https://api.creem.io`)

## Error Handling

The package throws typed exceptions for different error scenarios:

```php
use Creem\Laravel\Exceptions\CreemApiException;
use Creem\Laravel\Exceptions\CreemAuthenticationException;
use Creem\Laravel\Exceptions\CreemRateLimitException;

try {
    $product = Creem::getProduct('prod_123');
} catch (CreemAuthenticationException $e) {
    // Invalid API key (403)
} catch (CreemRateLimitException $e) {
    // Rate limited (429) - retry later
} catch (CreemApiException $e) {
    // Other API errors (400, 404, 500, etc.)
    $traceId = $e->getTraceId(); // For CREEM support debugging
}
```

## Demo App

> **Live Demo:** [https://creem.h90.space](https://creem.h90.space) — see the package in action with real CREEM API integration.

A fully functional Docker-based demo app is included in [`examples/demo/`](examples/demo/). It demonstrates:

- Facade API calls (`Creem::searchProducts()`)
- Billable trait (`$user->checkout()`)
- Webhook event handling with live dashboard
- Sandbox/production auto-detection

```bash
cd examples/demo
cp .env.example .env
# Add your CREEM API key and webhook secret to .env
docker compose up -d --build
# Visit http://localhost:8000
```

See the [demo README](examples/demo/README.md) for full setup instructions.

## Testing

```bash
composer test
```

Or run directly:

```bash
vendor/bin/phpunit
```

The package ships with 73 tests covering:
- API client HTTP handling & error responses
- All 24 Facade methods (checkout, products, subscriptions, etc.)
- Webhook signature verification
- Event dispatching for all 12 webhook types + AccessGranted/AccessRevoked
- Service provider bindings & configuration
- Artisan command registration & behavior

## Package Structure

```
creem-laravel/
├── config/
│   └── creem.php                 # Configuration file
├── database/
│   └── migrations/               # Billable migration
├── src/
│   ├── Api/                      # API endpoint classes
│   │   ├── CheckoutApi.php
│   │   ├── CustomerApi.php
│   │   ├── DiscountApi.php
│   │   ├── LicenseApi.php
│   │   ├── ProductApi.php
│   │   ├── SubscriptionApi.php
│   │   └── TransactionApi.php
│   ├── Commands/                 # Artisan commands
│   │   ├── SyncProductsCommand.php
│   │   └── WebhookSecretCommand.php
│   ├── Events/                   # Laravel events
│   │   ├── CheckoutCompleted.php
│   │   ├── CreemWebhookReceived.php
│   │   ├── SubscriptionActive.php
│   │   ├── AccessGranted.php
│   │   ├── AccessRevoked.php
│   │   └── ... (15 event classes total)
│   ├── Exceptions/               # Typed exceptions
│   ├── Facades/
│   │   └── Creem.php            # Facade with IDE hints
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── WebhookController.php
│   │   └── Middleware/
│   │       └── VerifyCreemWebhook.php
│   ├── Traits/
│   │   └── Billable.php         # Eloquent billing trait
│   ├── Creem.php                # Main service class
│   ├── CreemClient.php          # HTTP client
│   ├── CreemServiceProvider.php  # Service provider
│   └── WebhookEventType.php     # Event type constants
├── tests/
│   ├── Feature/                  # Integration tests
│   └── Unit/                     # Unit tests
├── composer.json
└── phpunit.xml
```

## Requirements

- PHP 8.1+
- Laravel 10, 11, or 12
- Guzzle HTTP 7.0+

## License

MIT License. See [LICENSE](LICENSE) for details.

## Resources

- [CREEM Documentation](https://docs.creem.io)
- [CREEM Dashboard](https://creem.io)
- [API Reference](https://docs.creem.io/api-reference)
