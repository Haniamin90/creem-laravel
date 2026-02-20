<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DemoController::class, 'index'])->name('home');
Route::get('/products', [DemoController::class, 'products'])->name('products');
Route::post('/checkout', [DemoController::class, 'checkout'])->name('checkout');
Route::get('/success', [DemoController::class, 'success'])->name('success');

// Seed sample products via CREEM API
Route::post('/seed-products', [DemoController::class, 'seedProducts'])->name('seed-products');

// Customers
Route::get('/customers', [DemoController::class, 'customers'])->name('customers');
Route::get('/api/customer', [DemoController::class, 'getCustomerApi']);
Route::get('/api/customer/{id}/billing-portal', [DemoController::class, 'billingPortal']);

// Subscriptions
Route::get('/subscriptions', [DemoController::class, 'subscriptions'])->name('subscriptions');
Route::get('/api/subscription/{id}', [DemoController::class, 'getSubscriptionApi']);
Route::post('/subscriptions/cancel', [DemoController::class, 'cancelSubscription'])->name('subscription.cancel');
Route::post('/subscriptions/pause', [DemoController::class, 'pauseSubscription'])->name('subscription.pause');
Route::post('/subscriptions/resume', [DemoController::class, 'resumeSubscription'])->name('subscription.resume');
Route::post('/subscriptions/upgrade', [DemoController::class, 'upgradeSubscription'])->name('subscription.upgrade');

// Transactions
Route::get('/transactions', [DemoController::class, 'transactions'])->name('transactions');
Route::get('/api/transaction/{id}', [DemoController::class, 'getTransactionApi']);

// Licenses
Route::get('/licenses', [DemoController::class, 'licenses'])->name('licenses');
Route::post('/licenses/activate', [DemoController::class, 'activateLicense'])->name('license.activate');
Route::post('/licenses/validate', [DemoController::class, 'validateLicense'])->name('license.validate');
Route::post('/licenses/deactivate', [DemoController::class, 'deactivateLicense'])->name('license.deactivate');

// Discounts
Route::get('/discounts', [DemoController::class, 'discounts'])->name('discounts');
Route::post('/discounts/create', [DemoController::class, 'createDiscount'])->name('discount.create');
Route::get('/api/discount', [DemoController::class, 'getDiscountApi']);
Route::post('/discounts/delete', [DemoController::class, 'deleteDiscount'])->name('discount.delete');

// API routes for testing
Route::get('/api/products', [DemoController::class, 'syncProducts']);
Route::get('/api/product/{id}', [DemoController::class, 'getProduct']);
Route::get('/api/checkout/{id}', [DemoController::class, 'getCheckout']);
Route::get('/api/webhook-logs', [DemoController::class, 'webhookLogs']);

// The CREEM webhook route is auto-registered by the package at POST /creem/webhook
