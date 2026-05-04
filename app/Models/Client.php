<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasMediaUrl;


class Client extends Model
{
    use HasMediaUrl;

    protected $mediaColumn = 'logo';

    protected $fillable = ['name', 'logo', 'url', 'order', 'is_server_subscribed', 'billing_date', 'subscription_price'];
}
