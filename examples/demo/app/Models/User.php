<?php

namespace App\Models;

use Creem\Laravel\Traits\Billable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Billable;

    protected $fillable = [
        'name',
        'email',
        'creem_customer_id',
    ];
}
