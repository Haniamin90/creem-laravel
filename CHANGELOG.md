# Changelog

All notable changes to `creem/laravel` will be documented in this file.

## 1.0.0 - 2026-02-20

### Added
- Initial release
- Full CREEM API coverage: Products, Checkouts, Subscriptions, Customers, Transactions, Licenses, Discounts
- `Creem` Facade with 24 API methods
- Webhook signature verification middleware (HMAC-SHA256)
- 15 typed Laravel events for all webhook types (12 webhook events + `AccessGranted`, `AccessRevoked`, `CreemWebhookReceived`)
- `AccessGranted` / `AccessRevoked` convenience events (mirrors TypeScript SDK pattern)
- `WebhookEventType` constants class with `shouldGrantAccess()`/`shouldRevokeAccess()` helpers
- `Billable` trait for Eloquent models
- `creem:webhook-secret` Artisan command
- `creem:list-products` Artisan command
- Auto sandbox/production detection based on API key prefix
- Published config with environment variable support
- Migration for `creem_customer_id` column
- 78 PHPUnit tests with 148 assertions
- Support for Laravel 10, 11, and 12
- Support for PHP 8.1+
- GitHub Actions CI with PHP 8.1–8.4 × Laravel 10–12 matrix
- Laravel Pint code style enforcement
- Docker-based demo app with glassmorphism UI (`examples/demo/`)
