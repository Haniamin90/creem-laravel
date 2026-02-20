#!/usr/bin/env php
<?php

/**
 * CREEM Sample Product Seeder
 *
 * Creates sample products in your CREEM account via the API.
 * Run this script to quickly populate your account with test products.
 *
 * Usage:
 *   php seed-products.php
 *
 * Requirements:
 *   - CREEM_API_KEY must be set in your .env file
 *   - The creem/laravel package must be installed
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Creem\Laravel\Facades\Creem;

$products = [
    [
        'name' => 'Starter Plan',
        'description' => 'Perfect for individuals and small projects. Includes core features and email support.',
        'price' => 999,
        'currency' => 'usd',
        'billing_type' => 'recurring',
        'billing_period' => 'every-month',
    ],
    [
        'name' => 'Pro Plan',
        'description' => 'For growing teams. Unlimited projects, priority support, and advanced analytics.',
        'price' => 2999,
        'currency' => 'usd',
        'billing_type' => 'recurring',
        'billing_period' => 'every-month',
    ],
    [
        'name' => 'Enterprise Annual',
        'description' => 'Best value — annual billing with 2 months free. Everything in Pro plus custom integrations.',
        'price' => 29900,
        'currency' => 'usd',
        'billing_type' => 'recurring',
        'billing_period' => 'every-year',
    ],
    [
        'name' => 'Lifetime License',
        'description' => 'One-time purchase. Lifetime access to all current and future features. No recurring fees.',
        'price' => 9900,
        'currency' => 'usd',
        'billing_type' => 'onetime',
    ],
];

echo "CREEM Sample Product Seeder\n";
echo str_repeat('=', 40)."\n\n";

$mode = Creem::client()->isSandbox() ? 'SANDBOX' : 'PRODUCTION';
echo "Mode: {$mode}\n";
echo "API:  ".Creem::client()->getBaseUrl()."\n\n";

$created = 0;
$failed = 0;

foreach ($products as $product) {
    echo "Creating '{$product['name']}'... ";
    try {
        $result = Creem::createProduct($product);
        $id = $result['id'] ?? 'unknown';
        echo "OK ({$id})\n";
        $created++;
    } catch (\Exception $e) {
        echo "FAILED: {$e->getMessage()}\n";
        $failed++;
    }
}

echo "\nDone! Created: {$created}, Failed: {$failed}\n";
