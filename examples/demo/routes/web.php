<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DemoController::class, 'index'])->name('home');
Route::get('/products', [DemoController::class, 'products'])->name('products');
Route::post('/checkout', [DemoController::class, 'checkout'])->name('checkout');
Route::get('/success', [DemoController::class, 'success'])->name('success');

// API routes for testing
Route::get('/api/products', [DemoController::class, 'syncProducts']);
Route::get('/api/webhook-logs', [DemoController::class, 'webhookLogs']);

// The CREEM webhook route is auto-registered by the package at POST /creem/webhook
