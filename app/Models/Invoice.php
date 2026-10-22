<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'billing_period' => 'date',
        'wa_sent_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('position');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function packageOrder()
    {
        return $this->hasOne(PackageOrder::class);
    }
}
