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

## What's Inside

### Pages

| Route | Description |
|-------|-------------|
| `GET /` | Dashboard with webhook events, users, and environment info |
| `GET /products` | Product listing fetched live from CREEM API |
| `POST /checkout` | Creates a checkout session via the Billable trait |
| `GET /success` | Post-payment success page |
| `GET /api/products` | Raw JSON product listing |
| `GET /api/webhook-logs` | Raw JSON webhook event log |

### Package Features Demonstrated

- **Facade** &mdash; `Creem::searchProducts()`, `Creem::client()`
- **Billable Trait** &mdash; `$user->checkout($productId, $params)`
- **Webhook Handling** &mdash; Auto-registered at `POST /creem/webhook`
- **Event Listeners** &mdash; `CreemWebhookReceived`, `AccessGranted`, `AccessRevoked`
- **Exception Handling** &mdash; `CreemApiException` with trace IDs
- **Sandbox Detection** &mdash; Auto-detects `creem_test_` prefix

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
| No products shown | Create products in your [CREEM dashboard](https://creem.io/dashboard) |
| No webhook events | Ensure your webhook URL is publicly accessible |
| 419 CSRF error | Normal for API calls; the web forms include CSRF tokens automatically |
