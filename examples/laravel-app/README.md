# CREEM Laravel Example App

A comprehensive reference implementation showing how to integrate the `creem/laravel` package into a Laravel application. Covers **every feature** of the package: checkouts, subscriptions, products, customers, transactions, licenses, discounts, and webhooks.

## Quick Start

1. **Install the package:**
   ```bash
   composer require creem/laravel
   ```

2. **Publish config and migrations:**
   ```bash
   php artisan vendor:publish --tag=creem-config
   php artisan vendor:publish --tag=creem-migrations
   php artisan migrate
   ```

3. **Configure `.env`:**
   ```env
   CREEM_API_KEY=creem_test_your_key_here
   CREEM_WEBHOOK_SECRET=your_webhook_secret
   ```

4. **Generate a webhook secret** (optional — you can also copy it from the CREEM dashboard):
   ```bash
   php artisan creem:webhook-secret
   ```

5. Copy the example files into your Laravel project and register the routes.

## Architecture Overview

```
app/
├── Http/Controllers/
│   ├── CheckoutController.php    ← Checkout flow (Facade + Billable trait)
│   ├── BillingController.php     ← Subscriptions, portal, transactions
│   ├── ProductController.php     ← Product CRUD
│   ├── CustomerController.php    ← Customer lookup, portal
│   ├── TransactionController.php ← Transaction search & details
│   ├── LicenseController.php     ← License activation/validation
│   └── DiscountController.php    ← Discount code management
├── Listeners/
│   ├── HandleCheckoutCompleted.php   ← Link customer, grant access
│   ├── HandleSubscriptionChange.php  ← All 9 subscription events
│   ├── HandleAccessChange.php        ← Convenience grant/revoke
│   ├── HandleRefundCreated.php       ← Process refunds
│   ├── HandleDisputeCreated.php      ← Handle chargebacks
│   └── LogAllWebhooks.php           ← Audit log for all events
├── Models/
│   └── User.php                  ← Billable trait (11 methods)
├── Providers/
│   └── EventServiceProvider.php  ← All 15 events mapped
└── routes/
    └── web.php                   ← 22 routes covering every feature
```

## What's Covered

### API Coverage (26/26 Facade Methods)

| Category | Methods | Controller |
|----------|---------|------------|
| **Checkouts** | `createCheckout`, `getCheckout` | `CheckoutController` |
| **Products** | `createProduct`, `getProduct`, `searchProducts` | `ProductController` |
| **Customers** | `getCustomer`, `listCustomers`, `customerBillingPortal` | `CustomerController` |
| **Subscriptions** | `getSubscription`, `searchSubscriptions`, `updateSubscription`, `upgradeSubscription`, `cancelSubscription`, `pauseSubscription`, `resumeSubscription` | `BillingController` |
| **Transactions** | `getTransaction`, `searchTransactions` | `TransactionController` |
| **Licenses** | `activateLicense`, `validateLicense`, `deactivateLicense` | `LicenseController` |
| **Discounts** | `createDiscount`, `getDiscount`, `deleteDiscount` | `DiscountController` |

### Webhook Event Coverage (15/15 Events)

| Event | Listener | Description |
|-------|----------|-------------|
| `CreemWebhookReceived` | `LogAllWebhooks` | Generic — fires for ALL webhooks |
| `CheckoutCompleted` | `HandleCheckoutCompleted` | Payment successful |
| `SubscriptionActive` | `HandleSubscriptionChange` | Subscription activated |
| `SubscriptionPaid` | `HandleSubscriptionChange` | Recurring payment collected |
| `SubscriptionTrialing` | `HandleSubscriptionChange` | Trial period started |
| `SubscriptionPaused` | `HandleSubscriptionChange` | Subscription paused |
| `SubscriptionScheduledCancel` | `HandleSubscriptionChange` | Cancellation scheduled |
| `SubscriptionCanceled` | `HandleSubscriptionChange` | Subscription canceled |
| `SubscriptionExpired` | `HandleSubscriptionChange` | Subscription expired |
| `SubscriptionPastDue` | `HandleSubscriptionChange` | Payment failed |
| `SubscriptionUpdated` | `HandleSubscriptionChange` | Subscription modified |
| `RefundCreated` | `HandleRefundCreated` | Refund issued |
| `DisputeCreated` | `HandleDisputeCreated` | Chargeback initiated |
| `AccessGranted` | `HandleAccessChange` | Convenience: grant access |
| `AccessRevoked` | `HandleAccessChange` | Convenience: revoke access |

### Billable Trait (11 Methods on User Model)

```php
// Customer management
$user->creemCustomerId();          // Get stored CREEM customer ID
$user->hasCreemCustomerId();       // Check if linked
$user->setCreemCustomerId($id);    // Link CREEM customer

// Checkout (auto-fills email and metadata)
$user->checkout('prod_...', ['success_url' => '...']);

// Billing portal
$user->billingPortalUrl();         // Get self-service portal URL

// Data retrieval
$user->creemCustomer();            // Fetch customer profile
$user->creemSubscriptions();       // List subscriptions
$user->creemTransactions();        // List transactions

// Subscription management
$user->cancelSubscription('sub_...', 'scheduled');
$user->pauseSubscription('sub_...');
$user->resumeSubscription('sub_...');
```

### Artisan Commands

```bash
# Generate and store a webhook signing secret
php artisan creem:webhook-secret
php artisan creem:webhook-secret --show     # Display current
php artisan creem:webhook-secret --force    # Overwrite existing

# Sync and display products from CREEM
php artisan creem:sync-products
php artisan creem:sync-products --limit=50
```

## Key Patterns Demonstrated

### 1. Two Checkout Approaches

```php
// Facade — full control
$checkout = Creem::createCheckout($productId, [
    'success_url' => route('checkout.success'),
    'customer' => ['email' => $user->email],
    'metadata' => ['user_id' => $user->id],
    'discount_code' => 'SAVE20',
]);

// Billable trait — auto-fills email, metadata, customer ID
$checkout = $user->checkout($productId, [
    'success_url' => route('checkout.success'),
]);
```

### 2. Comprehensive Error Handling

```php
try {
    $result = Creem::createCheckout($productId, $params);
} catch (CreemRateLimitException $e) {
    // 429: Too many requests
} catch (CreemAuthenticationException $e) {
    // 403: Invalid API key
} catch (CreemApiException $e) {
    // All other errors — includes trace ID for debugging
    Log::error("CREEM Error: {$e->getMessage()}", [
        'trace_id' => $e->getTraceId(),
    ]);
}
```

### 3. Convenience Access Events

```php
// Instead of listening to 5 separate events, use 2:
AccessGranted::class => [HandleAccessChange::class],  // grant
AccessRevoked::class => [HandleAccessChange::class],  // revoke

// AccessGranted fires on: checkout.completed, subscription.active, subscription.paid
// AccessRevoked fires on: subscription.canceled, subscription.expired
```

### 4. User Resolution Strategy

```php
// The example listeners resolve users with this priority:
// 1. metadata.model_id (set by Billable trait)
// 2. metadata.user_id (set manually)
// 3. customer.email (fallback)
// 4. customer_id → creem_customer_id column (for subscription events)
```

## Testing Webhooks Locally

**Option A: Cloudflare Tunnel (recommended)**
```bash
cloudflared tunnel --url http://localhost:8000
```

**Option B: ngrok**
```bash
ngrok http 8000
```

Then set the webhook URL in your [CREEM Dashboard](https://creem.io/dashboard):
```
https://your-tunnel-url/creem/webhook
```

## Routes Reference

| Method | URI | Controller | Purpose |
|--------|-----|------------|---------|
| GET | `/pricing` | `CheckoutController@pricing` | Display products |
| POST | `/checkout` | `CheckoutController@checkout` | Create checkout session |
| GET | `/checkout/success` | `CheckoutController@success` | Post-payment return |
| GET | `/billing` | `BillingController@index` | Billing dashboard |
| GET | `/billing/portal` | `BillingController@portal` | CREEM billing portal |
| POST | `/billing/subscriptions/{id}/cancel` | `BillingController` | Cancel subscription |
| POST | `/billing/subscriptions/{id}/pause` | `BillingController` | Pause subscription |
| POST | `/billing/subscriptions/{id}/resume` | `BillingController` | Resume subscription |
| POST | `/billing/subscriptions/{id}/upgrade` | `BillingController` | Upgrade plan |
| POST | `/billing/subscriptions/{id}/update` | `BillingController` | Update units/seats |
| GET | `/billing/subscriptions/{id}` | `BillingController` | View subscription |
| GET | `/billing/subscriptions` | `BillingController` | Search subscriptions |
| GET | `/products` | `ProductController@index` | List products |
| POST | `/products` | `ProductController@store` | Create product |
| GET | `/products/{id}` | `ProductController@show` | Get product |
| GET | `/customers` | `CustomerController@index` | List customers |
| GET | `/customers/show` | `CustomerController@show` | Get customer |
| GET | `/customers/{id}/portal` | `CustomerController@billingPortal` | Customer portal |
| GET | `/transactions` | `TransactionController@index` | Search transactions |
| GET | `/transactions/{id}` | `TransactionController@show` | Get transaction |
| POST | `/licenses/activate` | `LicenseController@activate` | Activate license |
| POST | `/licenses/validate` | `LicenseController@validate` | Validate license |
| POST | `/licenses/deactivate` | `LicenseController@deactivate` | Deactivate license |
| POST | `/discounts` | `DiscountController@store` | Create discount |
| GET | `/discounts/show` | `DiscountController@show` | Get discount |
| DELETE | `/discounts/{id}` | `DiscountController@destroy` | Delete discount |
| POST | `/creem/webhook` | *(auto-registered)* | Webhook endpoint |
