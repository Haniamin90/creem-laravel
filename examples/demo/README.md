# CREEM Laravel Demo App

A fully functional demo application showcasing the `creem/laravel` package. Run it locally with Docker to see the package in action.

## Quick Start

### 1. Configure environment

```bash
cp .env.example .env
```

Edit `.env` with your CREEM credentials:

```env
CREEM_API_KEY=creem_test_your_api_key_here
CREEM_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

> Get your API key and webhook secret from the [CREEM Dashboard](https://creem.io/dashboard).

### 2. Generate an app key

```bash
# Option A: Generate manually and paste into .env
php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"

# Option B: Or just set any 32-char string
# APP_KEY=base64:dGhpcyBpcyBhIHRoaXJ0eSB0d28gY2hhcmFjdGVyIHN0cmluZw==
```

### 3. Start with Docker

```bash
docker compose up -d --build
```

The app will be available at **http://localhost:8000**.

### 4. Set up webhooks (optional)

To receive webhook events, CREEM needs a public URL. Use a tunnel:

```bash
# Using Cloudflare Tunnel
cloudflared tunnel --url http://localhost:8000

# Or using ngrok
ngrok http 8000
```

Then set your webhook URL in the CREEM dashboard:

```
https://your-tunnel-url.com/creem/webhook
```

### 5. Seed sample products (optional)

No products yet? Create 4 sample products instantly:

**Option A — UI button:**
Visit `/products` and click the **⚡ Create Sample Products** button.

**Option B — CLI script:**
```bash
docker compose exec app php seed-products.php
```

This creates 3 subscriptions (Starter $9.99/mo, Pro $29.99/mo, Enterprise $299/yr) and 1 one-time product (Lifetime License $99) via `Creem::createProduct()`.

## What's Inside

### Pages

| Route | Description | Methods |
|-------|-------------|---------|
| `GET /` | Dashboard with webhooks, users, environment | `client()`, `isSandbox()` |
| `GET /products` | Product listing + seed button | `searchProducts()`, `createProduct()`, `getProduct()` |
| `GET /customers` | Customer list + billing portal | `listCustomers()`, `getCustomer()`, `customerBillingPortal()` |
| `GET /subscriptions` | Subscription management | `searchSubscriptions()`, `getSubscription()`, `cancelSubscription()`, `pauseSubscription()`, `resumeSubscription()`, `upgradeSubscription()`, `updateSubscription()` |
| `GET /transactions` | Transaction history | `searchTransactions()`, `getTransaction()` |
| `GET /licenses` | License activate/validate/deactivate | `activateLicense()`, `validateLicense()`, `deactivateLicense()` |
| `GET /discounts` | Discount create/lookup/delete | `createDiscount()`, `getDiscount()`, `deleteDiscount()` |
| `POST /checkout` | Checkout via Billable trait | `$user->checkout()` → `createCheckout()` |
| `GET /success` | Post-payment success | `getCheckout()` |
| `GET /api/*` | Raw JSON endpoints | Various |

### All 24 API Methods Covered

- **Products (3)** &mdash; `createProduct`, `getProduct`, `searchProducts`
- **Checkouts (2)** &mdash; `createCheckout`, `getCheckout`
- **Customers (3)** &mdash; `listCustomers`, `getCustomer`, `customerBillingPortal`
- **Subscriptions (7)** &mdash; `searchSubscriptions`, `getSubscription`, `updateSubscription`, `upgradeSubscription`, `cancelSubscription`, `pauseSubscription`, `resumeSubscription`
- **Transactions (2)** &mdash; `searchTransactions`, `getTransaction`
- **Licenses (3)** &mdash; `activateLicense`, `validateLicense`, `deactivateLicense`
- **Discounts (3)** &mdash; `createDiscount`, `getDiscount`, `deleteDiscount`
- **Billable Trait (8)** &mdash; `checkout`, `billingPortalUrl`, `creemCustomer`, `creemSubscriptions`, `creemTransactions`, `cancelSubscription`, `pauseSubscription`, `resumeSubscription`
- **Webhook Handling** &mdash; 15 event types with HMAC-SHA256 verification

### Architecture

```
app/
  Http/Controllers/DemoController.php    # All route handlers
  Listeners/
    LogWebhook.php                       # Logs every webhook to DB
    HandleAccessGranted.php              # Links CREEM customer to user
    HandleAccessRevoked.php              # Handles access revocation
  Models/
    User.php                             # Uses Billable trait
    WebhookLog.php                       # Stores webhook events
  Providers/
    AppServiceProvider.php               # Registers event listeners
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Container won't start | Run `docker compose logs -f` to check errors |
| `APP_KEY` missing | Generate one (see step 2 above) |
| No products shown | Click **⚡ Create Sample Products** on the products page, or run `php seed-products.php` |
| No webhook events | Ensure your webhook URL is publicly accessible |
| 419 CSRF error | Normal for API calls; the web forms include CSRF tokens automatically |
