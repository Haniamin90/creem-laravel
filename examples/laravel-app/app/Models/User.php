<?php

namespace App\Models;

use Creem\Laravel\Traits\Billable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User model with CREEM Billable trait.
 *
 * The Billable trait adds these methods to your User model:
 *
 * Customer ID Management:
 *   $user->creemCustomerId()         → ?string
 *   $user->hasCreemCustomerId()      → bool
 *   $user->setCreemCustomerId($id)   → self
 *
 * Checkout:
 *   $user->checkout($productId, $params)  → array
 *     Auto-fills customer.email from $user->email
 *     Auto-adds metadata: model_type, model_id, creem_customer_id
 *
 * Billing Portal:
 *   $user->billingPortalUrl()  → array (with 'url' key)
 *     Requires creem_customer_id to be set
 *
 * Customer Data:
 *   $user->creemCustomer()  → array
 *     Fetches by creem_customer_id if set, otherwise by email
 *
 * Subscriptions:
 *   $user->creemSubscriptions($params)                  → array
 *   $user->cancelSubscription($subId, $mode)            → array
 *   $user->pauseSubscription($subId)                    → array
 *   $user->resumeSubscription($subId)                   → array
 *
 * Transactions:
 *   $user->creemTransactions($params)  → array
 *
 * IMPORTANT: Run the package migration to add the creem_customer_id column:
 *   php artisan vendor:publish --tag=creem-migrations
 *   php artisan migrate
 */
class User extends Authenticatable
{
    use Billable, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'creem_customer_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
