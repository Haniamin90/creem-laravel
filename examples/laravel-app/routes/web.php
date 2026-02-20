<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CREEM Integration Routes
|--------------------------------------------------------------------------
|
| Complete example routes demonstrating every feature of the creem/laravel
| package. Covers checkouts, billing management, products, customers,
| transactions, licenses, and discounts.
|
| The webhook route is automatically registered by the package at:
| POST /creem/webhook (configurable via CREEM_WEBHOOK_PATH in .env)
|
*/

Route::middleware('auth')->group(function () {

    // ── Checkout Flow ────────────────────────────────────────────────
    // Core payment integration: display products, create checkout, handle return.

    Route::get('/pricing', [CheckoutController::class, 'pricing'])
        ->name('pricing');

    Route::post('/checkout', [CheckoutController::class, 'checkout'])
        ->name('checkout');

    Route::get('/checkout/success', [CheckoutController::class, 'success'])
        ->name('checkout.success');

    // ── Billing Dashboard ────────────────────────────────────────────
    // Customer self-service: view subscriptions, transactions, manage plans.

    Route::get('/billing', [BillingController::class, 'index'])
        ->name('billing');

    Route::get('/billing/portal', [BillingController::class, 'portal'])
        ->name('billing.portal');

    // ── Subscription Management ──────────────────────────────────────
    // Full subscription lifecycle: cancel, pause, resume, upgrade, update.

    Route::post('/billing/subscriptions/{subscription}/cancel', [BillingController::class, 'cancelSubscription'])
        ->name('billing.subscriptions.cancel');

    Route::post('/billing/subscriptions/{subscription}/pause', [BillingController::class, 'pauseSubscription'])
        ->name('billing.subscriptions.pause');

    Route::post('/billing/subscriptions/{subscription}/resume', [BillingController::class, 'resumeSubscription'])
        ->name('billing.subscriptions.resume');

    Route::post('/billing/subscriptions/{subscription}/upgrade', [BillingController::class, 'upgradeSubscription'])
        ->name('billing.subscriptions.upgrade');

    Route::post('/billing/subscriptions/{subscription}/update', [BillingController::class, 'updateSubscription'])
        ->name('billing.subscriptions.update');

    Route::get('/billing/subscriptions/{subscription}', [BillingController::class, 'showSubscription'])
        ->name('billing.subscriptions.show');

    Route::get('/billing/subscriptions', [BillingController::class, 'searchSubscriptions'])
        ->name('billing.subscriptions.search');

    // ── Product Management ───────────────────────────────────────────
    // Create and browse products via the CREEM API.

    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');

    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');

    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->name('products.show');

    // ── Customer Management ──────────────────────────────────────────
    // Look up customers by ID or email, list all, billing portal.

    Route::get('/customers', [CustomerController::class, 'index'])
        ->name('customers.index');

    Route::get('/customers/show', [CustomerController::class, 'show'])
        ->name('customers.show');

    Route::get('/customers/{customer}/portal', [CustomerController::class, 'billingPortal'])
        ->name('customers.portal');

    // ── Transaction History ──────────────────────────────────────────
    // Search and view payment transactions.

    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');

    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
        ->name('transactions.show');

    // ── License Key Management ───────────────────────────────────────
    // Activate, validate, and deactivate software licenses.

    Route::post('/licenses/activate', [LicenseController::class, 'activate'])
        ->name('licenses.activate');

    Route::post('/licenses/validate', [LicenseController::class, 'validate'])
        ->name('licenses.validate');

    Route::post('/licenses/deactivate', [LicenseController::class, 'deactivate'])
        ->name('licenses.deactivate');

    // ── Discount Code Management ─────────────────────────────────────
    // Create, look up, and delete discount codes.

    Route::post('/discounts', [DiscountController::class, 'store'])
        ->name('discounts.store');

    Route::get('/discounts/show', [DiscountController::class, 'show'])
        ->name('discounts.show');

    Route::delete('/discounts/{discount}', [DiscountController::class, 'destroy'])
        ->name('discounts.destroy');
});
