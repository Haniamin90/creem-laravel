<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CREEM API Key
    |--------------------------------------------------------------------------
    |
    | Your CREEM API key. Use creem_test_ prefix for sandbox mode
    | and creem_ prefix for production.
    |
    */

    'api_key' => env('CREEM_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | Your CREEM webhook signing secret used to verify incoming webhooks.
    | Found in your CREEM dashboard under Developers > Webhook.
    |
    */

    'webhook_secret' => env('CREEM_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the CREEM API. This is automatically determined
    | based on your API key prefix, but can be overridden here.
    |
    */

    'api_url' => env('CREEM_API_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhook Path
    |--------------------------------------------------------------------------
    |
    | The URI path where CREEM webhooks should be received. This will be
    | registered automatically when the package routes are loaded.
    |
    */

    'webhook_path' => env('CREEM_WEBHOOK_PATH', 'creem/webhook'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The default currency for new products and checkouts.
    |
    */

    'currency' => env('CREEM_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Customer Model
    |--------------------------------------------------------------------------
    |
    | The Eloquent model that represents your application's users.
    | This model should use the Billable trait.
    |
    | WARNING: Do not use this value for dynamic class instantiation without
    | validating it against an allowlist first.
    |
    */

    'customer_model' => env('CREEM_CUSTOMER_MODEL', 'App\\Models\\User'),

];
